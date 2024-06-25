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

    public function getRestorantsOrProductsNyNameWithPaginate(?string $name, int $paginate = 15): ?\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $restorants = Restorant::with('products')
        ->when($name != '', function($query) use($name){
            $query->where('name', 'like', '%' . $name . '%')
            ->orWhereHas('products', function($query) use($name){
                $query->where('name', 'like', '%' . $name . '%');
            });
        })
        ->paginate($paginate);

        return $restorants;
    }
}