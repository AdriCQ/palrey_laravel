<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
	private	$BASE_PERMISSIONS = ['list', 'get'];
	private	$ADVANCE_PERMISSIONS = ['create', 'update', 'destroy'];
	private	$ALL_PERMISSIONS = ['list', 'get', 'create', 'update', 'destroy'];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		app()[PermissionRegistrar::class]->forgetCachedPermissions();

		/**
		 * -----------------------------------------
		 *	Roles
		 * -----------------------------------------
		 */

		// Developer
		$develper = Role::create(['name' => 'Developer']);
		// Admin
		$admin = Role::create(['name' => 'Admin']);
		// Guest
		$guest = Role::create(['name' => 'Guest']);
		// Vendor
		$vendor = Role::create(['name' => 'Vendor']);

		/**
		 * -----------------------------------------
		 *	Grant Permissions
		 * -----------------------------------------
		 */

		/**
		 * Products
		 */
		$PERMISSION_NAME = 'products';
		$ROLES = [$develper, $guest, $vendor, $admin];
		$this->givePermissions($PERMISSION_NAME, $ROLES, $this->BASE_PERMISSIONS);

		$ROLES = [$develper, $vendor, $admin];
		$this->givePermissions($PERMISSION_NAME, $ROLES, $this->ADVANCE_PERMISSIONS);

		$ROLES = [$develper, $guest];
		$this->givePermissions($PERMISSION_NAME, $ROLES, ['buy']);

		/**
		 * Users
		 */
		$PERMISSION_NAME = 'users';
		$ROLES = [$develper, $admin];
		$this->givePermissions($PERMISSION_NAME, $ROLES, $this->ALL_PERMISSIONS);
	}

	/**
	 * 
	 */
	private function givePermissions($PERMISSION_NAME = '', $ROLES = [], $PERMISSIONS = [])
	{
		foreach ($ROLES as $userType) {
			foreach ($PERMISSIONS as $permission) {
				if (!Permission::query()->where('name', $PERMISSION_NAME . '.' . $permission)->exists())
					Permission::create(['name' => $PERMISSION_NAME . '.' . $permission]);
				// Grant Permission
				$userType->givePermissionTo($PERMISSION_NAME . '.' . $permission);
			}
		}
	}
}
