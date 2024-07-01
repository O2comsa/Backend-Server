<?php

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('permissions')->whereNotNull('id')->delete();

        Permission::create(['id' => '1', 'name' => 'admins.manage', 'display_name' => 'عرض المسؤولين']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'admins.add', 'display_name' => 'اضافة المسؤولين']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'admins.edit', 'display_name' => 'تعديل المسؤولين']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'admins.delete', 'display_name' => 'حذف المسؤولين']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'transactions.manage', 'display_name' => 'عرض سجل المبيعات']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'users.manage', 'display_name' => 'عرض المستخدمين']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'users.add', 'display_name' => 'اضافة مستخدم']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'users.edit', 'display_name' => 'تعديل مستخدم']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'users.delete', 'display_name' => 'حذف المستخدمين']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'contactus.manage', 'display_name' => 'عرض رسائل تواصل معانا']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'pushNotifications.manage', 'display_name' => 'اداة الااشعارات العامه']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'permissions.manage', 'display_name' => 'عرض الصلاحيات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'permissions.edit', 'display_name' => 'تعديل الصلاحيات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'roles.manage', 'display_name' => 'عرض الادوار']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'roles.edit', 'display_name' => 'تعديل الدور']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'roles.add', 'display_name' => 'اضافة دور']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'roles.delete', 'display_name' => 'حذف دور']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'settings.manage', 'display_name' => 'الاعدادات']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'articles.manage', 'display_name' => 'عرض المقالات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'articles.add', 'display_name' => 'اضافة مقال']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'articles.edit', 'display_name' => 'تعديل مقال']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'articles.delete', 'display_name' => 'حذف مقال']);


        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'dictionaries.manage', 'display_name' => 'عرض القاموس']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'dictionaries.add', 'display_name' => 'اضافة قاموس']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'dictionaries.edit', 'display_name' => 'تعديل قاموس']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'dictionaries.delete', 'display_name' => 'حذف قاموس']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'courses.manage', 'display_name' => 'عرض الدورات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'courses.add', 'display_name' => 'اضافة الدورات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'courses.edit', 'display_name' => 'تعديل الدورات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'courses.delete', 'display_name' => 'حذف الدورات']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'lessons.manage', 'display_name' => 'عرض الدروس']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'lessons.add', 'display_name' => 'اضافة درس']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'lessons.edit', 'display_name' => 'تعديل درس']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'lessons.delete', 'display_name' => 'حذف درس']);

        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'banner.manage', 'display_name' => 'عرض البنرات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'banner.add', 'display_name' => 'اضافة بانر']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'banner.edit', 'display_name' => 'تعديل البنرات']);
        Permission::create(['id' => (Permission::all()->max('id') + 1), 'name' => 'banner.delete', 'display_name' => 'حذف البنرات']);


        foreach (Permission::all() as $row) {
            DB::table('permission_role')->insert(['permission_id' => $row->id, 'role_id' => 1]);
        }
    }
}
