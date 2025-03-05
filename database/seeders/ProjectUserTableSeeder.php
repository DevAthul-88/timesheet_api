<?php

namespace Database\Seeders;

use App\Models\ProjectUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProjectUser::create([
            'user_id' => 1,
            'project_id' => 1,
            'role' => 'admin',
        ]);

        ProjectUser::create([
            'user_id' => 1,
            'project_id' => 2,
            'role' => 'member',
        ]);

    }
}
