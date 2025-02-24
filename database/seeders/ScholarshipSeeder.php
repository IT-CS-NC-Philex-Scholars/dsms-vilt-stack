<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\ScholarshipStatus;
use App\Models\Scholarship;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

                $scholarshipTypes = [
                    [
                        'name' => 'Academic Excellence Scholarship',
                        'requirements' => [
                            'Report Card/TOR with GPA of 3.5 or higher',
                            'Recommendation Letter from School',
                            'Personal Statement Essay',
                            'Certificate of Good Moral Character',
                            'Family Income Declaration'
                        ],
                        'amount' => 50000
                    ],
                    [
                        'name' => 'Sports Development Grant',
                        'requirements' => [
                            'Sports Achievement Certificates',
                            'Coach Recommendation Letter',
                            'Physical Fitness Assessment',
                            'Academic Standing Certificate',
                            'Medical Clearance'
                        ],
                        'amount' => 35000
                    ],
                    [
                        'name' => 'Digital Innovation Scholarship',
                        'requirements' => [
                            'Technology Project Portfolio',
                            'Programming Skills Certificate',
                            'Innovation Project Proposal',
                            'Academic Transcripts',
                            'Technical Skills Assessment'
                        ],
                        'amount' => 45000
                    ],
                    [
                        'name' => 'Arts and Culture Grant',
                        'requirements' => [
                            'Art Portfolio',
                            'Cultural Performance Videos',
                            'Artist Statement',
                            'Exhibition/Performance History',
                            'Creative Project Proposal'
                        ],
                        'amount' => 30000
                    ],
                    [
                        'name' => 'Leadership Excellence Award',
                        'requirements' => [
                            'Leadership Resume',
                            'Community Service Certificate',
                            'Organization Recommendation Letter',
                            'Leadership Essay',
                            'Project Implementation Plan'
                        ],
                        'amount' => 40000
                    ]
                ];

                foreach ($scholarshipTypes as $type) {
                    for ($i = 0; $i < 2; $i++) {
                        Scholarship::create([
                            'name' => $type['name'] . ($i > 0 ? ' ' . ($i + 1) : ''),
                            'description' => $faker->paragraphs(3, true),
                            'amount' => $type['amount'],
                            'requirements' => $type['requirements'],
                            'application_deadline' => $faker->dateTimeBetween('+1 month', '+6 months'),
                            'status' => $faker->randomElement(['active', 'inactive', 'closed'])
                        ]);
                    }
                }

    }
}
