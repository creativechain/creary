<?php

namespace App\Console\Commands;

use App\Accounts;
use App\Contents;
use App\Http\Crea\CrearyClient;
use Illuminate\Console\Command;

class UpdateAccountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:account {account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Crea Account data.';

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

        $accountName = $this->argument('account');


        $client = new CrearyClient();
        $accountData = $client->getAccount($accountName, false);

        dd($accountData);
        $account = Accounts::query()
            ->where('name', $accountName)
            ->first();

        if (!$account) {
            $account = new Accounts();
        }

        $account->applyData($accountData)
            ->save();
    }
}
