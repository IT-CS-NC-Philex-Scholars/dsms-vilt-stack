<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Announcement;
class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

        public function run(): void
            {
                $faker = Faker::create();

                $announcements = [
                    [
                        'title' => 'Scholarship Application Now Open',
                        'content' => 'Applications for the 2024 Academic Year are now being accepted.',
                        'priority' => 'high',
                        'published_at' => now(),
                    ],
                    [
                        'title' => 'Document Submission Deadline',
                        'content' => 'Please submit all required documents by the end of the month.',
                        'priority' => 'medium',
                        'published_at' => now(),
                    ],
                    // Add more announcements
                ];

                foreach ($announcements as $announcement) {
                    Announcement::create($announcement);
                }
            }

}
