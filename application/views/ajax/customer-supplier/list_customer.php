<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center">Mã KH</th>
        <th class="text-center">Tên KH</th>
        <th class="text-center">Điện thoại</th>
        <th class="text-center">Địa chỉ</th>
        <th class="text-center">Lần cuối mua hàng</th>
        <th class="text-center" style="background-color: #fff;">Tổng tiền hàng</th>
        <th class="text-center">Nợ</th>
        <th></th>
    </tr>
    </thead>
    <tbody class="ajax-loadlist-customer">
    <?php if (isset($_list_customer) && count($_list_customer)) :
        foreach ($_list_customer as $key => $item) :
            ?>
            <tr id="tr-item-<?php echo $item['ID']; ?>">
                <td onclick="cms_detail_customer(<?php echo $item['ID']; ?>)" class="text-center tr-detail-item"
                    style="cursor: pointer; color: #1b6aaa;"><?php echo $item['customer_code']; ?></td>
                <td onclick="cms_detail_customer(<?php echo $item['ID']; ?>)" class="text-center tr-detail-item"
                    style="cursor: pointer; color: #1b6aaa;"><?php echo $item['customer_name']; ?></td>
                <td class="text-center"><?php echo (!empty($item['customer_phone'])) ? $item['customer_phone'] :
                        '-'; ?></td>
                <td class="text-center"><?php echo (!empty($item['customer_addr'])) ? $item['customer_addr'] :
                        '-'; ?></td>
                <td class="text-center"><?php echo (!empty($item['sell_date'])) ? $item['sell_date'] :
                        '-'; ?></td>
                <td class="text-right"
                    style="font-weight: bold; background-color: #f9f9f9;"><?php echo (!empty($item['total_money'])) ? number_format($item['total_money']) :
                        '-'; ?></td>
                <td class="text-right"><?php echo (!empty($item['total_debt'])) ? number_format($item['total_debt']) :
                        '-'; ?></td>
                <td class="text-center"><i class="fa fa-trash-o" style="cursor:pointer;"
                                           onclick="cms_delCustomer(<?php echo $item['ID'].','.$page; ?>);"></i>
                </td>
            </tr>
        <?php
        endforeach;
    else: ?>
        <tr>
            <td colspan="8" class="text-center">Không có dữ liệu</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="ajax-loadlist-total sm-info pull-left padd-0">
        Số khách hàng:<span><?php echo (isset($_total_customer['quantity']) && !empty($_total_customer['quantity'])) ? $_total_customer['quantity'] : '0'; ?></span>
        Tổng tiền: <span><?php echo (isset($_total_customer['total_money']) && !empty($_total_customer['total_money'])) ? cms_encode_currency_format($_total_customer['total_money']) : '0'; ?> đ</span>
        Tổng nợ: <span><?php echo (isset($_total_customer['total_debt']) && !empty($_total_customer['total_debt'])) ? cms_encode_currency_format($_total_customer['total_debt']) : '0'; ?> đ</span>
    </div>
    <div class="pull-right">
        <?php echo $_pagination_link; ?>
    </div>
</div>