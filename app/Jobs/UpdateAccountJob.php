<?php

namespace App\Jobs;

use App\Accounts;
use App\Http\Crea\CrearyClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateAccountJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $accountName;

    /**
     * Create a new job instance.
     *
     * @param string $accountName
     */
    public function __construct(string $accountName)
    {
        //
        $this->accountName = $accountName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {
            $client = new CrearyClient();
            $accountData = $client->getAccount($this->accountName, false);

            $account = Accounts::query()
                ->where('id', $accountData['id'])
                ->first();

            if (!$account) {
                Log::debug('Account ' . $this->accountName . ' not found. Creating registry...');
                $account = new Accounts();
            }

            $account->applyData($accountData)
                ->save();

            Log::debug('Account ' . $this->accountName . ' updated!');
        } catch (\Exception $e) {
            Log::error('Cannot update account ' . $this->accountName . ': ' . $e->getMessage(), $e->getTrace());
        }
    }
}
