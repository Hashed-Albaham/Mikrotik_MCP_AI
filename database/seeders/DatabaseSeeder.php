<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\AiProvider;
use App\Models\Router;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Super Admin
        if (!User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]);
        }

        // 2. Create Default AI Provider (Ollama Local - Free/Safe default)
        if (!AiProvider::where('name', 'Local Ollama')->exists()) {
            AiProvider::create([
                'name' => 'Local Ollama',
                'base_url' => 'http://localhost:11434/v1',
                'api_key' => 'ollama', // Usually ignored by Ollama
                'model_identifier' => 'mistral', // Common default
                'context_window' => 4096,
                'is_active' => true,
                'supports_tools' => false, // Ollama tool support varies, safer false for MVP
            ]);
        }

        // 3. Create Placeholder Router (Example)
        if (!Router::where('host', '192.168.88.1')->exists()) {
            Router::create([
                'user_id' => 1, // Assigned to Admin
                'name' => 'Main Office Gateway',
                'host' => '192.168.88.1',
                'username' => 'admin',
                'password' => 'admin', // In real app, this should be encrypted
                'status' => 'offline',
            ]);
        }
    }
}
