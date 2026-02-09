<?php

namespace App\Mcp\Tools;

use Illuminate\Support\Facades\Validator;
use Exception;

abstract class BaseTool
{
    /**
     * The unique name of the tool (e.g., 'add_hotspot_user').
     */
    abstract public function name(): string;

    /**
     * A clear description for the AI.
     */
    abstract public function description(): string;

    /**
     * The JSON Schema for the parameters.
     */
    abstract public function schema(): array;

    /**
     * Execute the tool logic.
     *
     * @param array $arguments The arguments provided by the AI.
     * @param int|null $routerId Context: Which router are we acting on?
     * @return array The result to send back to the AI.
     */
    abstract public function execute(array $arguments, ?int $routerId = null): array;

    /**
     * Helper to validate arguments against rules.
     */
    protected function validate(array $arguments, array $rules): void
    {
        $validator = Validator::make($arguments, $rules);

        if ($validator->fails()) {
            throw new Exception("Tool Validation Failed: " . implode(', ', $validator->errors()->all()));
        }
    }
}
