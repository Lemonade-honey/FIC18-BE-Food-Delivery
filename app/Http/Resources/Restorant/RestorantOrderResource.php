<?php

namespace App\Http\Resources\Restorant;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestorantOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'latlog' => $this->latlong,
            'image' => $this->image
        ];
    }
}
