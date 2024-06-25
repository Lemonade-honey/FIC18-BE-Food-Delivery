<?php

namespace App\Services\Interfaces;

use App\Models\Restorant;
use Illuminate\Http\Request;

interface RestorantService
{
    /**
     * Check Restorant User by Request
     * 
     * restorant yang terikat dengan user, jika ada return model, jika tidak null
     * 
     * @return ?Restorant
     */
    function restorantUserByRequest(Request $request): ?Restorant;

    /**
     * Delete Restorant Data
     */
    function deleteRestorant(Restorant $restorant): void;

    function restorantsByNameOrProducts(Request $request): ?\Illuminate\Contracts\Pagination\LengthAwarePaginator;
}