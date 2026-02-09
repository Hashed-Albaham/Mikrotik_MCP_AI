<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class McpTool extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'parameters_schema' => 'array',
        'requires_confirmation' => 'boolean',
    ];
}
