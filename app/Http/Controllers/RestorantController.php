<?php

namespace App\Http\Controllers;

use App\Models\Restorant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestorantController extends Controller
{

    private $restorantService;

    private $restorantRepo;

    public function __construct(\App\Services\Interfaces\RestorantService $restorantService, \App\Repositorys\Interfaces\RestorantRepository $restorantRepository)
    {
        $this->restorantService = $restorantService;
        $this->restorantRepo = $restorantRepository;
    }

    public function restorants(Request $request): JsonResponse
    {
        $restorantsPaginate = $this->restorantService->restorantsByNameOrProducts($request);

        return response()->json([
            $restorantsPaginate
        ]);
    }

    public function restorant($id): JsonResponse
    {
        $restorant = $this->restorantService->restorantWithProductByRestorantId($id);

        if (! $restorant)
        {
            return self::errorResponseDataNotFound();
        }

        return response()->json([
            'data' => $restorant 
        ]);
    }

    public function restorantProduct($id, $productId): JsonResponse
    {
        $restorant = $this->restorantRepo->getRestorantById($id);

        if(! $restorant)
        {
            return self::errorResponseDataNotFound();
        }

        $product = \App\Models\Product::find($productId);

        if(! $product)
        {
            return self::errorResponseDataNotFound();
        }

        return response()->json([
            'data' => $product
        ]);
    }
}
