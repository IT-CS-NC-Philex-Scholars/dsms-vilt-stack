<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

final class AnnouncementSeeder extends Seeder
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
