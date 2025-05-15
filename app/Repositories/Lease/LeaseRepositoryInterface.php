<?php

namespace App\Repositories\Lease;

use App\Models\Lease;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @ignore
 */
interface LeaseRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Lease;
    public function create(array $data): Lease;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function forTenant(int $tenantId): Collection;
    public function forUnit(int $unitId): Collection;
    public function activeLeases(): Collection;
}
