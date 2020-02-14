<?php


namespace Helper;


class HelperSession
{
    /**
     * 获取session某个值
     *
     * @param string $key 存储在session数据中的key，默认不传获取所有数据
     *                    获取层级session值用“.”分割，如：userinfo.id;
     *                    session数据层级限制为5层；
     *
     * @return mixed
     */
    public static function get($key = '')
    {
        if (!$key) {
            return isset($_SESSION) ? $_SESSION : [];
        }
        $keys = explode('.', $key);
        $level = count($keys);
        if ($level > 5) {
            return null;
        }
        switch ($level) {
            case 2:
                list($a, $b) = $keys;
                return isset($_SESSION[$a][$b]) ? $_SESSION[$a][$b] : null;
            case 3:
                list($a, $b, $c) = $keys;
                return isset($_SESSION[$a][$b][$c]) ? $_SESSION[$a][$b][$c] : null;
            case 4:
                list($a, $b, $c, $d) = $keys;
                return isset($_SESSION[$a][$b][$c][$d]) ? $_SESSION[$a][$b][$c][$d] : null;
            case 5:
                list($a, $b, $c, $d, $e) = $keys;
                return isset($_SESSION[$a][$b][$c][$d][$e]) ? $_SESSION[$a][$b][$c][$d][$e] : null;
            default:
                $a = array_pop($keys);
                return isset($_SESSION[$a]) ? $_SESSION[$a] : null;
        }
    }

    /**
     * @param string $key 存储在session数据中的key，默认不传获取所有数据
     *                    获取层级session值用“.”分割，如：userinfo.id;
     *                    session数据层级限制为5层；
     * @param mixed  $val 当前key的值
     *
     * @return bool
     */
    public static function set($key, $val)
    {
        if (!$key || !$val) {
            return false;
        }
        $keys = explode('.', $key);
        $level = count($keys);
        if ($level > 5) {
            return null;
        }
        switch ($level) {
            case 2:
                list($a, $b) = $keys;
                $_SESSION[$a][$b] = $val;
                break;
            case 3:
                list($a, $b, $c) = $keys;
                $_SESSION[$a][$b][$c] = $val;
                break;
            case 4:
                list($a, $b, $c, $d) = $keys;
                $_SESSION[$a][$b][$c][$d] = $val;
                break;
            case 5:
                list($a, $b, $c, $d, $e) = $keys;
                $_SESSION[$a][$b][$c][$d][$e] = $val;
                break;
            default:
                $a = array_pop($keys);
                $_SESSION[$a] = $val;
        }
        return true;
    }
}