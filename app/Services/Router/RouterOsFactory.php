<?php

namespace App\Services\Router;

use App\Models\Router;
use App\Services\Router\Contracts\RouterOsAdapterInterface;
use App\Services\Router\Adapters\RouterOsV6Adapter;
use App\Services\Router\Adapters\RouterOsV7Adapter;
use Exception;

class RouterOsFactory
{
    /**
     * Create a connection to the router and return the appropriate adapter.
     */
    public static function make(Router $router): RouterOsAdapterInterface
    {
        // 1. Initial Connection (Default to V6 behavior to probe)
        // Decrypt password here in a real app. Assuming plain text for MVP as per prompt but schema said encrypted.
        // We will assume the accessor on the model handles decryption or we do it here.
        $password = $router->password; 
        
        // We use the V6 Adapter to probe because the login protocol allows version detection usually,
        // or we just run /system/resource/print which works on both.
        $adapter = new RouterOsV6Adapter(
            $router->host,
            $router->username,
            $password,
            $router->port
        );

        $adapter->connect();

        // 2. Auto-Detect / Switch if needed
        if ($router->os_version === null) {
            $resource = $adapter->getSystemResource();
            $version = $resource[0]['version'] ?? '6.x';
            
            // Save detected version to DB
            $router->update(['os_version' => $version]);
            
            if (str_starts_with($version, '7')) {
                // Switch to V7 Adapter
                $adapter = new RouterOsV7Adapter(
                    $router->host,
                    $router->username,
                    $password,
                    $router->port
                );
                $adapter->connect();
            }
        } elseif (str_starts_with($router->os_version, '7')) {
             $adapter = new RouterOsV7Adapter(
                $router->host,
                $router->username,
                $password,
                $router->port
            );
            $adapter->connect();
        }

        return $adapter;
    }
}
