<?php

namespace AppBundle\Util;

abstract class Bitwise
{
    public const COOKIE_INFO = array(
        'ambiguss' => 1,
        'facebook' => 2,
        'twitter' => 4,
        'google' => 8
    );

    public static function calcul($const, array $bits)
    {
        $result = 0;

        foreach ($bits as $bit) {
            $result += array_key_exists($bit, constant('self::' . $const)) ? constant('self::' . $const)[$bit] : 0;
        }

        return $result;
    }

    public static function isSet($const, $cookie, $service)
    {
        return $cookie && ($cookie & constant('self::' . $const)[$service]);
    }
}
