<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch all projects
        $projects = Project::all();

        foreach ($projects as $project) {
            // Define the attributes and their types
            $attributesData = [
                ['name' => 'department', 'type' => 'text'],
                ['name' => 'start_date', 'type' => 'date'],
                ['name' => 'end_date', 'type' => 'date']
            ];

            foreach ($attributesData as $data) {
                // Create an attribute for the project
                $attribute = Attribute::create([
                    'entity_id' => $project->id,
                    'name' => $data['name'],
                    'type' => $data['type']
                ]);

                // Generate a value based on the attribute type
                AttributeValue::create([
                    'attribute_id' => $attribute->id,
                    'entity_id' => $project->id,
                    'value' => $this->generateValue($data['name'])
                ]);
            }
        }
    }

    private function generateValue($attributeName)
    {
        $departments = ['HR', 'Finance', 'IT', 'Marketing', 'Operations'];

        switch ($attributeName) {
            case 'department':
                return $departments[array_rand($departments)];
            case 'start_date':
                return Carbon::now()->subDays(rand(10, 100))->format('Y-m-d');
            case 'end_date':
                return Carbon::now()->addDays(rand(10, 100))->format('Y-m-d');
            default:
                return null;
        }
    }
}
