<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    public function run()
    {
        Task::create([
            'title' => 'Task 1',
            'description' => 'Task 1 description',
            'project_id' => 1,
            'list_task_id' => 1,
            'category' => 'marketing',
            'priority' => 'basse',
        ]);

        Task::create([
            'title' => 'Task 2',
            'description' => 'Task 2 description',
            'project_id' => 1,
            'list_task_id' => 1,
            'category' => 'développement',
            'priority' => 'moyenne',
        ]);

        Task::create([
            'title' => 'Task 3',
            'description' => 'Task 3 description',
            'project_id' => 2,
            'list_task_id' => 1,
            'category' => 'communication',
            'priority' => 'élevée',
        ]);
    }
}
