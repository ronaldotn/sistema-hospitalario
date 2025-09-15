<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $doctorsRole = Role::create(['name' => 'doctors']);
        $patientRole = Role::create(['name' => 'patient']);

        // Create permissions
        $readPatientPermission = Permission::create(['name' => 'read patient']);
        $createPatientPermission = Permission::create(['name' => 'create patient']);
        $editPatientPermission = Permission::create(['name' => 'edit patient']);
        $deletePatientPermission = Permission::create(['name' => 'delete patient']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($readPatientPermission);
        $adminRole->givePermissionTo($createPatientPermission);
        $adminRole->givePermissionTo($editPatientPermission);
        $adminRole->givePermissionTo($deletePatientPermission);

        $doctorsRole->givePermissionTo($createPatientPermission);
        $doctorsRole->givePermissionTo($editPatientPermission);

        $patientRole->givePermissionTo($editPatientPermission);
        $patientRole->givePermissionTo($deletePatientPermission);

    }
}
