<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected $hidden = [
        'id',
        'user_id'
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'orders' => 'array'
        ];
    }
}
