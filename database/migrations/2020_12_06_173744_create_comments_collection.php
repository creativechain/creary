<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsCollection extends \App\Database\MongoMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection($this->connection)
            ->create('comments', function (Blueprint $collection) {
                $collection->index(['permlink', 'author'], null, null, ['unique' => true]);
                $collection->index('title');
                $collection->index('license');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }

    public function performMigration()
    {
        // TODO: Implement performMigration() method.
    }
}
