<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RestaurantsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('restaurants')->delete();
        
        \DB::table('restaurants')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Karting restoran',
                'address' => 'Nova adresa bb',
                'longitude' => '5.0000000',
                'latitude' => '13.1441000',
                'menu' => NULL,
                'description' => NULL,
                'reviews_grade' => '0.00',
                'reviews_count' => 0,
                'created_at' => '2021-02-01 12:39:35',
                'updated_at' => '2021-02-01 12:39:35',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Restoran Karting',
                'address' => 'Ante Star',
                'longitude' => '5.0000000',
                'latitude' => '6.0000000',
                'menu' => NULL,
                'description' => NULL,
                'reviews_grade' => '0.00',
                'reviews_count' => 0,
                'created_at' => '2021-02-04 11:58:36',
                'updated_at' => '2021-02-04 12:05:38',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}