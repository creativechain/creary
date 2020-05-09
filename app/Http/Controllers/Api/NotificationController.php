<?php


namespace App\Http\Controllers\Api;


use App\CreaUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotificationController extends Controller
{

    /**
     * @param $userNotifications
     * @return array
     */
    public static function normalizeNotificationsReponse($userNotifications) {
        $notifications = array();

        foreach ($userNotifications as $notification) {
            $nData = $notification->data;
            unset($nData['required_auths']);
            unset($nData['required_posting_auths']);

            $nData['created_at'] = $notification->created_at->format('Y-m-d H:i:s');
            if ($notification->read_at) {
                $nData['read_at'] = $notification->read_at->format('Y-m-d H:i:s');

            }
            $notifications[] = $nData;
        }

        return $notifications;
    }

    /**
     * @param Request $request
     * @param $creaUser
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request, $creaUser) {

        /**
         * @var CreaUser $creaUser
         */
        $creaUser = CreaUser::query()
            ->where('name', Str::replaceFirst('@', '', $creaUser))
            ->first();

        if ($creaUser) {
            return response(self::normalizeNotificationsReponse($creaUser->unreadNotifications));
        }

        return response(array());
    }

    /**
     * @param Request $request
     * @param $creaUser
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function unread(Request $request, $creaUser) {

        /**
         * @var CreaUser $creaUser
         */
        $creaUser = CreaUser::query()
            ->where('name', Str::replaceFirst('@', '', $creaUser))
            ->first();

        if ($creaUser) {
            return response(self::normalizeNotificationsReponse($creaUser->unreadNotifications));
        }

        return response(array());

    }

    /**
     * @param Request $request
     * @param $creaUser
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function markRead(Request $request, $creaUser) {
        /**
         * @var CreaUser $creaUser
         */
        $creaUser = CreaUser::query()
            ->where('name', Str::replaceFirst('@', '', $creaUser))
            ->first();

        $creaUser->unreadNotifications->markAsRead();
        return response(array(
            'status' => 'ok',
            'message' => 'All notfications read'
        ));
    }
}
