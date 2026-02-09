<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\McpTool;
use App\Mcp\Tools\GenerateVouchersTool;

class SyncMcpTools extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mcp:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan Codebase for MCP Tool classes and update the database registry.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Scanning for MCP Tools...');

        // In a real app, we would scan the directory using Reflection.
        // For now, we will manually register the known tools.
        $tools = [
            new GenerateVouchersTool(),
            // Add new tools here
        ];

        foreach ($tools as $tool) {
            $this->info("Syncing: " . $tool->name());

            McpTool::updateOrCreate(
                ['name' => $tool->name()],
                [
                    'description' => $tool->description(),
                    'parameters_schema' => $tool->schema(),
                    'requires_confirmation' => false, // Default
                    'min_role' => 'admin', // Default
                ]
            );
        }

        $this->info('MCP Tools Synced Successfully!');
    }
}
