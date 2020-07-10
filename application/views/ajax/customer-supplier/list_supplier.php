<table class="table table-bordered">
    <thead>
    <tr>
        <th class="text-center">Mã NCC</th>
        <th class="text-center">Tên NCC</th>
        <th class="text-center">Điện thoại</th>
        <th class="text-center">Địa chỉ</th>
        <th class="text-center">Lần cuối nhập hàng</th>
        <th class="text-center" style="background-color: #fff;">Tổng tiền hàng</th>
        <th class="text-center">Nợ</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if (!empty($_list_supplier) && count($_list_supplier)) :
        foreach ($_list_supplier as $key => $item) : ?>
            <tr>
                <td class="text-center" onclick="cms_detail_supplier(<?php echo $item['ID']; ?>)"
                    style="cursor: pointer; color: #1b6aaa;"><?php echo $item['supplier_code']; ?></td>
                <td class="text-center" onclick="cms_detail_supplier(<?php echo $item['ID']; ?>)"
                    style="cursor: pointer; color: #1b6aaa;"><?php echo $item['supplier_name']; ?></td>
                <td class="text-center"><?php echo (!empty($item['supplier_phone'])) ?
                        $item['supplier_phone'] : '-'; ?></td>
                <td class="text-left"><?php echo (!empty($item['supplier_addr'])) ? $item['supplier_addr'] :
                        ''; ?></td>
                <td class="text-center"><?php echo (!empty($item['input_date'])) ? $item['input_date'] :
                        '-'; ?></td>
                <td class="text-right"
                    style="font-weight: bold; background-color: #f9f9f9;"><?php echo (!empty($item['total_money'])) ? cms_encode_currency_format($item['total_money']) :
                        '0'; ?>
                </td>
                <td class="text-right"><?php echo (!empty($item['total_debt'])) ? cms_encode_currency_format($item['total_debt']) :
                        '0'; ?></td>
                <td class="text-center"><i class="fa fa-trash-o" style="cursor:pointer;"
                                           onclick="cms_delsup(<?php echo $item['ID'].','.$page; ?>);"></i></td>
            </tr>
        <?php endforeach;
    else : ?>
        <tr>
            <td colspan="8" class="text-center">Không có dữ liệu</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">
        Số NCC:<span><?php echo (isset($_total_supplier['quantity']) && !empty($_total_supplier['quantity'])) ? $_total_supplier['quantity'] : '0'; ?></span>
        Tổng tiền: <span><?php echo (isset($_total_supplier['total_money']) && !empty($_total_supplier['total_money'])) ? cms_encode_currency_format($_total_supplier['total_money']) : '0'; ?> đ</span>
        Tổng nợ: <span><?php echo (isset($_total_supplier['total_debt']) && !empty($_total_supplier['total_debt'])) ? cms_encode_currency_format($_total_supplier['total_debt']) : '0'; ?> đ</span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>