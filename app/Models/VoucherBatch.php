<?php

// 🛡️ SEC: Strict types prevent type confusion attacks [source:2]
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherBatch extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    public $timestamps = false; // Only created_at exists in DB, handled manually or by DB default
}
