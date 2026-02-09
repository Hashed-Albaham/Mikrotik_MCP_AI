<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false; // Only created_at is in schema, but we can manage manually if needed or rely on default

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
