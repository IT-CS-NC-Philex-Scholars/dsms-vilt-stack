<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Scholar;
use App\Models\Requirement;
use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Factories\Factory;

final class RequirementFactory extends Factory
{
    protected $model = Requirement::class;

    public function definition(): array
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $status = $this->faker->randomElement($statuses);

        $submitted_at = $this->faker->dateTimeBetween('-6 months', 'now');
        $reviewed_at = null;

        if (in_array($status, ['approved', 'rejected'])) {
            $reviewed_at = $this->faker->dateTimeBetween($submitted_at, 'now');
        }

        // Note: scholar_id, scholarship_id, and document_type should ideally
        // be set *after* creating the factory instance in the seeder
        // to ensure valid combinations.
        return [
            'scholar_id' => Scholar::query()->inRandomOrder()->first()->id ?? Scholar::factory(), // Placeholder
            'scholarship_id' => Scholarship::query()->inRandomOrder()->first()->id ?? Scholarship::factory(), // Placeholder
            'document_type' => $this->faker->randomElement(['Transcript of Records', 'Certificate of Enrollment', 'Proof of Income', 'Recommendation Letter']),
            'file_path' => 'documents/'.$this->faker->uuid.'.pdf',
            'status' => $status,
            'remarks' => ($status === 'rejected') ? $this->faker->sentence : null,
            'submitted_at' => $submitted_at,
            'reviewed_at' => $reviewed_at,
        ];
    }
}
