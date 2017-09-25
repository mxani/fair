<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        echo "\n> Category seeder";
        factory( App\Model\Category::class, 15 )->create();
        
        echo "\n> Product seeder";
		factory( App\Model\Product::class, 50 )->create();
        
        echo "\n> ProductCategroy seeder";
		factory( App\Model\ProductCategory::class, 50 )->create();

    }
}
