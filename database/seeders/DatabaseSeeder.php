<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\Config;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);

        $superAdmin = User::factory()->create([
            'name' => 'Braulio',
            'last_name' => 'Miramontes',
            'email' => 'braulio@felamedia.com',
            'password' => bcrypt('password'),
        ]);

        $superAdmin->assignRole('super_admin');

        //Create register config
        $config = new Config();
        $config->save();
    }
}
