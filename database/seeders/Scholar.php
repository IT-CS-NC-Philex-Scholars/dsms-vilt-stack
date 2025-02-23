<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Scholar as ScholarModel;
use App\Models\School;
use Faker\Factory as Faker;

class Scholar extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $schoolIds = School::pluck('id')->toArray();

        // Check if there are any schools before creating scholars
        if (empty($schoolIds)) {
            throw new \Exception('No schools found in database. Please seed schools first.');
        }

        for ($i = 0; $i < 300; $i++) {
            $additionalDetails = [
                [
                    'type' => 'text',
                    'data' => [
                        'label' => 'Nickname',
                        'value' => $faker->word()
                    ]
                ],
                [
                    'type' => 'number',
                    'data' => [
                        'label' => 'Number of Siblings',
                        'value' => $faker->numberBetween(0, 10)
                    ]
                ],
                [
                    'type' => 'date',
                    'data' => [
                        'label' => 'Registration Date',
                        'value' => $faker->date()
                    ]
                ],
                [
                    'type' => 'select',
                    'data' => [
                        'label' => 'Preferred Language',
                        'options' => ['English', 'Filipino', 'Cebuano'],
                        'value' => $faker->randomElement(['English', 'Filipino', 'Cebuano'])
                    ]
                ]
            ];

            ScholarModel::create([
                'first_name' => $faker->firstName,
                'middle_name' => $faker->lastName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'contact_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'birth_date' => $faker->date(),
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'school_id' => $schoolIds[array_rand($schoolIds)],
                'type' => $faker->randomElement(['High_School', 'Senior_High_School', 'College', 'Graduate']),
                'course' => $faker->randomElement(['BS Computer Science', 'BS Information Technology', 'BS Engineering', 'BS Architecture', 'BS Accountancy']),
                'year_level' => $faker->numberBetween(1, 4),
                'status' => $faker->randomElement(['active', 'inactive', 'graduated', 'terminated']),
                'additional_details' => $additionalDetails
            ]);
        }
    }
}
