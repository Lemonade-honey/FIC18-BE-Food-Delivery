<?php

namespace App\Services\Interfaces;

use App\Models\Order;
use App\Models\Restorant;
use Illuminate\Http\Request;

interface OrderService
{

    /**
     * Break Request
     * 
     * memecah request ke data yang telah disesuaikan
     */
    function breakProductsOrderRequestIntoProductsIds(\Illuminate\Support\Collection $productsOrder): array;

    /**
     * Merge Request Order With Relate Product Restorant
     * 
     * menggabungkan data order request dengan product restorant yang terikat
     * dengan request
     */
    function mergeCurrentProductsToProducsOrder(Request $request, \Illuminate\Support\Collection $productsRestorant): array;

    /**
     * Calculate Total Price Of Product Order
     */
    function getTotalProductsOrderPrice(Request $request, \Illuminate\Support\Collection $productsRestorant): int;

    /**
     * Get Detail
     * 
     * memberikan detail order price yang ada
     */
    function getDetailOrderPrice(int $totalProductsOrderPrice): array;

    /**
     * Create New Order
     * 
     * membuat order baru dari user, disini akan dikirim ke user dan ke restorant
     * yang bersangkutan
     */
    function createOrderUserByRequest(Request $request, Restorant $restorant): Order;
}