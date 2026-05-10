<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editor', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'author', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'user', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('roles')->insertOrIgnore($roles);

        $permissions = [
            ['name' => 'posts.create', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'posts.edit', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'posts.delete', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'posts.publish', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'categories.create', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'categories.edit', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'categories.delete', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'users.manage', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('permissions')->insertOrIgnore($permissions);

        // Assign all permissions to admin role
        $adminRole = DB::table('roles')->where('name', 'admin')->first();
        $allPermissions = DB::table('permissions')->pluck('id');
        
        foreach ($allPermissions as $permissionId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'permission_id' => $permissionId,
                'role_id' => $adminRole->id,
            ]);
        }
    }
}
