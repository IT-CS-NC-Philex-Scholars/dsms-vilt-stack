<?php

namespace Database\Seeders;

use App\Models\Scholar;
use App\Models\Scholarship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ScholarScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
         {
             $faker = Faker::create();

             $scholars = Scholar::all();
             $scholarships = Scholarship::all();

             foreach ($scholars as $scholar) {
                 // Assign 1-2 random scholarships to each scholar
                 $randomScholarships = $scholarships->random(rand(1, 2));

                 foreach ($randomScholarships as $scholarship) {
                     $scholar->scholarships()->attach($scholarship->id, [
                         'status' => $faker->randomElement(['active', 'inactive', 'completed']),
                         'start_date' => $faker->dateTimeBetween('-1 year', 'now'),
                         'end_date' => $faker->dateTimeBetween('now', '+1 year'),
                         'remarks' => $faker->optional()->sentence(),
                         'created_at' => now(),
                         'updated_at' => now()
                     ]);
                 }
             }
         }
}
