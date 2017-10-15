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
       
        for ($p = 0; $p<50; $p++) {
            
            if($p < 2){
                $type = 'page';
                $title = $p < 1 ? 'درباره ما' : 'تماس با ما';
            }else{
                $type = 'blog';
                $title = $faker->title;
            }
            
            DB::table('posts')->insert([
                'title' => $title,
                'thumb' => $faker->imageurl,
                'content' => $faker->realtext,
                'type' => $type,
                'status' => true,
            ]);
        }
        
    }
}
