<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear All table
        DB::table('admins')->whereNotNull('id')->delete();
        DB::table('role_user')->whereNotNull('role_id')->delete();
        $user = \App\Models\Admin::create([
            'id' => '1',
            'name' => 'المشرف الاساسي',
            'email' => 'info@o2.com.sa',
            'password' => bcrypt('123456'),
        ]);
        $user->attachRole('super_admin');
    }
}
