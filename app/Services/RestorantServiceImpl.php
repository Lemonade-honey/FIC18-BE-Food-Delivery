<?php

namespace App\Services;

use App\Models\Restorant;
use App\Services\Interfaces\RestorantService;

class RestorantServiceImpl implements RestorantService
{

    const FILE_PATH_PHOTO_RESTORANT = "restorant";

    private $fileService;

    private $restorantRepo;

    public function __construct(\App\Repositorys\Interfaces\RestorantRepository $restorantRepository)
    {
        $this->restorantRepo = $restorantRepository;
        $this->fileService = new FileServiceImpl;
    }

    public function restorantById(int $id): Restorant|null
    {
        $restorant = $this->restorantRepo->getRestorantById($id);

        return $restorant;
    }

    public function restorantUserByRequest(\Illuminate\Http\Request $request): ?Restorant
    {
        $user = $request->user();

        $restorantUser = $this->restorantRepo->getCurrentRestorantByUser($user->id);

        return $restorantUser;
    }

    public function createRestorantByRequest(\Illuminate\Http\Request $request): Restorant
    {
        $user = $request->user();

        $restorant = Restorant::create([
            'user_id' => $request->user()->id,
            'name' => $request->input('name'),
            'tags' => $request->input('tags') ?? [],
            'address' => $request->input('address'),
            'latlong' => $request->input('latlong'),
            'image' => $this->fileService->saveFileToStoragePath($request->file('image'), self::FILE_PATH_PHOTO_RESTORANT . "/$user->id/")
        ]);

        return $restorant;
    }

    public function updateRestorantDataByRequest(\Illuminate\Http\Request $request, Restorant $restorant): Restorant
    {
        $user = $request->user();

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

        if($request->has('image'))
        {
            $restorant->address = $this->fileService->saveFileToStoragePath($request->file('image'), self::FILE_PATH_PHOTO_RESTORANT . "/$user->id/");
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