<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroupAliasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->uuid('uuid')->index()->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('group_alias_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_alias_id')->constrained('group_aliases');
            $table->string('email');
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
        Schema::dropIfExists('group_alias_members');
        Schema::dropIfExists('group_aliases');
    }
}
