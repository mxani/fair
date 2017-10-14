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
        
        for ($a = 0; $a<50; $a++) {
            $this->call('productSeeder');
        }
        
        echo "\n> ProductCategroy seeder";
		factory( App\Model\ProductCategory::class, 50 )->create();

        
    }
}
