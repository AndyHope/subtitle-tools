<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupJobMetasTable extends Migration
{
    public function up()
    {
        Schema::create('sup_job_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sup_job_id')->unsigned();
            $table->unsignedInteger('file_size');
            $table->string('format')->nullable();
            $table->integer('cue_count')->nullable();
            $table->boolean('failed_to_open')->nullable();
            $table->timestamps();

            $table->foreign('sup_job_id')->references('id')->on('sup_jobs')->onDelete('cascade');
        });
    }
}
