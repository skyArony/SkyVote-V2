<?php

use Illuminate\Database\Seeder;

class UserStableTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "admin",
            'email' => "sky.arony@qq.com",
            'password' => bcrypt('secret'),
        ]);
    }
}
