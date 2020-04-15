<?php


namespace App\Utils;


class CreaUtils
{
    const DEFAULT_AVATAR = ['/img/avatar/avatar1.png', '/img/avatar/avatar2.png', '/img/avatar/avatar3.png', '/img/avatar/avatar4.png', '/img/avatar/avatar5.png'];

    public static function getDefaultAvatar($username) {
        $hexStr = bin2hex($username);
        $i = hexdec($hexStr);

        $avatar = $i % count(self::DEFAULT_AVATAR);
        $avatar = self::DEFAULT_AVATAR[$avatar];

        return $avatar;
    }
}
