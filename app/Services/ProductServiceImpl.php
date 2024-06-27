<?php

namespace App\Services;

use App\Models\Product;
use App\Services\Interfaces\ProductService;
use Illuminate\Support\Facades\Storage;

class ProductServiceImpl implements ProductService
{

    const FILE_PATH_IMAGE_PRODUCT = "products/";

    private $fileService;

    public function __construct()
    {
        $this->fileService = new FileServiceImpl;
    }

    public function productByIdAndRestorantId(int $productId, int $restorantId): ?Product
    {
        $product = Product::where(['restorant_id' => $restorantId, 'id' => $productId])->first();

        return $product;
    }

    public function productsByIdsAndRestorantId(array $productIds, int $restorantId): \Illuminate\Database\Eloquent\Collection
    {
        $products = Product::whereIn('id', $productIds)->where('restorant_id', $restorantId)->get();

        return $products;
    }

    public function createProductDataByRequest(\Illuminate\Http\Request $request, \App\Models\Restorant $restorant): Product
    {
        $product = Product::create([
            'restorant_id' => $restorant->id,
            'name' => $request->input('name'),
            'image' => $this->fileService->saveFileToStoragePath($request->file('image'), self::FILE_PATH_IMAGE_PRODUCT . "$restorant->id/"),
            'deskripsi' => $request->input('deskripsi'),
            'type' => $request->input('type'),
            'harga' => $request->input('harga')
        ]);

        return $product;
    }

    public function updateProductDataByRequest(Product $product, \Illuminate\Http\Request $request): Product
    {

        $fileService = new FileServiceImpl;

        if ($request->has('name'))
        {
            $product->name = $request->input('name');
        }

        if ($request->has('image'))
        {
            if (Storage::disk('public')->exists('products/test/' . $product->image))
            {
                Storage::delete('products/test/' . $product->image);
            }
            
            $product->image = $fileService->saveFileToStoragePath($request->file('image'), 'products/test');
        }

        if ($request->has('deskripsi'))
        {
            $product->deskripsi = $request->input('deskripsi');
        }

        if ($request->has('type'))
        {
            $product->type = $request->input('type');
        }

        if ($request->has('harga'))
        {
            $product->harga = $request->input('harga');
        }

        $product->save();

        return $product;
    }

    public function deleteProductDataByProduct(Product $product): void
    {
        $product->delete();
    }
}