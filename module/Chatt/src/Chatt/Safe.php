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

    static public function mys($data) {
        $db = ChatDB::getInstance();
        return $db->real_escape_string($data);
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

}
