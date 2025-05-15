<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'features' => $this->features,
            'landlord' => [
                'id' => $this->landlord->id,
                'name' => $this->landlord->name,
                'email' => $this->landlord->email,
            ],
            'units_count' => $this->whenCounted('units'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
