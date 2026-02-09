<?php

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
