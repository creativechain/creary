<?php

namespace App\Console\Commands;

use App\Contents;
use App\Http\Crea\CrearyClient;
use Illuminate\Console\Command;

class AddContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:content {author} {permlink}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index Blockchain content in DB.';

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


        $client = new CrearyClient();
        $post = $client->getPost($author, $permlink);

        $storedContent = Contents::query()
            ->where('author', $author)
            ->where('permlink', $permlink)
            ->first();

        if (!$storedContent) {
            $storedContent = new Contents();
        }

        $storedContent->applyData($post);
    }
}
