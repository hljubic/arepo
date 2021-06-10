<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OccupationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('occupations')->delete();
        
        \DB::table('occupations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Asistent',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Docent',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}