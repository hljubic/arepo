<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('tags')->delete();

        \DB::table('tags')->insert(array (
            0 =>
            array (
                'id' => 1,
                'title' => 'Odlican',
                'category_id' => 1,
                'created_at' => '2021-02-05 13:26:37',
                'updated_at' => '2021-02-05 13:26:37',
            ),
            1 =>
            array (
                'id' => 2,
                'title' => 'Dobar',
                'category_id' => 1,
                'created_at' => '2021-02-05 13:26:45',
                'updated_at' => '2021-02-05 13:26:45',
            ),
        ));


    }
}
