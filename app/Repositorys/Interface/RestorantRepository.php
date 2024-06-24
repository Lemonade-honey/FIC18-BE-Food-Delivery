<?php

namespace App\Repositorys\Interface;

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
}