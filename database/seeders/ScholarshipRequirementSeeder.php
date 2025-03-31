<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Scholarship;
use App\Models\Requirement;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ScholarshipRequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     public function run(): void
         {
             $faker = Faker::create();

             $scholarships = Scholarship::all();
             $requirements = Requirement::all();

             foreach ($scholarships as $scholarship) {
                 // Assign 2-4 random requirements to each scholarship
                 $randomRequirements = $requirements->random(rand(2, 4));

                 foreach ($randomRequirements as $requirement) {
                     $scholarship->requirements()->attach($requirement->id, [
                         'is_mandatory' => $faker->boolean(80),
                         'submission_order' => $faker->numberBetween(1, 5),
                         'description' => $faker->sentence(),
                         'created_at' => now(),
                         'updated_at' => now()
                     ]);
                 }
             }
         }
}
