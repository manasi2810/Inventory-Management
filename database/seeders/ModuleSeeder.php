<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $modules = ['Product', 'User', 'Order'];
        foreach ($modules as $module) {
            Module::firstOrCreate(['name' => $module]);
        }
    }
}
