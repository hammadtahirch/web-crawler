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
        Schema::create('avg_reports', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('site_link');
            $table->float('avg_page_load_time');
            $table->float('avg_title_length');
            $table->float('avg_world_count');
            $table->integer('crawled_pages');
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
        Schema::dropIfExists('avg_reports');
    }
};
