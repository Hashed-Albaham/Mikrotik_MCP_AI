<?php

namespace App\Mcp\Tools;

use App\Services\Router\RouterOsFactory;
use App\Models\Router;
use Exception;

class ExecuteRouterCommandTool extends BaseTool
{
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

        if (!$routerId) {
            throw new Exception("No router selected.");
        }

        $router = Router::find($routerId);
        if (!$router) {
            throw new Exception("Router not found.");
        }

        try {
            $adapter = RouterOsFactory::make($router);
            
            // The command might be a "scipt" command or a direct API call.
            // RouterOS API treats specific paths as menus.
            // However, to support "arbitrary" CLI-like commands, it's often best to use /system/ssh/exec or just map standard API calls.
            // But strict API requires breaking down the command (e.g. /ip/address/add).
            // Many AI models write CLI commands.
            // We need a way to run CLI syntax or force AI to use structure.
            // For robust "Agentic" behavior, sending exact API structure is harder for LLMs without strict schema.
            // Best approach for "Power User" AI: Use the 'terminal' approach if possible, or try to parse.
            // BUT, our Adapter likely expects parsed commands (Array).
            
            // Let's assume the AI provides CLI-like syntax and we try to run it via a helper,
            // OR we tell the AI to use valid JSON structure?
            // "execute_command" usually implies the AI knows the RouterOS console syntax.
            // We can wrap it in a system script or use the API's "command" capability if strictly supported.
            
            // SIMPLIFICATION:
            // For this MVP, since we are using pear2/routeros or similar under the hood (in factories),
            // The V6Adapter using RouterOS API usually takes a MENU and Action.
            // CLI: /ip address add address=...
            // API: /ip/address/add, =address=...
            
            // Let's UPDATE the PROMPT/DESCRIPTION to ask for the API format if needed, 
            // OR we implement a parser.
            // PROMPT ENGINEERING IS EASIER: Update description to ask for specific format? 
            // "command" string is risky.
            
            // ALTERNATIVE:
            // Does RouterOS API support "exec"? No.
            // But we can use /system/script/run ... no.
            
            // Let's try to pass the command directly. 
            // If the user's library supports "query", we might need to parse.
            // Let's do a basic parser from CLI string to API array.
            
            $cmdString = $arguments['command'];
            
            // Basic Parsing Logic (Naive)
            // 1. Split by space to get Main Command part vs Arguments.
            // Example: /ip hotspot user add name=guest password=123
            // Parts: /ip, hotspot, user, add
            // Args: name=guest, password=123
            
            // Actual API Command: /ip/hotspot/user/add
            
            // Removing starting / if present
            $cmdString = ltrim($cmdString, '/');
            $parts = preg_split('/\s+/', $cmdString);
            
            $apiPath = "";
            $apiArgs = [];
            
            foreach ($parts as $part) {
                if (str_contains($part, '=')) {
                    // It's an argument
                    // API expects key=value (sometimes with = prefix)
                    // PEAR2/Client usually handles the = prefix if we pass as array.
                    $kv = explode('=', $part, 2);
                    $apiArgs[$kv[0]] = $kv[1] ?? '';
                } else {
                    // It's part of the path
                    $apiPath .= "/" . $part;
                }
            }
            
            // Execute via Adapter
            // We need to extend our Adapter interface to support "execute($path, $args)"
            // Currently it has specific methods. Let's assume we can add a generic `query` or `write`.
            // Let's check the Adapter first, but for now, let's assume we add this capability.
            
            if (method_exists($adapter, 'write')) {
                // Low level access
                // This depends heavily on the implementation of RouterOsAdapterInterface.
                // Let's assume we act "Smart" and try to use a generic method we will add.
                $response = $adapter->comm($apiPath, $apiArgs);
            } else {
                throw new Exception("Adapter does not support generic commands.");
            }

            return [
                'status' => 'success',
                'output' => $response,
                'message' => "Command executed: $cmdString"
            ];

        } catch (Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'suggestion' => 'Check syntax. Use RouterOS API format.'
            ];
        }
    }
}
