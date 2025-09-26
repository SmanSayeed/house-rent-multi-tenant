<?php

namespace Database\Seeders;

use App\Models\BillCategory;
use Illuminate\Database\Seeder;

class BillCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electricity',
                'description' => 'Monthly electricity bills for flats',
                'is_active' => true,
                'icon' => 'bi-lightning',
                'color' => '#ffc107',
            ],
            [
                'name' => 'Gas Bill',
                'description' => 'Monthly gas bills for cooking and heating',
                'is_active' => true,
                'icon' => 'bi-fire',
                'color' => '#dc3545',
            ],
            [
                'name' => 'Water Bill',
                'description' => 'Monthly water supply bills',
                'is_active' => true,
                'icon' => 'bi-droplet',
                'color' => '#0d6efd',
            ],
            [
                'name' => 'Utility Charges',
                'description' => 'General utility charges and maintenance fees',
                'is_active' => true,
                'icon' => 'bi-tools',
                'color' => '#6c757d',
            ],
            [
                'name' => 'Internet',
                'description' => 'Monthly internet and broadband charges',
                'is_active' => true,
                'icon' => 'bi-wifi',
                'color' => '#20c997',
            ],
            [
                'name' => 'Phone',
                'description' => 'Monthly phone and communication bills',
                'is_active' => true,
                'icon' => 'bi-telephone',
                'color' => '#fd7e14',
            ],
            [
                'name' => 'Waste Management',
                'description' => 'Garbage collection and waste management fees',
                'is_active' => true,
                'icon' => 'bi-trash',
                'color' => '#6f42c1',
            ],
            [
                'name' => 'Security',
                'description' => 'Security services and monitoring charges',
                'is_active' => true,
                'icon' => 'bi-shield-check',
                'color' => '#198754',
            ],
        ];

        foreach ($categories as $category) {
            BillCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
