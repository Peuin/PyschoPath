<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Input extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
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

    /* default login when acess manager system */
    public function index()
    {
        if ($this->auth == null || !in_array(5, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'input/index';
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id', $this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_vsell_input()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $data['data']['user'] = $this->auth;
        $this->load->view('ajax/input/import_bill', isset($data) ? $data : null);
    }

    public function cms_search_box_sup($keyword = '')
    {
        $sup = $this->db->like('supplier_name', $keyword)->or_like('supplier_phone', $keyword)->or_like('supplier_email', $keyword)->or_like('supplier_code', $keyword)->from('suppliers')->get()->result_array();
        $data['data']['suppliers'] = $sup;
        $this->load->view('ajax/input/search_box_sup', isset($data) ? $data : null);
    }

    public function cms_return_input($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $input = $this->db->from('input')->where(['ID' => $id, 'deleted' => 0, 'input_status' => 1, 'canreturn' => 1])->get()->row_array();
        if (isset($input) && count($input)) {
            $detail_input = $this->db
                ->from('canreturn')
                ->join('products', 'products.ID=canreturn.product_id', 'INNER')
                ->where(['input_id' => $input['ID'], 'quantity >' => 0])
                ->get()
                ->result_array();
        }
        $data['data']['_input'] = $input;
        $data['data']['_detail_input'] = $detail_input;
        $this->load->view('ajax/input/return', isset($data) ? $data : null);
    }

    public function cms_select_product()
    {
        $id = $this->input->post('id');
        $seq = $this->input->post('seq');
        $product = $this->db
            ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_origin_price')
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
                <td class="text-center" style="max-width: 30px;"><input style="max-height: 22px;" type="text"
                                                                        class="txtNumber form-control quantity_product_import text-center"
                                                                        value="1"></td>
                <td class="text-center"><?php echo $product['prd_unit_name']; ?> </td>
                <td class="text-center" style="max-width: 120px;">
                    <input style="max-height: 22px;" type="text" class="txtMoney form-control text-center price-input"
                           value="<?php echo number_format($product['prd_origin_price']); ?>">
                </td>
                <td class="text-center total-money"><?php echo number_format($product['prd_origin_price']); ?></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-input"></i></td>
            </tr>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function cms_save_import($store_id)
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
            if ($input['input_status'] == 1) {
                foreach ($input['detail_input'] as $item) {
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($inventory_quantity)) {
                        $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_init]);
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_init];
                        $this->db->insert('inventory', $inventory);
                    }

                    $product = $this->db->select('prd_sls,prd_origin_price')->from('products')->where('ID', $item['id'])->get()->row_array();
                    $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                    $total_price += ($item['price'] * $item['quantity']);
                    $total_quantity += $item['quantity'];
                    if ($item['price'] != $product['prd_origin_price']) {
                        $sls['prd_origin_price'] = (($product['prd_origin_price'] * $product['prd_sls']) + ($item['quantity'] * $item['price'])) / $sls['prd_sls'];
                    }

                    $this->db->where('ID', $item['id'])->update('products', $sls);
                }
            } else
                foreach ($input['detail_input'] as $item) {
                    $total_price += ($item['price'] * $item['quantity']);
                    $total_quantity += $item['quantity'];
                }

            $input['total_quantity'] = $total_quantity;
            $input['total_price'] = $total_price;
            $lack = $total_price - $input['payed'] - $input['discount'];
            $input['total_money'] = $total_price - $input['discount'];
            $input['lack'] = $lack > 0 ? $lack : 0;
            $input['store_id'] = $store_id;
            $input['user_init'] = $this->auth['id'];
            $input['detail_input'] = json_encode($input['detail_input']);

            $this->db->select_max('input_code')->like('input_code', 'PN')->where('order_id', 0);
            $max_input_code = $this->db->get('input')->row();
            $max_code = (int)(str_replace('PN', '', $max_input_code->input_code)) + 1;
            if ($max_code < 10)
                $input['input_code'] = 'PN000000' . ($max_code);
            else if ($max_code < 100)
                $input['input_code'] = 'PN00000' . ($max_code);
            else if ($max_code < 1000)
                $input['input_code'] = 'PN0000' . ($max_code);
            else if ($max_code < 10000)
                $input['input_code'] = 'PN000' . ($max_code);
            else if ($max_code < 100000)
                $input['input_code'] = 'PN00' . ($max_code);
            else if ($max_code < 1000000)
                $input['input_code'] = 'PN0' . ($max_code);
            else if ($max_code < 10000000)
                $input['input_code'] = 'PN' . ($max_code);

            $this->db->insert('input', $input);
            $id = $this->db->insert_id();

            $percent_discount = 0;
            if ($total_price != 0)
                $percent_discount = $input['discount'] / $total_price;


            if ($input['input_status'] == 1) {
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
                $temp['type'] = 2;
                $temp['store_id'] = $store_id;

                $canreturn_temp = array();
                $canreturn_temp['store_id'] = $input['store_id'];
                $canreturn_temp['input_id'] = $id;
                $canreturn_temp['user_init'] = $input['user_init'];

                foreach ($detail_input_temp as $item) {
                    $report = $temp;
                    $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                    $report['product_id'] = $item['id'];
                    $report['price'] = $item['price'];
                    $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                    $report['input'] = $item['quantity'];
                    $report['stock'] = $stock['quantity'];
                    $report['total_money'] = ($report['price'] * $report['input']) - $report['discount'];
                    $this->db->insert('report', $report);

                    $canreturn = $canreturn_temp;
                    $canreturn['product_id'] = $item['id'];
                    $canreturn['price'] = $item['price'] - ($percent_discount * $item['price']);
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

    public function cms_update_input($input_id)
    {
        $input = $this->input->post('data');
        $store_id = $input['store_id'];
        $check_input = $this->db->from('input')->where(['deleted' => 0, 'ID' => $input_id])->get()->row_array();
        if ($this->auth['store_id'] == $store_id && !empty($check_input)) {
            $this->db->trans_begin();
            $user_init = $this->auth['id'];

            $list_product_delete = json_decode($check_input['detail_input'], true);
            foreach ($list_product_delete as $item) {
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
            }

            $this->db->where(['transaction_id' => $input_id, 'store_id' => $store_id])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
            
            $detail_input_temp = $input['detail_input'];
            if (empty($input['input_date'])) {
                $input['input_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            } else {
                $input['input_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $input['input_date'])) + 7 * 3600);;
            }

            $total_price = 0;
            $total_quantity = 0;

            foreach ($input['detail_input'] as $item) {
                $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                if (!empty($inventory_quantity)) {
                    $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_init]);
                } else {
                    $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_init];
                    $this->db->insert('inventory', $inventory);
                }

                $product = $this->db->select('prd_sls,prd_origin_price')->from('products')->where('ID', $item['id'])->get()->row_array();
                $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                $total_price += ($item['price'] * $item['quantity']);
                $total_quantity += $item['quantity'];
                if ($item['price'] != $product['prd_origin_price']) {
                    if($sls['prd_sls']==0)
                        $sls['prd_origin_price'] = (($product['prd_origin_price'] * $product['prd_sls']) + ($item['quantity'] * $item['price']));
                    else
                        $sls['prd_origin_price'] = (($product['prd_origin_price'] * $product['prd_sls']) + ($item['quantity'] * $item['price'])) / $sls['prd_sls'];
                }

                $this->db->where('ID', $item['id'])->update('products', $sls);
            }

            $input['total_quantity'] = $total_quantity;
            $input['total_price'] = $total_price;
            $lack = $total_price - $input['payed'] - $input['discount'];
            $input['total_money'] = $total_price - $input['discount'];
            $input['lack'] = $lack > 0 ? $lack : 0;
            $input['store_id'] = $store_id;
            $input['detail_input'] = json_encode($input['detail_input']);

            $this->db->where(['deleted' => 0, 'ID' => $input_id])->update('input', $input);

            $percent_discount = 0;
            if ($total_price != 0)
                $percent_discount = $input['discount'] / $total_price;

            $check_payment = $this->db->from('payment')->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->count_all_results();
            if ($check_payment > 1) {
                $this->db->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->update('payment', ['deleted' => 0, 'user_upd' => $user_init]);

                $payment['input_id'] = $input_id;
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
            } else {
                $check = $this->db->from('payment')->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->get()->row_array();
                if(empty($check)){
                    $payment['input_id'] = $input_id;
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
                }else{
                    $payment['store_id'] = $store_id;
                    $payment['notes'] = $input['notes'];
                    $payment['user_upd'] = $user_init;
                    $payment['payment_method'] = $input['payment_method'];
                    $payment['total_money'] = $input['payed'] - $total_price + $input['discount'] < 0 ? $input['payed'] : $total_price - $input['discount'];
                    $this->db->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->update('payment', $payment);
                }
            }

            $temp = array();
            $temp['transaction_code'] = $check_input['input_code'];
            $temp['transaction_id'] = $input_id;
            $temp['supplier_id'] = isset($input['supplier_id']) ? $input['supplier_id'] : 0;
            $temp['date'] = $input['input_date'];
            $temp['notes'] = $input['notes'];
            $temp['user_init'] = $user_init;
            $temp['type'] = 2;
            $temp['store_id'] = $store_id;
            
            foreach ($detail_input_temp as $item) {
                $report = $temp;
                $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                $report['product_id'] = $item['id'];
                $report['price'] = $item['price'];
                $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                $report['input'] = $item['quantity'];
                $report['stock'] = $stock['quantity'];
                $report['total_money'] = ($report['price'] * $report['input']) - $report['discount'];
                $this->db->insert('report', $report);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = 1;
            }
        } else
            echo $this->messages = "0";
    }

    public function cms_save_input_return($store_id)
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
                    $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($inventory_quantity)) {
                        $this->db->where(['store_id' => $store_id, 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity'], 'user_upd' => $user_init]);
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $item['id'], 'quantity' => -$item['quantity'], 'user_init' => $user_init];
                        $this->db->insert('inventory', $inventory);
                    }

                    $canreturn = $this->db->select('quantity,price')->from('canreturn')->where(['input_id' => $order['input_id'], 'product_id' => $item['id']])->get()->row_array();
                    if (empty($canreturn) || $canreturn['quantity'] < 1 || $canreturn['quantity'] < $item['quantity']) {
                        $this->db->trans_rollback();
                        echo $this->messages = "0";
                        return;
                    } else {
                        $canreturn['quantity'] = $canreturn['quantity'] - $item['quantity'];
                        $canreturn['user_upd'] = $user_init;
                        $this->db->where(['input_id' => $order['input_id'], 'product_id' => $item['id']])->update('canreturn', $canreturn);
                    }

                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                    $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];
                    $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                    $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                    $total_quantity += $item['quantity'];
                    $this->db->where('ID', $item['id'])->update('products', $sls);
                    $detail_order[] = $item;
                }
            else
                foreach ($order['detail_order'] as $item) {
                    $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                    $total_quantity += $item['quantity'];
                    $detail_order[] = $item;
                }

            if ($order['coupon'] == 'NaN')
                $order['coupon'] = 0;

            $order['total_price'] = $total_price;
            $order['total_origin_price'] = $total_origin_price;
            $order['total_money'] = $total_price - $order['coupon'];
            $order['total_quantity'] = $total_quantity;
            $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
            $order['user_init'] = $this->auth['id'];
            $order['store_id'] = $store_id;
            $order['detail_order'] = json_encode($detail_order);

            $this->db->select_max('output_code')->like('output_code', 'PXT')->where('input_id >', 0);
            $max_output_code = $this->db->get('orders')->row();
            $max_code = (int)(str_replace('PXT', '', $max_output_code->output_code)) + 1;
            if ($max_code < 10)
                $order['output_code'] = 'PXT00000' . ($max_code);
            else if ($max_code < 100)
                $order['output_code'] = 'PXT0000' . ($max_code);
            else if ($max_code < 1000)
                $order['output_code'] = 'PXT000' . ($max_code);
            else if ($max_code < 10000)
                $order['output_code'] = 'PXT00' . ($max_code);
            else if ($max_code < 100000)
                $order['output_code'] = 'PXT0' . ($max_code);
            else if ($max_code < 1000000)
                $order['output_code'] = 'PXT' . ($max_code);

            $order['canreturn'] = 0;
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

            $check = $this->db
                ->select('sum(quantity) as total_quantity')
                ->from('canreturn')
                ->where('input_id', $order['input_id'])
                ->get()
                ->row_array();
            if (empty($check) || $check['total_quantity'] < 1) {
                $this->db->where('ID', $order['input_id'])->update('input', ['canreturn' => 0, 'user_upd' => $user_init]);
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

    public function cms_del_temp_import($id)
    {
        $id = (int)$id;
        $input = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $store_id = $input['store_id'];
        $this->db->trans_begin();
        $user_init = $this->auth['id'];
        if (isset($input) && count($input)) {
            if ($input['input_status'] == 1) {
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
                    $this->db->where(['transaction_id' => $id, 'product_id' => $item['id'], 'store_id' => $store_id])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);

                    $canreturn = $this->db->select('quantity,price')->from('canreturn')->where(['order_id' => $input['order_id'], 'product_id' => $item['id']])->get()->row_array();
                    if (!empty($canreturn)) {
                        $canreturn['quantity'] = $canreturn['quantity'] + $item['quantity'];
                        $canreturn['user_upd'] = $user_init;
                        $this->db->where(['order_id' => $input['order_id'], 'product_id' => $item['id']])->update('canreturn', $canreturn);
                    }
                }

                $this->db->where('input_id', $id)->update('payment', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where('ID', $id)->update('input', ['deleted' => 1, 'user_upd' => $user_init]);
            } else {
                $this->db->where('ID', $id)->update('input', ['deleted' => 1, 'user_upd' => $user_init]);
            }
        }

        $check = $this->db
            ->select('sum(quantity) as check_quantity,canreturn')
            ->from('canreturn')
            ->where('order_id', $input['order_id'])
            ->join('orders', 'orders.ID=canreturn.order_id', 'INNER')
            ->get()
            ->row_array();
        if ($check['check_quantity'] > 0 && $check['canreturn'] == 0) {
            $this->db->where('ID', $input['order_id'])->update('orders', ['canreturn' => 1, 'user_upd' => $user_init]);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_del_import($id)
    {
        $id = (int)$id;
        $input = $this->db->from('input')->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
        $this->db->trans_begin();
        if (isset($input) && count($input)) {
            $this->db->where('ID', $id)->update('input', ['deleted' => 2, 'user_upd' => $this->auth['id']]);
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

    public function cms_paging_input($page = 1)
    {
        $option = $this->input->post('data');
        $total_imports = 0;
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));
        if ($option['option1'] == '0') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_imports = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
                    ->from('input')
                    ->where(['deleted' => 0])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_imports'] = $this->db
                    ->from('input')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            } else {
                $total_imports = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
                    ->from('input')
                    ->where(['deleted' => 0])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_imports'] = $this->db
                    ->from('input')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        } else if ($option['option1'] == '1') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_imports = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
                    ->from('input')
                    ->where(['deleted' => 1])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_imports'] = $this->db
                    ->from('input')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 1])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            } else {
                $total_imports = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
                    ->from('input')
                    ->where(['deleted' => 1])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_imports'] = $this->db
                    ->from('input')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 1])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        } else if ($option['option1'] == '2') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_imports = $this->db
                    ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
                    ->from('input')
                    ->where(['deleted' => 0, 'lack >' => 0])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_imports'] = $this->db
                    ->from('input')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'lack >' => 0])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            } else {
                $total_imports = $this->db
                    ->from('input')
                    ->where(['deleted' => 0, 'lack >' => 0])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->row_array();
                $data['_list_imports'] = $this->db
                    ->from('input')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'lack >' => 0])
                    ->where("(input_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        }

        $config['base_url'] = 'cms_paging_input';
        $config['total_rows'] = $total_imports['quantity'];
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_imports'] = $total_imports;
        $data['auth_name'] = $this->auth['display_name'];
        if ($page > 1 && ($total_imports['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option1'];
        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/input/list_imports', isset($data) ? $data : null);
    }

    public function cms_print_input()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_input = $this->db->from('input')->where('id', $data_post['id_input'])->limit(1)->get()->row_array();
        $supplier_name = '';
        if ($data_input['supplier_id'] != null)
            $supplier_name = cms_getNamesupplierbyID($data_input['supplier_id']);

        $user_name = '';
        if ($data_input['supplier_id'] != null)
            $user_name = cms_getNameAuthbyID($data_input['user_init']);

        $data_template['content'] = str_replace("{Ten_Cua_Hang}", "Phong Tran", $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Nhập}", $data_input['input_date'], $data_template['content']);
        $data_template['content'] = str_replace("{Nha_Cung_Cap}", $supplier_name, $data_template['content']);
        $data_template['content'] = str_replace("{Thu_Ngan}", $user_name, $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien_Hang}", $this->cms_common->cms_encode_currency_format($data_input['total_price']), $data_template['content']);
        $data_template['content'] = str_replace("{Chiec_Khau}", $this->cms_common->cms_encode_currency_format($data_input['discount']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien}", $this->cms_common->cms_encode_currency_format($data_input['total_money'] - $data_input['discount']), $data_template['content']);
        $data_template['content'] = str_replace("{Tra_Tien}", $this->cms_common->cms_encode_currency_format($data_input['payed']), $data_template['content']);
        $data_template['content'] = str_replace("{Con_No}", $this->cms_common->cms_encode_currency_format($data_input['lack']), $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Phieu_Nhap}", $data_input['input_code'], $data_template['content']);
        $data_template['content'] = str_replace("{Ghi_Chu}", $data_input['notes'], $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", $this->convert_number_to_words($data_input['lack']), $data_template['content']);

        $detail = '';
        $number = 1;
        if (isset($data_input) && count($data_input)) {
            $list_products = json_decode($data_input['detail_input'], true);
            foreach ($list_products as $product) {
                $prd = cms_finding_productbyID($product['id']);
                $quantity = $product['quantity'];
                $total = $quantity * $product['price'];
                $detail = $detail . '<tr ><td  style="text-align:center;">' . $number++ . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '</td><td style = "text-align:center">' . $quantity . '</td ><td style = "text-align:center">' . $prd['prd_unit_name'] . '</td ><td  style="text-align:center;">' . $this->cms_common->cms_encode_currency_format($product['price']) . '</td><td style="text-align:center;">' . $this->cms_common->cms_encode_currency_format($total) . '</td ></tr>';
            }
        }

        $table = '<table border="1" style="width:100%;border-collapse:collapse;">
                    <tbody >
                        <tr >
                            <td style="text-align:center;"><strong >STT</strong ></td >
                            <td style="text-align:center;"><strong >Tên sản phẩm</strong ></td >
                            <td style="text-align:center;"><strong > SL</strong ></td >
                            <td style="text-align:center;"><strong > ĐVT</strong ></td >
                            <td style="text-align:center;"><strong >Đơn giá</strong ></td >
                            <td style="text-align:center;"><strong > Thành tiền</strong ></td >
                        </tr >' . $detail . '
                    </tbody >
                 </table >';

        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham}", $table, $data_template['content']);

        echo $this->messages = $data_template['content'];
    }

    public function cms_delete_payment_in_input($id)
    {
        $id = (int)$id;
        $payment = $this->db->from('payment')->where(['ID' => $id, 'deleted' => 0, 'type_id' => 2])->get()->row_array();
        $user_id = $this->auth['id'];
        $this->db->trans_begin();
        if (isset($payment) && count($payment)) {
            $input = $this->db->select('payed,lack')->from('input')->where(['ID' => $payment['input_id'], 'deleted' => 0])->get()->row_array();
            $input['payed'] = $input['payed'] - $payment['total_money'];
            $input['lack'] = $input['lack'] + $payment['total_money'];
            $input['user_upd'] = $user_id;
            $this->db->where('ID', $payment['input_id'])->update('input', $input);
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

    public function save_payment_input()
    {
        $payment = $this->input->post('data');
        if ($payment['store_id'] == $this->auth['store_id']) {
            $input = $this->db->from('input')->where(['ID' => $payment['input_id'], 'deleted' => 0])->get()->row_array();
            if ($input['lack'] > 0) {
                $this->db->trans_begin();
                $update_input = array();
                if ($payment['total_money'] > $input['lack']) {
                    $payment['total_money'] = $input['lack'];
                    $update_input['payed'] = $input['payed'] + $input['lack'];
                    $update_input['lack'] = 0;
                    $update_input['user_upd'] = $this->auth['id'];
                } else {
                    $update_input['payed'] = $input['payed'] + $payment['total_money'];
                    $update_input['lack'] = $input['lack'] - $payment['total_money'];
                    $update_input['user_upd'] = $this->auth['id'];
                }
                $this->db->where(['ID' => $payment['input_id'], 'deleted' => 0])->update('input', $update_input);

                if (empty($payment['payment_date'])) {
                    $payment['payment_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                } else {
                    $payment['payment_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $payment['payment_date'])) + 7 * 3600);;
                }

                $payment['user_init'] = $this->auth['id'];
                $payment['type_id'] = 2;
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

                $this->db->insert('payment', $payment);

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

    public function cms_detail_input()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = $this->input->post('id');
        $import = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $payment = $this->db->from('payment')->where(['input_id' => $id, 'type_id' => 2, 'deleted' => 0])->get()->result_array();
        $data['_list_products'] = array();

        if (isset($import) && count($import)) {
            $list_products = json_decode($import['detail_input'], true);

            foreach ($list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = $product['quantity'];
                $_product['price'] = $product['price'];
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_input'] = $import;
        $data['data']['_payment'] = $payment;
        $this->load->view('ajax/input/detail_input', isset($data) ? $data : null);
    }

    public function cms_edit_input()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $id = $this->input->post('id');
        $import = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $data['_list_products'] = array();

        if (isset($import) && count($import)) {
            $list_products = json_decode($import['detail_input'], true);

            foreach ($list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = $product['quantity'];
                $_product['price'] = $product['price'];
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_input'] = $import;
        $this->load->view('ajax/input/edit_import', isset($data) ? $data : null);
    }
}

