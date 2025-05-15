<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @OA\Info(
 *     title="Property Management API",
 *     version="1.0.0",
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */

/**
 * @ignore
 */
class PaymentRepository implements PaymentRepositoryInterface
{
    public function all(): Collection
    {
        return Payment::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Payment::with(['lease', 'lease.tenant'])->paginate($perPage);
    }

    public function find(int $id): ?Payment
    {
        return Payment::with(['lease', 'lease.tenant'])->find($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $payment = $this->find($id);

        if (!$payment) {
            return false;
        }

        return $payment->update($data);
    }

    public function delete(int $id): bool
    {
        $payment = $this->find($id);

        if (!$payment) {
            return false;
        }

        return $payment->delete();
    }

    public function forLease(int $leaseId): Collection
    {
        return Payment::where('lease_id', $leaseId)->get();
    }

    public function forTenant(int $tenantId): Collection
    {
        return Payment::whereHas('lease', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->get();
    }

    public function overduePayments(): Collection
    {
        return Payment::where('status', 'pending')
            ->whereDate('payment_date', '<', now())
            ->get();
    }
}
