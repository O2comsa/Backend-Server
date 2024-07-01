<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('roles')->whereNotNull('id')->delete();
        \App\Models\Role::create(['id' => 1, 'name' => 'super_admin', 'display_name' => 'المسؤل العام']);
    }
}
