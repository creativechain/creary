<?php


namespace App\Utils;


class Obj
{

    public function __get($name)
    {
        try {
            return $this->{$name};
        } catch (\Exception $e) {

        }

        return null;
    }

    public static function isAssoc($arr)
    {
        if (!is_array($arr) || array() === $arr) {
            return false;
        }

        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * @param $obj
     * @return Obj|array|bool|int|string|null
     */
    public static function parse($obj) {
        if ($obj) {
            $newObj = new Obj();
            if (is_string($obj) || is_bool($obj) || is_numeric($obj) ) {
                return $obj;
            } else if (is_array($obj) && !self::isAssoc($obj)) {
                $newArr = [];
                foreach ($obj as $v) {
                    $newArr[] = self::parse($v);
                }

                return $newArr;
            } else if (is_object($obj)) {
                $props = get_object_vars($obj);
            } else {
                $props = $obj;
            }

            foreach ($props as $key => $value) {
                $newObj->{$key} = self::parse($value);
            }

            return $newObj;
        }

        return $obj;
    }
}
