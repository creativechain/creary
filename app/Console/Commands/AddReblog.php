<?php

namespace App\Console\Commands;

use App\Contents;
use App\Http\Crea\CrearyClient;
use Illuminate\Console\Command;

class AddReblog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:reblog {author} {permlink} {reblog}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add reblogs to post';

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
     * @return mixed
     */
    public function handle()
    {
        //

        $author = $this->argument('author');
        $permlink = $this->argument('permlink');
        $reblogUser = $this->argument('reblog');

        $content = Contents::query()
            ->where('permlink', $permlink)
            ->where('author', $author)
            ->first();

        if (!$content) {
            $client = new CrearyClient();
            $post = $client->getPost($author, $permlink);

            $content = new Contents();
            $content->applyData($post);
        }

        $content->addReblog($reblogUser);
        $content->save();

    }
}
