<?php

namespace Chatt;

class Safe {

    static public function san($data) {
        if (is_array($data)) {
            $arr = array();
            foreach ($data as $var => $val) {
                $arr[preg_replace('#[^a-z0-9_]#i', '_', trim($var))] = htmlspecialchars(nl2br(trim($val)));
            }
            return $arr;
        } else {
            return nl2br(htmlspecialchars(trim($data)));
        }
    }

    static public function par($s) {
        $allowed = array(
            '<b>',
            '</b>',
            '<i>',
            '</i>',
            '<br>',
            '<br />'
        );
        $replace = array();
        foreach ($allowed as $str)
            $replace[] = self::san($str);
        $s = str_ireplace($replace, $allowed, $s);
        if (stristr($s, '<b>') && !stristr($s, '</b>'))
            $s .= '</b>';
        elseif (stristr($s, '<i>') && !stristr($s, '</i>'))
            $s .= '</i>';
        return $s;
    }

    static public function format($color, $login, $time, $msg) {
        return '<p style="color: #' . $color . '"><b>' . $login . '</b> ' . date('H:i:s j.m.Y', $time) . ': ' . $msg . '</p>';
    }

    public static function khash($data) {
        static $map = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $hash = crc32($data) + 0x100000000;
        $str = "";
        do {
            $str = $map[31 + ($hash % 31)] . $str;
            $hash /= 31;
        } while ($hash >= 1);

        return $str;
    }

    public static function makeColor() {
        return dechex(mt_rand(0, 13)) .
                dechex(mt_rand(0, 13)) .
                dechex(mt_rand(0, 13)) .
                dechex(mt_rand(0, 13)) .
                dechex(mt_rand(0, 13)) .
                dechex(mt_rand(0, 13));
    }

}
