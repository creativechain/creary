<?php


namespace App\Http\Controllers\Api;


use App\CreaUser;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class NotificationController extends Controller
{

    /**
     * @param LengthAwarePaginator $userNotifications
     * @return array
     */
    public static function normalizeNotificationsReponse($userNotifications) {

        $notifications = $userNotifications->toArray();

        $data = array();
        foreach ($userNotifications as $notification) {
            $nData = $notification->data;
            unset($nData['required_auths']);
            unset($nData['required_posting_auths']);

            $nData['created_at'] = $notification->created_at->format('Y-m-d H:i:s');
            if ($notification->read_at) {
                $nData['read_at'] = $notification->read_at->format('Y-m-d H:i:s');
            }
            $data[] = $nData;
        }

        $notifications['data'] = $data;
        return $notifications;
    }

    /**
     * @param Request $request
     * @param $creaUser
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(Request $request, $creaUser) {

        $validations = [
            'page' => 'sometimes|min:1'
        ];

        $validatedData = Validator::make($request->all(), $validations);

        if ($validatedData->fails()) {
            return response()
                ->json([
                    'status' => 'error',
                    'message' => $validatedData->errors()->first(),
                    'error' => 'invalid_parameter'
                ], 400);
        }
        /**
         * @var CreaUser $creaUser
         */
        $creaUser = CreaUser::query()
            ->where('name', Str::replaceFirst('@', '', $creaUser))
            ->first();

        if ($creaUser) {
            $n = $creaUser->notifications()
                ->paginate(20);

            return response(self::normalizeNotificationsReponse($n));
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
            return response(self::normalizeNotificationsReponse($creaUser->unreadNotifications()->paginate(5)));
        }

        return response(array());

    }

    /**
     * @param Request $request
     * @param $creaUser
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function markRead(Request $request, $creaUser) {

        Log::debug("Mark all notificacions as read for user $creaUser");
        /**
         * @var CreaUser $creaUser
         */
        $creaUser = CreaUser::query()
            ->where('name', Str::replaceFirst('@', '', $creaUser))
            ->first();

        if ($creaUser) {
            $creaUser->unreadNotifications->markAsRead();
        }

        return response(array(
            'status' => 'ok',
            'message' => 'All notfications read'
        ));
    }
}
