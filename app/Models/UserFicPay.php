<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFicPay extends Model
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $guarded = [
        'id'
    ];

    /**
     * Relasai
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
