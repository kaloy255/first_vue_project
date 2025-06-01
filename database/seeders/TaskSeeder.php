<?php

namespace Database\Seeders;

use App\Models\Tasks;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignedUser = User::where('email', 'user@gmail.com')->first();
        $creator = User::where('email', 'admin@gmail.com')->first();

         Tasks::create([
            'name' => 'Design Homepage',
            'description' => 'Create a responsive homepage layout.',
            'assign_to' => $assignedUser->id,
            'created_by' => $creator->id,
            'status' => 'pending',
        ]);

        Tasks::create([
            'name' => 'API Integration',
            'description' => 'Integrate third-party API for payments.',
            'assign_to' => $assignedUser->id,
            'created_by' => $creator->id,
            'status' => 'inprogress',
        ]);

        Tasks::create([
            'name' => 'Bug Fix: Login Issue',
            'description' => 'Fix session bug that logs out users too early.',
            'assign_to' => $assignedUser->id,
            'created_by' => $creator->id,
            'status' => 'checking',
        ]);
    }
}
