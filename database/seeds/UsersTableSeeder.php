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
                'first_name' => 'admin',
				'last_name' => 'Kim',
				'phone' => '+1-862-643-1894',
				'email' => 'admin@test.com',
                'password' => '$2y$10$VSWjeuoTXs5fiBhzto38reLqFVY2mB3T9GcmEe.naCk3hroA5iT7y',
                'remember_token' => NULL,
                'verified' => '1',
				'show_phone' => '1',
				'show_subscription' => '1',
				'show_forks' => '1',
				'created_at' => '2017-05-24 04:03:52',
				'updated_at' => '2017-05-24 04:03:52',
            ),
        ));

		factory(App\Models\User::class, 10)->create();
        
    }
}