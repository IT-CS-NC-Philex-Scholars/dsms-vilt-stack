<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Requirement;
use App\Models\Scholarship;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

final class ScholarshipRequirementSeeder extends Seeder
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
            $randomRequirements = $requirements->random(random_int(2, 4));

            foreach ($randomRequirements as $requirement) {
                $scholarship->requirements()->attach($requirement->id, [
                    'is_mandatory' => $faker->boolean(80),
                    'submission_order' => $faker->numberBetween(1, 5),
                    'description' => $faker->sentence(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
