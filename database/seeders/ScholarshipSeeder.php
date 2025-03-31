<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use Illuminate\Database\Seeder;

class ScholarshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Scholarship::factory(15)->create(); // Create 15 random scholarship programs
        $this->command->info('Created 15 scholarship programs.');
    }
}
