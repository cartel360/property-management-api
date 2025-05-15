<?php

namespace App\Repositories\Tenant;

use App\Models\Tenant;
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
class TenantRepository implements TenantRepositoryInterface
{
    public function all(): Collection
    {
        return Tenant::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Tenant::with('leases')->paginate($perPage);
    }

    public function find(int $id): ?Tenant
    {
        return Tenant::with('leases')->find($id);
    }

    public function create(array $data): Tenant
    {
        return Tenant::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $tenant = $this->find($id);

        if (!$tenant) {
            return false;
        }

        return $tenant->update($data);
    }

    public function delete(int $id): bool
    {
        $tenant = $this->find($id);

        if (!$tenant) {
            return false;
        }

        return $tenant->delete();
    }

    public function search(string $query): Collection
    {
        return Tenant::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->get();
    }
}
