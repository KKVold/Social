<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'admin'
            ],
            [
                'name' => 'member'
            ],
            [
                'name' => 'limited_access'
            ],
            [
                'name' => 'blocked'
            ]
        ];
        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }
    }
}
