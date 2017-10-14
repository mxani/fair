<?php

use Illuminate\Database\Seeder;

class productSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        
        $faker = \Faker\Factory::create('fa_IR');

            $rand = rand(1, 4);
            for ($i = 0; $i < $rand; $i++) {
                $selected[$i]=$faker->imageurl;
            }
            $selected = json_encode($selected, JSON_FORCE_OBJECT);
            
            DB::table('products')->insert([
            'title' => $faker->word,
            'description' => $faker->paragraph,
            'files' => $selected,
            'price' => mt_rand(1000, 9000),
            'status' => true,
            ]);

    }
}
