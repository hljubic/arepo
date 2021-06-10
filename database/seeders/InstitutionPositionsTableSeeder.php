<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstitutionPositionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('institution_positions')->delete();
        
        \DB::table('institution_positions')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Dekan',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Direktor',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}