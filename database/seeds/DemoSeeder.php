<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cards')->truncate();
        DB::table('promocodes')->truncate();
        DB::table('promocode_usages')->truncate();
        DB::table('provider_devices')->truncate();
        DB::table('provider_documents')->truncate();
        DB::table('provider_profiles')->truncate();
        DB::table('provider_services')->truncate();
        DB::table('request_filters')->truncate();
        DB::table('user_request_payments')->truncate();
        DB::table('user_request_ratings')->truncate();
        DB::table('user_requests')->truncate();
        DB::table('users')->truncate();
        DB::table('users')->insert([[
            'first_name' => 'Hepto',
            'last_name' => 'Demo',
            'email' => 'demo@hepto.com',
            'password' => bcrypt('123456'),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'picture' => 'http://lorempixel.com/512/512/business/?34733',
        ]]);
    }
}
