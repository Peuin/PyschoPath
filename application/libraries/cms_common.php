<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class CMS_common
{

    var $CI;

    /**
     * Constructor
     *
     * Loads the calendar language file and sets the default time reference
     */
    public function __construct($config = [])
    {
        $this->CI =& get_instance();
    }

    /**
     * @param array $param config mail
     */
    public function sentMail($param = [])
    {
        $config = ['protocol' => 'smtp', 'smtp_host' => 'ssl://smtp.googlemail.com', 'smtp_port' => 465, 'smtp_user' => $param['from'], 'smtp_pass' => $param['password'], 'charset' => 'utf-8', 'newline' => "\r\n", 'mailtype' => 'html'];

        $this->CI->load->library('email', $config);
        $this->CI->email->set_newline("\r\n");
        $this->CI->email->from($param['from'], $param['name']);
        $this->CI->email->to($param['to']);
        $this->CI->email->subject($param['subject']);
        $this->CI->email->message($param['message']);
        if ($this->CI->email->send()) return true;

        return false;
    }

    public function cms_pagination_custom()
    {
        $param['base_url'] = '';
        $param['prefix'] = '';
        $param['suffix'] = '';
        $param['total_rows'] = 0;
        $param['per_page'] = 10;
        $param['num_links'] = 1000;
        $param['cur_page'] = 0;
        $param['use_page_numbers'] = true;
        $param['first_link'] = '&laquo';
        $param['next_link'] = '&rsaquo;';
        $param['prev_link'] = '&lsaquo;';
        $param['last_link'] = '&raquo';
        $param['uri_segment'] = 3;
        $param['full_tag_open'] = '<ul class="pagination">';
        $param['full_tag_close'] = '</ul>';
        $param['first_tag_open'] = '<li>';
        $param['first_tag_close'] = '</li>';
        $param['last_tag_open'] = '<li>';
        $param['last_tag_close'] = '</li>';
        $param['first_url'] = '';
        $param['cur_tag_open'] = '<li class="active"><a href="#">';
        $param['cur_tag_close'] = '</a></li>';
        $param['next_tag_open'] = '<li>';
        $param['next_tag_close'] = '</li>';
        $param['prev_tag_open'] = '<li>';
        $param['prev_tag_close'] = '</li>';
        $param['num_tag_open'] = '<li>';
        $param['num_tag_close'] = '</li>';
        $param['page_query_string'] = false;
        $param['query_string_segment'] = 'per_page';
        $param['display_pages'] = true;
        $param['anchor_class'] = '';
        return $param;
    }

    function cms_encode_currency_format($priceFloat)
    {
        $symbol_thousand = ',';
        $decimal_place = 0;
        if($priceFloat=='')
            return $priceFloat;

        if($priceFloat==0)
            return 0;

        $price = number_format($priceFloat, $decimal_place, '', $symbol_thousand);
        return $price;
    }

    function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
//        $key_array = array();

        foreach ($array as $val) {
            if (!in_array($val[$key], $temp_array) && $val[$key]!=0) {
//                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val[$key];
            }
            $i++;
        }
        return $temp_array;
    }
}