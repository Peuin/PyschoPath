<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center">Mã phiếu thu</th>
        <th class="text-center">Kho thu</th>
        <th class="text-center">Ngày thu</th>
        <th class="text-center">Người thu</th>
        <th class="text-center">Ghi chú</th>
        <th class="text-center">Hình thức thu</th>
        <th class="text-center" style="background-color: #fff;">Tổng tiền</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_receipt) && count($_list_receipt)) :
        foreach ($_list_receipt as $key => $item) :
            ?>
            <tr>
                <td class="text-center"><?php echo $item['receipt_code']; ?></td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                <td class="text-center"><?php echo ($item['receipt_date'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $item['receipt_date'])) + 7 * 3600) : '-'; ?></td>
                <td class="text-center"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                <td class="text-center"><?php echo $item['notes']; ?></td>
                <td class="text-center"><?php echo cms_getNameReceiptTypeByID($item['type_id']); ?></td>
                <td class="text-center"
                    style="background-color: #F2F2F2;"><?php echo cms_encode_currency_format($item['total_money']); ?>
                </td>
                <td class="text-center" style="background: #fff;">
                    <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                       onclick="cms_del_temp_receipt(<?php echo $item['ID'] . ',' . $page; ?>)"></i>
                </td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="7" class="text-center">Không có dữ liệu</td></tr>';
    endif;
    ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">
        Tổng phiếu thu: <span><?php echo (isset($total_receipt['quantity'])) ? $total_receipt['quantity'] : 0; ?></span>
        Tổng tiền: <span><?php echo cms_encode_currency_format((isset($total_receipt['total_money']) ? $total_receipt['total_money'] : 0)); ?></span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>