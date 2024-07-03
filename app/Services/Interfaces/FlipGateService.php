<?php

namespace App\Services\Interfaces;

interface FlipGateService
{
    /**
     * Get Balance
     * 
     * mendapatkan balance pada dashboard Flip
     */
    function getBalance();


    /**
     * Create Bill Payment
     * 
     * membuat bill payment untuk pembayaran
     */
    function createBill(string $title, int $amount);
}