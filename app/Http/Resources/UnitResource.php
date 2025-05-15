<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
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
            'unit_number' => $this->unit_number,
            'rent_amount' => $this->rent_amount,
            'size' => $this->size,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'features' => $this->features,
            'status' => $this->status,
            'property' => [
                'id' => $this->property->id,
                'name' => $this->property->name,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
