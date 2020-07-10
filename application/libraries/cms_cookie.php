<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Cookie
{

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public static function exists($name)
    {

        return (isset($_COOKIE[$name])) ? true : false;

    }

    public static function get($name)
    {
        return $_COOKIE[$name];
    }

    public static function put($name, $val, $expiry)
    {
        setcookie($name, $val, time() + $expiry, '/');
    }

    public static function delete($name)
    {
        setcookie($name, '', -1, '/');
    }

    public static function encode($cookie_val)
    {
        return 'cms' . base64_encode($cookie_val);
    }

    public static function decode($cookie_val)
    {
        return base64_decode(substr($cookie_val, 3));
    }
}