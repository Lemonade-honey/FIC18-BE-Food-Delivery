<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    /**
     * Static response for server error
     */
    static function errorResponseServerError(): JsonResponse
    {
        return response()->json([
            'massage' => 'server erros'
        ], 504);
    }

    /**
     * Static Respone for data not found
     */
    static function errorResponseDataNotFound(): JsonResponse
    {
        return response()->json([
            'massage' => 'data not found'
        ], 404);
    }
}
