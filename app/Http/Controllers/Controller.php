<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Static response for server error
     */
    static function errorResponseServerError(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'massage' => 'server erros'
        ], 504);
    }
}
