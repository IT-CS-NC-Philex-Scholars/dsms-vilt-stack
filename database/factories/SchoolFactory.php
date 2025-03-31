<?php
namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        $regions = ['NCR', 'CAR', 'Region I', 'Region II', 'Region III', 'Region IV-A', 'Region IV-B', 'Region V', 'Region VI', 'Region VII', 'Region VIII', 'Region IX', 'Region X', 'Region XI', 'Region XII', 'Region XIII', 'BARMM'];
        $levels = ['Elementary', 'High School', 'Senior High School', 'Vocational', 'College', 'University'];
        $types = ['Public', 'Private', 'SUC']; // State University/College

        return [
            'name' => $this->faker->company . ' ' . $this->faker->randomElement(['Institute', 'University', 'College', 'School', 'Academy']),
            'address' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'province' => $this->faker->state, // Using state as placeholder for province
            'region' => $this->faker->randomElement($regions),
            'type' => $this->faker->randomElement($types),
            'level' => $this->faker->randomElement($levels),
            'contact_number' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'website' => 'https://www.' . $this->faker->domainName,
            'description' => $this->faker->realText(150),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
            'additional_info' => [ // Directly use an array, Eloquent handles JSON casting
                'founded' => $this->faker->year,
                'student_population' => $this->faker->numberBetween(500, 15000),
                'accreditation_level' => $this->faker->optional()->randomElement(['Level I', 'Level II', 'Level III', 'Level IV']),
            ]
        ];
    }
}
