<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AiProvider;

class GeminiSeeder extends Seeder
{
    public function run()
    {
        AiProvider::create([
            'name' => 'Google Gemini',
            'base_url' => 'https://generativelanguage.googleapis.com/v1beta/openai/',
            'api_key' => 'AIzaSyA5Xe-dyGpPyzfc3ujFAQun-X6Huya7r8E',
            'model_identifier' => 'gemini-1.5-flash',
            'is_active' => true,
        ]);
        
        $this->command->info('Google Gemini Provider Added Successfully!');
    }
}
