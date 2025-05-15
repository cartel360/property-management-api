<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'payment_method' => $this->payment_method,
            'transaction_reference' => $this->transaction_reference,
            'status' => $this->status,
            'notes' => $this->notes,
            'lease' => [
                'id' => $this->lease->id,
                'monthly_rent' => $this->lease->monthly_rent,
                'tenant' => [
                    'id' => $this->lease->tenant->id,
                    'name' => $this->lease->tenant->name,
                ],
                'unit' => [
                    'id' => $this->lease->unit->id,
                    'unit_number' => $this->lease->unit->unit_number,
                ],
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
