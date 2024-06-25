<?php

namespace App\Services;

use App\Models\Restorant;
use App\Services\Interfaces\RestorantService;

class RestorantServiceImpl implements RestorantService
{

    private $restorantRepo;

    public function __construct(\App\Repositorys\Interfaces\RestorantRepository $restorantRepository)
    {
        $this->restorantRepo = $restorantRepository;
    }

    public function restorantUserByRequest(\Illuminate\Http\Request $request): ?Restorant
    {
        $user = $request->user();

        $restorantUser = $this->restorantRepo->getCurrentRestorantByUser($user->id);

        return $restorantUser;
    }

    public function restorantsByNameOrProducts(\Illuminate\Http\Request $request): \Illuminate\Contracts\Pagination\LengthAwarePaginator|null
    {
        $restorants = $this->restorantRepo->getRestorantsOrProductsNyNameWithPaginate($request->search, 10);

        return $restorants;
    }

    public function deleteRestorant(Restorant $restorant): void
    {
        // delete all restorant with product relate
        // dikarenakan sudah men-set untuk relasi pada database schema dan model
        $restorant->delete();
    }
}