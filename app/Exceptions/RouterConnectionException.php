<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Exceptions;

use Exception;

/**
 * FIXED: Custom exception for router connection failures.
 * Provides details about the connection attempt.
 */
class RouterConnectionException extends Exception
{
    protected string $host;
    protected int $port;
    protected string $connectionMethod;

    public function __construct(
        string $host, 
        int $port, 
        string $message, 
        string $connectionMethod = 'API',
        int $code = 0, 
        ?\Throwable $previous = null
    ) {
        $this->host = $host;
        $this->port = $port;
        $this->connectionMethod = $connectionMethod;
        
        parent::__construct("[Router: $host:$port via $connectionMethod] $message", $code, $previous);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getConnectionMethod(): string
    {
        return $this->connectionMethod;
    }
}
