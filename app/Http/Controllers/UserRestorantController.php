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

    private $fileService;
    private $restorantService;
    private $productService;

    public function __construct(\App\Services\Interfaces\FileService $fileService, \App\Services\Interfaces\RestorantService $restorantService, \App\Services\Interfaces\ProductService $productService)
    {
        $this->fileService = $fileService;
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
            ],
            'errors' => []
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
            'photo' => ['required', 'image', 'max:10280']
        ], [
            'photo.size' => 'photo maksimal berukuran 10MB'
        ]);

        try {
            $userRestorant = $this->restorantService->restorantUserByRequest($request);
            
            if($userRestorant)
            {
                // conflic status
                return response()->json([
                    'errors' => [
                        'massage' => 'restorant sudah terdata, silahkan hapus data restorant sebelumnya'
                    ]
                ], 409);
            }

            $savedFilePath = $this->fileService->saveFileToStoragePath($request->file('photo'), self::FILE_PATH_PHOTO_RESTORANT);

            $restorant = Restorant::create([
                'user_id' => $request->user()->id,
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'latlong' => $request->input('latlong'),
                'photo' => $savedFilePath
            ]);

            return response()->json([
                'data' => $restorant
            ], 201);
        }
        
        catch (Throwable $th) {
            Log::critical('user gagal update role. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return response()->json([
                'errors' => [
                    'massage' => 'server error'
                ]
            ], 504);
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
            return response()->json([
                'massage' => 'restorant user not found'
            ], 404);
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
            return self::errorResponseRestorantNotFound();
        }

        if ($restorant->products()->count() == 0)
        {
            return response()->json([
                'massage' => 'tidak ada product terdaftar'
            ], 404);
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
                return self::errorResponseRestorantNotFound();
            }

            $product = Product::create([
                'restorant_id' => $userRestorant->id,
                'name' => $request->input('name'),
                'image' => $this->fileService->saveFileToStoragePath($request->file('image'), 'products/test'),
                'deskripsi' => $request->input('deskripsi'),
                'type' => $request->input('type'),
                'harga' => $request->input('harga')
            ]);

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
            return self::errorResponseRestorantNotFound();
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
                return self::errorResponseRestorantNotFound();
            }

            $product = $this->productService->productByIdAndRestorantId(productId: $id, restorantId: $userRestorant->id);

            if (! $product)
            {
                return response()->json([
                    'massage' => 'product tidak ditemukan'
                ], 404);
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
                return self::errorResponseRestorantNotFound();
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

    /**
     * Static response for restorant not found
     * 
     * no access for outside resorce
     */
    private static function errorResponseRestorantNotFound(): JsonResponse
    {
        return response()->json([
            'massage' => 'restorant user not found'
        ], 404);
    }

}
