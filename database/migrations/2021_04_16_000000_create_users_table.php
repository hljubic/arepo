<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('password')->nullable();
            $table->string('oib')->unique();
            $table->string('uid')->unique()->nullable();
            $table->integer('role_id');
            $table->foreignId('school_id')->nullable();
            // Temeljna povezanost s ustanovom
            $table->foreignId('basic_institution_connection_id')->nullable()->constrained('institution_connections');
            // # Povezanost s ustanovom - međutablica:

            //  Datum isteka temeljne povezanosti
            $table->date('basic_connection_expiration_date')->nullable();
            //  Pripadnost grupi
            $table->text('group_affiliation')->nullable();
            // Lokalni telefonski broj
            $table->string('phone_number')->nullable();
            // Broj mobilnog telefona
            $table->string('mobile_phone_number')->nullable();
            // Datum rođenja
            $table->date('birth_date')->nullable();
            // Spol
            $table->enum('sex', ['m', 'z', 'nepoznat', 'neodabran'])->nullable();
            // Stručni status
            $table->foreignId('professional_status_id')->nullable()->constrained();
            // Zvanje
            $table->foreignId('occupation_id')->nullable()->constrained();
            // Područje znanosti
            $table->foreignId('science_field_id')->nullable()->constrained();
            // Vrsta studenta
            $table->foreignId('student_type_id')->nullable()->constrained();
            // Položaj u ustanovi
            $table->foreignId('institution_position_id')->nullable()->constrained();
            // Uloga u ustanovi
            $table->text('institution_role')->nullable();
            // Vrsta posla u ustanovi:
            $table->text('institution_job_type')->nullable();
            // Organizacijska jedinica
            $table->text('organisational_unit')->nullable();
            // Broj sobe
            $table->text('room_number')->nullable();
            // Poštanski broj
            $table->string('postal_code')->nullable();
            // Ulica i kućni broj:
            $table->string('street_house_number')->nullable();
            // Kućna poštanska adresa
            $table->text('home_postal_address')->nullable();
            // Kućni telefonski broj
            $table->text('home_phone_number')->nullable();
            // Desktop uređaj
            $table->text('desktop_device')->nullable();
            // Oznaka privatnosti
            $table->text('privacy_label')->nullable();
            $table->boolean('locked')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
