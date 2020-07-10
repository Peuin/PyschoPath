<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/* Get value after form submited */
if (!function_exists('cms_common_input')) {
    function cms_common_input($obj, $item)
    {
        return (isset($obj[$item]) && !empty($obj[$item])) ? htmlspecialchars($obj[$item]) : '';
    }
}

/*
 * Render status
/*****************************************/
if (!function_exists('cms_render_html')) {
    function cms_render_html($val, $class, $icon = [], $text = [])
    {
        return ($val == 1) ? "<span class='{$class}'><i class='fa {$icon[0]}'></i> " . $text[0] . "</span>" : "<span class='{$class}'><i class='fa {$icon[1]}'></i> " . $text[1] . "</span>";
    }
}
/*
 * số lượng nhân viên theo nhóm
/*****************************************/
if (!function_exists('cms_getEmployee')) {
    function cms_getEmployee($gid)
    {
        $CI =& get_instance();
        $count = $CI->db->where('group_id', $gid)->from('users')->count_all_results();

        return (!isset($count) && !empty($count)) ? '-' : $count;
    }
}

if (!function_exists('cms_fullURL')) {
    function cms_fullURL()
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}
if (!function_exists('cms_getNamegroupbyID')) {
    function cms_getNamegroupbyID($id)
    {
        $name = 'Chưa có';
        if ($id == 1) return $name;
        $CI =& get_instance();
        $group = $CI->db->select('prd_group_name')->from('products_group')->where('ID', $id)->get()->row_array();
        if (isset($group) && count($group)) {
            return $name = $group['prd_group_name'];
        }

        return $name;
    }
}
if (!function_exists('cms_getNamemanufacturebyID')) {
    function cms_getNamemanufacturebyID($id)
    {
        $name = 'Chưa có';
        $CI =& get_instance();
        $manufacture = $CI->db->select('prd_manuf_name')->from('products_manufacture')->where('ID', $id)->get()->row_array();
        if (isset($manufacture) && count($manufacture)) {
            $name = $manufacture['prd_manuf_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNameunitbyID')) {
    function cms_getNameunitbyID($id)
    {
        $name = 'Chưa có';
        $CI =& get_instance();
        $unit = $CI->db->select('prd_unit_name')->from('products_unit')->where('ID', $id)->get()->row_array();
        if (isset($unit) && count($unit)) {
            $name = $unit['prd_unit_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNamecustomerbyID')) {
    function cms_getNamecustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_name')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getAddresscustomerbyID')) {
    function cms_getAddresscustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_addr')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_addr'];
        }

        return $name;
    }
}

if (!function_exists('cms_getPhonecustomerbyID')) {
    function cms_getPhonecustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_phone')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_phone'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNamesupplierbyID')) {
    function cms_getNamesupplierbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('supplier_name')->from('suppliers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['supplier_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNameVATbyID')) {
    function cms_getNameVATbyID($id)
    {
        $list = cms_getListVAT();
        return $list[$id];
    }
}

if (!function_exists('cms_getListVAT')) {
    function cms_getListVAT()
    {
        return array(
            '0' => '0%',
            '5' => '5%',
            '10' => '10%'
        );
    }
}

if (!function_exists('cms_getListReceiptType')) {
    function cms_getListReceiptType()
    {
        return array(
            '3' => 'Thu bán hàng',
			'5' => 'Thu nhân viên',
			'6' => 'Thu sửa chữa',
			'7' => 'Thu dịch vụ',
            '4' => 'Thu khác'
        );
    }
}

if (!function_exists('cms_getListPaymentType')) {
    function cms_getListPaymentType()
    {
        return array(
            '2' => 'Chi mua hàng',
            '4' => 'Chi nhân viên',
			'5' => 'Chi cố định',
			'6' => 'Chi khách hàng',
			'3' => 'Chi khác'
        );
    }
}

if (!function_exists('cms_getNamePaymentTypeByID')) {
    function cms_getNamePaymentTypeByID($id)
    {
        $list = cms_getListPaymentType();
        return $list[$id];
    }
}


if (!function_exists('cms_getNameReceiptTypeByID')) {
    function cms_getNameReceiptTypeByID($id)
    {
        $list = cms_getListReceiptType();
        return $list[$id];
    }
}

if (!function_exists('cms_getNamestatusbyID')) {
    function cms_getNamestatusbyID($id)
    {
        $name = "";
        switch ($id) {
            case '0': {
                $name = 'Khởi tạo';
                break;
            }
            case '1': {
                $name = 'Hoàn thành';
                break;
            }
            case '2': {
                $name = 'Xác nhận';
                break;
            }
            case '3': {
                $name = 'Đang giao';
                break;
            }
            case '4': {
                $name = 'Đã giao';
                break;
            }
            case '5': {
                $name = 'Hủy';
                break;
            }
        }
        return $name;
    }
}

if (!function_exists('cms_finding_productbyID')) {
    function cms_finding_productbyID($id)
    {
        $CI =& get_instance();
        $product = $CI->db
            ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_edit_price')
            ->where('products.ID', $id)
            ->from('products')
            ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
            ->get()
            ->row_array();
        return $product;
    }
}

if (!function_exists('cms_getNameAuthbyID')) {
    function cms_getNameAuthbyID($id)
    {
        $name = "Không nhập";
        $CI =& get_instance();
        $customer = $CI->db->select('display_name')->from('users')->where('id', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['display_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNamestockbyID')) {

    function cms_getNamestockbyID($id)
    {
        $name = "không xác định";
        $CI =& get_instance();
        $customer = $CI->db->select('store_name')->from('stores')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['store_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_encode_currency_format')) {
    function cms_encode_currency_format($priceFloat)
    {
        $symbol_thousand = ',';
        $decimal_place = 0;
        if ($priceFloat == '')
            return $priceFloat;

        if ($priceFloat == 0)
            return 0;

        $price = number_format($priceFloat, $decimal_place, '', $symbol_thousand);
        return $price;
    }
}