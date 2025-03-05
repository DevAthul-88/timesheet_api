<?php

namespace Database\Seeders;

use App\Models\Timesheet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimesheetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Timesheet::create([
            'user_id' => 1,
            'project_id' => 1,
            'task_name' => 'Design',
            'date' => '2023-10-01',
            'hours' => 8.5,
            'description' => 'Worked on the initial design.',
        ]);

        Timesheet::create([
            'user_id' => 2,
            'project_id' => 1,
            'task_name' => 'Development',
            'date' => '2023-10-02',
            'hours' => 7.0,
            'description' => 'Worked on the backend development.',
        ]);
    }
}
