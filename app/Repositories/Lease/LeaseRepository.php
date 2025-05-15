<?php

namespace App\Repositories\Lease;

use App\Models\Lease;
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
class LeaseRepository implements LeaseRepositoryInterface
{
    public function all(): Collection
    {
        return Lease::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Lease::with(['tenant', 'unit', 'payments'])->paginate($perPage);
    }

    public function find(int $id): ?Lease
    {
        return Lease::with(['tenant', 'unit', 'payments'])->find($id);
    }

    public function create(array $data): Lease
    {
        return Lease::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $lease = $this->find($id);

        if (!$lease) {
            return false;
        }

        return $lease->update($data);
    }

    public function delete(int $id): bool
    {
        $lease = $this->find($id);

        if (!$lease) {
            return false;
        }

        return $lease->delete();
    }

    public function forTenant(int $tenantId): Collection
    {
        return Lease::where('tenant_id', $tenantId)->get();
    }

    public function forUnit(int $unitId): Collection
    {
        return Lease::where('unit_id', $unitId)->get();
    }

    public function activeLeases(): Collection
    {
        return Lease::where('status', 'active')->get();
    }
}
