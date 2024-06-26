<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Restorant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserRestorantController extends Controller
{

    const FILE_PATH_PHOTO_RESTORANT = "restorant";

    private $restorantService;
    private $productService;

    public function __construct(\App\Services\Interfaces\RestorantService $restorantService, \App\Services\Interfaces\ProductService $productService)
    {
        $this->restorantService = $restorantService;
        $this->productService = $productService;
    }

    public function currentRestorant(Request $request): JsonResponse
    {
        $userRestorant = $this->restorantService->restorantUserByRequest($request);

        if(! $userRestorant)
        {
            return response()->json([
                'errors' => [
                    'massage' => 'restorant user not found'
                ]
            ], 404);
        }

        return response()->json([
            'data' => [
                $userRestorant
            ]
        ]);
    }

    /**
     * Create Restorant By User
     * 
     * untuk saat ini hanya dapat membuat 1 restoran/user dan jika user terikat dengan data driver
     * maka `data driver` sebelumnya akan `terhapus`
     */
    public function createRestorant(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'max:255', 'min:3'],
            'address' => 'required',
            'latlong' => 'required',
            'image' => ['required', 'image', 'max:10280']
        ], [
            'image.size' => 'photo maksimal berukuran 10MB'
        ]);

        try {
            $userRestorant = $this->restorantService->restorantUserByRequest($request);
            
            if($userRestorant)
            {
                // conflic status
                return response()->json([
                   'massage' => 'restorant sudah terdata, silahkan hapus data restorant sebelumnya'
                ], 409);
            }

            $restorant = $this->restorantService->createRestorantByRequest($request);

            return response()->json([
                'data' => $restorant
            ], 201);
        }
        
        catch (Throwable $th) {
            Log::critical('user gagal membuat restorant. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    /**
     * Patch Update Data Restorant
     * 
     * update data restorants
     */
    public function currentRestorantPatch(Request $request): JsonResponse
    {

        $request->validate([
            'name' => ['sometimes', 'max:255', 'min:3'],
            'address' => 'sometimes',
            'latlong' => 'sometimes',
            'image' => ['sometimes', 'image', 'max:10280']
        ]);

        try {
            $userRestorant = $this->restorantService->restorantUserByRequest($request);

            if (! $userRestorant)
            {
                return self::errorResponseDataNotFound();
            }

            $userRestorantUpdate = $this->restorantService->updateRestorantDataByRequest($request, $userRestorant);

            return response()->json([
                'data' => $userRestorantUpdate
            ]);
            
        } catch (Throwable $th) {
            Log::critical('user gagal update data restorant. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    /**
     * Delete Restorant
     * 
     * menghapus data restorant secara `permanent` beserta dengan relasi productnya
     */
    public function currentRestorantDelete(Request $request)
    {
        $userRestorant = $this->restorantService->restorantUserByRequest($request);

        if(! $userRestorant)
        {
            return self::errorResponseDataNotFound();
        }

        $this->restorantService->deleteRestorant($userRestorant);

        return response()->json(status: 204);
    }

    public function currentRestorantProducts(Request $request): JsonResponse
    {
        $user = $request->user();

        $restorant = Restorant::with('products')->where('user_id', $user->id)->first();

        if (! $restorant)
        {
            return self::errorResponseDataNotFound();
        }

        if ($restorant->products()->count() == 0)
        {
            return self::errorResponseDataNotFound();
        }

        return response()->json([
            'data' => $restorant->products
        ]);
    }

    public function currentRestorantCreateProduct(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'max:255', 'min:3'],
            'image' => ['required', 'image', 'max:10280'],
            'deskripsi' => 'required',
            'type' => ['required', 'in:makanan,minuman'],
            'harga' => ['required', 'numeric']
        ]);

        try{
            $userRestorant = $this->restorantService->restorantUserByRequest($request);

            if (! $userRestorant)
            {
                return self::errorResponseDataNotFound();
            }

            $product = $this->productService->createProductDataByRequest($request, $userRestorant);

            return response()->json([
                'data' => $product
            ], 201);
        }

        catch (Throwable $th){

            Log::critical('user gagal membuat product. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    public function currentRestorantProduct(Request $request, $id): JsonResponse
    {

        $userRestorant = $this->restorantService->restorantUserByRequest($request);

        if (! $userRestorant)
        {
            return self::errorResponseDataNotFound();
        }

        $product = Product::where(['restorant_id' => $userRestorant->id, 'id' => $id])->first();

        if (! $product)
        {
            return response()->json([
                'massage' => 'product tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'data' => $product
        ]);
    }

    public function currentRestorantProductPatch(Request $request, $id): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'max:255', 'min:3'],
            'image' => ['sometimes', 'image', 'max:10280'],
            'deskripsi' => 'sometimes',
            'type' => ['sometimes', 'in:makanan,minuman'],
            'harga' => ['sometimes', 'numeric']
        ]);

        try {
            $userRestorant = $this->restorantService->restorantUserByRequest($request);

            if (! $userRestorant)
            {
                return self::errorResponseDataNotFound();
            }

            $product = $this->productService->productByIdAndRestorantId(productId: $id, restorantId: $userRestorant->id);

            if (! $product)
            {
                return self::errorResponseDataNotFound();
            }

            $updateProduct = $this->productService->updateProductDataByRequest($product, $request);

            return response()->json([
                'data' => $updateProduct
            ]);

        } 
        
        catch (Throwable $th) {
            Log::critical('user gagal update product. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    public function currentRestorantProductDelete(Request $request, $id): JsonResponse
    {
        try {
            $userRestorant = $this->restorantService->restorantUserByRequest($request);

            if (! $userRestorant)
            {
                return self::errorResponseDataNotFound();
            }

            $product = $this->productService->productByIdAndRestorantId(productId: $id, restorantId: $userRestorant->id);

            if (! $product)
            {
                return response()->json([
                    'massage' => 'product tidak ditemukan'
                ], 404);
            }

            // delete
            $this->productService->deleteProductDataByProduct($product);

            return response()->json(status: 204);
        } 
        
        catch (Throwable $th) {
            Log::critical('user gagal hapus product. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

}
