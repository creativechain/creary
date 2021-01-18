<?php

namespace App\Jobs;

use App\Comments;
use App\Http\Crea\CrearyClient;
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
        if ($this->data->type === 'comment' && $this->data->parent_author === '' || $this->data->type === 'reblog') {
            $comment = Comments::query()
                ->where('permlink', $this->data->permlink)
                ->where('author', $this->data->author)
                ->first();

            if (!$comment) {
                $comment = new Comments();
            }

            $content = (new CrearyClient())
                ->getPost($this->data->author, $this->data->permlink);

            $comment->applyData($content)
                ->save();
        }
    }
}
