<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::query()->createOrFirst(['name' => 'certificates.manage',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'عرض الشهادات']);
        Permission::query()->createOrFirst(['name' => 'certificates.add',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'اضافة شهادة']);
        Permission::query()->createOrFirst(['name' => 'certificates.edit',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'تعديل شهادة']);
        Permission::query()->createOrFirst(['name' => 'certificates.delete',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'حذف شهادة']);

        Permission::query()->createOrFirst(['name' => 'live-event.manage',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'عرض دورة مباشرة']);
        Permission::query()->createOrFirst(['name' => 'live-event.add',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'اضافة دورة مباشرة']);
        Permission::query()->createOrFirst(['name' => 'live-event.edit',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'تعديل دورة مباشرة']);
        Permission::query()->createOrFirst(['name' => 'live-event.delete',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'حذف دورة مباشرة']);

        Permission::query()->createOrFirst(['name' => 'plans.manage',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'عرض باقة الدعم الفني']);
        Permission::query()->createOrFirst(['name' => 'plans.add',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'اضافة باقة الدعم الفني']);
        Permission::query()->createOrFirst(['name' => 'plans.edit',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'تعديل باقة الدعم الفني']);
        Permission::query()->createOrFirst(['name' => 'plans.delete',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'حذف باقة الدعم الفني']);


        foreach (Permission::all() as $row) {
            DB::table('permission_role')->insertOrIgnore(['permission_id' => $row->id, 'role_id' => 1]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
