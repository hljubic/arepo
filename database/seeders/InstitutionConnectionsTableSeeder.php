<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstitutionConnectionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('institution_connections')->delete();
        
        \DB::table('institution_connections')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'Djelatnik',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'Gost',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}