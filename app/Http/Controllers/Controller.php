<?php

namespace App\Http\Controllers;

use App\Http\Crea\CrearyClient;
use App\Utils\Lang;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @return CrearyClient
     */
    protected function getCrearyClient() {
        return new CrearyClient();
    }

    protected function buildMeta($key, $keyValue, $content) {
        return array(
            'key' => $key,
            'keyValue' => $keyValue,
            'content' => $content
        );
    }

    /**
     * @param $cookie
     * @return mixed
     */
    protected function getCookie($cookie) {
        if (array_key_exists($cookie, $_COOKIE)) {
            return $_COOKIE[$cookie];
        }

        return null;
    }

    /**
     * @return null
     */
    protected function getProfileOfCookie() {
        $profileName = $this->getCookie('creary_username');
        if ($profileName) {
            $client = $this->getCrearyClient();
            $profile = $client->getAccount($profileName);

            return $profile;
        }

        return null;
    }

    /**
     * @return mixed
     */
    protected function getLanguage() {
        $profile = $this->getProfileOfCookie();

        if ($profile && array_key_exists('metadata', $profile) && array_key_exists('avatar', $profile['metadata'])) {
            $lang = $profile['metadata']['lang'];
        } else {
            $lang = $this->getCookie('creary_language');
            $lang = $lang ? $lang : 'en';
        }

        return Lang::getLang($lang);
    }
}
