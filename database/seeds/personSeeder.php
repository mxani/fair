<?php

use Illuminate\Database\Seeder;

class personSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 50; $i ++) {
            $detail = ['username'=>$faker->userName,'last_name'=>$faker->lastName,'first_name'=>$faker->firstName];
            $datatime = $faker->dateTimeBetween('-30 days', 'now');

            DB::table('people')->insert([
            'telegramID' => rand(111111111, 999999999),
            'detail' => json_encode($detail, JSON_FORCE_OBJECT),
            'type' => 'guest',
            'status' => 'limit',
            'created_at' => $datatime,
            'updated_at' => $datatime,
            ]);
        }
    }
}
