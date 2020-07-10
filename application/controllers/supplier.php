<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Supplier extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function cms_crsup()
    {
        $data = $this->input->post('data');
        $data = $this->cms_common_string->allow_post($data, ['supplier_code','supplier_name', 'supplier_phone', 'supplier_email', 'supplier_addr', 'tax_code', 'notes']);
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        if ($data['supplier_code'] == '') {
            $this->db->select_max('supplier_code')->like('supplier_code', 'NCC');
            $max_supplier_code = $this->db->get('suppliers')->row();
            $max_code = (int)(str_replace('NCC', '', $max_supplier_code->supplier_code)) + 1;
            if ($max_code < 10)
                $data['supplier_code'] = 'NCC0000' . ($max_code);
            else if ($max_code < 100)
                $data['supplier_code'] = 'NCC000' . ($max_code);
            else if ($max_code < 1000)
                $data['supplier_code'] = 'NCC00' . ($max_code);
            else if ($max_code < 10000)
                $data['supplier_code'] = 'NCC0' . ($max_code);
            else if ($max_code < 100000)
                $data['supplier_code'] = 'NCC' . ($max_code);

            $this->db->insert('suppliers', $data);
            $id = $this->db->insert_id();
            echo $this->messages = $id;
        } else {
            $count = $this->db->where('supplier_code', $data['supplier_code'])->from('suppliers')->count_all_results();
            if ($count > 0) {
                echo $this->messages = "0";
            } else {
                $this->db->insert('suppliers', $data);
                $id = $this->db->insert_id();
                echo $this->messages = $id;
            }
        }
    }

    public function cms_detail_input_in_supplier()
    {
        if ($this->auth == null) 
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        
        $id = $this->input->post('id');
        $input = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $data['_list_products'] = array();

        if (isset($input) && count($input)) {
            $list_products = json_decode($input['detail_input'], true);

            foreach ($list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = $product['quantity'];
                $_product['price'] = $product['price'];
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_import'] = $input;
        $this->load->view('ajax/customer-supplier/detail_input', isset($data) ? $data : null);
    }
    
    public function cms_paging_input_by_supplier_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();

        $total_inputs = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('input')
            ->where('deleted', 0)
            ->where('supplier_id', $option['supplier_id'])
            ->get()
            ->row_array();
        $data['_list_inputs'] = $this->db
            ->from('input')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->where('deleted', 0)
            ->where('supplier_id', $option['supplier_id'])
            ->get()
            ->result_array();

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_inputs'], 'supplier_id');
        $data['supplier_id'] = $option['supplier_id'];
        $config['base_url'] = 'cms_paging_input_by_supplier_id';
        $config['total_rows'] = $total_inputs['quantity'];
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_inputs'] = $total_inputs;
        if ($page > 1 && ($total_inputs['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_inputs', isset($data) ? $data : null);
    }

    public function cms_paging_input_debt_by_supplier_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $config['per_page'] = 100;
        $total_inputs = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('input')
            ->where(['deleted'=> 0,'input_status'=>1])
            ->where(['supplier_id'=> $option['supplier_id'],'lack >'=>0])
            ->get()
            ->row_array();
        $data['_list_inputs'] = $this->db
            ->from('input')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->where(['deleted'=> 0,'input_status'=>1])
            ->where(['supplier_id'=> $option['supplier_id'],'lack >'=>0])
            ->get()
            ->result_array();

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_inputs'], 'supplier_id');
        $data['supplier_id'] = $option['supplier_id'];
        $config['base_url'] = 'cms_paging_input_debt_by_supplier_id';
        $config['total_rows'] = $total_inputs['quantity'];
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_inputs'] = $total_inputs;
        if ($page > 1 && ($total_inputs['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_inputs_debt', isset($data) ? $data : null);
    }

    public function cms_paging_supplier($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $option = $this->input->post('data');

        if ($option['option'] == 0) {
            $total_supplier = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'LEFT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();
            $temp = $this->db
                ->select('suppliers.ID')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'LEFT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
            $total_supplier['quantity'] = count($temp);
            $data['_list_supplier'] = $this->db
                ->select('supplier_code,suppliers.ID,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'LEFT')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
        } else if ($option['option'] == 1) {
            $total_supplier = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();
            $temp = $this->db
                ->select('suppliers.ID')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
            $total_supplier['quantity'] = count($temp);
            $data['_list_supplier'] = $this->db
                ->select('supplier_code,suppliers.ID,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
        } else {
            $total_supplier = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('suppliers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->row_array();
            $temp = $this->db
                ->select('suppliers.ID')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->group_by('suppliers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->result_array();
            $total_supplier['quantity'] = count($temp);
            $data['_list_supplier'] = $this->db
                ->select('supplier_code,suppliers.ID,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->result_array();
        }

        $config['base_url'] = 'cms_paging_supplier';
        $config['per_page'] = 10;
        $config['total_rows'] = $total_supplier['quantity'];
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['_total_supplier'] = $total_supplier;
        $data['_pagination_link'] = $_pagination_link;
        $data['user'] = $this->auth;
        if ($page > 1 && ($total_supplier['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option'];
        $data['page'] = $page;
        $this->load->view('ajax/customer-supplier/list_supplier', isset($data) ? $data : null);
    }

    public function cms_delsup()
    {
        $id = (int)$this->input->post('id');

        $sup = $this->db->from('suppliers')->where('id', $id)->get()->row_array();
        if (!isset($sup) && count($sup) == 0) {
            echo $this->messages;

            return;
        } else {
            $this->db->where('ID', $id)->delete('suppliers');
            echo $this->messages = '1';
        }
    }

    public function cms_detail_supplier($id)
    {
        $id = (int)$id;
        $sup = $this->db->from('suppliers')->where('id', $id)->get()->row_array();
        if (!isset($sup) && count($sup) == 0) {
            echo $this->messages;
            return;
        } else {
            $data['_list_sup'] = $sup;
            $this->load->view('ajax/customer-supplier/detail_sup', isset($data) ? $data : null);
        }
    }

    public function cms_save_edit_sup($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $data = $this->cms_common_string->allow_post($data, ['supplier_name', 'supplier_phone', 'supplier_email', 'supplier_addr', 'notes', 'tax_code']);
        $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('ID', $id)->update('suppliers', $data);
        echo $this->messages = '1';
    }
}