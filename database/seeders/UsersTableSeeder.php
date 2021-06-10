<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 1,
                'first_name' => 'Superadmin',
                'last_name' => 'Test',
                'email' => 'superadmin.test@webmail.sumit.carnet.hr',
                'email_verified_at' => '2021-04-15 12:19:02',
                'username' => 'korisnik',
                'password' => '$2y$10$z.8WKXeixxOoBc.UyQd2LekHGdjmWf6LkjwVopFRe5LG7KdG3/MpS',
                'oib' => '12345678909',
                'uid' => 'superadmin.test',
                'role_id' => 1,
                'school_id' => NULL,
                'basic_institution_connection_id' => 1,
                'basic_connection_expiration_date' => '2021-04-15',
                'group_affiliation' => 'Grupa',
                'phone_number' => '063123456',
                'mobile_phone_number' => '063222333',
                'birth_date' => '1995-04-15',
                'sex' => 'm',
                'professional_status_id' => 1,
                'occupation_id' => 1,
                'science_field_id' => 1,
                'student_type_id' => 1,
                'institution_position_id' => 1,
                'institution_role' => 'Uloga',
                'institution_job_type' => 'Vrsta',
                'organisational_unit' => 'Jedinica',
                'room_number' => '11',
                'postal_code' => '10000',
                'street_house_number' => '24',
                'home_postal_address' => 'Adresa Kućna',
                'home_phone_number' => '033123456',
                'desktop_device' => 'Windows',
                'privacy_label' => 'ABC',
                'remember_token' => NULL,
                'created_at' => '2021-04-15 12:20:31',
                'updated_at' => '2021-04-15 12:20:32',
            ),
            1 =>
            array (
                'id' => 2,
                'first_name' => 'Admin',
                'last_name' => 'Test',
                'email' => 'admin.test@webmail.sumit.carnet.hr',
                'email_verified_at' => '2021-04-15 12:19:02',
                'username' => 'test',
                'password' => '$2y$10$z.8WKXeixxOoBc.UyQd2LekHGdjmWf6LkjwVopFRe5LG7KdG3/MpS',
                'oib' => '22345678909',
                'uid' => 'admin.testas',
                'role_id' => 2,
                'school_id' => 1,
                'basic_institution_connection_id' => 2,
                'basic_connection_expiration_date' => '2022-04-15',
                'group_affiliation' => 'Grupa 2',
                'phone_number' => '063123456',
                'mobile_phone_number' => '063222334',
                'birth_date' => '1992-04-15',
                'sex' => 'z',
                'professional_status_id' => 2,
                'occupation_id' => 2,
                'science_field_id' => 2,
                'student_type_id' => 2,
                'institution_position_id' => 1,
                'institution_role' => 'Uloga 2',
                'institution_job_type' => 'Kategorija',
                'organisational_unit' => 'Dvica',
                'room_number' => '22',
                'postal_code' => '10000',
                'street_house_number' => '25',
                'home_postal_address' => 'Kućna adresa',
                'home_phone_number' => '033123456',
                'desktop_device' => 'Linux',
                'privacy_label' => 'ABCD',
                'remember_token' => NULL,
                'created_at' => '2021-04-15 12:20:31',
                'updated_at' => '2021-04-15 12:20:32',
            ),
        ));


    }
}
