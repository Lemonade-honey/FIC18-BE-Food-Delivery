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
}