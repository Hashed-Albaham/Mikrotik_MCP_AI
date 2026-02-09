<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * 🧪 QA: Unit tests for ExecuteRouterCommandTool whitelist security
 * Tests command validation logic to ensure security rules are enforced
 */
class ExecuteRouterCommandToolTest extends TestCase
{
    private $tool;
    private $validateMethod;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create instance and make private method accessible
        $this->tool = new \App\Mcp\Tools\ExecuteRouterCommandTool();
        
        $reflection = new ReflectionClass($this->tool);
        $this->validateMethod = $reflection->getMethod('validateCommand');
        $this->validateMethod->setAccessible(true);
    }

    /**
     * 🛡️ SEC: Test that allowed commands pass validation
     */
    public function test_allowed_commands_pass_validation(): void
    {
        $allowedCommands = [
            '/ip/address/print',
            '/ip/hotspot/user/add name=test',
            '/interface/print',
            '/system/resource/print',
            '/queue/simple/print',
            '/tool/ping address=8.8.8.8',
        ];

        foreach ($allowedCommands as $command) {
            $exception = null;
            try {
                $this->validateMethod->invoke($this->tool, $command);
            } catch (\Exception $e) {
                $exception = $e;
            }
            
            $this->assertNull($exception, "Command should be allowed: $command");
        }
    }

    /**
     * 🛡️ SEC: Test that blocked patterns are rejected
     */
    public function test_blocked_patterns_are_rejected(): void
    {
        $blockedCommands = [
            '/export',
            '/import',
            '/system/script/add',
            '/system/reboot',
            '/system/shutdown',
            '/file/print',
            '/system/backup/save',
        ];

        foreach ($blockedCommands as $command) {
            $this->expectException(\Exception::class);
            $this->validateMethod->invoke($this->tool, $command);
        }
    }

    /**
     * 🛡️ SEC: Test that commands not in whitelist are rejected
     */
    public function test_unlisted_commands_are_rejected(): void
    {
        $unlistedCommands = [
            '/some/random/command',
            '/unknown/path',
        ];

        foreach ($unlistedCommands as $command) {
            $this->expectException(\Exception::class);
            $this->validateMethod->invoke($this->tool, $command);
        }
    }

    /**
     * 🛡️ SEC: Test that password-related commands are blocked
     */
    public function test_password_commands_blocked(): void
    {
        $this->expectException(\Exception::class);
        $this->validateMethod->invoke($this->tool, '/user/set password=test123');
    }

    /**
     * 🧪 QA: Test parseCommand correctly splits path and args
     */
    public function test_parse_command_splits_correctly(): void
    {
        $reflection = new ReflectionClass($this->tool);
        $parseMethod = $reflection->getMethod('parseCommand');
        $parseMethod->setAccessible(true);

        $result = $parseMethod->invoke($this->tool, '/ip/address/add address=192.168.1.1/24 interface=ether1');
        
        $this->assertEquals('/ip/address/add', $result['path']);
        $this->assertEquals('192.168.1.1/24', $result['args']['address']);
        $this->assertEquals('ether1', $result['args']['interface']);
    }

    /**
     * 🧪 QA: Test parseCommand handles queries correctly
     */
    public function test_parse_command_handles_queries(): void
    {
        $reflection = new ReflectionClass($this->tool);
        $parseMethod = $reflection->getMethod('parseCommand');
        $parseMethod->setAccessible(true);

        $result = $parseMethod->invoke($this->tool, '/interface/print ?type=ether');
        
        $this->assertEquals('/interface/print', $result['path']);
        $this->assertContains('?type=ether', $result['queries']);
    }
}
