<?php

namespace App\Services\Interfaces;

use App\Models\Restorant;
use Illuminate\Http\Request;

interface RestorantService
{
    /**
     * Get Restorant By restorant id
     * 
     * mendaapatkan data restorant berdasarkan idnya
     */
    public function restorantById(int $id): ?Restorant;


    /**
     * Check Restorant User by Request
     * 
     * restorant yang terikat dengan user, jika ada return model, jika tidak null
     * 
     * @return ?Restorant
     */
    function restorantUserByRequest(Request $request): ?Restorant;

    /**
     * Create new Restorant data
     * 
     * membuat restorant baru
     */
    function createRestorantByRequest(Request $request): Restorant;

    /**
     * Update restorant data
     */
    function updateRestorantDataByRequest(Request $request, Restorant $restorant): Restorant;

    /**
     * Delete Restorant Data
     */
    function deleteRestorant(Restorant $restorant): void;

    /**
     * Get Restorant with product selected
     * 
     * mendapatkan data restorant dan productnya berdasarkan nama
     */
    function restorantWithProductByRestorantId(int $restorantId): ?Restorant;

    /**
     * Get Restorants Name Or Products
     * 
     * mendapatkan data restorant yang menandung nama yang sesuai dengan
     * permintaan request
     */
    function restorantsByNameOrProducts(Request $request): ?\Illuminate\Contracts\Pagination\LengthAwarePaginator;
}