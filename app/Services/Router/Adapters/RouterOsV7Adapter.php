<?php

namespace App\Services\Router\Adapters;

use RouterOS\Query;

class RouterOsV7Adapter extends RouterOsV6Adapter
{
    // RouterOS v7 syntax is largely compatible with v6 for basic tasks, 
    // but differs in areas like queues, routing (BGP/OSPF), and wireless (WiFiWave2).
    // We override specific methods here where v7 syntax differs.

    /**
     * Example override: Creating a user might use different validation or properties in v7
     */
    public function addHotspotUser(string $name, string $password, string $profile, ?string $server = 'all'): array
    {
        // For standard Hotspot, v7 is consistent with v6. 
        // We include this method to demonstrate the Strategy Pattern explicitly.
        // If v7 required a different "limit-uptime" format, we would handle it here.
        
        return parent::addHotspotUser($name, $password, $profile, $server);
    }
}
