<?php

namespace App\Console\Commands;

use App\Comments;
use App\Http\Crea\CrearyClient;
use App\Jobs\UpdateCommentJob;
use App\Utils\Obj;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class UpdateCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:comments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update comments';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $comments = Comments::query()
            ->get();

        $this->output->writeln("Updating " . $comments->count() . ' comments!');

        foreach ($comments as $c) {
            /** @var Comments $c */

            $this->output->writeln("Updating comment: " . $c->author  . "/" . $c->permlink);

            $cc = new CrearyClient();
            $content = $cc->getPost($c->author, $c->permlink);
            if ($content->parent_author === '') {
                $c->applyData($content);
            } else {
                $c->delete();
                $this->output->error("Deleted this comment");
            }


        }
        return 0;
    }
}
