<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ScienceFieldsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('science_fields')->delete();
        
        \DB::table('science_fields')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Tehničke znanosti',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Prirodne znanosti',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}