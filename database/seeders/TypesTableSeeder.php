<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('types')->delete();
        
        \DB::table('types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'International',
                'created_at' => '2021-02-04 12:25:31',
                'updated_at' => '2021-02-04 12:25:31',
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'International 3',
                'created_at' => '2021-02-04 12:36:49',
                'updated_at' => '2021-02-04 12:36:49',
            ),
        ));
        
        
    }
}