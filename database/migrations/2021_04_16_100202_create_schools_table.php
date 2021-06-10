<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('identifier');
            $table->string('identifier_no');
            $table->text('name');
            $table->text('email');
            $table->text('location');
            $table->text('postal_address');
            $table->text('url');
            $table->foreignId('institution_type_id')->constrained('institution_types');
            $table->text('uri_policy')->nullable();
            $table->text('postal_no')->nullable();
            $table->text('address')->nullable();
            $table->text('telephone')->nullable();
            $table->text('mobile_phone')->nullable();
            $table->text('fax')->nullable();
            $table->text('affiliation')->nullable();
            $table->text('dns')->nullable();
            $table->text('mx')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schools');
    }
}
