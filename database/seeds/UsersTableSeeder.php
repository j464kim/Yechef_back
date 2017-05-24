<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'first_name' => 'Darion',
                'email' => 'admin@test.com',
                'password' => '$2y$10$VSWjeuoTXs5fiBhzto38reLqFVY2mB3T9GcmEe.naCk3hroA5iT7y',
                'remember_token' => NULL,
                'created_at' => '2017-05-24 04:03:52',
                'updated_at' => '2017-05-24 04:03:52',
                'last_name' => 'Mitchell',
                'phone' => '+1-862-643-1894',
            ),
        ));
        
        
    }
}