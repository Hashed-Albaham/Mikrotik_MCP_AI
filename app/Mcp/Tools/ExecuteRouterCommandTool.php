<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Mcp\Tools;

use App\Services\Router\RouterOsFactory;
use App\Models\Router;
use Exception;

class ExecuteRouterCommandTool extends BaseTool
{
    /**
     * ENHANCED: Comprehensive RouterOS API Whitelist
     * Based on official MikroTik API documentation
     * Organized by module for maintainability
     */
    private const ALLOWED_COMMAND_PATTERNS = [
        // === System Module ===
        '/^\/system\/resource/',      // CPU, memory, uptime
        '/^\/system\/identity/',      // Router name
        '/^\/system\/clock/',         // Date/time
        '/^\/system\/health/',        // Hardware health
        '/^\/system\/routerboard/',   // Hardware info
        '/^\/system\/package/',       // Installed packages
        '/^\/system\/license/',       // License info
        '/^\/system\/history/',       // Command history
        '/^\/system\/note/',          // System notes
        '/^\/system\/ntp/',           // NTP client settings
        '/^\/system\/logging/',       // Logging settings (read only)
        
        // === Interface Module ===
        '/^\/interface\/print/',
        '/^\/interface\/getall/',
        '/^\/interface\/listen/',
        '/^\/interface\/set/',
        '/^\/interface\/ethernet/',
        '/^\/interface\/vlan/',
        '/^\/interface\/bridge/',
        '/^\/interface\/bonding/',
        '/^\/interface\/wireless/',
        '/^\/interface\/lte/',
        '/^\/interface\/pppoe-client/',
        '/^\/interface\/pptp-client/',
        '/^\/interface\/l2tp-client/',
        '/^\/interface\/ovpn-client/',
        '/^\/interface\/wireguard/',
        
        // === IP Module ===
        '/^\/ip\/address/',
        '/^\/ip\/route/',
        '/^\/ip\/arp/',
        '/^\/ip\/neighbor/',
        '/^\/ip\/dns/',
        '/^\/ip\/pool/',
        '/^\/ip\/dhcp-server/',
        '/^\/ip\/dhcp-client/',
        '/^\/ip\/firewall\/filter/',
        '/^\/ip\/firewall\/nat/',
        '/^\/ip\/firewall\/mangle/',
        '/^\/ip\/firewall\/address-list/',
        '/^\/ip\/firewall\/connection/',
        '/^\/ip\/hotspot/',
        '/^\/ip\/service/',          // View services only
        '/^\/ip\/cloud/',
        '/^\/ip\/proxy/',
        '/^\/ip\/ipsec/',
        
        // === Routing Module ===
        '/^\/routing\/ospf/',
        '/^\/routing\/bgp/',
        '/^\/routing\/rip/',
        '/^\/routing\/filter/',
        '/^\/routing\/table/',
        
        // === Queue Module ===
        '/^\/queue\/simple/',
        '/^\/queue\/tree/',
        '/^\/queue\/type/',
        
        // === User & Access Module ===
        '/^\/user\/active/',
        '/^\/user\/print/',          // List users (not modify)
        '/^\/user\/group/',
        
        // === Wireless & CAPsMAN ===
        '/^\/caps-man/',
        '/^\/interface\/wifiwave2/',
        
        // === PPP & VPN ===
        '/^\/ppp\/active/',
        '/^\/ppp\/profile/',
        '/^\/ppp\/secret/',          // Manage PPP users
        '/^\/ppp\/aaa/',
        
        // === Tools (Safe) ===
        '/^\/tool\/ping/',
        '/^\/tool\/traceroute/',
        '/^\/tool\/bandwidth-test/',
        '/^\/tool\/torch/',
        '/^\/tool\/netwatch/',
        '/^\/tool\/e-mail/',         // Email notifications
        '/^\/tool\/sniffer/',        // Packet capture
        '/^\/tool\/profile/',        // CPU profiler
        
        // === Logs & Certificates ===
        '/^\/log\/print/',
        '/^\/certificate/',
        
        // === RADIUS ===
        '/^\/radius/',
        
        // === SNMP ===
        '/^\/snmp/',
    ];

    /**
     * ENHANCED: Blocked dangerous commands
     * These will be rejected even if they match allowed patterns
     */
    private const BLOCKED_PATTERNS = [
        // Configuration export/import (data exfiltration risk)
        '/export/',
        '/import/',
        
        // Script execution (code injection)
        '/script/',
        '/scheduler/',
        '/environment/',
        
        // Credential access
        '/password/',
        '/secret.*=/',            // Setting secrets
        '/api-key/',
        '/private-key/',
        
        // System destructive
        '/backup/',
        '/reset-configuration/',
        '/system\/reboot/',
        '/system\/shutdown/',
        '/system\/upgrade/',
        '/system\/package\/downgrade/',
        
        // File system access
        '/file/',
        '/fetch/',                // Download files
        
        // System User management (write) - 🛡️ More specific to not block hotspot users
        '/^\\/user\\/add/',        // System users only (starts with /user)
        '/^\\/user\\/set/',        // System users only
        '/^\\/user\\/remove/',     // System users only
        
        // SSH/Console access
        '/system\\/ssh/',
        '/console/',
    ];

