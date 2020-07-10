<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Authentication extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    /* default login when acess manager system */
    public function index()
    {
        if ($this->auth != null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $data['seo']['title'] = "Login - Phần mềm quản lý bán hàng";

        if ($this->input->post('login')) {
            $_post = $this->input->post('data');
            $data['data']['_post'] = $_post;

            $this->form_validation->set_error_delimiters('<li>', '</li>');
            $this->form_validation->set_rules('data[username]', 'tên đăng nhập', 'trim|required|min_length[3]|max_length[100]|regex_match[/^([a-z0-9_@\.])+$/i]|callback__check_user');
            $this->form_validation->set_rules('data[password]', 'mật khẩu', 'trim|required|min_length[1]|callback__check_password[' . $_post['username'] . ']');
            if ($this->form_validation->run() == true) {
                $user = $this->db->select('username,password,salt')->where('username', $_post['username'])->or_where('email', $_post['username'])->from('users')->get()->row_array();
                CMS_Cookie::put('user_logged' . CMS_PREFIX, CMS_Cookie::encode(json_encode($user)), COOKIE_EXPIRY);
                CMS_Session::put('username', $user['username']);
                $this->db->where('username', $user['username'])->update('users', ['logined' => gmdate("Y:m:d H:i:s", time() + 7 * 3600), 'ip_logged' => $_SERVER['SERVER_ADDR']]);
                $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
            }
        }
        $data['template'] = 'auth/login';
        $this->load->view('layout/auth', isset($data) ? $data : null);
    }

    public function _check_password($password, $username)
    {
        if ($this->_check_user($username) == true) {
            $user = $this->db->select('username,password,salt')->where('username', $username)->or_where('email', $username)->from('users')->get()->row_array();
            $password = $this->cms_common_string->password_encode($password, $user['salt']);
            if ($password != $user['password']) {
                $this->form_validation->set_message('_check_password', 'Mật khẩu không chính xác.');
                return false;
            }
        }
        return true;
    }

    public function _check_user($username)
    {

        $count = $this->db->where('user_status', 1)->where('username', $username)->or_where('email', $username)->from('users')->count_all_results();
        if ($count == 0) {
            $this->form_validation->set_message('_check_user', 'Tài khoản đăng nhập không hợp lệ.');//tự tạo câu lệnh xuất riêng vs hàm riêng
            return false;
        }
        return true;
    }

    public function _email($email)
    {
        $count = $this->db->where('email', $email)->from('users')->count_all_results();
        if ($count == 0) {
            $this->form_validation->set_message('_email', 'Email Không tồn tại.');//tự tạo câu lệnh xuất riêng vs hàm riêng
            return false;
        }

        return true;
    }

    /*
     * Alert link expired
     ****************************************************/
    public function n_link()
    {
        $data['seo']['title'] = 'Thông báo - Phần mềm quan lý bán hàng';
        $data['template'] = 'auth/n_link';
        $this->load->view('layout/auth', isset($data) ? $data : null);
    }

    public function logout()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        CMS_Cookie::delete('user_logged' . CMS_PREFIX);
        $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
    }
}