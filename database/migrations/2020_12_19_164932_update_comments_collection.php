<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentsCollection extends \App\Database\MongoMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::connection($this->connection)
            ->table('comments', function (Blueprint $collection) {
                $collection->string('author_reward');
                $collection->string('curator_reward');
                $collection->string('download')->nullable();
                $collection->float('reward')->default(0.0);
                $collection->boolean('is_paid')->default(false);
                $collection->timestamp('cashout_at');
                $collection->json('reblogs');
                $collection->index(['title', 'description', 'download', 'is_paid', 'reward', 'license']);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
