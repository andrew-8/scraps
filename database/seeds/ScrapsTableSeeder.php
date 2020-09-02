<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScrapsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('scraps')->insert([
            'title' => Str::random(10),
            'text' => Str::random(10).'@gmail.com',
            'user_id' => User::latest()->first(),
            'publish' => 1,
            'private' => 0,
            'share_user_ids' => null,
            'created_at' => \Illuminate\Support\Facades\Date::now(),
            'updated_at' => \Illuminate\Support\Facades\Date::now()
        ]);
    }
}
