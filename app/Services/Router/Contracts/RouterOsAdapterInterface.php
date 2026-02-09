<?php

namespace App\Services\Router\Contracts;

interface RouterOsAdapterInterface
{
    /**
     * Connect to the router.
     */
    public function connect(): void;

    /**
     * Get system resource/info to verify connection and version.
     */
    public function getSystemResource(): array;

    /**
     * Execute a raw command on the router.
     *
     * @param string $command e.g., "/ip/address/print"
     * @param array $query Wrapper for queries (optional)
     */
    public function execute(string $command, array $query = []): array;

    /**
     * Create a Hotspot User.
     */
    public function addHotspotUser(string $name, string $password, string $profile, ?string $server = 'all'): array;

    /**
     * Generic communication method.
     * @param string $path e.g. "/ip/address/add"
     * @param array $args e.g. ["address" => "192.168.1.1/24", "interface" => "ether1"]
     */
    public function comm(string $path, array $args = []): array;

    /**
     * Reboot the router.
     */
    public function reboot(): void;
}
