<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTxHistoryCollection extends \App\Database\MongoMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->create('tx', function (Blueprint $table) {
                $table->index(['hash'], null, null, ['unique' => true]);
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
        Schema::dropIfExists('tx');
    }
}
