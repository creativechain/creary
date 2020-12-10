<?php

namespace App\Jobs;

use App\Comments;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCommentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(object $data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        if ($this->data->type === 'comment') {
            $comment = Comments::query()
                ->where('permlink', $this->data->permlink)
                ->where('author', $this->data->author)
                ->first();

            if (!$comment) {
                $comment = new Comments();
            }

            $comment->applyData($this->data)
                ->save();
        }
    }
}