    public function name(): string
    {
        return 'execute_router_command';
    }

    public function description(): string
    {
        return 'Executes a RouterOS scripting command on the connected router. Use this for ANY configuration change (IPs, Hotspot, Queues, Firewall). Returns the output or error.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'command' => [
                    'type' => 'string',
                    'description' => 'The full RouterOS command (e.g., "/ip address add address=192.168.2.1/24 interface=ether2", "/ip hotspot user print").',
                ],
            ],
            'required' => ['command'],
        ];
    }

    public function execute(array $arguments, ?int $routerId = null): array
    {
        $this->validate($arguments, [
            'command' => 'required|string',
        ]);

        // FIXED: Validate command against security whitelist
        $this->validateCommand($arguments['command']);

        if (!$routerId) {
            throw new Exception("No router selected.");
        }

        $router = Router::find($routerId);
        if (!$router) {
            throw new Exception("Router not found.");
        }

        try {
            $adapter = RouterOsFactory::make($router);
            
            $cmdString = $arguments['command'];
            
            // ENHANCED: Parse command with query support
            $parsed = $this->parseCommand($cmdString);
            
            // Execute via Adapter
            if (method_exists($adapter, 'comm')) {
                $response = $adapter->comm($parsed['path'], $parsed['args'], $parsed['queries']);
            } elseif (method_exists($adapter, 'write')) {
                $response = $adapter->write($parsed['path'], $parsed['args']);
            } else {
                throw new Exception("Adapter does not support generic commands.");
            }

            return [
                'status' => 'success',
                'output' => $response,
                'command_path' => $parsed['path'],
                'message' => "Command executed successfully"
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'suggestion' => 'Check syntax. Use RouterOS API format: /menu/submenu/action =arg=value'
            ];
        }
    }

    /**
     * ENHANCED: Parse RouterOS command with query support
     * Supports: /path/to/menu, =attribute=value, ?query, .proplist
     */
    private function parseCommand(string $command): array
    {
        $command = trim($command);
        $command = ltrim($command, '/');
        
        // Split by whitespace but preserve quoted strings
        $parts = preg_split('/\s+(?=(?:[^"]*"[^"]*")*[^"]*$)/', $command);
        
        $path = "";
        $args = [];
        $queries = [];
        
        foreach ($parts as $part) {
            $part = trim($part, '"'); // Remove quotes
            
            if (str_starts_with($part, '?')) {
                // Query word (e.g., ?type=ether, ?name, ?#|)
                // RouterOS API query format
                $queries[] = $part;
            } elseif (str_starts_with($part, '.')) {
                // API attribute (e.g., .proplist=name,address, .tag=myTag)
                if (str_contains($part, '=')) {
                    $kv = explode('=', substr($part, 1), 2);
                    $args['.' . $kv[0]] = $kv[1] ?? '';
                }
            } elseif (str_contains($part, '=')) {
                // Regular attribute (e.g., address=192.168.1.1, name=test)
                $kv = explode('=', $part, 2);
                $args[$kv[0]] = $kv[1] ?? '';
            } else {
                // Path component (e.g., ip, address, add)
                $path .= "/" . $part;
            }
        }
        
        return [
            'path' => $path,
            'args' => $args,
            'queries' => $queries,
        ];
    }

    // FIXED: Security validation method
    private function validateCommand(string $command): void
    {
        // Normalize: convert CLI format to API format for checking
        $normalizedCmd = str_replace(' ', '/', ltrim($command, '/'));
        $normalizedCmd = preg_replace('/\/+/', '/', $normalizedCmd); // Remove double slashes
        $normalizedCmd = '/' . explode('=', $normalizedCmd)[0]; // Get only the path part
        
        // Check blocked patterns first (highest priority)
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (preg_match($pattern, $normalizedCmd)) {
                throw new Exception("Command blocked for security reasons. This action is not permitted via AI.");
            }
        }
        
        // Check if matches any allowed pattern
        $isAllowed = false;
        foreach (self::ALLOWED_COMMAND_PATTERNS as $pattern) {
            if (preg_match($pattern, $normalizedCmd)) {
                $isAllowed = true;
                break;
            }
        }
        
        if (!$isAllowed) {
            throw new Exception("Command not in allowed list. Contact admin to add this command to the whitelist: " . $normalizedCmd);
        }
    }
}
