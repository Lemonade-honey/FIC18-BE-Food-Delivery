<?php

namespace App\Services\Interfaces;

use App\Models\Product;
use Illuminate\Http\Request;

interface ProductService
{

    /**
     * Product Restorant 
     */
    function productByIdAndRestorantId(int $productId, int $restorantId): ?Product;

    /**
     * Craete Product Data
     * 
     * membuat product baru restorant
     */
    function createProductDataByRequest(Request $request, \App\Models\Restorant $restorant): Product;

    /**
     * Update Product data
     * 
     * masukan data yang perlu diupdate saja
     */
    function updateProductDataByRequest(Product $product, Request $request): Product;

    /**
     * Delete Product Restorant
     * 
     * menghapus product restorant
     */
    function deleteProductDataByProduct(Product $product): void;
}