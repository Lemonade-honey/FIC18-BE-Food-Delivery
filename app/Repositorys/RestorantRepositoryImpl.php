<?php

namespace App\Repositorys;

use App\Models\Restorant;
use App\Repositorys\Interfaces\RestorantRepository;

class RestorantRepositoryImpl implements RestorantRepository{
    public function getCurrentRestorantByUser(int $userId): ?Restorant
    {
        $restorant = Restorant::where('user_id', $userId)->first();

        return $restorant;
    }

    public function getCurrentRestorantWithProducts(int $userId): ?Restorant
    {
        $restorant = Restorant::with('products')->where('user_id', $userId)->first();

        return $restorant;
    }
}