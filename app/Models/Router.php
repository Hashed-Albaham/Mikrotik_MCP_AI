<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // In a real app, use an accessor/mutator to decrypt/encrypt $this->password
}
