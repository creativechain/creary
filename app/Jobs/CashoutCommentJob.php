<?php

namespace App\Jobs;

use App\Comments;
use App\Http\Crea\CrearyClient;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CashoutCommentJob implements ShouldQueue
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
        $unpaidComments = Comments::query()
            ->where('is_paid', false)
            ->where('cashout_at', '>=', Carbon::now())
            ->get();

        $cc = new CrearyClient();
        Log::debug('Updating ' . $unpaidComments->count() . ' comments!');
        foreach ($unpaidComments as $c) {
            /** @var Comments $c */
            Log::debug('Updating comment ' . $c->author . '/' . $c->permlink);
            $commentData = $cc->getPost($c->author, $c->permlink);
            $c->applyData($commentData);
            Log::debug('Paid: ' . ($c->isPaid ? 'true' : 'false') . ', Cashout: ' . $c->cashout_at->toString());
        }
    }
}
