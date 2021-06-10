<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserInstitutionConnectionTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_institution_connection')->delete();
        
        \DB::table('user_institution_connection')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 1,
                'institution_connection_id' => 2,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}