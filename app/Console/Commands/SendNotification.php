<?php

namespace App\Console\Commands;

use App\CreaUser;
use App\Http\Crea\CrearyClient;
use App\Notifications\CrearyNotification;
use App\Utils\CreaOperationsUtils;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crea:notify {block}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify operations of a given block';

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
                    $data = CreaOperationsUtils::{$op[0]}($op);

                    if ($data) {
                        if ($op[0] === 'comment') {
                            $dataCloned = clone $data;
                            $dataCloned->type = 'mention';

                            foreach ($dataCloned->mentions as $userMentioned) {
                                $userMentioned = Str::replaceFirst('@', '', $userMentioned);
                                $dataCloned->to = $userMentioned;
                                $notification = new CrearyNotification($dataCloned);
                                $cUser = CreaUser::query()
                                    ->updateOrCreate(['name' => $userMentioned], ['name' => $userMentioned]);

                                $cUser->save();
                                Notification::send($cUser, $notification);
                            }
                        }

                        $data->timestamp = $timestamp;
                        $notification = new CrearyNotification($data);
                        $cUser = CreaUser::query()
                            ->updateOrCreate(['name' => $data->to], ['name' => $data->to]);

                        $cUser->save();
                        Notification::send($cUser, $notification);
                    }
                }
            }
        }
    }
}
