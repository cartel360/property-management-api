<?php

namespace App\Repositories\Unit;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UnitRepository implements UnitRepositoryInterface
{
    public function all(): Collection
    {
        return Unit::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Unit::with('property')->paginate($perPage);
    }

    public function find(int $id): ?Unit
    {
        return Unit::with('property')->find($id);
    }

    public function create(array $data): Unit
    {
        return Unit::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $unit = $this->find($id);

        if (!$unit) {
            return false;
        }

        return $unit->update($data);
    }

    public function delete(int $id): bool
    {
        $unit = $this->find($id);

        if (!$unit) {
            return false;
        }

        return $unit->delete();
    }

    public function forProperty(int $propertyId): Collection
    {
        return Unit::where('property_id', $propertyId)->get();
    }
}
