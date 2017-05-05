<?php

use Illuminate\Database\Seeder;

class KitchenTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Kitchen::class, 10)->create()->each(function ($u) {
            $u->media()->save(factory(App\Models\Media::class)->make());
            $u->media()->save(factory(App\Models\Media::class)->make());
            $u->media()->save(factory(App\Models\Media::class)->make());
        });
    }
}
