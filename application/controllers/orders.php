<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Orders extends CI_Controller
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
        if ($this->auth == null || !in_array(2, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'order/index';
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id', $this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function convert_number_to_words($number)
    {
        $hyphen = ' ';
        $conjunction = '  ';
        $separator = ' ';
        $negative = 'âm ';
        $decimal = ' phẩy ';
        $dictionary = array(
            0 => 'Không',
            1 => 'Một',
            2 => 'Hai',
            3 => 'Ba',
            4 => 'Bốn',
            5 => 'Năm',
            6 => 'Sáu',
            7 => 'Bảy',
            8 => 'Tám',
            9 => 'Chín',
            10 => 'Mười',
            11 => 'Mười một',
            12 => 'Mười hai',
            13 => 'Mười ba',
            14 => 'Mười bốn',
            15 => 'Mười năm',
            16 => 'Mười sáu',
            17 => 'Mười bảy',
            18 => 'Mười tám',
            19 => 'Mười chín',
            20 => 'Hai mươi',
            30 => 'Ba mươi',
            40 => 'Bốn mươi',
            50 => 'Năm mươi',
            60 => 'Sáu mươi',
            70 => 'Bảy mươi',
            80 => 'Tám mươi',
            90 => 'Chín mươi',
            100 => 'trăm',
            1000 => 'ngàn',
            1000000 => 'triệu',
            1000000000 => 'tỷ',
            1000000000000 => 'nghìn tỷ',
            1000000000000000 => 'ngàn triệu triệu',
            1000000000000000000 => 'tỷ tỷ'
        );

        if (!is_numeric($number)) {
            return false;
        }

        if (($number >= 0 && (int)$number < 0) || (int)$number < 0 - PHP_INT_MAX) {
            trigger_error(
                'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
                E_USER_WARNING
            );
            return false;
        }

        if ($number < 0) {
            return $negative . $this->convert_number_to_words(abs($number));
        }

        $string = $fraction = null;

        if (strpos($number, '.') !== false) {
            list($number, $fraction) = explode('.', $number);
        }

        switch (true) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int)($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if ($units) {
                    $string .= $hyphen . $dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
                if ($remainder) {
                    $string .= $conjunction . $this->convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int)($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = $this->convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
                if ($remainder) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= $this->convert_number_to_words($remainder);
                }
                break;
        }

        if (null !== $fraction && is_numeric($fraction)) {
            $string .= $decimal;
            $words = array();
            foreach (str_split((string)$fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }

    public function cms_print_order()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_order = $this->db->from('orders')->where('ID', $data_post['id_order'])->get()->row_array();
        $customer_name = '';
        $customer_phone = '';
        $customer_address = '';
        $debt = 0;
        if ($data_order['customer_id'] != null) {
            $customer_name = cms_getNamecustomerbyID($data_order['customer_id']);
            $customer_phone = cms_getPhonecustomerbyID($data_order['customer_id']);
            $customer_address = cms_getAddresscustomerbyID($data_order['customer_id']);
            $order = $this->db
                ->select('sum(lack) as debt')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1, 'lack >' => 0, 'customer_id' => $data_order['customer_id']])
                ->get()
                ->row_array();
            $debt = $order['debt'];
        }

        $user_name = '';
        if ($data_order['customer_id'] != null)
            $user_name = cms_getNameAuthbyID($data_order['user_init']);

        $data_template['content'] = str_replace("{Ten_Cua_Hang}", "Phong Tran", $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Xuat}", $data_order['sell_date'], $data_template['content']);
        $data_template['content'] = str_replace("{Khach_Hang}", $customer_name, $data_template['content']);
        $data_template['content'] = str_replace("{DT_Khach_Hang}", $customer_phone, $data_template['content']);
        $data_template['content'] = str_replace("{DC_Khach_Hang}", $customer_address, $data_template['content']);
        $data_template['content'] = str_replace("{Thu_Ngan}", $user_name, $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien_Hang}", $this->cms_common->cms_encode_currency_format($data_order['total_price']), $data_template['content']);
        $data_template['content'] = str_replace("{Chiec_Khau}", $this->cms_common->cms_encode_currency_format($data_order['coupon']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien}", $this->cms_common->cms_encode_currency_format($data_order['total_money'] - $data_order['coupon']), $data_template['content']);
        $data_template['content'] = str_replace("{Khach_Dua}", $this->cms_common->cms_encode_currency_format($data_order['customer_pay']), $data_template['content']);
        $data_template['content'] = str_replace("{Con_No}", $this->cms_common->cms_encode_currency_format($data_order['lack']), $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Don_Hang}", $data_order['output_code'], $data_template['content']);
        $data_template['content'] = str_replace("{Ghi_Chu}", $data_order['notes'], $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", $this->convert_number_to_words($data_order['lack']), $data_template['content']);
        $data_template['content'] = str_replace("{Cong_No}", $this->cms_common->cms_encode_currency_format($debt), $data_template['content']);

        $detail = '';
        $detail2 = '';
        $number = 1;
        if (isset($data_order) && count($data_order)) {
            $list_products = json_decode($data_order['detail_order'], true);
            foreach ($list_products as $product) {
                $prd = cms_finding_productbyID($product['id']);
                $quantity = $product['quantity'];
                $total = $quantity * $product['price'];
                $detail = $detail . '<tr><td style="text-align:center;">' . $number . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '</td><td style = "text-align:center">' . $quantity . '</td ><td style = "text-align:center">' . $prd['prd_unit_name'] . '</td ><td  style="text-align:center;">' . $this->cms_common->cms_encode_currency_format($product['price']) . '</td><td style="text-align:center;">' . $this->cms_common->cms_encode_currency_format($total) . '</td ></tr>';
                $detail2 = $detail2 . '
                <tr>
                    <td colspan="4" style="margin-left:10px;">' . $number++ . '. ' . $prd['prd_name'] . '</td>
                </tr>
                <tr>
                    <td  style="text-align:center;">' . $this->cms_common->cms_encode_currency_format($product['price']) . '</td>
                    <td style = "text-align:center">' . $quantity . '</td >
                    <td style = "text-align:center">' . $prd['prd_unit_name'] . '</td >
                    <td style="text-align:center;">' . $this->cms_common->cms_encode_currency_format($total) . '</td >
                </tr>';
            }
        }

        $table = '<table border="1" style="width:100%;border-collapse:collapse;">
                    <tbody >
                        <tr >
                            <td style="text-align:center;"><strong >STT</strong ></td >
                            <td style="text-align:center;"><strong >Tên sản phẩm</strong ></td >
                            <td style="text-align:center;"><strong >SL</strong ></td >
                            <td style="text-align:center;"><strong >ĐVT</strong ></td >
                            <td style="text-align:center;"><strong >Đơn giá</strong ></td >
                            <td style="text-align:center;"><strong >Thành tiền</strong ></td >
                        </tr >' . $detail . '
                    </tbody >
                 </table >';

        $table2 = '<table border="1" style="width:100%;border-collapse:collapse;">
                    <tbody >
                        <tr >
                            <td style="text-align:center;"><strong >Đơn giá</strong ></td >
                            <td style="text-align:center;"><strong >SL</strong ></td >
                            <td style="text-align:center;"><strong >ĐVT</strong ></td >
                            <td style="text-align:center;"><strong >Thành tiền</strong ></td >
                        </tr >' . $detail2 . '
                    </tbody >
                 </table >';

        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham}", $table, $data_template['content']);
        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham2}", $table2, $data_template['content']);

        echo $this->messages = $data_template['content'];
    }

    public function cms_paging_order($page = 1)
    {
        $option = $this->input->post('data');
        $total_orders = 0;
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['order_status'] >= 0) {
            if ($option['customer_id'] >= 0) {
                if ($option['option1'] == '0') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '1') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '2') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                }
            } else {
                if ($option['option1'] == '0') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '1') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '2') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('order_status', $option['order_status'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                }
            }
        } else {
            if ($option['customer_id'] >= 0) {
                if ($option['option1'] == '0') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('customer_id', $option['customer_id'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '1') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('customer_id', $option['customer_id'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '2') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('customer_id', $option['customer_id'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                }
            } else {
                if ($option['option1'] == '0') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 0)
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 0)
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '1') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where('deleted', 1)
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where('deleted', 1)
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                } else if ($option['option1'] == '2') {
                    if ($option['date_from'] != '' && $option['date_to'] != '') {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where('sell_date >=', $option['date_from'])
                            ->where('sell_date <=', $option['date_to'])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    } else {
                        $total_orders = $this->db
                            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
                            ->from('orders')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->row_array();
                        $data['_list_orders'] = $this->db
                            ->from('orders')
                            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                            ->order_by('created', 'desc')
                            ->where(['deleted' => 0, 'lack >' => 0])
                            ->where("(output_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->get()
                            ->result_array();
                    }
                }
            }
        }

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_orders'], 'customer_id');
        $data['customer_id'] = $option['customer_id'];
        $data['order_status'] = $option['order_status'];
        $config['base_url'] = 'cms_paging_order';
        $config['total_rows'] = $total_orders['quantity'];
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_orders'] = $total_orders;
        if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option1'];
        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/orders/list_orders', isset($data) ? $data : null);
    }

    public function cms_del_temp_order($id)
    {
        if ($this->auth == null || !in_array(13, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
        $store_id = $order['store_id'];
        $this->db->trans_begin();
        $user_init = $this->auth['id'];
        if (isset($order) && count($order)) {
            if ($order['order_status'] == 1) {
                $list_products = json_decode($order['detail_order'], true);
                foreach ($list_products as $item) {
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($inventory_quantity)) {
                        $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_init]);
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_init];
                        $this->db->insert('inventory', $inventory);
                    }

                    $product = $this->db->select('prd_sls')->from('products')->where('ID', $item['id'])->get()->row_array();
                    $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                    $this->db->where('ID', $item['id'])->update('products', $sls);
                    $this->db->where(['transaction_id' => $id, 'product_id' => $item['id'], 'store_id' => $store_id])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
                }

                $this->db->where('order_id', $id)->update('receipt', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where('ID', $id)->update('orders', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->cms_del_temp_input($id);

            } else {
                $this->db->where('ID', $id)->update('orders', ['deleted' => 1, 'user_upd' => $user_init]);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_change_status_order($id)
    {
        $data = $this->input->post('data');
        $id = (int)$id;
        $user_init = $this->auth['id'];
        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
        if ($order['order_status'] == 5 || $order['order_status'] == 0) {
            echo $this->messages = "0";
        } else if ($data['order_status'] == 5) {
            $store_id = $order['store_id'];
            $this->db->trans_begin();
            if (isset($order) && count($order)) {
                $list_products = json_decode($order['detail_order'], true);
                foreach ($list_products as $item) {
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($inventory_quantity)) {
                        $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_init]);
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_init];
                        $this->db->insert('inventory', $inventory);
                    }

                    $product = $this->db->select('prd_sls')->from('products')->where('ID', $item['id'])->get()->row_array();
                    $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                    $this->db->where('ID', $item['id'])->update('products', $sls);
                    $this->db->where(['transaction_id' => $id, 'product_id' => $item['id'], 'store_id' => $store_id])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
                }
                $this->db->where('ID', $id)->update('orders', ['order_status' => 5, 'user_upd' => $user_init]);
                $this->cms_del_temp_input($id);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        } else {
            $this->db->where('ID', $id)->update('orders', ['order_status' => $data['order_status'], 'user_upd' => $user_init]);
            echo $this->messages = "1";
        }
    }

    public function cms_delete_receipt_in_order($id)
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $receipt = $this->db->from('receipt')->where(['ID' => $id, 'deleted' => 0, 'type_id' => 3])->get()->row_array();
        $user_id = $this->auth['id'];
        $this->db->trans_begin();
        if (isset($receipt) && count($receipt)) {
            $order = $this->db->select('customer_pay,lack')->from('orders')->where(['ID' => $receipt['order_id'], 'deleted' => 0])->get()->row_array();
            $order['customer_pay'] = $order['customer_pay'] - $receipt['total_money'];
            $order['lack'] = $order['lack'] + $receipt['total_money'];
            $order['user_upd'] = $user_id;
            $this->db->where('ID', $receipt['order_id'])->update('orders', $order);
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

    public function cms_del_temp_input($order_id)
    {
        $order_id = (int)$order_id;
        $input_list = $this->db->from('input')->where(['order_id' => $order_id, 'deleted' => 0, 'input_status' => 1])->get()->result_array();
        foreach ($input_list as $input) {
            $store_id = $input['store_id'];
            $user_init = $this->auth['id'];
            if (isset($input) && count($input)) {
                $list_products = json_decode($input['detail_input'], true);
                foreach ($list_products as $item) {
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($inventory_quantity)) {
                        $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity'], 'user_upd' => $user_init]);
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => -$item['quantity'], 'user_init' => $user_init];
                        $this->db->insert('inventory', $inventory);
                    }

                    $product = $this->db->select('prd_sls')->from('products')->where('ID', $item['id'])->get()->row_array();
                    $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];
                    $this->db->where('ID', $item['id'])->update('products', $sls);
                    $this->db->where(['transaction_id' => $input['ID'], 'product_id' => $item['id'], 'store_id' => $store_id])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
                }

                $this->db->where('input_id', $input['ID'])->update('payment', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where('ID', $input['ID'])->update('input', ['deleted' => 1, 'user_upd' => $user_init]);
            }
        }
    }

    public function cms_del_order($id)
    {
        if ($this->auth == null || !in_array(13, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
        $this->db->trans_begin();
        if (isset($order) && count($order)) {
            $this->db->where('ID', $id)->update('orders', ['deleted' => 2, 'user_upd' => $this->auth['id']]);
        } else
            echo $this->messages = "0";

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_save_order_return($store_id)
    {
        if ($this->auth['store_id'] == $store_id) {
            $input = $this->input->post('data');
            $detail_input_temp = $input['detail_input'];
            if (empty($input['input_date'])) {
                $input['input_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            } else {
                $input['input_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $input['input_date'])) + 7 * 3600);;
            }
            $total_price = 0;
            $total_quantity = 0;
            $this->db->trans_begin();
            $user_init = $this->auth['id'];
            $input['total_origin_price_return'] = 0;
            foreach ($input['detail_input'] as $item) {
                $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                if (!empty($inventory_quantity)) {
                    $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_init]);
                } else {
                    $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_init];
                    $this->db->insert('inventory', $inventory);
                }

                $canreturn = $this->db->select('quantity,price')->from('canreturn')->where(['order_id' => $input['order_id'], 'product_id' => $item['id']])->get()->row_array();
                if (empty($canreturn) || $canreturn['quantity'] < 1 || $canreturn['quantity'] < $item['quantity']) {
                    $this->db->trans_rollback();
                    echo $this->messages = "0";
                    return;
                } else {
                    $canreturn['quantity'] = $canreturn['quantity'] - $item['quantity'];
                    $canreturn['user_upd'] = $user_init;
                    $this->db->where(['order_id' => $input['order_id'], 'product_id' => $item['id']])->update('canreturn', $canreturn);
                }

                $product = $this->db->select('prd_sls,prd_origin_price')->from('products')->where('ID', $item['id'])->get()->row_array();
                $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                $total_price += ($item['price'] * $item['quantity']);
                $total_quantity += $item['quantity'];
                $this->db->where('ID', $item['id'])->update('products', $sls);

                $report_temp = $this->db->select('output,origin_price')->from('report')->where(['transaction_id' => $input['order_id'], 'type' => 3, 'product_id' => $item['id']])->get()->row_array();
                $input['total_origin_price_return'] += $item['quantity'] * ($report_temp['origin_price'] / $report_temp['output']);
            }

            $input['total_quantity'] = $total_quantity;
            $input['total_price'] = $total_price;
            $lack = $total_price - $input['payed'] - $input['discount'];
            $input['total_money'] = $total_price - $input['discount'];
            $input['lack'] = $lack > 0 ? $lack : 0;
            $input['store_id'] = $store_id;
            $input['user_init'] = $this->auth['id'];
            $input['detail_input'] = json_encode($input['detail_input']);

            $this->db->select_max('input_code')->like('input_code', 'PNT')->where('order_id >', 0);
            $max_input_code = $this->db->get('input')->row();
            $max_code = (int)(str_replace('PNT', '', $max_input_code->input_code)) + 1;
            if ($max_code < 10)
                $input['input_code'] = 'PNT00000' . ($max_code);
            else if ($max_code < 100)
                $input['input_code'] = 'PNT0000' . ($max_code);
            else if ($max_code < 1000)
                $input['input_code'] = 'PNT000' . ($max_code);
            else if ($max_code < 10000)
                $input['input_code'] = 'PNT00' . ($max_code);
            else if ($max_code < 100000)
                $input['input_code'] = 'PNT0' . ($max_code);
            else if ($max_code < 1000000)
                $input['input_code'] = 'PNT' . ($max_code);

            $input['canreturn'] = 0;
            $this->db->insert('input', $input);
            $id = $this->db->insert_id();

            $payment = array();
            $payment['input_id'] = $id;
            $this->db->select_max('payment_code')->like('payment_code', 'PC');
            $max_payment_code = $this->db->get('payment')->row();
            $max_code = (int)(str_replace('PC', '', $max_payment_code->payment_code)) + 1;
            if ($max_code < 10)
                $payment['payment_code'] = 'PC000000' . ($max_code);
            else if ($max_code < 100)
                $payment['payment_code'] = 'PC00000' . ($max_code);
            else if ($max_code < 1000)
                $payment['payment_code'] = 'PC0000' . ($max_code);
            else if ($max_code < 10000)
                $payment['payment_code'] = 'PC000' . ($max_code);
            else if ($max_code < 100000)
                $payment['payment_code'] = 'PC00' . ($max_code);
            else if ($max_code < 1000000)
                $payment['payment_code'] = 'PC0' . ($max_code);
            else if ($max_code < 10000000)
                $payment['payment_code'] = 'PC' . ($max_code);

            $payment['type_id'] = 2;
            $payment['store_id'] = $store_id;
            $payment['payment_date'] = $input['input_date'];
            $payment['notes'] = $input['notes'];
            $payment['payment_method'] = $input['payment_method'];
            $payment['total_money'] = $input['payed'] - $total_price + $input['discount'] < 0 ? $input['payed'] : $total_price - $input['discount'];
            $payment['user_init'] = $input['user_init'];
            $this->db->insert('payment', $payment);

            $temp = array();
            $temp['transaction_code'] = $input['input_code'];
            $temp['transaction_id'] = $id;
            $temp['supplier_id'] = isset($input['supplier_id']) ? $input['supplier_id'] : 0;
            $temp['date'] = $input['input_date'];
            $temp['notes'] = $input['notes'];
            $temp['user_init'] = $input['user_init'];
            $temp['type'] = 6;
            $temp['store_id'] = $store_id;
            foreach ($detail_input_temp as $item) {
                $report = $temp;
                $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                $report['product_id'] = $item['id'];
                $report['price'] = $item['price'];
                $report['input'] = $item['quantity'];
                $report['stock'] = $stock['quantity'];
                $report['total_money'] = $report['price'] * $report['input'];
                $this->db->insert('report', $report);
            }

            $check = $this->db
                ->select('sum(quantity) as total_quantity')
                ->from('canreturn')
                ->where('order_id', $input['order_id'])
                ->get()
                ->row_array();
            if (empty($check) || $check['total_quantity'] < 1) {
                $this->db->where('ID', $input['order_id'])->update('orders', ['canreturn' => 0, 'user_upd' => $user_init]);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = $id;
            }
        } else
            echo $this->messages = "0";
    }

    public function cms_return_order($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0, 'order_status' => 1, 'canreturn' => 1])->get()->row_array();
        if (isset($order) && count($order)) {
            $detail_order = $this->db
                ->from('canreturn')
                ->join('products', 'products.ID=canreturn.product_id', 'INNER')
                ->where(['order_id' => $order['ID'], 'quantity >' => 0])
                ->get()
                ->result_array();
        }
        $data['data']['_order'] = $order;
        $data['data']['_detail_order'] = $detail_order;
        $this->load->view('ajax/orders/return', isset($data) ? $data : null);
    }

    public function cms_vsell_order()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $data['data']['sale'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();		$data['data']['user'] = $this->auth;

        $this->load->view('ajax/orders/sell_bill', isset($data) ? $data : null);
    }

    public function cms_detail_order()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $id = $this->input->post('id');
        $order = $this->db->from('orders')->where('ID', $id)->get()->row_array();
        $receipt = $this->db->from('receipt')->where(['order_id' => $id, 'type_id' => 3, 'deleted' => 0])->get()->result_array();
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
        $data['data']['_receipt'] = $receipt;
        $this->load->view('ajax/orders/detail_order', isset($data) ? $data : null);
    }

    public function cms_edit_order()
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = $this->input->post('id');

        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0, 'order_status <' => 5])->get()->row_array();
        $data['_list_products'] = array();
        $data['data']['user'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();
        if (isset($order) && count($order)) {
            $list_products = json_decode($order['detail_order'], true);
            foreach ($list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = $product['quantity'];
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_order'] = $order;
        $this->load->view('ajax/orders/edit_order', isset($data) ? $data : null);
    }

    public function cms_autocomplete_products()
    {
        $data = $this->input->get('term');
        $products = $this->db
            ->from('products')
            ->where('(prd_code like "%' . $data . '%" or prd_name like "%' . $data . '%") and prd_status = 1 and deleted =0 ')
            ->get()
            ->result_array();
        echo json_encode($products);
    }

    public function cms_check_barcode($keyword)
    {
        $products = $this->db->from('products')->where(array('prd_status' => '1', 'deleted' => '0', 'prd_code' => $keyword))->get()->result_array();
        if (count($products) == 1)
            echo $products[0]['ID'];
        else
            echo 0;
    }

    public function cms_search_box_customer()
    {
        $data = $this->input->post('data');
        $customer = $this->db->like('customer_name', $data['keyword'])->or_like('customer_phone', $data['keyword'])->or_like('customer_email', $data['keyword'])->or_like('customer_code', $data['keyword'])->from('customers')->get()->result_array();
        $data['data']['customers'] = $customer;
        $this->load->view('ajax/orders/search_box_customer', isset($data) ? $data : null);
    }

    public function cms_select_product()
    {
        $id = $this->input->post('id');
        $seq = $this->input->post('seq');
        $product = $this->db
            ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_edit_price')
            ->from('products')
            ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
            ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
            ->get()
            ->row_array();
        if (isset($product) && count($product) != 0) {
            ob_start(); ?>
            <tr data-id="<?php echo $product['ID']; ?>">
                <td class="text-center seq"><?php echo $seq; ?></td>
                <td><?php echo $product['prd_code']; ?></td>
                <td><?php echo $product['prd_name']; ?></td>
                <td class="text-center zoomin"><img height="30"
                                                    src="public/templates/uploads/<?php echo $product['prd_image_url']; ?>">
                </td>
                <td class="text-center" style="max-width: 30px;"><input style="min-width:80px;max-height: 22px;" type="text"
                                                                        class="txtNumber form-control quantity_product_order text-center"
                                                                        value="1"></td>
                <td class="text-center"><?php echo $product['prd_unit_name']; ?> </td>
                <td style="max-width: 100px;" class="text-center output">
                    <input type="text" <?php if ($product['prd_edit_price'] == 0) echo 'disabled'; ?>
                           style="min-width:80px;max-height: 22px;"
                           class="txtMoney form-control text-center price-order"
                           value="<?php echo cms_encode_currency_format($product['prd_sell_price']); ?>"></td>
                <td class="text-center total-money"><?php echo cms_encode_currency_format($product['prd_sell_price']); ?></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-order"></i></td>
            </tr>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function save_receipt_order()
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $receipt = $this->input->post('data');
        if ($receipt['store_id'] == $this->auth['store_id']) {
            $order = $this->db->from('orders')->where(['ID' => $receipt['order_id'], 'deleted' => 0])->get()->row_array();
            if ($order['lack'] > 0) {
                $this->db->trans_begin();
                $update_order = array();
                if ($receipt['total_money'] > $order['lack']) {
                    $receipt['total_money'] = $order['lack'];
                    $update_order['customer_pay'] = $order['customer_pay'] + $order['lack'];
                    $update_order['lack'] = 0;
                    $update_order['user_upd'] = $this->auth['id'];
                } else {
                    $update_order['customer_pay'] = $order['customer_pay'] + $receipt['total_money'];
                    $update_order['lack'] = $order['lack'] - $receipt['total_money'];
                    $update_order['user_upd'] = $this->auth['id'];
                }
                $this->db->where(['ID' => $receipt['order_id'], 'deleted' => 0])->update('orders', $update_order);

                if (empty($receipt['receipt_date'])) {
                    $receipt['receipt_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                } else {
                    $receipt['receipt_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $receipt['receipt_date'])) + 7 * 3600);;
                }

                $receipt['user_init'] = $this->auth['id'];
                $receipt['type_id'] = 3;
                $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                $max_receipt_code = $this->db->get('receipt')->row();
                $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                if ($max_code < 10)
                    $receipt['receipt_code'] = 'PT000000' . ($max_code);
                else if ($max_code < 100)
                    $receipt['receipt_code'] = 'PT00000' . ($max_code);
                else if ($max_code < 1000)
                    $receipt['receipt_code'] = 'PT0000' . ($max_code);
                else if ($max_code < 10000)
                    $receipt['receipt_code'] = 'PT000' . ($max_code);
                else if ($max_code < 100000)
                    $receipt['receipt_code'] = 'PT00' . ($max_code);
                else if ($max_code < 1000000)
                    $receipt['receipt_code'] = 'PT0' . ($max_code);
                else if ($max_code < 10000000)
                    $receipt['receipt_code'] = 'PT' . ($max_code);

                $this->db->insert('receipt', $receipt);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $this->messages = "0";
                } else {
                    $this->db->trans_commit();
                    echo $this->messages = 1;
                }
            } else
                echo $this->messages = "0";
        } else
            echo $this->messages = "0";
    }

    public function cms_save_orders($store_id)
    {
        if ($store_id == $this->auth['store_id']) {
            $order = $this->input->post('data');
            $detail_order_temp = $order['detail_order'];
            if (empty($order['sell_date'])) {
                $order['sell_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                $date = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            } else {
                $order['sell_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);;
                $date = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);;
            }
            $this->db->trans_begin();
            $user_init = $this->auth['id'];
            $total_price = 0;
            $total_origin_price = 0;
            $total_quantity = 0;

            if ($order['order_status'] == 1)
                foreach ($order['detail_order'] as $item) {
                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();

                    if (!empty($inventory_quantity)) {
                        if ($product['prd_allownegative'] == 0 && $inventory_quantity['quantity'] < $item['quantity']) {
                            $this->db->trans_rollback();
                            echo $this->messages = $product['prd_code'] . ' đang còn tồn chỉ ' . $inventory_quantity['quantity'] . ' sản phẩm';
                            return;
                        } else {
                            $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity'], 'user_upd' => $user_init]);
                        }
                    } else {
                        if ($product['prd_allownegative'] == 0) {
                            $this->db->trans_rollback();
                            echo $this->messages = $product['prd_code'] . ' đang hết hàng.';
                            return;
                        } else {
                            $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => -$item['quantity'], 'user_init' => $user_init];
                            $this->db->insert('inventory', $inventory);
                        }
                    }

                    if ($product['prd_edit_price'] == 0)
                        $item['price'] = $product['prd_sell_price'];

                    $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];
                    $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                    $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                    $total_quantity += $item['quantity'];
                    $this->db->where('ID', $item['id'])->update('products', $sls);
                    $detail_order[] = $item;
                }
            else
                foreach ($order['detail_order'] as $item) {
                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                    if ($product['prd_edit_price'] == 0)
                        $item['price'] = $product['prd_sell_price'];

                    $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                    $total_quantity += $item['quantity'];
                    $detail_order[] = $item;
                }

            if ($order['coupon'] == 'NaN')
                $order['coupon'] = 0;

            if($order['vat']>0)
                $total_price = ($total_price + ($total_price*$order['vat'])/100);

            $order['total_price'] = $total_price;
            $order['total_origin_price'] = $total_origin_price;
            $order['total_money'] = $total_price - $order['coupon'];
            $order['total_quantity'] = $total_quantity;
            $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
            $order['user_init'] = $this->auth['id'];
            $order['store_id'] = $store_id;
            $order['detail_order'] = json_encode($detail_order);

            $this->db->select_max('output_code')->like('output_code', 'PX')->where('input_id', 0);
            $max_output_code = $this->db->get('orders')->row();
            $max_code = (int)(str_replace('PX', '', $max_output_code->output_code)) + 1;
            if ($max_code < 10)
                $order['output_code'] = 'PX000000' . ($max_code);
            else if ($max_code < 100)
                $order['output_code'] = 'PX00000' . ($max_code);
            else if ($max_code < 1000)
                $order['output_code'] = 'PX0000' . ($max_code);
            else if ($max_code < 10000)
                $order['output_code'] = 'PX000' . ($max_code);
            else if ($max_code < 100000)
                $order['output_code'] = 'PX00' . ($max_code);
            else if ($max_code < 1000000)
                $order['output_code'] = 'PX0' . ($max_code);
            else if ($max_code < 10000000)
                $order['output_code'] = 'PX' . ($max_code);

            if ($order['sale_id'] == null)
                $order['sale_id'] = 0;

            if ($order['order_status'] == 1 && $order['customer_id'] > 0 && $order['customer_pay'] > $order['total_money']) {
                $orders = $this->db
                    ->from('orders')
                    ->where(['deleted' => 0, 'order_status' => 1, 'lack >' => 0, 'customer_id' => $order['customer_id']])
                    ->get()
                    ->result_array();
                $money = $order['customer_pay'] - $order['total_money'];
                foreach ($orders as $order_temp) {
                    if ($money > $order_temp['lack']) {
                        $update_order = array();
                        $receipt['order_id'] = $order_temp['ID'];
                        $receipt['store_id'] = $store_id;
                        $receipt['receipt_method'] = $order['payment_method'];
                        $receipt['total_money'] = $order_temp['lack'];
                        $update_order['customer_pay'] = $order_temp['customer_pay'] + $order_temp['lack'];
                        $update_order['lack'] = 0;
                        $update_order['user_upd'] = $this->auth['id'];

                        $this->db->where(['ID' => $order_temp['ID'], 'deleted' => 0])->update('orders', $update_order);

                        $receipt['receipt_date'] = $date;
                        $receipt['user_init'] = $this->auth['id'];
                        $receipt['type_id'] = 3;
                        $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                        $max_receipt_code = $this->db->get('receipt')->row();
                        $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                        if ($max_code < 10)
                            $receipt['receipt_code'] = 'PT000000' . ($max_code);
                        else if ($max_code < 100)
                            $receipt['receipt_code'] = 'PT00000' . ($max_code);
                        else if ($max_code < 1000)
                            $receipt['receipt_code'] = 'PT0000' . ($max_code);
                        else if ($max_code < 10000)
                            $receipt['receipt_code'] = 'PT000' . ($max_code);
                        else if ($max_code < 100000)
                            $receipt['receipt_code'] = 'PT00' . ($max_code);
                        else if ($max_code < 1000000)
                            $receipt['receipt_code'] = 'PT0' . ($max_code);
                        else if ($max_code < 10000000)
                            $receipt['receipt_code'] = 'PT' . ($max_code);

                        $this->db->insert('receipt', $receipt);

                        $money -= $order_temp['lack'];
                    } else {
                        $update_order = array();
                        $receipt['order_id'] = $order_temp['ID'];
                        $receipt['store_id'] = $store_id;
                        $receipt['receipt_method'] = $order['payment_method'];
                        $receipt['total_money'] = $money;
                        $update_order['customer_pay'] = $order_temp['customer_pay'] + $money;
                        $update_order['lack'] = $order_temp['lack'] - $money;
                        $update_order['user_upd'] = $this->auth['id'];

                        $this->db->where(['ID' => $order_temp['ID'], 'deleted' => 0])->update('orders', $update_order);

                        $receipt['receipt_date'] = $date;

                        $receipt['user_init'] = $this->auth['id'];
                        $receipt['type_id'] = 3;
                        $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                        $max_receipt_code = $this->db->get('receipt')->row();
                        $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                        if ($max_code < 10)
                            $receipt['receipt_code'] = 'PT000000' . ($max_code);
                        else if ($max_code < 100)
                            $receipt['receipt_code'] = 'PT00000' . ($max_code);
                        else if ($max_code < 1000)
                            $receipt['receipt_code'] = 'PT0000' . ($max_code);
                        else if ($max_code < 10000)
                            $receipt['receipt_code'] = 'PT000' . ($max_code);
                        else if ($max_code < 100000)
                            $receipt['receipt_code'] = 'PT00' . ($max_code);
                        else if ($max_code < 1000000)
                            $receipt['receipt_code'] = 'PT0' . ($max_code);
                        else if ($max_code < 10000000)
                            $receipt['receipt_code'] = 'PT' . ($max_code);

                        $this->db->insert('receipt', $receipt);

                        break;
                    }
                }
            }

            if($order['customer_id']<1 && $order['lack']>0){
                $this->db->trans_rollback();
                echo $this->messages = "-1";
                return;
            }

            $this->db->insert('orders', $order);
            $id = $this->db->insert_id();

            if ($total_price == 0)
                $percent_discount = 0;
            else
                $percent_discount = $order['coupon'] / $total_price;

            if ($order['order_status'] == 1) {
                $receipt = array();
                $receipt['order_id'] = $id;
                $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                $max_receipt_code = $this->db->get('receipt')->row();
                $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                if ($max_code < 10)
                    $receipt['receipt_code'] = 'PT000000' . ($max_code);
                else if ($max_code < 100)
                    $receipt['receipt_code'] = 'PT00000' . ($max_code);
                else if ($max_code < 1000)
                    $receipt['receipt_code'] = 'PT0000' . ($max_code);
                else if ($max_code < 10000)
                    $receipt['receipt_code'] = 'PT000' . ($max_code);
                else if ($max_code < 100000)
                    $receipt['receipt_code'] = 'PT00' . ($max_code);
                else if ($max_code < 1000000)
                    $receipt['receipt_code'] = 'PT0' . ($max_code);
                else if ($max_code < 10000000)
                    $receipt['receipt_code'] = 'PT' . ($max_code);

                $receipt['type_id'] = 3;
                $receipt['store_id'] = $store_id;
                $receipt['receipt_date'] = $order['sell_date'];
                $receipt['notes'] = $order['notes'];
                $receipt['receipt_method'] = $order['payment_method'];
                $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                $receipt['user_init'] = $order['user_init'];
                $this->db->insert('receipt', $receipt);

                $temp = array();
                $temp['transaction_code'] = $order['output_code'];
                $temp['transaction_id'] = $id;
                $temp['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;
                $temp['date'] = $order['sell_date'];
                $temp['notes'] = $order['notes'];
                $temp['sale_id'] = $order['sale_id'];
                $temp['user_init'] = $order['user_init'];
                $temp['type'] = 3;
                $temp['store_id'] = $order['store_id'];
                $canreturn_temp = array();
                $canreturn_temp['store_id'] = $order['store_id'];
                $canreturn_temp['order_id'] = $id;
                $canreturn_temp['user_init'] = $order['user_init'];
                foreach ($detail_order_temp as $item) {
                    $report = $temp;
                    $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $temp['store_id'], 'product_id' => $item['id']])->get()->row_array();
                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                    if ($product['prd_edit_price'] == 0)
                        $item['price'] = $product['prd_sell_price'];

                    $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                    $report['product_id'] = $item['id'];
                    $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                    $report['price'] = $item['price'];
                    $report['output'] = $item['quantity'];
                    $report['stock'] = $stock['quantity'];
                    $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                    $this->db->insert('report', $report);

                    $canreturn = $canreturn_temp;
                    $canreturn['product_id'] = $item['id'];
                    $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                    $canreturn['quantity'] = $item['quantity'];
                    $this->db->insert('canreturn', $canreturn);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = $id;
            }
        } else
            echo $this->messages = "0";
    }

    public function cms_update_orders($order_id)
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $check_order = $this->db->from('orders')->where(['deleted' => 0, 'ID' => $order_id])->get()->row_array();
        if ($check_order['order_status'] == 0) {
            $order = $this->input->post('data');
            $user_id = $this->auth['id'];
            if ($order['order_status'] == 5) {
                $order_cancel['user_upd'] = $user_id;
                $order_cancel['order_status'] = 5;
                $order_cancel['notes'] = $order['notes'];
                $this->db->where(['order_status' => 0, 'deleted' => 0, 'ID' => $order_id])->update('orders', $order_cancel);
                echo $this->messages = "5";
            } else {
                $store_id = $check_order['store_id'];
                $detail_order_temp = $order['detail_order'];
                if (empty($order['sell_date'])) {
                    $date = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                    $order['sell_date'] = $date;
                } else {
                    $date = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);
                    $order['sell_date'] = $date;
                }

                $this->db->trans_begin();
                $total_price = 0;
                $total_origin_price = 0;
                $total_quantity = 0;

                if ($order['order_status'] != 0) {
                    foreach ($order['detail_order'] as $item) {
                        $inventory_quantity = $this->db
                            ->select('quantity')
                            ->from('inventory')
                            ->where(['store_id' => $store_id, 'product_id' => $item['id']])
                            ->get()
                            ->row_array();
                        if (!empty($inventory_quantity)) {
                            $this->db
                                ->where(['store_id' => $store_id, 'product_id' => $item['id']])
                                ->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity'], 'user_upd' => $user_id]);
                        } else {
                            $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => -$item['quantity'], 'user_init' => $user_id];
                            $this->db->insert('inventory', $inventory);
                        }

                        $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                        if ($product['prd_edit_price'] == 0)
                            $item['price'] = $product['prd_sell_price'];

                        $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];
                        $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                        $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                        $total_quantity += $item['quantity'];
                        $this->db->where('ID', $item['id'])->update('products', $sls);
                        $detail_order[] = $item;
                    }
                } else {
                    foreach ($order['detail_order'] as $item) {
                        $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                        if ($product['prd_edit_price'] == 0)
                            $item['price'] = $product['prd_sell_price'];

                        $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                        $total_quantity += $item['quantity'];
                        $detail_order[] = $item;
                    }
                }

                if ($order['coupon'] == 'NaN')
                    $order['coupon'] = 0;

                if($order['vat']>0)
                    $total_price = ($total_price + ($total_price*$order['vat'])/100);

                $order['total_price'] = $total_price;
                $order['total_origin_price'] = $total_origin_price;
                $order['total_money'] = $total_price - $order['coupon'];
                $order['total_quantity'] = $total_quantity;
                $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
                $order['detail_order'] = json_encode($detail_order);

                if ($order['sale_id'] == null)
                    $order['sale_id'] = 0;

                if ($order['order_status'] == 1 && $order['customer_id'] > 0 && $order['customer_pay'] > $order['total_money']) {
                    $orders = $this->db
                        ->from('orders')
                        ->where(['deleted' => 0, 'order_status' => 1, 'lack >' => 0, 'ID !=' => $order_id, 'customer_id' => $order['customer_id']])
                        ->get()
                        ->result_array();
                    $money = $order['customer_pay'] - $order['total_money'];
                    foreach ($orders as $order_temp) {
                        if ($money > $order_temp['lack']) {
                            $update_order = array();
                            $receipt['order_id'] = $order_temp['ID'];
                            $receipt['store_id'] = $store_id;
                            $receipt['receipt_method'] = $order['payment_method'];
                            $receipt['total_money'] = $order_temp['lack'];
                            $update_order['customer_pay'] = $order_temp['customer_pay'] + $order_temp['lack'];
                            $update_order['lack'] = 0;
                            $update_order['user_upd'] = $user_id;

                            $this->db->where(['ID' => $order_temp['ID'], 'deleted' => 0])->update('orders', $update_order);

                            $receipt['receipt_date'] = $date;

                            $receipt['user_init'] = $user_id;
                            $receipt['type_id'] = 3;
                            $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                            $max_receipt_code = $this->db->get('receipt')->row();
                            $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                            if ($max_code < 10)
                                $receipt['receipt_code'] = 'PT000000' . ($max_code);
                            else if ($max_code < 100)
                                $receipt['receipt_code'] = 'PT00000' . ($max_code);
                            else if ($max_code < 1000)
                                $receipt['receipt_code'] = 'PT0000' . ($max_code);
                            else if ($max_code < 10000)
                                $receipt['receipt_code'] = 'PT000' . ($max_code);
                            else if ($max_code < 100000)
                                $receipt['receipt_code'] = 'PT00' . ($max_code);
                            else if ($max_code < 1000000)
                                $receipt['receipt_code'] = 'PT0' . ($max_code);
                            else if ($max_code < 10000000)
                                $receipt['receipt_code'] = 'PT' . ($max_code);

                            $this->db->insert('receipt', $receipt);

                            $money -= $order_temp['lack'];
                        } else {
                            $update_order = array();
                            $receipt['order_id'] = $order_temp['ID'];
                            $receipt['store_id'] = $store_id;
                            $receipt['receipt_method'] = $order['payment_method'];
                            $receipt['total_money'] = $money;
                            $update_order['customer_pay'] = $order_temp['customer_pay'] + $money;
                            $update_order['lack'] = $order_temp['lack'] - $money;
                            $update_order['user_upd'] = $user_id;

                            $this->db->where(['ID' => $order_temp['ID'], 'deleted' => 0])->update('orders', $update_order);

                            $receipt['receipt_date'] = $date;

                            $receipt['user_init'] = $user_id;
                            $receipt['type_id'] = 3;
                            $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                            $max_receipt_code = $this->db->get('receipt')->row();
                            $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                            if ($max_code < 10)
                                $receipt['receipt_code'] = 'PT000000' . ($max_code);
                            else if ($max_code < 100)
                                $receipt['receipt_code'] = 'PT00000' . ($max_code);
                            else if ($max_code < 1000)
                                $receipt['receipt_code'] = 'PT0000' . ($max_code);
                            else if ($max_code < 10000)
                                $receipt['receipt_code'] = 'PT000' . ($max_code);
                            else if ($max_code < 100000)
                                $receipt['receipt_code'] = 'PT00' . ($max_code);
                            else if ($max_code < 1000000)
                                $receipt['receipt_code'] = 'PT0' . ($max_code);
                            else if ($max_code < 10000000)
                                $receipt['receipt_code'] = 'PT' . ($max_code);

                            $this->db->insert('receipt', $receipt);

                            break;
                        }
                    }
                }

                if($order['customer_id']<1 && $order['lack']>0){
                    $this->db->trans_rollback();
                    echo $this->messages = "-1";
                    return;
                }

                $this->db->where(['order_status' => 0, 'deleted' => 0, 'ID' => $order_id])->update('orders', $order);
                $id = $order_id;

                if ($total_price == 0)
                    $percent_discount = 0;
                else
                    $percent_discount = $order['coupon'] / $total_price;

                if ($order['order_status'] != 0) {
                    $receipt = array();
                    $receipt['order_id'] = $id;
                    $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                    $max_receipt_code = $this->db->get('receipt')->row();
                    $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                    if ($max_code < 10)
                        $receipt['receipt_code'] = 'PT000000' . ($max_code);
                    else if ($max_code < 100)
                        $receipt['receipt_code'] = 'PT00000' . ($max_code);
                    else if ($max_code < 1000)
                        $receipt['receipt_code'] = 'PT0000' . ($max_code);
                    else if ($max_code < 10000)
                        $receipt['receipt_code'] = 'PT000' . ($max_code);
                    else if ($max_code < 100000)
                        $receipt['receipt_code'] = 'PT00' . ($max_code);
                    else if ($max_code < 1000000)
                        $receipt['receipt_code'] = 'PT0' . ($max_code);
                    else if ($max_code < 10000000)
                        $receipt['receipt_code'] = 'PT' . ($max_code);

                    $receipt['type_id'] = 3;
                    $receipt['store_id'] = $store_id;
                    $receipt['receipt_date'] = $date;
                    $receipt['notes'] = $order['notes'];
                    $receipt['receipt_method'] = $order['payment_method'];
                    $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                    $receipt['user_init'] = $user_id;
                    $this->db->insert('receipt', $receipt);

                    $temp = array();
                    $temp['transaction_code'] = $check_order['output_code'];
                    $temp['transaction_id'] = $id;
                    $temp['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;
                    $temp['date'] = $date;
                    $temp['notes'] = $order['notes'];
                    $temp['sale_id'] = $order['sale_id'];
                    $temp['user_init'] = $user_id;
                    $temp['type'] = 3;
                    $temp['store_id'] = $store_id;

                    $canreturn_temp = array();
                    $canreturn_temp['store_id'] = $store_id;
                    $canreturn_temp['order_id'] = $id;
                    $canreturn_temp['user_init'] = $user_id;
                    foreach ($detail_order_temp as $item) {
                        $report = $temp;
                        $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                        $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                        if ($product['prd_edit_price'] == 0)
                            $item['price'] = $product['prd_sell_price'];

                        $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                        $report['product_id'] = $item['id'];
                        $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                        $report['price'] = $item['price'];
                        $report['output'] = $item['quantity'];
                        $report['stock'] = $stock['quantity'];
                        $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                        $this->db->insert('report', $report);

                        $canreturn = $canreturn_temp;
                        $canreturn['product_id'] = $item['id'];
                        $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                        $canreturn['quantity'] = $item['quantity'];
                        $this->db->insert('canreturn', $canreturn);
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $this->messages = "6";
                } else {
                    $this->db->trans_commit();
                    echo $this->messages = $order['order_status'];
                }
            }
        } else if ($check_order['order_status'] > 0 && $check_order['order_status'] != 5) {
            $order = $this->input->post('data');
            unset($order['order_status']);
            $this->db->trans_begin();
            $delete = $this->db->from('orders')->where(['ID' => $check_order['ID'], 'deleted' => 0])->get()->row_array();
            $user_id = $this->auth['id'];
            if (isset($delete) && count($delete)) {
                $list_products_delete = json_decode($delete['detail_order'], true);
                foreach ($list_products_delete as $item) {
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $delete['store_id'], 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($inventory_quantity)) {
                        $this->db->where(['store_id' => $delete['store_id'], 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_id]);
                    } else {
                        $inventory = ['store_id' => $delete['store_id'], 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_id];
                        $this->db->insert('inventory', $inventory);
                    }

                    $product = $this->db->select('prd_sls')->from('products')->where('ID', $item['id'])->get()->row_array();
                    $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                    $this->db->where('ID', $item['id'])->update('products', $sls);
                    $this->db->where(['transaction_id' => $delete['ID'], 'product_id' => $item['id'], 'store_id' => $delete['store_id']])->update('report', ['deleted' => 1, 'user_upd' => $user_id]);
                }
            }

            $store_id = $check_order['store_id'];
            $detail_order_temp = $order['detail_order'];
            if (empty($order['sell_date'])) {
                $date = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                $order['sell_date'] = $date;
            } else {
                $date = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);
                $order['sell_date'] = $date;
            }

            $total_price = 0;
            $total_origin_price = 0;
            $total_quantity = 0;

            foreach ($order['detail_order'] as $item) {
                $inventory_quantity = $this->db
                    ->select('quantity')
                    ->from('inventory')
                    ->where(['store_id' => $store_id, 'product_id' => $item['id']])
                    ->get()
                    ->row_array();
                if (!empty($inventory_quantity)) {
                    $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity'], 'user_upd' => $user_id]);
                } else {
                    $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => -$item['quantity'], 'user_init' => $user_id];
                    $this->db->insert('inventory', $inventory);
                }

                $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                if ($product['prd_edit_price'] == 0)
                    $item['price'] = $product['prd_sell_price'];

                $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];
                $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                $total_quantity += $item['quantity'];
                $this->db->where('ID', $item['id'])->update('products', $sls);
                $detail_order[] = $item;
            }

            if ($order['coupon'] == 'NaN')
                $order['coupon'] = 0;

            if($order['vat']>0)
                $total_price = ($total_price + ($total_price*$order['vat'])/100);

            $order['total_price'] = $total_price;
            $order['total_origin_price'] = $total_origin_price;
            $order['total_money'] = $total_price - $order['coupon'];
            $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
            $order['total_quantity'] = $total_quantity;
            $order['detail_order'] = json_encode($detail_order);

            if ($order['sale_id'] == null)
                $order['sale_id'] = 0;

            if($order['customer_id']<1 && $order['lack']>0){
                $this->db->trans_rollback();
                echo $this->messages = "-1";
                return;
            }

            $this->db->where(['deleted' => 0, 'ID' => $order_id])->update('orders', $order);
            $id = $order_id;

            if ($total_price == 0)
                $percent_discount = 0;
            else
                $percent_discount = $order['coupon'] / $total_price;

            $check_receipt = $this->db->from('receipt')->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->count_all_results();
            if ($check_receipt > 1) {
                $this->db->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->update('receipt', ['deleted' => 0, 'user_upd' => $user_id]);

                $receipt['order_id'] = $id;
                $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                $max_receipt_code = $this->db->get('receipt')->row();
                $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                if ($max_code < 10)
                    $receipt['receipt_code'] = 'PT000000' . ($max_code);
                else if ($max_code < 100)
                    $receipt['receipt_code'] = 'PT00000' . ($max_code);
                else if ($max_code < 1000)
                    $receipt['receipt_code'] = 'PT0000' . ($max_code);
                else if ($max_code < 10000)
                    $receipt['receipt_code'] = 'PT000' . ($max_code);
                else if ($max_code < 100000)
                    $receipt['receipt_code'] = 'PT00' . ($max_code);
                else if ($max_code < 1000000)
                    $receipt['receipt_code'] = 'PT0' . ($max_code);
                else if ($max_code < 10000000)
                    $receipt['receipt_code'] = 'PT' . ($max_code);

                $receipt['type_id'] = 3;
                $receipt['store_id'] = $store_id;
                $receipt['receipt_date'] = $date;
                $receipt['notes'] = $order['notes'];
                $receipt['receipt_method'] = $order['payment_method'];
                $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                $receipt['user_init'] = $user_id;
                $this->db->insert('receipt', $receipt);
            } else {
                $check = $this->db->from('receipt')->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->get()->row_array();
                if(empty($check)){
                    $receipt['order_id'] = $id;
                    $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                    $max_receipt_code = $this->db->get('receipt')->row();
                    $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                    if ($max_code < 10)
                        $receipt['receipt_code'] = 'PT000000' . ($max_code);
                    else if ($max_code < 100)
                        $receipt['receipt_code'] = 'PT00000' . ($max_code);
                    else if ($max_code < 1000)
                        $receipt['receipt_code'] = 'PT0000' . ($max_code);
                    else if ($max_code < 10000)
                        $receipt['receipt_code'] = 'PT000' . ($max_code);
                    else if ($max_code < 100000)
                        $receipt['receipt_code'] = 'PT00' . ($max_code);
                    else if ($max_code < 1000000)
                        $receipt['receipt_code'] = 'PT0' . ($max_code);
                    else if ($max_code < 10000000)
                        $receipt['receipt_code'] = 'PT' . ($max_code);

                    $receipt['type_id'] = 3;
                    $receipt['store_id'] = $store_id;
                    $receipt['receipt_date'] = $date;
                    $receipt['notes'] = $order['notes'];
                    $receipt['receipt_method'] = $order['payment_method'];
                    $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                    $receipt['user_init'] = $user_id;
                    $this->db->insert('receipt', $receipt);
                }else{
                    $receipt['store_id'] = $store_id;
                    $receipt['notes'] = $order['notes'];
                    $receipt['user_upd'] = $user_id;
                    $receipt['receipt_method'] = $order['payment_method'];
                    $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                    $this->db->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->update('receipt', $receipt);
                }
            }

            $temp = array();
            $temp['transaction_code'] = $check_order['output_code'];
            $temp['transaction_id'] = $id;
            $temp['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;
            $temp['date'] = $date;
            $temp['notes'] = $order['notes'];
            $temp['sale_id'] = $order['sale_id'];
            $temp['user_init'] = $user_id;
            $temp['type'] = 3;
            $temp['store_id'] = $store_id;

            foreach ($detail_order_temp as $item) {
                $report = $temp;
                $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                if ($product['prd_edit_price'] == 0)
                    $item['price'] = $product['prd_sell_price'];

                $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                $report['product_id'] = $item['id'];
                $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                $report['price'] = $item['price'];
                $report['output'] = $item['quantity'];
                $report['stock'] = $stock['quantity'];
                $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                $this->db->insert('report', $report);
            }


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "9";
            } else {
                $this->db->trans_commit();
                echo $this->messages = 1;
            }
        } else
            echo $this->messages = "0";
    }
}

