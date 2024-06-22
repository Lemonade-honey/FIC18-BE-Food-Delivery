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

    /**
     * Relasi yang terikat dengan Restorant Model
     */
    public function oneUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
