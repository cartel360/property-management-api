<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = Property::all();

        foreach ($properties as $property) {
            Unit::factory(5)->create([
                'property_id' => $property->id
            ]);
        }
    }
}
