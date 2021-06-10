<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InstitutionRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('institution_roles')->delete();
        
        \DB::table('institution_roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'ICT koordinator',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'ISVU koordinator',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}