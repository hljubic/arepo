<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('schools')->delete();

        \DB::table('schools')->insert(array (
            0 =>
            array (
                'id' => 1,
                'identifier' => 'test-os-sumba',
                'identifier_no' => 'MZOS_SIFRA: 234-56-789',
                'name' => 'TEST- Osnovna škola Sumba',
                'email' => 'ured@test-os-sumba.skole.hr',
                'location' => 'Mostar',
                'postal_address' => 'Nikole Šubića Zrinjskog',
                'url' => 'http://www.test-os-sumba.skole.hr/',
                'uri_policy' => 'www.skole.hr/policy.html',
                'postal_no' => '88000',
                'address' => NULL,
                'telephone' => NULL,
                'mobile_phone' => NULL,
                'fax' => NULL,
                'affiliation' => NULL,
                'admin_id' => 2,
                'institution_type_id' => 1,
                'created_at' => NULL,
                'updated_at' => NULL,
                'deleted_at' => NULL,
            ),
        ));


    }
}
