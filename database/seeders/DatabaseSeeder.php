<?php

namespace Database\Seeders;

use App\Models\InstitutionType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StudentTypesTableSeeder::class);
        $this->call(ScienceFieldsTableSeeder::class);
        $this->call(ProfessionalStatusesTableSeeder::class);
        $this->call(OccupationsTableSeeder::class);
        $this->call(InstitutionRolesTableSeeder::class);
        $this->call(InstitutionPositionsTableSeeder::class);
        $this->call(InstitutionConnectionsTableSeeder::class);
        $this->call(InstitutionTypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SchoolsTableSeeder::class);
        $this->call(UserInstitutionConnectionTableSeeder::class);
    }
}
