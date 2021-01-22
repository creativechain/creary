<?php

namespace App\Jobs;

use App\Comments;
use App\Tags;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateActiveTagsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::debug('Updating active tags');
        $tagsIds = [];

        $activeComments = Comments::query()
            ->where('is_paid', false)
            ->get();

        Log::debug('Found ' . $activeComments->count() . ' active comments. Extracting tags ids...');

        foreach ($activeComments as $c) {
            /** @var Comments $c */
            $tagsIds = array_merge($tagsIds, $c->tags_ids);
        }

        $tags = Tags::query()
            ->whereIn('_id', $tagsIds)
            ->get();

        Log::debug('Found ' . $tags->count() . ' active tags. Updating...');

        foreach ($tags as $t) {
            /** @var Tags $t */
            $t->updateCounters();
            $t->save();
        }

        Log::debug('Updated active tags finished!');
    }
}
