<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CMS_Session
{

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public static function flash($name, $string = '')
    {
        if (self::exist($name)) {
            $session = self::get($name);
            self::delete($name);
            return $session;
        } else {
            self::put($name, $string);
        };
    }

    public static function exist($name)
    {
        return (isset($_SESSION[$name])) ? true : false;
    }

    public static function get($name)
    {
        if (self::exist($name)) {
            return $_SESSION[$name];
        }
    }

    public static function delete($name)
    {
        if (self::exist($name)) {
            unset($_SESSION[$name]);
        }
    }

    public static function put($name, $val)
    {
        return $_SESSION[$name] = $val;
    }

}