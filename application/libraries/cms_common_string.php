<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CMS_common_string
{

    private $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    /**
     * Random String
     *
     * @param $length get length of string
     * @param $char get special character
     *
     * return string
     */
    public function random($length = 10, $char = false)
    {

        if ($char == false) $s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()';
        else $s = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

        mt_srand((double)microtime() * 1000000);

        $salt = '';
        for ($i = 0; $i < $length; $i++) {
            $salt .= substr($s, mt_rand() % (strlen($s)), 1);
        }

        return $salt;
    }

    /**
     *  Encode string
     *
     * @param $passwd encode string
     * @param $salt character encode
     *
     * return string
     */
    public function password_encode($passwd = '', $salt = '')
    {
        return md5($salt . md5($salt . md5($passwd) . $salt) . $salt);
    }

    /**
     *  only field allowed then passed
     *
     * @param $param array field compare
     * @param $allow array field allow pass
     *
     * return array
     */
    public function allow_post($param, $allow)
    {
        $_temp = null;
        if (isset($param) && count($param) && isset($allow) && count($allow)) {
            foreach ($param as $key => $val) {
                if (in_array($key, $allow)) $_temp[$key] = trim($val);
            }

            return $_temp;
        }

        return $param;
    }

    /**
     *  CMS redirect page
     *
     * @param $url redirect to page url
     */
    public function cms_redirect($url = CMS_BASE_URL)
    {

        return header('location:' . $url);
        die;

    }

    /**
     *  CMS redirect page and notice
     *
     * @param $alert alert string
     * @param $url redirect to page url
     */
    public function cms_jsredirect($alert, $url)
    {
        die('<meta http-equiv="content-type" content="text/html; charset=UTF-8"><script type="text/javascript">alert(\'' . $alert . '\'); location.href=\'' . $url . '\'; </script>');
    }


}
