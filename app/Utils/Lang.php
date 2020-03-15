<?php
/**
 * Created by PhpStorm.
 * User: ander
 * Date: 28/03/19
 * Time: 10:35
 */

namespace App\Utils;

use Illuminate\Support\Facades\Storage;

class Lang
{

    const LANG_DIR = __DIR__ . '/../../language/';

    /**
     * @return mixed
     */
    public static function getAvailableLangs() {
        $isoLangs = Storage::disk('public')->get('isolangs.json');
        return json_decode($isoLangs, true);
    }

    /**
     * @param string $lang
     * @return mixed
     */
    public static function getLang(string $lang = 'en') {
        $availableLangs = self::getAvailableLangs();

        if (!array_key_exists($lang, $availableLangs) || !$lang) {
            $lang = 'en';
        }

        $language = Storage::disk('local')->get("translations/$lang/lang.json");
        return json_decode($language);
    }
}
