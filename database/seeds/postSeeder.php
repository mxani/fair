<?php

use Illuminate\Database\Seeder;

class postSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create('fa_IR');
        $types =['blog','page'];            
        DB::table('posts')->insert([
            'title' => $faker->word,
            'thumb' => $faker->imageurl,
            'content' => $faker->paragraph,
            'type' => $types[rand(0,count($types)-1)],
            'status' => true,
        ]);
    }
}
