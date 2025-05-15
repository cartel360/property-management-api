<?php

namespace App\Repositories\Property;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PropertyRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 15): LengthAwarePaginator;
    public function find(int $id): ?Property;
    public function create(array $data): Property;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function forLandlord(int $landlordId): Collection;
}
