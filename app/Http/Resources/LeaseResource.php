<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaseResource extends JsonResource
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
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'monthly_rent' => $this->monthly_rent,
            'security_deposit' => $this->security_deposit,
            'status' => $this->status,
            'tenant' => [
                'id' => $this->tenant->id,
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
            ],
            'unit' => [
                'id' => $this->unit->id,
                'unit_number' => $this->unit->unit_number,
                'property' => [
                    'id' => $this->unit->property->id,
                    'name' => $this->unit->property->name,
                ],
            ],
            'payments_count' => $this->whenCounted('payments'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
