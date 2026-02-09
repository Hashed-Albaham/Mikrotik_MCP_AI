<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Casts\EncryptedCast;

class Router extends Model
{
    use HasFactory;

    // 🛡️ SEC: Explicit $fillable prevents mass assignment vulnerabilities [source:1]
    protected $fillable = ['user_id', 'name', 'host', 'port', 'username', 'password', 'os_version'];

    // FIXED: Hide sensitive fields from JSON serialization
    protected $hidden = ['password'];

    // FIXED: Automatically encrypt/decrypt password field
    protected $casts = [
        'password' => EncryptedCast::class,
    ];
}
