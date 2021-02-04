<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpHistoryCollection extends \App\Database\MongoMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->create('op', function (Blueprint $table) {
                $table->index(['type'], null, null, ['unique' => false]);
                $table->index(['timestamp'], null, null, ['unique' => false]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('op');
    }
}
