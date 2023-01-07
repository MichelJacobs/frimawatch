<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'name' => '管理者',
            'email' => 'testadmin@gmail.com',
            'password' => Hash::make('password'),
            'license' => Str::random(20),
            'is_admin' => 1,
            'active' => 1,
        ]);
        DB::table('users')->insert([
            'name' => 'テスト ユーザー',
            'email' => 'testuser@gmail.com',
            'password' => Hash::make('password'),
            'license' => Str::random(20),
            'is_admin' => 0,
            'active' => 1,
        ]);
    }
}
