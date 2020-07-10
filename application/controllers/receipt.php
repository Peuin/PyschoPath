<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Receipt extends CI_Controller
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
        $data['template'] = 'receipt/index';
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id', $this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_receipt($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option1'] == '-1') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_receipt = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('receipt')
                    ->where('receipt_date >=', $option['date_from'])
                    ->where('receipt_date <=', $option['date_to'])
                    ->where(['deleted' => 0])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_receipt'] = $this->db
                    ->from('receipt')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('receipt_date >=', $option['date_from'])
                    ->where('receipt_date <=', $option['date_to'])
                    ->where(['deleted' => 0])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }else{
                $total_receipt = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('receipt')
                    ->where(['deleted' => 0])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_receipt'] = $this->db
                    ->from('receipt')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        }else{
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_receipt = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('receipt')
                    ->where('receipt_date >=', $option['date_from'])
                    ->where('receipt_date <=', $option['date_to'])
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_receipt'] = $this->db
                    ->from('receipt')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('receipt_date >=', $option['date_from'])
                    ->where('receipt_date <=', $option['date_to'])
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }else{
                $total_receipt = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money')
                    ->from('receipt')
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_receipt'] = $this->db
                    ->from('receipt')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0,'type_id'=>$option['option1']])
                    ->where("(receipt_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        }

        $config['base_url'] = 'cms_paging_receipt';
        $config['total_rows'] = $total_receipt['quantity'];
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_receipt'] = $total_receipt;
        if ($page > 1 && ($total_receipt['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/receipt/list_receipt', isset($data) ? $data : null);
    }

    public function cms_del_temp_receipt($id)
    {
        $id = (int)$id;
        $receipt = $this->db->from('receipt')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
        $user_id = $this->auth['id'];
        $this->db->trans_begin();
        if (isset($receipt) && count($receipt)) {
            if ($receipt['order_id'] > 0) {
                $order = $this->db->select('customer_pay,lack')->from('orders')->where(['ID' => $receipt['order_id'], 'deleted' => 0])->get()->row_array();
                if (!empty($order)) {
                    $order['customer_pay'] = $order['customer_pay'] - $receipt['total_money'];
                    $order['lack'] = $order['lack'] + $receipt['total_money'];
                    $order['user_upd'] = $user_id;
                    $this->db->where('ID', $receipt['order_id'])->update('orders', $order);
                }
            }

            $this->db->where('ID', $id)->update('receipt', ['deleted' => 1, 'user_upd' => $user_id]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_save_receipt()
    {
        $data = $this->input->post('data');
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['receipt_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        $data['receipt_method'] = 1;

        $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
        $max_receipt_code = $this->db->get('receipt')->row();
        $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
        if ($max_code < 10)
            $data['receipt_code'] = 'PT000000' . ($max_code);
        else if ($max_code < 100)
            $data['receipt_code'] = 'PT00000' . ($max_code);
        else if ($max_code < 1000)
            $data['receipt_code'] = 'PT0000' . ($max_code);
        else if ($max_code < 10000)
            $receipt['receipt_code'] = 'PT000' . ($max_code);
        else if ($max_code < 100000)
            $data['receipt_code'] = 'PT00' . ($max_code);
        else if ($max_code < 1000000)
            $data['receipt_code'] = 'PT0' . ($max_code);
        else if ($max_code < 10000000)
            $data['receipt_code'] = 'PT' . ($max_code);

        $this->db->insert('receipt', $data);
        echo $this->messages = "1";
    }
}