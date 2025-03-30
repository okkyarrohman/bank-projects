<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $categories = [
            'Web Development',
            'Android Development',
            'IOS Development',
            'UI UX Design',
            'System Analyst',
            'Machine Learning'
        ];

        foreach ($categories as $name) {
            DB::table('categories')
                ->insert([
                    'name' => $name,
                    'status' => 'Active'
                ]);
        }
    }
}
