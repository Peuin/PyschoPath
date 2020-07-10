<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Pos extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(19, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['data']['sale'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id',$this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/pos', isset($data) ? $data : null);
    }
}