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
        Permission::query()->createOrFirst(['name' => 'live-support-request.manage',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'عرض طلبات الدعم']);
        Permission::query()->createOrFirst(['name' => 'live-support-request.add',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'اضافة طلبات الدعم']);
        Permission::query()->createOrFirst(['name' => 'live-support-request.edit',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'تعديل طلبات الدعم']);
        Permission::query()->createOrFirst(['name' => 'live-support-request.delete',], ['id' => (Permission::all()->max('id') + 1), 'display_name' => 'حذف طلبات الدعم']);

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
