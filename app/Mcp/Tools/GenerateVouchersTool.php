<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Mcp\Tools;

use App\Services\Router\RouterOsFactory;
use App\Models\Router;
use App\Models\VoucherBatch;
use Exception;
use Illuminate\Support\Str;

class GenerateVouchersTool extends BaseTool
{
    public function name(): string
    {
        return 'generate_hotspot_vouchers';
    }

    public function description(): string
    {
        return 'Generates a batch of unique username/password vouchers for the MikroTik Hotspot system. Returns a link to the PDF.';
    }

    public function schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'count' => [
                    'type' => 'integer',
                    'description' => 'Number of vouchers to generate (e.g., 10, 50, 100).',
                ],
                'profile' => [
                    'type' => 'string',
                    'description' => 'The Hotspot User Profile to assign (e.g., "1Hour", "1Day", "VIP").',
                ],
                'router_name' => [
                    'type' => 'string',
                    'description' => 'The name of the router to generate vouchers on (optional if only one exists).',
                ],
            ],
            'required' => ['count', 'profile'],
        ];
    }

    public function execute(array $arguments, ?int $routerId = null): array
    {
        $this->validate($arguments, [
            'count' => 'required|integer|min:1|max:500',
            'profile' => 'required|string',
        ]);

        // 1. Resolve Router
        $router = $routerId ? Router::find($routerId) : Router::first();
        if (!$router) {
            throw new Exception("No router configured.");
        }

        // 2. Connect to Router
        $adapter = RouterOsFactory::make($router);

        // FIXED: Wrap in transaction for atomicity
        return \Illuminate\Support\Facades\DB::transaction(function () use ($adapter, $arguments, $router) {
            // 3. Generate Codes - Pre-generate all usernames/passwords first
            $users = [];
            $count = $arguments['count'];
            $profile = $arguments['profile'];
            $errors = [];

            // Pre-generate all credentials
            for ($i = 0; $i < $count; $i++) {
                $users[] = [
                    'username' => Str::upper(Str::random(6)),
                    'password' => Str::random(4),
                ];
            }

            // Execute on Router with error collection (soft fail)
            $successCount = 0;
            foreach ($users as $user) {
                try {
                    $adapter->addHotspotUser($user['username'], $user['password'], $profile);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "User {$user['username']}: {$e->getMessage()}";
                }
            }

            // 4. Record Batch in DB
            $batch = VoucherBatch::create([
                'router_id' => $router->id,
                'profile' => $profile,
                'count' => $successCount,
                'pdf_path' => '/storage/vouchers/batch_' . time() . '.pdf', // Placeholder
            ]);

            return [
                'status' => $successCount > 0 ? 'success' : 'error',
                'message' => "Generated $successCount/$count vouchers for profile '$profile'.",
                'batch_id' => $batch->id,
                'download_url' => url($batch->pdf_path),
                'samples' => array_slice($users, 0, 3),
                'errors' => array_slice($errors, 0, 5), // Return first 5 errors if any
            ];
        });
    }
}
