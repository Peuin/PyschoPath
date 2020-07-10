<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Profit extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(10, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'profit/index';
        $user = $this->db
            ->distinct()
            ->select('users.id,username')
            ->from('orders')
            ->join('users', 'orders.user_init = users.id', 'LEFT')
            ->where(['deleted' => 0, 'order_status' => 1])
            ->get()
            ->result_array();
        $data['data']['users'] = $user;
        $sale = $this->db
            ->select('users.id,username')
            ->from('users')
            ->get()
            ->result_array();
        $data['data']['sales'] = $sale;
        $customer = $this->db->from('customers')->get()->result_array();
        $data['data']['customers'] = $customer;
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['stores'] = $store;
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_profit($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $return_money=array();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));
        if ($option['type'] == 1) {
            if ($option['option1'] > -1) {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    }
                }
            } else {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_orders'] = $this->db
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->result_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                        }
                    }
                }
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/all', isset($data) ? $data : null);
        } else if ($option['type'] == 2) {
            if ($option['option1'] > -1) {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    }
                }
            } else {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $option['option2'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_customers = $this->db
                                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('customer_id')
                                ->get()
                                ->result_array();
                            foreach ($list_customers as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where('customer_id', $item['customer_id'])
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_customers'][] = $item;
                            }
                        }
                    }
                }
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/customer', isset($data) ? $data : null);
        } else if ($option['type'] == 3) {
            if ($option['option1'] > -1) {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    }
                }
            } else {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $item['user_init'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $item['user_init'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $item['user_init'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $item['user_init'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $item['user_init'])
                                    ->where('store_id', $option['option3'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $item['user_init'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_users = $this->db
                                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('user_init')
                                ->get()
                                ->result_array();
                            foreach ($list_users as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('user_init', $item['user_init'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_users'][] = $item;
                            }
                        }
                    }
                }
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/user', isset($data) ? $data : null);
        } else if ($option['type'] == 4) {
            if ($option['option1'] > -1) {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('store_id', $option['option3'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('store_id', $option['option3'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('customer_id', $option['option1'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    }
                }
            } else {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('user_init', $option['option2'])
                                    ->where('store_id', $option['option3'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('user_init', $option['option2'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('user_init', $option['option2'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('store_id', $option['option3'])
//                                    ->where('sale_id', $option['option4'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->where('store_id', $option['option3'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
//                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_sales = $this->db
                                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('sale_id')
                                ->get()
                                ->result_array();
                            foreach ($list_sales as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('sale_id', $item['sale_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_sales'][] = $item;
                            }
                        }
                    }
                }
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/sale', isset($data) ? $data : null);
        } else if ($option['type'] == 5) {
            if ($option['option1'] > -1) {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('customer_id', $option['option1'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    }
                }
            } else {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('user_init', $option['option2'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('sale_id', $option['option4'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $list_stores = $this->db
                                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(coupon) as total_discount, sum(total_origin_price) as total_origin_price')
                                ->from('orders')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('created', 'desc')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->group_by('store_id')
                                ->get()
                                ->result_array();
                            foreach ($list_stores as $item) {
                                $item['_list_orders'] = $this->db
                                    ->from('orders')
                                    ->order_by('created', 'desc')
                                    ->where(['deleted' => 0, 'order_status' => 1])
                                    ->where('store_id', $item['store_id'])
                                    ->where('sell_date >=', $option['date_from'])
                                    ->where('sell_date <=', $option['date_to'])
                                    ->get()
                                    ->result_array();
                                $data['_list_stores'][] = $item;
                            }
                        }
                    }
                }
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/store', isset($data) ? $data : null);
        } else if ($option['type'] == 6) {
            if ($option['option1'] > -1) {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(ID)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('report.user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('report.user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('report.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('report.user_init', $option['option2'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('store_id', $option['option3'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('customer_id', $option['option1'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('customer_id', $option['option1'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    }
                }
            } else {
                if ($option['option2'] > -1) {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('report.user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('report.user_init', $option['option2'])
                                ->where('store_id', $option['option3'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('report.user_init', $option['option2'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('user_init', $option['option2'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.user_init', $option['option2'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('report.user_init', $option['option2'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    }
                } else {
                    if ($option['option3'] > -1) {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('store_id', $option['option3'])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('store_id', $option['option3'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('o.store_id', $option['option3'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('store_id', $option['option3'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    } else {
                        if ($option['option4'] > -1) {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('sale_id', $option['option4'])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('sale_id', $option['option4'])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        } else {
                            $total_orders = $this->db
                                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(coupon) as total_discount, sum(total_quantity) as total_quantity')
                                ->from('orders')
                                ->where(['deleted' => 0, 'order_status' => 1])
                                ->where('sell_date >=', $option['date_from'])
                                ->where('sell_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $return_money = $this->db
                                ->select('sum(i.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                                ->from('input as i')
                                ->join('orders as o', 'o.ID=i.order_id', 'INNER')
                                ->where(['i.deleted' => 0, 'order_status' => 1])
                                ->where('input_date >=', $option['date_from'])
                                ->where('input_date <=', $option['date_to'])
                                ->get()
                                ->row_array();
                            $data['_list_products'] = $this->db
                                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                                ->from('report')
                                ->join('products','report.product_id=products.ID')
                                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                                ->order_by('report.created', 'desc')
                                ->where(['report.deleted' => 0])
                                ->where('date >=', $option['date_from'])
                                ->where('date <=', $option['date_to'])
                                ->where('type',3)
                                ->group_by('product_id')
                                ->get()
                                ->result_array();
                        }
                    }
                }
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];
            $config['per_page'] = 10;
            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/product', isset($data) ? $data : null);
        }
    }
}