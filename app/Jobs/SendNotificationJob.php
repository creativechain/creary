<?php

namespace App\Jobs;

use App\CreaUser;
use App\Notifications\CrearyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;

    /**
     * Create a new job instance.
     *
     * @param object $data
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
        if ($this->data && property_exists($this->data, 'to')) {
            if ($this->data->type === 'comment') {

                $dataCloned = clone $this->data;
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

            $notification = new CrearyNotification($this->data);
            $cUser = CreaUser::query()
                ->updateOrCreate(['name' => $this->data->to], ['name' => $this->data->to]);

            $cUser->save();
            Notification::send($cUser, $notification);
        }
    }
}
