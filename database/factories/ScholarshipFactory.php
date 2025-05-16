<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Scholarship;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scholarship>
 */
final class ScholarshipFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = ['active', 'inactive'];
        $requiredDocs = ['Transcript of Records', 'Certificate of Enrollment', 'Proof of Income (Parents/Guardian)', 'Barangay Clearance', 'Application Form', 'Essay', 'Recommendation Letter'];

        return [
            'name' => $this->faker->catchPhrase.' Scholarship Program',
            'description' => $this->faker->realText(200),
            'amount' => $this->faker->randomElement([5000, 10000, 15000, 20000, 25000]), // Per semester/year? Assume per period
            'requirements' => $this->faker->randomElements($requiredDocs, $this->faker->numberBetween(2, 5)), // Need 2 to 5 docs
            'application_deadline' => $this->faker->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'status' => $this->faker->randomElement($statuses),
        ];
    }
}
