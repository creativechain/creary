<?php

namespace App\Console\Commands;

use App\CreaUser;
use App\Http\Crea\CrearyClient;
use App\Jobs\SendNotificationJob;
use App\Jobs\UpdateAccountJob;
use App\Jobs\UpdateCommentJob;
use App\Notifications\CrearyNotification;
use App\Utils\CreaOperationsUtils;
use App\Utils\Obj;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class ScanBlockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:scan {block}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan operations of a given block';

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
        $blockHeight = $this->argument('block');
        $creaClient = new CrearyClient();
        $block = $creaClient->getBlock($blockHeight);

        if ($block) {
            $timestamp = Carbon::parse($block->timestamp);
            $txs = $block->transactions;

            foreach ($txs as $tx) {

                $ops = $tx->operations;
                foreach ($ops as $op) {
                    try {
                        $op_name = $op[0];
                        $data = CreaOperationsUtils::{$op_name}($op);
                        $data->timestamp = $timestamp;

                        switch ($data->type) {
                            case 'vote':
                            case 'comment':
                                UpdateCommentJob::dispatch($data);
                            case 'comment_download':
                            case 'reblog':
                            case 'follow':
                                $notInitialDate = Carbon::parse(env('CREA_NOTIFICATION_INITIAL_DATE'));
                                if ($timestamp->isAfter($notInitialDate)) {
                                    SendNotificationJob::dispatch($data);
                                }
                                break;
                            case 'account_create':
                                UpdateAccountJob::dispatch($data->new_account_name);
                                break;
                            case 'account_update':
                                UpdateAccountJob::dispatch($data->account);
                                break;
                        }
                    } catch (\Throwable $e) {
                        Log::error('Error execution: ' . $e->getMessage(), $e->getTrace());
                    }
                }
            }
        }
    }
}
