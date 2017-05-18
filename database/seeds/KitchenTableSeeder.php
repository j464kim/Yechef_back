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
            $u->medias()->save(factory(App\Models\Media::class)->make());
            $u->medias()->save(factory(App\Models\Media::class)->make());
            $u->medias()->save(factory(App\Models\Media::class)->make());
        });
    }
}
