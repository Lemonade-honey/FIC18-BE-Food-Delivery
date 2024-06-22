<?php

namespace App\Repositorys;

use App\Models\Restorant;
use App\Repositorys\Interface\RestorantRepository;

class RestorantRepositoryImpl implements RestorantRepository{
    public function getCurrentRestorantByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        $restorant = Restorant::where('user_id', $userId)->first();

        return $restorant;
    }
}