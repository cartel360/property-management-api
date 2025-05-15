<?php

namespace App\Repositories\Payment;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Payment;
    public function create(array $data): Payment;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function forLease(int $leaseId): Collection;
    public function forTenant(int $tenantId): Collection;
    public function overduePayments(): Collection;
}
