<?php

namespace App\Services;

use App\Models\Order;
use App\Services\Interfaces\OrderService;

class OrderServiceImpl implements OrderService
{

    private $productService;

    public function __construct()
    {
        $this->productService = new ProductServiceImpl();
    }

    private function generateUUIDv7Order(): string
    {
        return \Ramsey\Uuid\Uuid::uuid7();
    }

    public function breakProductsOrderRequestIntoProductsIds(\Illuminate\Support\Collection $productsOrder): array
    {
        $productIds = $productsOrder->pluck('product_id')->toArray();

        return $productIds;
    }

    public function breakProductsOrderRequestIntoProductsQtys(\Illuminate\Support\Collection $productsOrder): array
    {
        $productQtys = $productsOrder->pluck('qty')->toArray();

        return $productQtys;
    }

    public function mergeCurrentProductsToProducsOrder(\Illuminate\Http\Request $request, \Illuminate\Support\Collection $productsRestorant): array
    {
        if ($productsRestorant->isEmpty() && $request->isNotFilled('products_order'))
        {
            throw new \Exception("no data product get");
        }

        $orders = collect($request->input('products_order'));

        $mergeProductsOrder = $orders->map(function($item) use($productsRestorant){
            $product = $productsRestorant->firstWhere('id', $item['product_id']);
            return array_merge($item, [
                'product_name' => $product->name,
                'product_price' => $product->harga,
            ]);
        })->toArray();

        return $mergeProductsOrder;
    }

    public function getTotalProductsOrderPrice(\Illuminate\Http\Request $request, \Illuminate\Support\Collection $productsRestorant): int
    {
        
        if ($productsRestorant->isEmpty() && $request->isNotFilled('products_order'))
        {
            throw new \Exception("no data product get");
        }

        $requestProductsOrder = collect($request->input('products_order'));
        
        $totalProductsOrderPrice = $requestProductsOrder->reduce(function($carry, $item) use($productsRestorant){
            $product = $productsRestorant->firstWhere('id', $item['product_id']);
            
            return $carry + ($product->harga * $item['qty']);
        });

        return $totalProductsOrderPrice;
    }

    public function getDetailOrderPrice(int $totalProductsOrderPrice): array
    {
        $totalProductsPrice = [
            'name' => 'price',
            'price' => $totalProductsOrderPrice
        ];

        $totalServicePrice = [
            'name' => 'service',
            'price' => (int) env('SERVICE_FEE', 0)
        ];

        $detailPrice = [
            $totalProductsPrice,
            $totalServicePrice
        ];

        $totalPrice = collect($detailPrice)->reduce(function($carry, $item){
            return $carry + $item['price'];
        });

        return [
            'detail_price' => $detailPrice,
            'total_price' => $totalPrice
        ];
    }

    public function createOrderUserByRequest(\Illuminate\Http\Request $request, \App\Models\Restorant $restorant): Order
    {
        // product yang dipesan, ambil id nya
        $productsIds = $this->breakProductsOrderRequestIntoProductsIds(collect($request->input('products_order')));

        $productsRestorant = $this->productService->productsByIdsAndRestorantId($productsIds, $restorant->id);

        // hitung harga total order productnya saja
        $totalProductsOrderPrice = $this->getTotalProductsOrderPrice($request, $productsRestorant);

        $detailsOrder = $this->getDetailOrderPrice($totalProductsOrderPrice);

        $convertOrder = $this->mergeCurrentProductsToProducsOrder($request, $productsRestorant);


        /**
         * Better Flow
         * 
         * mungkin lebih baik setelah create masuk ke dalam Redis cache, setelah user setting payment
         * dan menuju pembayaran dan setelah bayar baru bisa update status order to proses
         * agar lebih cepat saja dalam update datanya
         * 
         * dapat di set juga expirednya, menggunakan schedule. jika sudah expired otomatis cancel.
         */
        $order = Order::create([
            'uuid' => $this->generateUUIDv7Order(),
            'user_id' => $request->user()->id,
            'restorant_id' => $restorant->id,
            'details' => $detailsOrder,
            'orders' => $convertOrder,
            'price' =>$detailsOrder['total price'],
            'status' => 1 // waitting payment
        ]);

        return $order;
    }
}