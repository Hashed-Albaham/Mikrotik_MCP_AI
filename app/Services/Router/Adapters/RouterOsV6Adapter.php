<?php

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

    public function comm(string $path, array $args = []): array
    {
        // 1. Build Query
        $query = new Query($path);
        
        // 2. Add Arguments
        foreach ($args as $key => $value) {
            // Check if value is boolean true, send as flag?
            // RouterOS PHP Client usually handles key=value.
            // Some flags are passed as key only.
            // For now, assume key=value.
            $query->equal($key, $value);
        }

        // 3. Execute
        return $this->client->query($query)->read();
    }

    public function reboot(): void
    {
        $query = new Query('/system/reboot');
        $this->client->query($query)->read();
    }
}
