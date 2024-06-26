<?php

namespace App\Repositorys\Interfaces;

use App\Models\Restorant;

interface RestorantRepository{

    /**
     * Get Current Restorant By User
     * 
     * mengambil data restorant berdasarkan user
     */
    function getCurrentRestorantByUser(int $userId): ?Restorant;

    /**
     * Get Current Restorant with Products
     * 
     * mengambil data restorant berdasrkan user dengan product restorantnya
     */
    function getCurrentRestorantWithProducts(int $userId): ?Restorant;

    /**
     * Get Restoran By Id
     * 
     * mendapatkan restoran dengan id
     */
    function getRestorantById(int $restorantId): ?Restorant;

    /**
     * Get Restorant By Id With Products
     * 
     * mendapatkan restoran dengan id beserta dengan productnya
     */
    function getRestorantByIdWithProducts(int $restorantId): ?Restorant;

    /**
     * Get Restorants with target key
     * 
     * mengambil restoran berdasarkan key/target value nama yang akan dicari
     * menggunakan paginate
     */
    function getRestorantsOrProductsNyNameWithPaginate(?string $name, int $paginate = 15): ?\Illuminate\Contracts\Pagination\LengthAwarePaginator;
}