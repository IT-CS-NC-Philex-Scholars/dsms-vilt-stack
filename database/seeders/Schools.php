<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\School;

class Schools extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'Springfield Elementary School',
                'address' => '123 School Street',
                'city' => 'Springfield',
                'province' => 'IL',
                'region' => 'Midwest',
                'type' => 'Public',
                'level' => 'Elementary',
                'contact_number' => '555-0123',
                'email' => 'springfield.elementary@edu.com',
                'website' => 'www.springfieldelementary.edu',
                'description' => 'A public elementary school serving Springfield community',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1950', 'student_count' => 500])
            ],
            [
                'name' => 'Riverside High School',
                'address' => '456 River Road',
                'city' => 'Riverside',
                'province' => 'CA',
                'region' => 'West',
                'type' => 'Public',
                'level' => 'High School',
                'contact_number' => '555-0124',
                'email' => 'riverside.high@edu.com',
                'website' => 'www.riversidehigh.edu',
                'description' => 'Comprehensive public high school',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1965', 'student_count' => 1200])
            ],
            [
                'name' => 'St. Mary\'s Academy',
                'address' => '789 Church Lane',
                'city' => 'Boston',
                'province' => 'MA',
                'region' => 'Northeast',
                'type' => 'Private',
                'level' => 'K-12',
                'contact_number' => '555-0125',
                'email' => 'stmarys@edu.com',
                'website' => 'www.stmarysacademy.edu',
                'description' => 'Private Catholic school offering K-12 education',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1900', 'student_count' => 800])
            ],
            [
                'name' => 'Tech Valley Middle School',
                'address' => '321 Innovation Drive',
                'city' => 'San Jose',
                'province' => 'CA',
                'region' => 'West',
                'type' => 'Charter',
                'level' => 'Middle School',
                'contact_number' => '555-0126',
                'email' => 'techvalley@edu.com',
                'website' => 'www.techvalley.edu',
                'description' => 'Technology-focused charter middle school',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '2010', 'student_count' => 400])
            ],
            [
                'name' => 'Montessori Learning Center',
                'address' => '567 Education Avenue',
                'city' => 'Portland',
                'province' => 'OR',
                'region' => 'Northwest',
                'type' => 'Private',
                'level' => 'Elementary',
                'contact_number' => '555-0127',
                'email' => 'montessori@edu.com',
                'website' => 'www.montessorilearning.edu',
                'description' => 'Montessori-based elementary education',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1995', 'student_count' => 150])
            ],
            [
                'name' => 'Lincoln High School',
                'address' => '890 Liberty Street',
                'city' => 'Chicago',
                'province' => 'IL',
                'region' => 'Midwest',
                'type' => 'Public',
                'level' => 'High School',
                'contact_number' => '555-0128',
                'email' => 'lincoln.high@edu.com',
                'website' => 'www.lincolnhigh.edu',
                'description' => 'Urban public high school',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1925', 'student_count' => 1500])
            ],
            [
                'name' => 'International Prep Academy',
                'address' => '432 Global Way',
                'city' => 'Miami',
                'province' => 'FL',
                'region' => 'Southeast',
                'type' => 'Private',
                'level' => 'K-12',
                'contact_number' => '555-0129',
                'email' => 'international.prep@edu.com',
                'website' => 'www.internationalprep.edu',
                'description' => 'International Baccalaureate World School',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '2005', 'student_count' => 600])
            ],
            [
                'name' => 'Arts & Performance School',
                'address' => '765 Creative Boulevard',
                'city' => 'New York',
                'province' => 'NY',
                'region' => 'Northeast',
                'type' => 'Magnet',
                'level' => 'High School',
                'contact_number' => '555-0130',
                'email' => 'arts.performance@edu.com',
                'website' => 'www.artsperformance.edu',
                'description' => 'Specialized arts magnet school',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1980', 'student_count' => 700])
            ],
            [
                'name' => 'STEM Academy',
                'address' => '543 Science Park',
                'city' => 'Austin',
                'province' => 'TX',
                'region' => 'South',
                'type' => 'Charter',
                'level' => 'Middle School',
                'contact_number' => '555-0131',
                'email' => 'stem.academy@edu.com',
                'website' => 'www.stemacademy.edu',
                'description' => 'Science and technology focused charter school',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '2015', 'student_count' => 350])
            ],
            [
                'name' => 'Valley View Elementary',
                'address' => '987 Mountain Road',
                'city' => 'Denver',
                'province' => 'CO',
                'region' => 'West',
                'type' => 'Public',
                'level' => 'Elementary',
                'contact_number' => '555-0132',
                'email' => 'valley.view@edu.com',
                'website' => 'www.valleyview.edu',
                'description' => 'Community elementary school',
                'is_active' => true,
                'additional_info' => json_encode(['founded' => '1970', 'student_count' => 400])
            ]
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
