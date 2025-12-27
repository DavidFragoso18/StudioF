<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            ['category' => 'Hommes', 'name' => 'Coupe', 'price_display' => 'dès 25.-', 'duration_minutes' => 30, 'order_index' => 1],

            ['category' => 'Femmes', 'name' => 'Coupe & brushing', 'price_display' => 'dès 40.-', 'duration_minutes' => 75, 'order_index' => 2], // Avg of 1h-1h45
            ['category' => 'Femmes', 'name' => 'Brushing', 'price_display' => 'dès 25.-', 'duration_minutes' => 45, 'order_index' => 3],
            ['category' => 'Femmes', 'name' => 'Mèches & brushing', 'price_display' => 'dès 80.-', 'duration_minutes' => 150, 'order_index' => 4], // 2h30
            ['category' => 'Femmes', 'name' => 'Coloration & brushing', 'price_display' => 'dès 60.-', 'duration_minutes' => 90, 'order_index' => 5], // 1h30
            ['category' => 'Femmes', 'name' => 'Permanente', 'price_display' => 'dès 70.-', 'duration_minutes' => 120, 'order_index' => 6], // 1h30-2h -> 2h safe

            ['category' => 'Epilation', 'name' => 'Sourcils', 'price_display' => 'dès 10.-', 'duration_minutes' => 15, 'order_index' => 7], // 10-20 min
            ['category' => 'Epilation', 'name' => 'Lèvre supérieure', 'price_display' => 'dès 9.-', 'duration_minutes' => 15, 'order_index' => 8], // 10-20 min
        ];

        foreach ($services as $service) {
            \App\Models\Service::firstOrCreate(
                ['name' => $service['name'], 'category' => $service['category']],
                $service
            );
        }
    }
}
