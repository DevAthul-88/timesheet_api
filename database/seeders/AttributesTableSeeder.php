<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = [
            [
                'name' => 'Department',
                'type' => 'select',
                'description' => 'Project department',
                'options' => json_encode(['IT', 'Marketing', 'Sales', 'HR', 'Finance']),
                'is_required' => true,
                'entity_type' => 'App\Models\Project',
            ],
            [
                'name' => 'Start Date',
                'type' => 'date',
                'description' => 'Project start date',
                'options' => null,
                'is_required' => false,
                'entity_type' => 'App\Models\Project',
            ],
            [
                'name' => 'Priority',
                'type' => 'select',
                'description' => 'Project priority level',
                'options' => json_encode(['Low', 'Medium', 'High', 'Critical']),
                'is_required' => true,
                'entity_type' => 'App\Models\Project',
            ],
            [
                'name' => 'Budget',
                'type' => 'number',
                'description' => 'Project total budget',
                'options' => null,
                'is_required' => false,
                'entity_type' => 'App\Models\Project',
            ],
            [
                'name' => 'Client',
                'type' => 'text',
                'description' => 'Client name or organization',
                'options' => null,
                'is_required' => false,
                'entity_type' => 'App\Models\Project',
            ]
        ];

        Attribute::query()->delete();

        foreach ($attributes as $attributeData) {
            Attribute::create($attributeData);
        }
    }
}
