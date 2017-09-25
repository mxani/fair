<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Model\ProductCategory::class, function(Faker $faker){
    static $cat_ids,$product_ids;

    $cat_ids = $cat_ids ?: DB::table('categories')->pluck('id');
    $product_ids = $product_ids ?: DB::table('products')->pluck('id');


    return[
        'cat_id' => $cat_ids[rand(1,count($cat_ids)-1)],
        'product_id' => $product_ids[rand(1,count($product_ids)-1)],
    ];
});

