<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customer extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index($page = 1)
    {
        if ($this->auth == null || !in_array(4, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $config = $this->cms_common->cms_pagination_custom();
        $total_customer = $this->db->from('customers')->count_all_results();
        $config['base_url'] = 'cms_paging_listcustomer';
        $config['total_rows'] = $total_customer;
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data['_list_customer'] = $this->db
            ->select('customers.ID,customer_code,customer_name,customer_phone,customer_addr,max(sell_date) as sell_date,sum(total_money) as total_money,sum(lack) as total_debt')
            ->from('customers')
            ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'LEFT')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('customers.created', 'desc')
            ->group_by('customers.ID')
            ->get()
            ->result_array();
        $data['user'] = $this->auth;
        $data['_total_customer'] = $total_customer;
        $total_money = 0;
        $total_debt = 0;
        foreach($data['_list_customer'] as $key => $item){
            $total_money+=$item['total_money'];
        }
        foreach($data['_list_customer'] as $key => $item){
            $total_debt+=$item['total_debt'];
        }
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id',$this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $data['_total_money'] = $total_money;
        $data['_total_debt'] = $total_debt;
        $data['template'] = 'customer/index';
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_order_by_customer_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();

        $total_orders = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('orders')
            ->where('deleted', 0)
            ->where('customer_id', $option['customer_id'])
            ->get()
            ->row_array();
        $data['_list_orders'] = $this->db
            ->from('orders')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->where('deleted', 0)
            ->where('customer_id', $option['customer_id'])
            ->get()
            ->result_array();

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_orders'], 'customer_id');
        $data['customer_id'] = $option['customer_id'];
        $config['base_url'] = 'cms_paging_order_by_customer_id';
        $config['total_rows'] = $total_orders['quantity'];
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_orders'] = $total_orders;
        if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_orders', isset($data) ? $data : null);
    }

    public function cms_paging_order_debt_by_customer_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $config['per_page'] = 100;

        $total_orders = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('orders')
            ->where(['deleted'=> 0,'order_status'=>1])
            ->where(['customer_id'=> $option['customer_id'],'lack >'=>0])
            ->get()
            ->row_array();
        $data['_list_orders'] = $this->db
            ->from('orders')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'asc')
            ->where(['deleted'=> 0,'order_status'=>1])
            ->where(['customer_id'=> $option['customer_id'],'lack >'=>0])
            ->get()
            ->result_array();

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_orders'], 'customer_id');
        $data['customer_id'] = $option['customer_id'];
        $config['base_url'] = 'cms_paging_order_debt_by_customer_id';
        $config['total_rows'] = $total_orders['quantity'];
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_orders'] = $total_orders;
        if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_orders_debt', isset($data) ? $data : null);
    }

    public function cms_paging_listcustomer($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $option = $this->input->post('data');

        if ($option['option'] == 0) {
            $total_customer = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'LEFT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();
            $temp = $this->db
                ->select('customers.ID')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'LEFT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('customers.ID')
                ->get()
                ->result_array();
            $total_customer['quantity'] = count($temp);
            $data['_list_customer'] = $this->db
                ->select('customers.ID,customer_code,customer_name,customer_phone,customer_addr,max(sell_date) as sell_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'LEFT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('customers.created', 'desc')
                ->group_by('customers.ID')
                ->get()
                ->result_array();
        } else if ($option['option'] == 1) {
            $total_customer = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'RIGHT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();
            $temp = $this->db
                ->select('customers.ID')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'RIGHT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('customers.ID')
                ->get()
                ->result_array();
            $total_customer['quantity'] = count($temp);
			$data['_list_customer'] = $this->db
                ->select('customers.ID,customer_code,customer_name,customer_phone,customer_addr,max(sell_date) as sell_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'RIGHT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('customers.created', 'desc')
                ->group_by('customers.ID')
                ->get()
                ->result_array();
        } else {
            $total_customer = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('customers')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'RIGHT')
                ->order_by('customers.created', 'desc')
                ->group_by('customers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->row_array();
            $temp = $this->db
                ->select('customers.ID')
                ->from('customers')
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'RIGHT')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('customers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->result_array();
            $total_customer['quantity'] = count($temp);
            $data['_list_customer'] = $this->db
                ->select('customers.ID,customer_code,customer_name,customer_phone,customer_addr,max(sell_date) as sell_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('customers')
                ->where("(customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('orders', 'orders.customer_id=customers.ID and cms_orders.deleted=0', 'RIGHT')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('customers.created', 'desc')
                ->group_by('customers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->result_array();
        }

        $config['base_url'] = 'cms_paging_listcustomer';
        $config['per_page'] = 10;
        $config['total_rows'] = isset($total_customer['quantity']) ? $total_customer['quantity'] : 0;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['_total_customer'] = $total_customer;
        $data['_pagination_link'] = $_pagination_link;
        $data['user'] = $this->auth;
        if ($page > 1 && ($total_customer['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option'];
        $data['page'] = $page;
        $this->load->view('ajax/customer-supplier/list_customer', isset($data) ? $data : null);
    }

    public function cms_detail_customer($id)
    {
        $id = (int)$id;
        $cus = $this->db->from('customers')->where('id', $id)->get()->row_array();
        if (!isset($cus) && count($cus) == 0) {
            echo $this->messages;
            return;
        } else {
            $data['_list_cus'] = $cus;
            $data['customer_id'] = $id;
            $this->load->view('ajax/customer-supplier/detail_cus', isset($data) ? $data : null);
        }
    }

    public function cms_crcustomer()
    {
        $data = $this->input->post('data');
        $data = $this->cms_common_string->allow_post($data, ['customer_code', 'customer_name', 'customer_phone', 'customer_email', 'customer_addr', 'notes', 'customer_birthday', 'customer_gender']);
        $data['customer_birthday'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['customer_birthday'])) + 7 * 3600);
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        if ($data['customer_code'] == '') {
            $this->db->select_max('customer_code')->like('customer_code', 'KH');
            $max_customer_code = $this->db->get('customers')->row();
            $max_code = (int)(str_replace('KH', '', $max_customer_code->customer_code)) + 1;
            if ($max_code < 10)
                $data['customer_code'] = 'KH00000' . ($max_code);
            else if ($max_code < 100)
                $data['customer_code'] = 'KH0000' . ($max_code);
            else if ($max_code < 1000)
                $data['customer_code'] = 'KH000' . ($max_code);
            else if ($max_code < 10000)
                $data['customer_code'] = 'KH00' . ($max_code);
            else if ($max_code < 100000)
                $data['customer_code'] = 'KH0' . ($max_code);
            else if ($max_code < 1000000)
                $data['customer_code'] = 'KH' . ($max_code);

            $this->db->insert('customers', $data);
            $id = $this->db->insert_id();
            echo $this->messages = $id;
        } else {
            $count = $this->db->where('customer_code', $data['customer_code'])->from('customers')->count_all_results();
            if ($count > 0) {
                echo $this->messages = "0";
            } else {
                $this->db->insert('customers', $data);
                $id = $this->db->insert_id();
                echo $this->messages = $id;
            }
        }
    }

    public function cms_delCustomer()
    {
        $id = (int)$this->input->post('id');
        $customer = $this->db->from('customers')->where('ID', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages;

            return;
        } else {
            $this->db->where('ID', $id)->delete('customers');
            echo $this->messages = '1';
        }
    }

    public function cms_edit_customer()
    {
        $id = (int)$this->input->post('id');
        $customer = $this->db->from('customers')->where('id', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages;
            return;
        } else {
            ob_start();
            $html = ob_get_contents();
            ob_end_clean();
        }
    }

    public function cms_detail_itemcust($id)
    {
        $id = (int)$id;
        $customer = $this->db->from('customers')->where('id', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages;
            return;
        } else {
            $data['_list_cus'] = $customer;
            $data['_list_cus']['customer_birthday'] = ($customer['customer_birthday'] != '1970-01-01 07:00:00') ? gmdate("d/m/Y", strtotime(str_replace('-', '/', $customer['customer_birthday'])) + 7 * 3600) : '';
            $this->load->view('ajax/customer-supplier/detail_cus', isset($data) ? $data : null);
        }
    }

    public function cms_detail_order_in_customer()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $id = $this->input->post('id');
        $order = $this->db->from('orders')->where('ID', $id)->get()->row_array();
        $data['_list_products'] = array();

        if (isset($order) && count($order)) {
            $list_products = json_decode($order['detail_order'], true);

            foreach ($list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = $product['quantity'];
                $_product['price'] = $product['price'];
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_order'] = $order;
        $this->load->view('ajax/customer-supplier/detail_order', isset($data) ? $data : null);
    }

    public function cms_save_edit_customer($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $data = $this->cms_common_string->allow_post($data, ['customer_name', 'customer_phone', 'customer_email', 'customer_addr', 'notes', 'customer_birthday', 'customer_gender']);
        $data['customer_birthday'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['customer_birthday'])) + 7 * 3600);
        $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('ID', $id)->update('customers', $data);
        echo $this->messages = '1';
    }
}