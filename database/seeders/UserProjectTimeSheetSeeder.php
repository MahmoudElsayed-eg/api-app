<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProjectTimeSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Create users
        $users = User::factory()->count(19)->create()->push(
            User::factory()->create(['email' => 'test@example.com'])
        );
        // Create projects
        $projects = Project::factory()->count(10)->create();


        // Attach users to multiple projects
        $users->each(function ($user) use ($projects) {
            $assignedProjects = $projects->random(rand(1, 10));

            $user->projects()->attach($assignedProjects->pluck('id'));

            // Create MULTIPLE sheets per user per project
            foreach ($assignedProjects as $project) {
                Timesheet::factory()->count(rand(1, 3))->create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                ]);
            }
        });
    }
}
