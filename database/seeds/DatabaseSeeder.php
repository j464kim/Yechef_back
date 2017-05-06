<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
  		Model::unguard();

        \DB::table('dishes')->delete();
        \DB::table('kitchens')->delete();
        \DB::table('media')->delete();


        $this->call(DishTableSeeder::class);
        $this->call(KitchenTableSeeder::class);

        Model::reguard();
    }
}
