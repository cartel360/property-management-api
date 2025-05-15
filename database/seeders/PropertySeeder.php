<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Property;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $landlords = User::where('role', 'landlord')->get();

        foreach ($landlords as $landlord) {
            Property::factory(5)->create([
                'landlord_id' => $landlord->id
            ]);
        }
    }
}
