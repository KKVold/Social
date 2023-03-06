<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'name' => 'math',
                'discreption' => 'no thing'
            ]
        ];
        foreach ($tags as $tag) {
            DB::table('tags')->insert($tag);
        }
    }
}
