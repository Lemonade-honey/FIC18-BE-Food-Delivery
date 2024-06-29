<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{

    private $restoranService;
    private $orderService;

    public function __construct()
    {
        $this->orderService = App::make(\App\Services\Interfaces\OrderService::class);
        $this->restoranService = App::make(\App\Services\Interfaces\RestorantService::class);
    }

    public function userUpdatePatch(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'max:255', 'min:3', 'regex:/^[^\d]*$/'],
            'email' => ['sometimes', 'email', 'max:255', 'min:3', 'unique:users,email,' . $request->user()->id],
            'phone' => ['sometimes', 'numeric', 'min:6', 'unique:users,phone,' . $request->user()->id]
        ]);

        try {
            $user = $request->user();

            if($request->has('name')){
                $user->name = $request->input('name');
            }
            if($request->has('email')){
                $user->email = $request->input('email');
            }
            if($request->has('phone')){
                $user->phone = $request->input('phone');
            }
            
            $user->save();

            Log::info('user update data sendiri', [
                'data' => $user
            ]);

            return response()->json([
                'data' => $user
            ]);
        } 
        
        catch (Throwable $th) {
            Log::critical('user gagal update data. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    public function userRolePatch(Request $request)
    {
        $request->validate([
            'role' => ['required', 'max:12', 'in:user,driver,restorant']
        ]);

        try {
            $user = $request->user();

            $user->role = $request->input('role');
            $user->save();

            return response()->json([
                'data' => $user
            ]);
        } 
        
        catch (Throwable $th) {
            Log::critical('user gagal update role. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }

    public function userCreateOrderPost(Request $request)
    {
        $request->validate([
            'restorant_id' => ['required', 'numeric'],
            'products_order' => ['required', 'array']
        ]);

        try {

            $restorant = $this->restoranService->getRestorantById($request->input('restorant_id'));

            if (! $restorant)
            {
                return self::errorResponseDataNotFound();
            }

            $order = $this->orderService->createOrderUserByRequest($request, $restorant);

            return response()->json($order, 201);
        } catch (Throwable $th) {
            Log::critical('user gagal update role. Error Code : ' . $th->getCode(), [
                'class' => get_class(),
                'massage' => $th->getMessage()
            ]);

            return self::errorResponseServerError();
        }
    }
}
