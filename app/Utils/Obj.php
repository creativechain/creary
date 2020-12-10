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

    public static function parse($obj) {
        $newObj = new Obj();

        if ($obj) {
            if (is_object($obj)) {
                $props = get_object_vars($obj);
            } else {
                $props = $obj;
            }
            foreach ($props as $key => $value) {
                if (is_object($value) || is_array($value)) {
                    $newObj->{$key} = self::parse($value);
                } else {
                    $newObj->{$key} = $value;

                }
            }
        }

        return $newObj;
    }
}
