<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Scholar;
use App\Models\Requirement;
use App\Models\Scholarship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class RequirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have scholars and scholarships
        if (\App\Models\Scholar::query()->count() === 0 || \App\Models\Scholarship::query()->count() === 0) {
            $this->command->warn('No scholars or scholarships found. Skipping requirement seeding.');

            return;
        }

        $scholars = \App\Models\Scholar::query()->pluck('id');
        $scholarships = Scholarship::with('requirements')->get(); // Eager load requirements array

        $requirementsToCreate = [];
        $targetCount = 500; // Create around 500 requirement entries

        DB::transaction(function () use ($scholars, $scholarships, $targetCount, &$requirementsToCreate): void {
            Requirement::query()->delete(); // Clear existing before seeding (optional)

            for ($i = 0; $i < $targetCount; $i++) {
                $scholar = $scholars->random();
                $scholarship = $scholarships->random();

                // Ensure the scholarship has requirements defined
                if (empty($scholarship->requirements) || ! is_array($scholarship->requirements)) {
                    continue; // Skip if no requirements defined for this scholarship
                }

                // Pick a random document type from *this* scholarship's list
                $documentType = $scholarship->requirements[array_rand($scholarship->requirements)];

                // Use factory, but override the keys with specific values
                $requirementsToCreate[] = Requirement::factory()->make([
                    'scholar_id' => $scholar,
                    'scholarship_id' => $scholarship->id,
                    'document_type' => $documentType,
                ])->toArray(); // Convert to array for batch insert

                // Avoid memory issues with very large inserts
                if (count($requirementsToCreate) >= 200) {
                    \App\Models\Requirement::query()->insert($requirementsToCreate);
                    $requirementsToCreate = [];
                }
            }

            // Insert any remaining
            if ($requirementsToCreate !== []) {
                \App\Models\Requirement::query()->insert($requirementsToCreate);
            }

        });

        $this->command->info('Created requirement links between scholars and scholarships.');

    }
}
