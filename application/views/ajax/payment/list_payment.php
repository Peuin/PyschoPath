<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center">Mã phiếu chi</th>
        <th class="text-center">Kho chi</th>
        <th class="text-center">Ngày chi</th>
        <th class="text-center">Người chi</th>
        <th class="text-center">Ghi chú</th>
        <th class="text-center">Hình thức chi</th>
        <th class="text-center" style="background-color: #fff;">Tổng tiền</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_payment) && count($_list_payment)) :
        foreach ($_list_payment as $key => $item) :
            ?>
            <tr>
                <td class="text-center"><?php echo $item['payment_code']; ?></td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                <td class="text-center"><?php echo ($item['payment_date'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $item['payment_date'])) + 7 * 3600) : '-'; ?></td>
                <td class="text-center"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                <td class="text-center"><?php echo $item['notes']; ?></td>
                <td class="text-center"><?php echo cms_getNamepaymentTypeByID($item['type_id']); ?></td>
                <td class="text-center"
                    style="background-color: #F2F2F2;"><?php echo cms_encode_currency_format($item['total_money']); ?>
                </td>
                <td class="text-center" style="background: #fff;">
                    <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                       onclick="cms_del_temp_payment(<?php echo $item['ID'] . ',' . $page; ?>)"></i>
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
        Tổng phiếu chi: <span><?php echo (isset($total_payment['quantity'])) ? $total_payment['quantity'] : 0; ?></span>
        Tổng tiền: <span><?php echo cms_encode_currency_format((isset($total_payment['total_money']) ? $total_payment['total_money'] : 0)); ?></span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>