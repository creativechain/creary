<?php

namespace App\Console\Commands;

use App\Comments;
use App\Http\Crea\CrearyClient;
use App\Jobs\UpdateCommentJob;
use Illuminate\Console\Command;

class UpdateCommentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:comments {--p|page=}';

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
        $page = $this->hasOption('page');
        if ($page) {
            $page = intval($this->option('page'));
        }


        if (!$page) {
            $page = 1;
        }

        $limit = 100;


        do {
            $offset = ($page * $limit) - $limit;

            $this->output->success("Comments page: $page");
            $comments = Comments::query()
                ->orderBy('created_at')
                ->offset($offset)
                ->limit($limit)
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
            $page++;
        } while (!$comments->isEmpty());

        return 0;
    }
}
