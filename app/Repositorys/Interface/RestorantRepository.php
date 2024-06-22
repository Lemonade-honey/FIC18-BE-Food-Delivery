<?php

namespace App\Repositorys\Interface;



interface RestorantRepository{

    /**
     * Get Current Restorant By User
     * 
     * mengambil data restorant berdasarkan user
     */
    function getCurrentRestorantByUser(int $userId): \Illuminate\Database\Eloquent\Collection;
}