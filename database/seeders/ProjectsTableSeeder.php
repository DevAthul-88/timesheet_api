<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Website Redesign',
                'status' => 'active',
                'attributes' => [
                    'Department' => 'IT',
                    'Start Date' => '2024-01-15',
                    'Priority' => 'High',
                    'Budget' => 50000,
                    'Client' => 'ABC Corporation'
                ]
            ],
            [
                'name' => 'Marketing Campaign',
                'status' => 'pending',
                'attributes' => [
                    'Department' => 'Marketing',
                    'Start Date' => '2024-02-01',
                    'Priority' => 'Medium',
                    'Budget' => 25000,
                    'Client' => 'XYZ Company'
                ]
            ]
        ];

        $createdUsers = User::all();

        foreach ($projects as $projectData) {
            $project = new Project([
                'name' => $projectData['name'],
                'status' => $projectData['status']
            ]);
            $project->save();

            foreach ($projectData['attributes'] as $attributeName => $value) {
                $attribute = Attribute::where('name', $attributeName)->first();

                $pArr = $project->toArray();

                if ($attribute) {
                    AttributeValue::create([
                        'attribute_id' => $attribute->id,
                        'entity_id' => $pArr['id'],
                        'entity_type' => Project::class,
                        'value' => $value
                    ]);
                } else {
                    Log::warning("Attribute '{$attributeName}' not found. Skipping.");
                }
            }
        }
    }
}
