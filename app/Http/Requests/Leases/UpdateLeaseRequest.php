<?php

namespace App\Http\Requests\Leases;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'unit_id' => 'sometimes|exists:units,id',
            'tenant_id' => 'sometimes|exists:tenants,id',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'monthly_rent' => 'sometimes|numeric|min:0',
            'security_deposit' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:active,ended,terminated',
            'terms' => 'nullable|string',
        ];
    }
}
