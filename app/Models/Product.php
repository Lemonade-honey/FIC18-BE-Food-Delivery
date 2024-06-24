<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * Relation yang terikat
     */

    public function restorant()
    {
        return $this->belongsTo(Restorant::class, 'id', 'restorant_id');
    }
}
