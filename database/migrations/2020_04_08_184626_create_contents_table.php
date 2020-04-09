<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->string('permlink');
            $table->string('author');
            $table->string('title');
            $table->string('description');
            $table->integer('license');
            $table->string('tags');
            $table->string('hash')->nullable();
            $table->boolean('adult');
            $table->string('reblogged_by')->nullable();
            $table->timestamps();

            $table->primary(['permlink', 'author']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('content');
    }
}
