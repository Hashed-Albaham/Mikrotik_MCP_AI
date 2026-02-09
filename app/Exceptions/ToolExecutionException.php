<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

/**
 * FIXED: Custom exception for tool execution failures.
 * Provides context about which tool failed and why.
 */
class ToolExecutionException extends Exception
{
    protected string $toolName;
    protected array $arguments;

    public function __construct(string $toolName, string $message, array $arguments = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->toolName = $toolName;
        $this->arguments = $arguments;
        
        parent::__construct("[Tool: $toolName] $message", $code, $previous);
    }

    public function getToolName(): string
    {
        return $this->toolName;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
