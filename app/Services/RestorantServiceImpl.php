<?php

namespace App\Services;

use App\Models\Restorant;
use App\Services\Interfaces\RestorantService;

class RestorantServiceImpl implements RestorantService
{

    const FILE_PATH_PHOTO_RESTORANT = "restorant";

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

    public function updateRestorantDataByRequest(\Illuminate\Http\Request $request, Restorant $restorant): Restorant
    {
        if($request->has('name'))
        {
            $restorant->name = $request->input('name');
        }

        if($request->has('address'))
        {
            $restorant->address = $request->input('address');
        }
        
        if($request->has('latlong'))
        {
            $restorant->latlong = $request->input('latlong');
        }

        if($request->has('photo'))
        {
            $fileService = new \App\Services\FileServiceImpl;

            $restorant->address = $fileService->saveFileToStoragePath($request->file('photo'), self::FILE_PATH_PHOTO_RESTORANT);
        }

        $restorant->save();

        return $restorant;
    }

    public function restorantWithProductByRestorantId(int $restorantId): Restorant|null
    {
        $restorant = $this->restorantRepo->getRestorantByIdWithProducts($restorantId);


        return $restorant;
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