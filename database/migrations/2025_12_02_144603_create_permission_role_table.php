<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id');
            $table->foreignId('role_id');
        });

        $rolAdmin = Role::find(1);
        $perms = Permission::all();
        $rolAdmin->permissions()->sync($perms);
        $rolAdmin->update(['name' => 'Super Admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
