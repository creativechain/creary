<?php


namespace App\Utils;


use App\Http\Crea\CrearyClient;
use Carbon\Carbon;
use Illuminate\Support\Str;

class CreaOperationsUtils
{
    public static function parse($op) {
        $data = $op[1];
        $data->type = $op[0];
        return $data;
    }

    public static function vote($op) {
        $data = self::parse($op);
        $data->to = $data->author;
        $weight = intval($data->weight);

        //Notify only likes
        if ($weight > 0) {
            $content = (new CrearyClient())
                ->getPost($data->author, $data->permlink);
            $cashoutTime = Carbon::parse($content->cashout_time);
            if ($cashoutTime->isAfter(Carbon::now())) {
                $data->vote_value = CreaUtils::calculateVoteValue($data->voter, $data->weight);
            } else {
                $data->vote_value = 0;
            }

        }

        return $data;
    }

    public static function mention($op) {
        $data = self::parse($op);
        if ($data->body) {
            $results = array();
            preg_match_all('(@[\w\.\d-]+)', $data->body, $results);
            $data->mentions = $results[0];
        }

        return $data;
    }

    public static function comment($op) {
        $data = self::mention($op);
        if ($data->parent_author) {
            //Only return notification data for comments in publications
            $data->to = $data->parent_author;
            return $data;
        }

        return $data;
    }

    public static function comment_download($op) {
        $data = self::parse($op);
        if ($data->comment_author) {
            //Only return notification data for comments in publications
            $data->to = $data->comment_author;
            return $data;
        }

        return null;
    }


    public static function custom_json($op) {
        $data = self::parse($op);
        $json = json_decode($op[1]->json);
        $data->type = $json[0];

        switch ($data->type) {
            case 'reblog':
                $data->account = $json[1]->account;
                $data->author = $json[1]->author;
                $data->permlink = $json[1]->permlink;
                $data->to = $data->author;
                break;
            case 'follow':
                $data->follower = $json[1]->follower;
                $data->following = $json[1]->following;
                $data->what = $json[1]->what;
                $data->to = $data->following;
                if (count($data->what) <= 0) {
                    //Unfollow operation, no notify
                    return null;
                }
                break;
        }

        return $data;
    }

    /**
     * @param $op
     * @return mixed
     */
    public static function account_create($op) {
        $data = self::parse($op);

        return $data;
    }

    /**
     * @param $op
     * @return mixed
     */
    public static function account_update($op) {
        $data = self::parse($op);

        return $data;
    }
}
