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
        Announcement::factory(8)->create(); // Create 8 announcements
        $this->command->info('Created 8 announcements.');
    }

}
