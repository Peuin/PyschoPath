<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Transfer extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(6, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'transfer/index';
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id', $this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_print_transfer()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_transfer = $this->db->from('transfer')->where('ID', $data_post['id_transfer'])->limit(1)->get()->row_array();
        $data_template['content'] = str_replace("{Ten_Cua_Hang}", "Phong Tran", $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Xuat}", $data_transfer['created'], $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_Xuat}", cms_getNameAuthbyID($data_transfer['user_init']), $data_template['content']);
        $data_template['content'] = str_replace("{Kho_Xuat}", cms_getNamestockbyID($data_transfer['from_store']), $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Nhan}", $data_transfer['updated'], $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_Nhan}", cms_getNameAuthbyID($data_transfer['user_upd']), $data_template['content']);
        $data_template['content'] = str_replace("{Kho_Nhan}", cms_getNamestockbyID($data_transfer['to_store']), $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Don_Hang}", $data_transfer['transfer_code'], $data_template['content']);
        $data_template['content'] = str_replace("{Ghi_Chu}", $data_transfer['notes'], $data_template['content']);
        $detail = '';
        $number = 1;
        if (isset($data_transfer) && count($data_transfer)) {
            $list_products = json_decode($data_transfer['detail_transfer'], true);
            foreach ($list_products as $product) {
                $prd = cms_finding_productbyID($product['id']);
                $quantity = $product['quantity'];
                $detail = $detail . '<tr ><td  style="text-align:center;">' . $number++ . '</td><td  style="text-align:center;">' . $prd['prd_code'] . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '</td><td style = "text-align:center">' . $quantity . '</td ></tr>';
            }
        }

        $table = '<table border="1" style="width:100%;border-collapse:collapse;">
                    <tbody >
                        <tr >
                            <td style="text-align:center;"><strong >STT</strong ></td>
                            <td style="text-align:center;"><strong >Mã sản phẩm</strong ></td>
                            <td style="text-align:center;"><strong >Tên sản phẩm</strong ></td >
                            <td style="text-align:center;"><strong >SL</strong ></td >
                        </tr >' . $detail . '
                    </tbody >
                 </table >';

        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham}", $table, $data_template['content']);

        echo $this->messages = $data_template['content'];
    }

    public function cms_paging_transfer($page = 1)
    {
        $option = $this->input->post('data');
        $total_transfer = 0;
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option1'] == '0') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_transfer = $this->db
                    ->select('transfer_status, count(*) as count')
                    ->from('transfer')
                    ->where('deleted', 0)
                    ->where('created >=', $option['date_from'])
                    ->where('created <=', $option['date_to'])
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->group_by('transfer_status')
                    ->get()
                    ->result_array();
                $data['_list_transfer'] = $this->db
                    ->from('transfer')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('deleted', 0)
                    ->where('created >=', $option['date_from'])
                    ->where('created <=', $option['date_to'])
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            } else {
                $total_transfer = $this->db
                    ->select('transfer_status, count(*) as count')
                    ->from('transfer')
                    ->where('deleted', 0)
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->group_by('transfer_status')
                    ->get()
                    ->result_array();
                $data['_list_transfer'] = $this->db
                    ->from('transfer')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('deleted', 0)
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        } else if ($option['option1'] == '1') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_transfer = $this->db
                    ->select('transfer_status, count(*) as count')
                    ->from('transfer')
                    ->where('deleted', 0)
                    ->where('transfer_status', 0)
                    ->where('created >=', $option['date_from'])
                    ->where('created <=', $option['date_to'])
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->group_by('transfer_status')
                    ->get()
                    ->result_array();
                $data['_list_transfer'] = $this->db
                    ->from('transfer')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('deleted', 0)
                    ->where('transfer_status', 0)
                    ->where('created >=', $option['date_from'])
                    ->where('created <=', $option['date_to'])
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            } else {
                $total_transfer = $this->db
                    ->select('transfer_status, count(*) as count')
                    ->from('transfer')
                    ->where('transfer_status', 0)
                    ->where('deleted', 0)
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
                $data['_list_transfer'] = $this->db
                    ->from('transfer')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('transfer_status', 0)
                    ->where('deleted', 0)
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        } else if ($option['option1'] == '2') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $total_transfer = $this->db
                    ->select('transfer_status, count(*) as count')
                    ->from('transfer')
                    ->where('deleted', 0)
                    ->where('transfer_status', 1)
                    ->where('created >=', $option['date_from'])
                    ->where('created <=', $option['date_to'])
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
                $data['_list_transfer'] = $this->db
                    ->from('transfer')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('deleted', 0)
                    ->where('transfer_status', 1)
                    ->where('created >=', $option['date_from'])
                    ->where('created <=', $option['date_to'])
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            } else {
                $total_transfer = $this->db
                    ->select('transfer_status, count(*) as count')
                    ->from('transfer')
                    ->where('deleted', 0)
                    ->where('transfer_status', 1)
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
                $data['_list_transfer'] = $this->db
                    ->from('transfer')
                    ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                    ->order_by('created', 'desc')
                    ->where('deleted', 0)
                    ->where('transfer_status', 1)
                    ->where("(transfer_code LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                    ->get()
                    ->result_array();
            }
        }

        $status0 = 0;
        $status1 = 0;
        foreach ($total_transfer as $transfer) {
            if ($transfer['transfer_status'] == 0)
                $status0 = $transfer['count'];
            else
                $status1 = $transfer['count'];
        }

        $total_row = $status0 + $status1;
        $config['base_url'] = 'cms_paging_transfer';
        $config['total_rows'] = $total_row;
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_rows'] = $total_row;
        $data['total_status0'] = $status0;
        $data['total_status1'] = $status1;

        if ($page > 1 && ($total_row - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option1'];
        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/transfer/list_transfer', isset($data) ? $data : null);
    }

    public function cms_accept_transfer($id)
    {
        $id = (int)$id;
        $user_id = $this->auth['id'];
        $transfer = $this->db->from('transfer')->where(['ID' => $id, 'transfer_status' => 0])->get()->row_array();
        if (!empty($transfer) && count($transfer)) {
            $this->db->trans_begin();
            $temp['transaction_code'] = $transfer['transfer_code'];
            $temp['transaction_id'] = $id;
            $temp['notes'] = $transfer['notes'];
            $temp['user_init'] = $user_id;
            $temp['type'] = 5;
            $temp['store_id'] = $transfer['to_store'];
            $list_products = json_decode($transfer['detail_transfer'], true);
            foreach ($list_products as $item) {
                $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $transfer['to_store'], 'product_id' => $item['id']])->get()->row_array();
                if (!empty($inventory_quantity)) {
                    $this->db->where(['store_id' => $transfer['to_store'], 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_id]);
                } else {
                    $inventory = ['store_id' => $transfer['to_store'], 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_id];
                    $this->db->insert('inventory', $inventory);
                }

                $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
                $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                $this->db->where('ID', $item['id'])->update('products', $sls);

                $report = $temp;
                $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $transfer['to_store'], 'product_id' => $item['id']])->get()->row_array();
                $report['product_id'] = $item['id'];
                $report['input'] = $item['quantity'];
                $report['stock'] = $stock['quantity'];
                $this->db->insert('report', $report);
            }

            $this->db->where(['ID' => $id, 'transfer_status' => 0])->update('transfer', ['user_upd' => $user_id, 'transfer_status' => 1]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = 1;
            }
        } else {
            echo $this->messages = "0";
        }
    }

    public function cms_detail_transfer()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = $this->input->post('id');
        $transfer = $this->db->from('transfer')->where('ID', $id)->get()->row_array();
        $data['_list_products'] = array();

        if (isset($transfer) && count($transfer)) {
            $list_products = json_decode($transfer['detail_transfer'], true);

            foreach ($list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = $product['quantity'];
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_transfer'] = $transfer;
        $this->load->view('ajax/transfer/detail_transfer', isset($data) ? $data : null);
    }

    public function cms_del_temp_transfer($id)
    {
        $id = (int)$id;
        $transfer = $this->db->from('transfer')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
        $user_id = $this->auth['id'];
        if ($transfer['transfer_status'] == 0) {
            $this->db->trans_begin();

            $list_products = json_decode($transfer['detail_transfer'], true);
            foreach ($list_products as $item) {
                $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $transfer['from_store'], 'product_id' => $item['id']])->get()->row_array();
                if (!empty($inventory_quantity)) {
                    $this->db->where(['store_id' => $transfer['from_store'], 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity'], 'user_upd' => $user_id]);
                } else {
                    $inventory = ['store_id' => $transfer['from_store'], 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_id];
                    $this->db->insert('inventory', $inventory);
                }

                $product = $this->db->select('prd_sls')->from('products')->where('ID', $item['id'])->get()->row_array();
                $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];
                $this->db->where('ID', $item['id'])->update('products', $sls);
                $this->db->where(['transaction_id' => $id, 'product_id' => $item['id'], 'store_id' => $transfer['from_store'],'type' => 4])->update('report', ['deleted' => 1, 'user_upd' => $user_id]);
            }

            $this->db->where('ID', $id)->update('transfer', ['deleted' => 1, 'user_upd' => $user_id]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        } else if ($transfer['transfer_status'] == 1) {
            $this->db->trans_begin();

            $list_products = json_decode($transfer['detail_transfer'], true);
            foreach ($list_products as $item) {
                $inventory_quantity_from = $this->db->select('quantity')->from('inventory')->where(['store_id' => $transfer['from_store'], 'product_id' => $item['id']])->get()->row_array();
                if (!empty($inventory_quantity_from)) {
                    $this->db->where(['store_id' => $transfer['from_store'], 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity_from['quantity'] + $item['quantity'], 'user_upd' => $user_id]);
                } else {
                    $inventory = ['store_id' => $transfer['from_store'], 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_id];
                    $this->db->insert('inventory', $inventory);
                }

                $inventory_quantity_to = $this->db->select('quantity')->from('inventory')->where(['store_id' => $transfer['to_store'], 'product_id' => $item['id']])->get()->row_array();
                if (!empty($inventory_quantity_to)) {
                    $this->db->where(['store_id' => $transfer['to_store'], 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity_to['quantity'] - $item['quantity'], 'user_upd' => $user_id]);
                } else {
                    $inventory = ['store_id' => $transfer['to_store'], 'product_id' => $item['id'], 'quantity' => $item['quantity'], 'user_init' => $user_id];
                    $this->db->insert('inventory', $inventory);
                }

                $this->db->where(['transaction_id' => $id, 'product_id' => $item['id'], 'store_id' => $transfer['from_store'],'type' => 4])->update('report', ['deleted' => 1, 'user_upd' => $user_id]);
                $this->db->where(['transaction_id' => $id, 'product_id' => $item['id'], 'store_id' => $transfer['to_store'],'type' => 5])->update('report', ['deleted' => 1, 'user_upd' => $user_id]);
            }

            $this->db->where('ID', $id)->update('transfer', ['deleted' => 1, 'user_upd' => $user_id]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        } else {
            echo $this->messages = "0";
        }
    }

    public function cms_del_order($id)
    {
        $id = (int)$id;
        $order = $this->db->from('transfer')->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
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

    public function cms_add_transfer()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['data'] = $this->db->from('stores')->get()->result_array();
        $this->load->view('ajax/transfer/add_transfer', isset($data) ? $data : null);
    }

    public function cms_detail_order()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $id = $this->input->post('id');
        $order = $this->db->from('transfer')->where('ID', $id)->get()->row_array();
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
        $this->load->view('ajax/orders/detail_order', isset($data) ? $data : null);
    }

    public function cms_edit_order()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $id = $this->input->post('id');
        $order = $this->db->from('transfer')->where(['ID' => $id, 'order_status' => 0])->get()->row_array();
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
        $product = $this->db->from('products')->where('ID', $id)->get()->row_array();
        if (isset($product) && count($product) != 0) {
            ob_start(); ?>
            <tr data-id="<?php echo $product['ID']; ?>">
                <td class="text-center seq"><?php echo $seq; ?></td>
                <td><?php echo $product['prd_code']; ?></td>
                <td><?php echo $product['prd_name']; ?></td>
                <td class="text-center" style="max-width: 30px;"><input style="max-height: 22px;" type="text"
                                                                        class="txtNumber form-control quantity_product_order text-center"
                                                                        value="1"></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-transfer"></i></td>
            </tr>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function cms_save_transfer()
    {
        $transfer = $this->input->post('data');
        $detail_transfer_temp = $transfer['detail_transfer'];
        $this->db->trans_begin();
        $user_init = $this->auth['id'];
        $total_quantity = 0;

        foreach ($transfer['detail_transfer'] as $item) {
            $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $transfer['from_store'], 'product_id' => $item['id']])->get()->row_array();
            if (!empty($inventory_quantity)) {
                $this->db->where(['store_id' => $transfer['from_store'], 'product_id' => $item['id']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity'], 'user_upd' => $user_init]);
            } else {
                $inventory = ['store_id' => $transfer['from_store'], 'product_id' => $item['id'], 'quantity' => -$item['quantity'], 'user_init' => $user_init];
                $this->db->insert('inventory', $inventory);
            }

            $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();
            $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];
            $total_quantity += $item['quantity'];
            $this->db->where('ID', $item['id'])->update('products', $sls);
            $detail_transfer[] = $item;
        }

        $transfer['total_quantity'] = $total_quantity;
        $transfer['user_init'] = $this->auth['id'];
        $transfer['detail_transfer'] = json_encode($detail_transfer);
        $this->db->select_max('transfer_code')->like('transfer_code', 'CK');
        $max_transfer_code = $this->db->get('transfer')->row();
        $max_code = (int)(str_replace('CK', '', $max_transfer_code->transfer_code)) + 1;
        if ($max_code < 10)
            $transfer['transfer_code'] = 'CK000000' . ($max_code);
        else if ($max_code < 100)
            $transfer['transfer_code'] = 'CK00000' . ($max_code);
        else if ($max_code < 1000)
            $transfer['transfer_code'] = 'CK0000' . ($max_code);
        else if ($max_code < 10000)
            $transfer['transfer_code'] = 'CK000' . ($max_code);
        else if ($max_code < 100000)
            $transfer['transfer_code'] = 'CK00' . ($max_code);
        else if ($max_code < 1000000)
            $transfer['transfer_code'] = 'CK0' . ($max_code);
        else if ($max_code < 10000000)
            $transfer['transfer_code'] = 'CK' . ($max_code);

        $this->db->insert('transfer', $transfer);
        $id = $this->db->insert_id();

        $temp = array();
        $temp['transaction_code'] = $transfer['transfer_code'];
        $temp['transaction_id'] = $id;
        $temp['notes'] = $transfer['notes'];
        $temp['user_init'] = $transfer['user_init'];
        $temp['type'] = 4;
        $temp['store_id'] = $transfer['from_store'];
        foreach ($detail_transfer_temp as $item) {
            $report = $temp;
            $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $temp['store_id'], 'product_id' => $item['id']])->get()->row_array();
            $report['product_id'] = $item['id'];
            $report['output'] = $item['quantity'];
            $report['stock'] = $stock['quantity'];
            $this->db->insert('report', $report);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = $id;
        }
    }
}

