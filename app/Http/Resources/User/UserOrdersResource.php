<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Restorant\RestorantOrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserOrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'=> $this->uuid,
            'restorant' => new RestorantOrderResource($this->restorant),
            'details' => $this->details,
            'orders' => $this->orders,
            'price' => $this->price,
            'status' => $this->status,
            'created_at' => $this->created_at
        ];
    }
}
