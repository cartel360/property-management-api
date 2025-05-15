<?php

namespace App\Repositories\Property;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class PropertyRepository implements PropertyRepositoryInterface
{
    public function all(): Collection
    {
        return Property::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return Property::paginate($perPage);
    }

    public function find(int $id): ?Property
    {
        return Property::find($id);
    }

    public function create(array $data): Property
    {
        Log::info('Creating property with data: ', $data);
        return Property::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $property = $this->find($id);

        if (!$property) {
            return false;
        }

        return $property->update($data);
    }

    public function delete(int $id): bool
    {
        $property = $this->find($id);

        if (!$property) {
            return false;
        }

        return $property->delete();
    }

    public function forLandlord(int $landlordId): Collection
    {
        return Property::where('landlord_id', $landlordId)->get();
    }
}
