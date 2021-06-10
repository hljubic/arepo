<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfessionalStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('professional_statuses')->delete();
        
        \DB::table('professional_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'title' => 'DR',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'title' => 'KV',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}