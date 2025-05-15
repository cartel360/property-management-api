<?php

namespace App\Repositories\Unit;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UnitRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Unit;
    public function create(array $data): Unit;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function forProperty(int $propertyId): Collection;
}
