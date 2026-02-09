<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false; // Schema has created_at only

    protected $casts = [
        'tool_calls' => 'array',
        'ui_widget' => 'array',
        'created_at' => 'datetime',
    ];
}
