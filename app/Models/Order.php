<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Ramsey\Uuid\Uuid::uuid7();
            }
        });
    }

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

    /**
     * Relation
     */

    public function restorant(): HasOne
    {
        return $this->hasOne(Restorant::class, 'id', 'restorant_id');
    }
}
