<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaskPriority;

class TaskPrioritySeeder extends Seeder
{
    public function run()
    {
        TaskPriority::create([
            'name' => 'basse',
        ]);

        TaskPriority::create([
            'name' => 'moyenne',
        ]);

          TaskPriority::create([
            'name' => 'élevée',
        ]);
    }
}
