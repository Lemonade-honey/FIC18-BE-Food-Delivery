<?php

namespace App\Http\Controllers;

use App\Models\Restorant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RestorantController extends Controller
{

    const FILE_PATH_PHOTO_RESTORANT = "restorant";

    private $fileService;

    private $restorantRepo;

    public function __construct(\App\Repositorys\Interface\RestorantRepository $restorantRepository, \App\Services\Interface\FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->restorantRepo = $restorantRepository;
    }

    public function currentRestorant(Request $request): JsonResponse
    {
        $userRestorant = $this->restorantRepo->getCurrentRestorantByUser($request->user()->id);

        if(! $userRestorant)
        {
            return response()->json([
                'errors' => [
                    'massage' => 'restorant user not found'
                ]
            ], 404);
        }

        return response()->json([
            'data' => [
                $userRestorant
            ],
            'errors' => []
        ]);
    }

    /**
     * Create Restorant By User
     * 
     * untuk saat ini hanya dapat membuat 1 restoran/user dan jika user terikat dengan data driver
     * maka `data driver` sebelumnya akan `terhapus`
     */
    public function createRestorant(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'max:255', 'min:3'],
            'address' => 'required',
            'latlong' => 'required',
            'photo' => ['required', 'image', 'max:10280']
        ], [
            'photo.size' => 'photo maksimal berukuran 10MB'
        ]);

        try {
            $userRestorant = $this->restorantRepo->getCurrentRestorantByUser($request->user()->id);
            
            if($userRestorant)
            {
                // conflic status
                return response()->json([
                    'errors' => [
                        'massage' => 'restorant sudah terdata, silahkan hapus data restorant sebelumnya'
                    ]
                ], 409);
            }

            $savedFilePath = $this->fileService->saveFileToStoragePath($request->file('photo'), self::FILE_PATH_PHOTO_RESTORANT);

            $restorant = Restorant::create([
                'user_id' => $request->user()->id,
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'latlong' => $request->input('latlong'),
                'photo' => $savedFilePath
            ]);

            return response()->json([
                'data' => $restorant
            ], 201);
        }
        
        catch (Throwable $th) {
            Log::critical('user gagal update role. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return response()->json([
                'errors' => [
                    'massage' => 'server error'
                ]
            ], 504);
        }
    }

}
