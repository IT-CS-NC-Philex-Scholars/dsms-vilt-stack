<?php

namespace Database\Factories;

use App\Models\Scholar;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScholarFactory extends Factory
{
    protected $model = Scholar::class;

    public function definition(): array
    {
        $genders = ['male', 'female', 'other'];
        $types = ['High_School', 'Senior_High_School', 'College', 'Graduate'];
        $statuses = ['active', 'inactive', 'graduated', 'terminated'];
        $courses = ['BS Computer Science', 'BS Information Technology', 'BS Civil Engineering', 'BS Electrical Engineering', 'BS Mechanical Engineering', 'BS Architecture', 'BS Accountancy', 'BS Business Administration', 'AB Political Science', 'BS Nursing', 'BS Education'];

        $scholarType = $this->faker->randomElement($types);
        $yearLevel = 1;
        if ($scholarType === 'High_School') {
            $yearLevel = $this->faker->numberBetween(7, 10); // Assuming Grades 7-10
        } elseif ($scholarType === 'Senior_High_School') {
            $yearLevel = $this->faker->numberBetween(11, 12);
        } elseif ($scholarType === 'College') {
            $yearLevel = $this->faker->numberBetween(1, 5); // Allow up to 5th year
        } elseif ($scholarType === 'Graduate') {
            $yearLevel = $this->faker->numberBetween(1, 2); // Master's years
        }

        // Generate more realistic additional details
        $additionalDetails = [
            [
                'type' => 'text',
                'data' => [
                    'label' => 'Father\'s Name',
                    'value' => $this->faker->name('male')
                ]
            ],
             [
                'type' => 'text',
                'data' => [
                    'label' => 'Father\'s Occupation',
                    'value' => $this->faker->jobTitle
                ]
            ],
            [
                'type' => 'text',
                'data' => [
                    'label' => 'Mother\'s Name',
                    'value' => $this->faker->name('female')
                ]
            ],
             [
                'type' => 'text',
                'data' => [
                    'label' => 'Mother\'s Occupation',
                    'value' => $this->faker->jobTitle
                ]
            ],
            [
                'type' => 'number',
                'data' => [
                    'label' => 'Number of Siblings',
                    'value' => $this->faker->numberBetween(0, 8)
                ]
            ],
            [
                'type' => 'select',
                'data' => [
                    'label' => 'Household Income Bracket',
                    'options' => ['Below 10k', '10k-20k', '20k-35k', '35k-50k', 'Above 50k'],
                    'value' => $this->faker->randomElement(['Below 10k', '10k-20k', '20k-35k', '35k-50k', 'Above 50k'])
                ]
            ]
        ];


        return [
            'user_id' => User::query()->inRandomOrder()->first()->id,
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional(0.7)->lastName, // 70% chance of having middle name
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'contact_number' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'birth_date' => $this->faker->dateTimeBetween('-30 years', '-15 years')->format('Y-m-d'), // Age 15-30
            'gender' => $this->faker->randomElement($genders),
            'school_id' => School::query()->inRandomOrder()->first()->id ?? School::factory(), // Get random existing school or create one
            'type' => $scholarType,
            'course' => ($scholarType === 'College' || $scholarType === 'Graduate') ? $this->faker->randomElement($courses) : null,
            'year_level' => $yearLevel,
            'status' => $this->faker->randomElement($statuses),
            'additional_details' => $additionalDetails, // Directly use array
        ];
    }
}
