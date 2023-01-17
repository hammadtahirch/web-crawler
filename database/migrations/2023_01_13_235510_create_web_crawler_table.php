<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('page_link');
            $table->integer('status_code');
            $table->integer('images_links');
            $table->integer('internal_links');
            $table->integer('external_links');
            $table->float('page_load_time');
            $table->integer('word_count');
            $table->integer('title_length');
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
        Schema::dropIfExists('reports');
    }
};
