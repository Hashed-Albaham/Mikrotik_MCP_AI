<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. USERS - Modifying existing users table to add role
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['superadmin', 'admin', 'user'])->default('user')->after('password');
            }
        });

        // 2. DYNAMIC AI PROVIDERS
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('base_url', 255);
            $table->string('api_key', 255)->nullable();
            $table->string('model_identifier', 100);
            $table->integer('context_window')->default(128000);
            $table->boolean('is_active')->default(true);
            $table->boolean('supports_tools')->default(true);
            $table->timestamps();
        });

        // 3. MIKROTIK ROUTERS
        Schema::create('routers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('name', 255);
            $table->string('host', 255);
            $table->integer('port')->default(8728);
            $table->string('username', 255);
            $table->text('password'); // Encrypted
            $table->string('os_version', 20)->nullable();
            $table->enum('status', ['online', 'offline'])->default('offline');
            $table->timestamps();
        });

        // 4. MCP TOOLS REGISTRY
        Schema::create('mcp_tools', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description');
            $table->json('parameters_schema');
            $table->boolean('requires_confirmation')->default(false);
            $table->enum('min_role', ['user', 'admin'])->default('admin');
            $table->timestamps(); // Not explicitly requested but good practice
        });

        // 5. CHAT SESSIONS & MESSAGES
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Assuming user_id maps to users
            $table->foreignId('provider_id')->constrained('ai_providers');
            $table->string('title', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            // No updated_at requested in schema, but Laravel needs created_at. 
            // The schema says created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP. 
            // Schema builder timestamps() adds both. I'll stick to requested schema strictly where possible.
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->onDelete('cascade'); // Added cascade for safety
            $table->enum('role', ['user', 'assistant', 'system', 'tool']);
            $table->text('content')->nullable();
            $table->json('tool_calls')->nullable();
            $table->json('ui_widget')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        // 6. VOUCHER BATCHES
        Schema::create('voucher_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('router_id')->constrained('routers')->onDelete('cascade'); // Added cascade for safety
            $table->string('profile', 255)->nullable();
            $table->integer('count')->nullable();
            $table->string('pdf_path', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_batches');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_sessions');
        Schema::dropIfExists('mcp_tools');
        Schema::dropIfExists('routers');
        Schema::dropIfExists('ai_providers');
        
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
