<?php
namespace Database\Seeders;

use App\Models\Requirement;
use App\Models\Scholar;
use App\Models\Scholarship;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class RequirementSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $scholars = Scholar::where('status', 'active')->get();
        $scholarships = Scholarship::where('status', 'active')->get();

        foreach ($scholars as $scholar) {
            // Assign 1-2 random scholarships to each scholar
            $assignedScholarships = $scholarships->random(rand(1, 2));

            foreach ($assignedScholarships as $scholarship) {
                // Create requirements for each assigned scholarship
                foreach ($scholarship->requirements as $reqType) {
                    Requirement::create([
                        'scholar_id' => $scholar->id,
                        'scholarship_id' => $scholarship->id,
                        'document_type' => $reqType,
                        'file_path' => 'sample/path/document_' . $faker->uuid . '.pdf',
                        'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                        'remarks' => $faker->optional(0.7)->sentence(),
                        'submitted_at' => $faker->dateTimeBetween('-3 months', 'now'),
                        'reviewed_at' => $faker->optional(0.6)->dateTimeBetween('-2 months', 'now')
                    ]);
                }
            }
        }
    }
}
