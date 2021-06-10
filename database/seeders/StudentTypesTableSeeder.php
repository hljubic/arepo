<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('student_types')->delete();
        
        \DB::table('student_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Osnovnoškolac',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Prediplomac',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}