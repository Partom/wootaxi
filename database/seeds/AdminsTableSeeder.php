<?php

use Illuminate\Database\Seeder;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->truncate();
        DB::table('admins')->insert([
            'name' => 'WooCabs',
            'email' => 'contact@WooCabs.com',
            'password' => bcrypt('newnew123'),
        ]);
    }
}
