<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Services\Router\Adapters;

use App\Services\Router\Contracts\RouterOsAdapterInterface;
use RouterOS\Client;
use RouterOS\Config;
use RouterOS\Query;

class RouterOsV6Adapter implements RouterOsAdapterInterface
{
    protected Client $client;

    public function __construct(
        protected string $host,
        protected string $username,
        protected string $password,
        protected int $port = 8728
    ) {}

    public function connect(): void
    {
        $config = (new Config())
            ->set('host', $this->host)
            ->set('port', $this->port)
            ->set('user', $this->username)
            ->set('pass', $this->password);

        $this->client = new Client($config);
    }

    public function getSystemResource(): array
    {
        // RouterOS v6/v7 compatible command
        $query = new Query('/system/resource/print');
        return $this->client->query($query)->read();
    }

    public function execute(string $command, array $query = []): array
    {
        $q = new Query($command);
        foreach ($query as $key => $value) {
            $q->where($key, $value);
        }
        return $this->client->query($q)->read();
    }

    public function addHotspotUser(string $name, string $password, string $profile, ?string $server = 'all'): array
    {
        $query = (new Query('/ip/hotspot/user/add'))
            ->equal('name', $name)
            ->equal('password', $password)
            ->equal('profile', $profile)
            ->equal('server', $server);

        return $this->client->query($query)->read();
    }

    // 🏛️ ARCH: Updated comm() to support query filtering [source:1]
    public function comm(string $path, array $args = [], array $queries = []): array
    {
        // 1. Build Query
        $query = new Query($path);
        
        // 2. Add Arguments
        foreach ($args as $key => $value) {
            $query->equal($key, $value);
        }
        
        // 3. Add Query filters (e.g., ?type=ether)
        foreach ($queries as $q) {
            // Parse ?key=value or ?key format
            if (str_starts_with($q, '?')) {
                $q = substr($q, 1);
                if (str_contains($q, '=')) {
                    [$key, $value] = explode('=', $q, 2);
                    $query->where($key, $value);
                } else {
                    $query->where($q);
                }
            }
        }

        // 4. Execute
        return $this->client->query($query)->read();
    }

    // 🛡️ SEC: Protected reboot - must be explicitly authorized [source:3]
    public function reboot(): void
    {
        // Disabled by default for security - router reboot is destructive
        // To enable: remove this exception and implement proper authorization
        throw new \Exception('Reboot is disabled for security. Contact administrator.');
        
        // Original code (kept for reference):
        // $query = new Query('/system/reboot');
        // $this->client->query($query)->read();
    }
}
