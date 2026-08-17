<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// database/seeders/CategorySeeder.php
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $bangladesh = Category::create([
            'name' => 'বাংলাদেশ',
            'slug' => 'bangladesh',
            'icon' => 'map',
            'order' => 2,
        ]);

        $subcategories = [
            ['name' => 'রাজধানী', 'slug' => 'capital'],
            ['name' => 'জেলা', 'slug' => 'district'],
            ['name' => 'অপরাধ', 'slug' => 'crime'],
            ['name' => 'পরিবেশ', 'slug' => 'environment'],
        ];

        foreach ($subcategories as $index => $sub) {
            Category::create([
                ...$sub,
                'parent_id' => $bangladesh->id,
                'order' => $index + 1,
            ]);
        }
    }
}
