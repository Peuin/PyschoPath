<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// controller control user authentication
class Inventory extends CI_Controller
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
        if ($this->auth == null || !in_array(7, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        $sls_manufacture = $this->db->from('products_manufacture')->get()->result_array();
        $data['data']['_prd_group'] = $sls_group;
        $data['data']['_prd_manufacture'] = $sls_manufacture;
        $data['data']['user'] = $this->auth;
        $data['template'] = 'inventory/index';
        $store = $this->db->from('stores')->get()->result_array();
        $data['data']['store'] = $store;
        $store_id = $this->db->select('store_id')->from('users')->where('id',$this->auth['id'])->limit(1)->get()->row_array();
        $data['data']['store_id'] = $store_id['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_inventory($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $total_prd=0;
        $data=null;
        if ($option['option1'] == '-1') {
            if ($option['option2'] == '-1') {
                if ($option['option3'] == '0') {
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where('deleted', 0)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where('deleted', 0)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '1'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '2'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                }
            } else {
                if ($option['option3'] == '0') {
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'prd_manufacture_id'=>$option['option2']])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'prd_manufacture_id'=>$option['option2']])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '1'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '2'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                }
            }
        } else {
            $temp = $this->getCategoriesByParentId($option['option1']);
            $temp[] = $option['option1'];
            if ($option['option2'] == '-1') {
                if ($option['option3'] == '0') {
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where('deleted', 0)
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where('deleted', 0)
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '1'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '2'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                }
            } else {
                if ($option['option3'] == '0') {
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'prd_manufacture_id'=>$option['option2']])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'prd_manufacture_id'=>$option['option2']])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '1'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity >' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                } else if ($option['option3'] == '2'){
                    $total_prd = $this->db
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->count_all_results();
                    $data['data']['_list_product'] = $this->db
                        ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price')
                        ->from('inventory')
                        ->join('products', 'products.ID=inventory.product_id', 'INNER')
                        ->where(['deleted'=> 0,'quantity ' => 0,'prd_manufacture_id'=>$option['option2']])
                        ->where_in('prd_group_id',$temp)
                        ->where('store_id',$option['store_id'])
                        ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                        ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                        ->order_by('inventory.created', 'desc')
                        ->get()->result_array();
                }
            }
        }

        $config['base_url'] = 'cms_paging_inventory';
        $config['total_rows'] = $total_prd;
        $config['per_page'] = 10;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $totaloinvent = $totalsinvent = $sls = 0;
        $tempdata = $data['data']['_list_product'];
        foreach ($tempdata as $item) {
            $sls += $item['quantity'];
            $totaloinvent += ($item['quantity'] * $item['prd_origin_price']);
            $totalsinvent += ($item['quantity'] * $item['prd_sell_price']);
        }
        $data['total_sls'] = $sls;
        $data['totaloinvent'] = $totaloinvent;
        $data['totalsinvent'] = $totalsinvent;
        $data['data']['_sl_product'] = $total_prd;
        $data['data']['_sl_manufacture'] = $this->db->from('products_manufacture')->count_all_results();
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/inventory/list_inven', isset($data) ? $data : null);
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

    public function ExportInventory()
    {
        //Load thư viện PHP Exell

        $date_inventory = gmdate("dmY", time() + 7 * 3600);
        $today = gmdate("d/m/Y H:i", time() + 7 * 3600);
        require_once "public/templates/libs/PHPExcel/PHPExcel.php";
        $obPHPExcel = new PHPExcel();
        $obPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Ngày lập')->setCellValue('A2', 'Kho')->setCellValue('A3', 'Tổng tồn kho')->setCellValue('A4', 'Tổng giá trị tồn kho')->setCellValue('A5', 'Tổng giá trị bán');
        $obPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $obPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
        $obPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(10);
        $obPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(18);
        $obPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(18);
        $stylehead = array(
            'font' => array(
                'color' => array('argb' => PHPExcel_Style_Color::COLOR_BLUE),
                'bold' => true
            )
        );
        $obPHPExcel->setActiveSheetIndex(0)->mergeCells('A6:E6');
        $activeSheet = $obPHPExcel->getActiveSheet();
        $activeSheet->getStyle('A1:A5')->applyFromArray($stylehead);
        $obPHPExcel->getActiveSheet()->setTitle('Báo cáo tồn kho');
        $data['data']['_list_product'] = $this->db->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_origin_price')->where(['prd_status' => 1, 'deleted' => 0, 'prd_sls >' => 0])->from('products')->order_by('created', 'desc')->get()->result_array();

        $totaloinvent = $totalsinvent = $sls = 0;
        $tempdata = $data['data']['_list_product'];
        foreach ($tempdata as $item) {
            $sls += $item['prd_sls'];
            $totaloinvent += ($item['prd_sls'] * $item['prd_origin_price']);
            $totalsinvent += ($item['prd_sls'] * $item['prd_sell_price']);
        }

        $obPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $today)->setCellValue('B2', 'Thái Nguyên')->setCellValue('B3', $this->cms_common->cms_encode_currency_format($sls))->setCellValue('B4', $this->cms_common->cms_encode_currency_format($totaloinvent))->setCellValue('B5', $this->cms_common->cms_encode_currency_format($totalsinvent));
        $obPHPExcel->setActiveSheetIndex(0)->setCellValue('A7', 'Mã hàng')->setCellValue('B7', 'Tên hàng')->setCellValue('C7', 'Tồn kho')->setCellValue('D7', 'Giá trị tồn')->setCellValue('E7', 'Giá trị bán');
        $activeSheet->getStyle('A7:E7')->applyFromArray(
            array(
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => '0B87C9')
                )
            )
        );
        $i = 8;
        $valinven = $valsinven = 0;
        foreach ($tempdata as $key => $item) {
            $valinven = $this->cms_common->cms_encode_currency_format($item['prd_sls'] * $item['prd_origin_price']);
            $valsinven = $this->cms_common->cms_encode_currency_format($item['prd_sls'] * $item['prd_sell_price']);
            $obPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $i, $item['prd_code'])->setCellValue('B' . $i, $item['prd_name'])->setCellValue('C' . $i, $item['prd_sls'])->setCellValue('D' . $i, $valinven)->setCellValue('E' . $i, $valsinven);
            $i++;
        }
        $file_export = 'Baocaotonkho_' . $date_inventory . '.xls';
        $objWriter = PHPExcel_IOFactory::createWriter($obPHPExcel, 'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename='$file_export'");
        header('Cache-Control: max-age=0');
        if (isset($objWriter)) {
            $objWriter->save('php://output');
        }
    }
}

