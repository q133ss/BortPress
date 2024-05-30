<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            'Рекламодатель' => 'advertiser',
            'Рекламная площадка' => 'adv_platform',
            'Админ' => 'admin'
        ];

        foreach ($roles as $name => $slug){
            Role::create(['name' => $name, 'slug' => $slug]);
        }
    }
}
