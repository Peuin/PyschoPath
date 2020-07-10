<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Payment extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(2, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'payment/index';
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id', $this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_payment($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option1'] == '-1') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_payment = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('payment')
                    ->where('payment_date >=', $option['date_from'])
                    ->where('payment_date <=', $option['date_to'])
                    ->where(['deleted' => 0])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_payment'] = $this->db
                    ->from('payment')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('payment_date >=', $option['date_from'])
                    ->where('payment_date <=', $option['date_to'])
                    ->where(['deleted' => 0])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }else{
                $total_payment = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('payment')
                    ->where(['deleted' => 0])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_payment'] = $this->db
                    ->from('payment')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        }else{
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_payment = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('payment')
                    ->where('payment_date >=', $option['date_from'])
                    ->where('payment_date <=', $option['date_to'])
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_payment'] = $this->db
                    ->from('payment')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('payment_date >=', $option['date_from'])
                    ->where('payment_date <=', $option['date_to'])
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }else{
                $total_payment = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('payment')
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_payment'] = $this->db
                    ->from('payment')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(payment_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        }

        $config['base_url'] = 'cms_paging_payment';
        $config['total_rows'] = $total_payment['quantity'];
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_payment'] = $total_payment;
        if ($page > 1 && ($total_payment['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/payment/list_payment', isset($data) ? $data : null);
    }

    public function cms_del_temp_payment($id)
    {
        $id = (int)$id;
        $payment = $this->db->from('payment')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
        $user_id = $this->auth['id'];
        $this->db->trans_begin();
        if (isset($payment) && count($payment)) {
            if ($payment['input_id'] > 0) {
                $input = $this->db->select('payed,lack')->from('input')->where(['ID' => $payment['input_id'], 'deleted' => 0])->get()->row_array();
                if (!empty($input)) {
                    $input['payed'] = $input['payed'] - $payment['total_money'];
                    $input['lack'] = $input['lack'] + $payment['total_money'];
                    $input['user_upd'] = $user_id;
                    $this->db->where('ID', $payment['input_id'])->update('input', $input);
                }
            }

            $this->db->where('ID', $id)->update('payment', ['deleted' => 1, 'user_upd' => $user_id]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_save_payment()
    {
        $data = $this->input->post('data');
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['payment_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        $data['payment_method'] = 1;

        $this->db->select_max('payment_code')->like('payment_code', 'PC');
        $max_payment_code = $this->db->get('payment')->row();
        $max_code = (int)(str_replace('PC', '', $max_payment_code->payment_code)) + 1;
        if ($max_code < 10)
            $data['payment_code'] = 'PC000000' . ($max_code);
        else if ($max_code < 100)
            $data['payment_code'] = 'PC00000' . ($max_code);
        else if ($max_code < 1000)
            $data['payment_code'] = 'PC0000' . ($max_code);
        else if ($max_code < 10000)
            $payment['payment_code'] = 'PC000' . ($max_code);
        else if ($max_code < 100000)
            $data['payment_code'] = 'PC00' . ($max_code);
        else if ($max_code < 1000000)
            $data['payment_code'] = 'PC0' . ($max_code);
        else if ($max_code < 10000000)
            $data['payment_code'] = 'PC' . ($max_code);

        $this->db->insert('payment', $data);
        echo $this->messages = "1";
    }
}