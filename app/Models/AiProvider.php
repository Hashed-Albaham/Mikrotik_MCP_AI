<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'api_key',
        'base_url',
        'model_identifier',
        'is_active',
        'supports_tools',
        'system_instruction',
        'config'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'supports_tools' => 'boolean',
        'config' => 'array',
    ];
}
