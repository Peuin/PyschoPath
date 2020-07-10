<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Product extends CI_Controller
{
    public $auth;
    private $messages = '0';

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(3, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $manufacture = $this->db->from('products_manufacture')->get()->result_array();
        $data['data']['_prd_manufacture'] = $manufacture;
        $data['data']['user'] = $this->auth;
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id',$this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $data['template'] = 'products/index';
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_vcrproduct()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        $sls_manufacture = $this->db->from('products_manufacture')->get()->result_array();
        $sls_unit = $this->db->from('products_unit')->get()->result_array();
        $data['data']['_prd_group'] = $sls_group;
        $data['data']['_prd_manufacture'] = $sls_manufacture;
        $data['data']['_prd_unit'] = $sls_unit;
        $this->load->view('products/add_prd', isset($data) ? $data : null);
    }

    public function cms_clone_product($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $product = $this->db->from('products')->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['data']['_detail_product'] = $product;
            $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
            $sls_manufacture = $this->db->from('products_manufacture')->get()->result_array();
            $sls_unit = $this->db->from('products_unit')->get()->result_array();
            $data['data']['_prd_group'] = $sls_group;
            $data['data']['_prd_manufacture'] = $sls_manufacture;
            $data['data']['_prd_unit'] = $sls_unit;
            $this->load->view('products/add_prd', isset($data) ? $data : null);
        }
    }

    public function cms_edit_product($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $product = $this->db->from('products')->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['data']['_detail_product'] = $product;
            $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
            $sls_manufacture = $this->db->from('products_manufacture')->get()->result_array();
            $sls_unit = $this->db->from('products_unit')->get()->result_array();
            $data['data']['_prd_group'] = $sls_group;
            $data['data']['_prd_manufacture'] = $sls_manufacture;
            $data['data']['_prd_unit'] = $sls_unit;
            $this->load->view('products/edit_prd', isset($data) ? $data : null);
        }
    }

    public function cms_create_manufacture()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data = $this->input->post('data');
        $prd_manuf = $this->db->from('products_manufacture')->where('prd_manuf_name', $data['prd_manuf_name'])->get()->row_array();
        if (!empty($prd_manuf) && count($prd_manuf)) {
            echo $this->messages = '0';
            return;
        } else {
            $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_manufacture', $data);
            echo $this->messages = '1';
        }
    }

    public function cms_create_unit()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data = $this->input->post('data');
        $prd_unit = $this->db->from('products_unit')->where('prd_unit_name', $data['prd_unit_name'])->get()->row_array();
        if (!empty($prd_unit) && count($prd_unit)) {
            echo $this->messages = '0';
            return;
        } else {
            $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_unit', $data);
            echo $this->messages = '1';
        }
    }

    public function cms_paging_manufacture($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $total_prdmanuf = $this->db->from('products_manufacture')->count_all_results();
        $config['base_url'] = 'cms_paging_manufacture';
        $config['total_rows'] = $total_prdmanuf;
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data ['_list_prd_manuf'] = $this->db->from('products_manufacture')->limit($config['per_page'], ($page - 1) * $config['per_page'])->order_by('created', 'desc')->get()->result_array();
        if ($page > 1 && ($total_prdmanuf - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data ['page'] = $page;
        $this->load->view('ajax/product/list_prd_manufacture', isset($data) ? $data : null);
    }

    public function cms_paging_unit($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $total_prdunit = $this->db->from('products_unit')->count_all_results();
        $config['base_url'] = 'cms_paging_unit';
        $config['total_rows'] = $total_prdunit;
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data ['_list_prd_unit'] = $this->db->from('products_unit')->limit($config['per_page'], ($page - 1) * $config['per_page'])->order_by('created', 'desc')->get()->result_array();
        if ($page > 1 && ($total_prdunit - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data ['page'] = $page;
        $this->load->view('ajax/product/list_prd_unit', isset($data) ? $data : null);
    }

    public function cms_delete_manufacture($id)
    {
        $id = (int)$id;
        $prd_manuf = $this->db->from('products_manufacture')->where('ID', $id)->get()->row_array();
        if (!isset($prd_manuf) || count($prd_manuf) == 0) {
            echo $this->messages;
            return;
        } else {
            $this->db->where('ID', $id)->delete('products_manufacture');
            echo $this->messages = '1';
        }
    }

    public function cms_delete_unit($id)
    {
        $id = (int)$id;
        $prd_manuf = $this->db->from('products_unit')->where('ID', $id)->get()->row_array();
        if (!isset($prd_manuf) || count($prd_manuf) == 0) {
            echo $this->messages;
            return;
        } else {
            $this->db->where('ID', $id)->delete('products_unit');
            echo $this->messages = '1';
        }
    }

    public function cms_update_prdmanufacture($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $prd_manuf = $this->db->from('products_manufacture')->where('ID', $id)->get()->row_array();
        if (!empty($prd_manuf) || count($prd_manuf) != 0) {
            $check = $this->db->from('products_manufacture')->where('prd_manuf_name', $data['prd_manuf_name'])->get()->result_array();
            if (empty($check) && count($check) == 0) {
                $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                $data['user_upd'] = $this->auth['id'];
                $this->db->where('ID', $id)->update('products_manufacture', $data);
                echo $this->messages = '1';
            }
        } else
            echo $this->messages = '0';
    }

    public function cms_update_prdunit($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $prd_manuf = $this->db->from('products_unit')->where('ID', $id)->get()->row_array();
        if (!empty($prd_manuf) || count($prd_manuf) != 0) {
            $check = $this->db->from('products_unit')->where('prd_unit_name', $data['prd_unit_name'])->get()->result_array();
            if (empty($check) && count($check) == 0) {
                $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                $data['user_upd'] = $this->auth['id'];
                $this->db->where('ID', $id)->update('products_unit', $data);
                echo $this->messages = '1';
            }
        } else
            echo $this->messages = '0';
    }

    public function cms_create_group()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data = $this->input->post('data');
        $data['level'] = 0;
        if (isset($data['parentid']) && $data['parentid'] > 0) {
            $level = $this->db->select('level')->from('products_group')->where('ID', $data['parentid'])->limit(1)->get()->row_array();
            $data['level'] = $level['level'] + 1;
            $prd_group = $this->db->from('products_group')->where(['parentid' => $data['parentid'], 'prd_group_name' => $data['prd_group_name']])->get()->row_array();
        } else {
            $prd_group = $this->db->from('products_group')->where(['parentid' => 0, 'prd_group_name' => $data['prd_group_name']])->get()->row_array();
        }

        if (!empty($prd_group) && count($prd_group)) {
            echo $this->messages = '0';
            return;
        } else {
            $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_group', $data);
            echo $this->messages = '1';
        }
    }

    public function cms_load_listgroup()
    {
        $this->cms_nestedset->set('products_group');
        $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        ob_start();
        echo '<option value="-1" selected="selected">--Danh mục--</option>';
        echo '<optgroup label="Chọn danh mục">';
        if ($sls_group)
            foreach ($sls_group as $key => $val) :
                ?>
                <option
                    value="<?php echo $val['id']; ?>"><?php echo $val['prd_group_name']; ?>
                </option>
            <?php
            endforeach;

        echo '</optgroup>';
        echo '<optgroup label="------------------------">
                                                <option value="product_group" data-toggle="modal" data-target="#list-prd-group">Tạo mới danh
                                                    mục
                                                </option>
                                            </optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_load_listgroup_withoutCreate()
    {
        $this->cms_nestedset->set('products_group');
        $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        ob_start();
        echo '<option value="-1" selected="selected">--Danh mục--</option>';
        echo '<optgroup label="Chọn danh mục">';
        if ($sls_group)
            foreach ($sls_group as $key => $val) :
                ?>
                <option
                    value="<?php echo $val['id']; ?>"><?php echo $val['prd_group_name']; ?>
                </option>
            <?php
            endforeach;
        echo '</optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_load_listmanufacture()
    {
        $this->cms_nestedset->set('products_group');
        $data = $this->db->from('products_manufacture')->order_by('created', 'desc')->get()->result_array();
        ob_start();
        echo '<option value="-1" selected="selected">--Nhà sản xuất--</option>';
        echo '<optgroup label="Chọn nhà sản xuất">';
        foreach ($data as $key => $item) :
            ?>
            <option
                value="<?php echo $item['ID']; ?>"><?php echo $item['prd_manuf_name']; ?>
            </option>
        <?php
        endforeach;
        echo '</optgroup>';
        echo '<optgroup label="------------------------">
        <option value="product_manufacture" data-toggle="modal" data-target="#list-prd-manufacture">Tạo mới nhà sản xuất
        </option></optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_load_listunit()
    {
        $this->cms_nestedset->set('products_group');
        $data = $this->db->from('products_unit')->order_by('created', 'desc')->get()->result_array();
        ob_start();
        echo '<option value="-1" selected="selected">--Đơn vị tính--</option>';
        echo '<optgroup label="Chọn đơn vị tính">';
        foreach ($data as $key => $item) :
            ?>
            <option
                value="<?php echo $item['ID']; ?>"><?php echo $item['prd_unit_name']; ?>
            </option>
        <?php
        endforeach;
        echo '</optgroup>';
        echo '<optgroup label="------------------------">
        <option value="product_unit" data-toggle="modal" data-target="#list-prd-unit">Tạo mới đơn vị tính
        </option></optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_paging_group($page = 1)
    {
        $this->cms_nestedset->set('products_group');
        $config = $this->cms_common->cms_pagination_custom();
        $total_prdGroup = $this->db->from('products_group')->count_all_results();
        $config['base_url'] = 'cms_paging_group';
        $config['total_rows'] = $total_prdGroup;
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data ['_list_prd_group'] = $this->cms_nestedset->data('products_group', NULL, ['per_page' => $config['per_page'], 'page' => $page]);
        if ($page > 1 && ($total_prdGroup - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data ['page'] = $page;
        $this->load->view('ajax/product/list_prd_group', isset($data) ? $data : null);
    }

    public function cms_save_item_prdGroup($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $prd_group = $this->db->from('products_group')->where('id', $id)->get()->row_array();
        if (empty($prd_group) && count($prd_group) == 0) {
            echo $this->messages = '0';
            return;
        }
        $prd_group_check = $this->db->from('products_group')->where(['parentid' => $prd_group['parentid'], 'prd_group_name' => $data['prd_group_name']])->get()->row_array();
        if (empty($prd_group_check) || count($prd_group_check) == 0) {
            $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_upd'] = $this->auth['id'];
            $this->db->where('ID', $id)->update('products_group', $data);
            echo $this->messages = '1';
        } else
            echo $this->messages = '0';
    }

    public function cms_delete_Group($id)
    {
        $id = (int)$id;
        $prd_group = $this->db->where('id', $id)->from('products_group')->get()->row_array();
        if (isset($prd_group) && count($prd_group)) {
            $countitem = $this->db->where('parentid', $prd_group['ID'])->from('products_group')->count_all_results();
            $countprd = $this->db->where('prd_group_id', $prd_group['ID'])->from('products')->count_all_results();
            if ($countitem > 0) {
                echo $this->messages = 'Không thể xóa danh mục khi có danh mục cấp con.';;
            } elseif ($countprd > 0) {
                echo $this->messages = '2';
            } else {
                $this->db->delete('products_group', ['id' => $id]);
                echo $this->messages = '1';
            }
        }
    }

    public function cms_delete_Group_WithProduct($id)
    {
        $data['prd_group_id'] = 0;
        $this->db->where('prd_group_id', $id)->update('products', $data);
        $this->db->delete('products_group', ['id' => $id]);
        echo $this->messages = '1';
    }

    public function upload_img(){
        $path = "public/templates/uploads/";
        $valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
        if(isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST")
        {
            $name = $_FILES['photo']['name'];
            $size = $_FILES['photo']['size'];
            if(strlen($name)) {
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext,$valid_formats)) {
                    if($size<(10024*10024)) {
                        $image_name = time().".".$ext;
                        $tmp = $_FILES['photo']['tmp_name'];
                        if(move_uploaded_file($tmp, $path.$image_name)){
                            echo $image_name;
                        }
                        else
                            echo "Image Upload failed";
                    }
                    else
                        echo "Image file size max 10 MB";
                }
                else
                    echo "Invalid file format..";
            }
            else
                echo "Please select image..!";
            exit;
        }
    }

    public function cms_add_product($store_id)
    {
        $data = $this->input->post('data');
        $data = $this->cms_common_string->allow_post($data, ['prd_code', 'prd_name', 'prd_sls', 'prd_inventory', 'prd_allownegative','prd_image_url', 'prd_origin_price', 'prd_sell_price', 'prd_group_id', 'prd_manufacture_id','prd_unit_id', 'prd_vat', 'prd_descriptions', 'display_website', 'prd_new', 'prd_hot', 'prd_highlight']);
        $check_code = $this->db->select('ID')->from('products')->where('prd_code', $data['prd_code'])->get()->row_array();
        if (!empty($check_code) && count($check_code)) {
            echo $this->messages = 'Mã sản phẩm ' . $data['prd_code'] . ' đã tồn tại trong hệ thống. Vui lòng chọn mã khác.';
        } else {
            $this->db->trans_begin();
            $data['user_init'] = $this->auth['id'];

            if ($data['prd_code'] == '') {
                $this->db->select_max('prd_code')->like('prd_code', 'SP');
                $max_product_code = $this->db->get('products')->row();
                $max_code = (int)(str_replace('SP', '', $max_product_code->prd_code)) + 1;
                if ($max_code < 10)
                    $data['prd_code'] = 'SP0000' . ($max_code);
                else if ($max_code < 100)
                    $data['prd_code'] = 'SP000' . ($max_code);
                else if ($max_code < 1000)
                    $data['prd_code'] = 'SP00' . ($max_code);
                else if ($max_code < 10000)
                    $data['prd_code'] = 'SP0' . ($max_code);
                else if ($max_code < 100000)
                    $data['prd_code'] = 'SP' . ($max_code);
            }

            if($data['prd_sls']=='')
                $data['prd_sls']= 0;

            $quantity = $data['prd_sls'];
            $this->db->insert('products', $data);
            $product_id = $this->db->insert_id();
            $user_init = $data['user_init'];
            $inventory = ['store_id'=>$store_id,'product_id'=>$product_id,'quantity'=>$quantity,'user_init'=>$user_init];
            $this->db->insert('inventory', $inventory);

            $report= array();
            $report['transaction_code'] = $data['prd_code'];
            $report['notes'] = 'Khai báo hàng hóa';
            $report['user_init'] = $data['user_init'];
            $report['type'] = 1;
            $report['store_id'] = $store_id;
            $report['product_id'] = $product_id;
            $report['input'] = $quantity;
            $report['stock'] = $quantity;

            $this->db->insert('report', $report);

            if ($this->db->trans_status() === FALSE)
            {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            }
            else
            {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        }
    }

    public function cms_update_product($id)
    {
        $data = $this->input->post('data');
        $data = $this->cms_common_string->allow_post($data, ['prd_code', 'prd_name', 'prd_sls', 'prd_inventory', 'prd_allownegative', 'prd_origin_price', 'prd_sell_price', 'prd_group_id', 'prd_manufacture_id','prd_unit_id','prd_image_url', 'prd_descriptions', 'display_website', 'prd_new', 'prd_hot', 'prd_highlight']);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('ID', $id)->update('products', $data);
        echo $this->messages = "1";
    }

    public function cms_paging_product($page = 1)
    {
        $option = $this->input->post('data');
        $total_prd = 0;
        $config = $this->cms_common->cms_pagination_custom();
        if ($option['option1'] == '0') {
            if ($option['option2'] == '-1') {
                if ($option['option3'] == '-1') {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 1, 'deleted' => 0])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 1, 'deleted' => 0])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                } else {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 1, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 1, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()->result_array();
                }
            } else {
                $temp = $this->getCategoriesByParentId($option['option2']);
                $temp[] = $option['option2'];
                if ($option['option3'] == '-1') {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 1, 'deleted' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 1, 'deleted' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                } else {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 1, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 1, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                }
            }
        } else if ($option['option1'] == '1') {
            if ($option['option2'] == '-1') {
                if ($option['option3'] == '-1') {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 0, 'deleted' => 0])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 0, 'deleted' => 0])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                } else {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 0, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 0, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                }
            } else {
                $temp = $this->getCategoriesByParentId($option['option2']);
                $temp[] = $option['option2'];
                if ($option['option3'] == '-1') {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 0, 'deleted' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 0, 'deleted' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                } else {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['prd_status' => 0, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['prd_status' => 0, 'deleted' => 0, 'prd_manufacture_id' => $option['option3']])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                }
            }
        } else if ($option['option1'] == '2') {
            if ($option['option2'] == '-1') {
                if ($option['option3'] == '-1') {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['deleted' => 1])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['deleted' => 1])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                } else {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['deleted' => 1, 'prd_manufacture_id' => $option['option3']])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['deleted' => 1, 'prd_manufacture_id' => $option['option3']])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                }
            } else {
                $temp = $this->getCategoriesByParentId($option['option2']);
                $temp[] = $option['option2'];
                if ($option['option3'] == '-1') {
                    $total_prd = $this->db
                        ->from('products')
                        ->where('deleted', 1)
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where('deleted', 1)
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                } else {
                    $total_prd = $this->db
                        ->from('products')
                        ->where(['deleted' => 1, 'prd_manufacture_id' => $option['option3']])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
                        ->from('products')
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('created', 'desc')
                        ->where(['deleted' => 1, 'prd_manufacture_id' => $option['option3']])
                        ->where_in('prd_group_id',$temp)
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->get()
                        ->result_array();
                }
            }
        }

        $config['base_url'] = 'cms_paging_product';
        $config['total_rows'] = $total_prd;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['data']['_sl_product'] = $total_prd;
        $data['data']['_sl_manufacture'] = $this->db->from('products_manufacture')->count_all_results();
        $data['_pagination_link'] = $_pagination_link;
        if ($page > 1 && ($total_prd - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['data']['option'] = $option['option1'];
        $data['data']['page'] = $page;
        $this->load->view('ajax/product/list_products', isset($data) ? $data : null);
    }

    function getCategoriesByParentId($category_id) {
        $category_data = array();

        $category_query = $this->db
            ->from('products_group')
            ->where('parentid',$category_id)
            ->get();

        foreach ($category_query->result() as $category) {
            $category_data[] = $category->ID;
            $children = $this->getCategoriesByParentId($category->ID);

            if ($children) {
                $category_data = array_merge($children, $category_data);
            }
        }

        return $category_data;
    }

    public function cms_delete_product($id)
    {
        $id = (int)$id;
        $product = $this->db->select('prd_name')->from('products')->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $this->db->where('ID', $id)->update('products', ['deleted' => 1]);
            echo $this->messages = '1';
        } else {
            echo $this->messages;
        }
    }

    public function cms_restore_product_deleted($id)
    {
        $id = (int)$id;
        $product = $this->db->select('prd_name')->from('products')->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $this->db->where('ID', $id)->update('products', ['deleted' => 0]);
            echo $this->messages = '1';
        } else {
            echo $this->messages;
        }
    }

    public function cms_restore_product_deactivated($id)
    {
        $id = (int)$id;
        $product = $this->db->select('prd_name')->from('products')->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $this->db->where('ID', $id)->update('products', ['prd_status' => 1]);
            echo $this->messages = '1';
        } else {
            echo $this->messages;
        }
    }

    public function cms_deactivate_product($id)
    {
        $id = (int)$id;
        $product = $this->db->select('prd_name')->from('products')->get()->row_array();
        if (!empty($product) && count($product)) {
            $this->db->where('ID', $id)->update('products', ['prd_status' => 0]);
            echo $this->messages = '1';
        } else {
            echo $this->messages;
        }
    }

    public function cms_detail_product($id)
    {
        $id = (int)$id;
        $product = $this->db->from('products')->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $this->load->view('ajax/product/detail_product', isset($data) ? $data : null);
        }
    }

    public function cms_detail_product_deleted($id)
    {
        $id = (int)$id;
        $product = $this->db->from('products')->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $this->load->view('ajax/product/detail_product_deleted', isset($data) ? $data : null);
        }
    }

    public function cms_detail_product_deactivated($id)
    {
        $id = (int)$id;
        $product = $this->db->from('products')->where(['ID' => $id, 'prd_status' => 0])->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $this->load->view('ajax/product/detail_product_deactivated', isset($data) ? $data : null);
        }
    }
}