<?php

namespace App\Http\Controllers;

use App\Models\Restorant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestorantController extends Controller
{

    private $restorantService;

    public function __construct(\App\Services\Interfaces\RestorantService $restorantService)
    {
        $this->restorantService = $restorantService;
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
}
