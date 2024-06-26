<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restorant extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array'
        ];
    }

    /**
     * Relasi yang terikat dengan Restorant Model
     */
    public function oneUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'restorant_id', 'id');
    }
}
