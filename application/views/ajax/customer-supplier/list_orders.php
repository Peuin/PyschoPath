<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th></th>
        <th class="text-center">Mã đơn hàng</th>
        <th class="text-center">Kho xuất</th>
        <th class="text-center">Ngày bán</th>
        <th class="text-center">Thu ngân</th>
        <th class="text-center">Trạng thái</th>
        <th class="text-center">Tổng tiền</th>
        <th class="text-center">Khách đưa</th>
        <th class="text-center"><i class="fa fa-clock-o"></i> Nợ</th>
        <th></th>
        <th class="text-center"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                  class="checkbox chkAll"><span
                    style="width: 15px; height: 15px;"></span></label></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_orders) && count($_list_orders)) :
        foreach ($_list_orders as $key => $item) :
            $list_products = json_decode($item['detail_order'], true);
            ?>
            <tr>
                <td style="text-align: center;">
                    <i style="color: #478fca!important;" title="Chi tiết đơn hàng"
                       onclick="cms_show_detail_order(<?php echo $item['ID'];?>)"
                       class="fa fa-plus-circle i-detail-order-<?php echo $item['ID']?>">

                    </i>
                    <i style="color: #478fca!important;" title="Chi tiết đơn hàng"
                       onclick="cms_show_detail_order(<?php echo $item['ID'];?>)"
                       class="fa fa-minus-circle i-hide i-detail-order-<?php echo $item['ID']?>">

                    </i>
                </td>
                <td class="text-center" style="color: #2a6496; cursor: pointer;"
                    onclick="cms_detail_order_in_customer(<?php echo $item['ID']; ?>)"><?php echo $item['output_code']; ?></td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                <td class="text-center"><?php echo ($item['sell_date'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $item['sell_date'])) + 7 * 3600) : '-'; ?></td>
                <td class="text-center"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                <td class="text-center"><?php echo cms_getNamestatusbyID($item['order_status']); ?></td>
                <td class="text-center" style="background-color: #F2F2F2;"><?php echo cms_encode_currency_format($item['total_money']); ?></td>
                <td class="text-center" style="background-color: #F2F2F2;"><?php echo cms_encode_currency_format($item['customer_pay']); ?></td>
                <td class="text-center" style="background: #fff;"><?php echo cms_encode_currency_format($item['lack']); ?></td>
                <td class="text-center" style="background: #fff;"><i title="In" onclick="cms_print_order(1,<?php echo $item['ID']; ?>)"
                                                                     class="fa fa-print blue"
                                                                     style="margin-right: 5px;"></i>
                    <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                       onclick="cms_del_order_in_customer(<?php echo $item['ID'].','.$page; ?>)"></i></td>
                <td class="text-center"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                          value="<?php echo $item['ID']; ?>"
                                                                                          class="checkbox chk"><span
                            style="width: 15px; height: 15px;"></span></label>
            </tr>
            <tr class="tr-hide" id="tr-detail-order-<?php echo $item['ID']?>">
                <td colspan="15">
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab">
                                    <i class="green icon-reorder bigger-110"></i>
                                    Chi tiết đơn hàng
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <div class="alert alert-success clearfix" style="display: flex;">
                                    <div>
                                        <i class="fa fa-cart-arrow-down">
                                        </i>
                                        <span
                                            class="hidden-768">Số lượng SP:
                                        </span>
                                        <label><?php echo $item['total_quantity']; ?>
                                        </label>
                                    </div>
                                    <div class="padding-left-10">
                                        <i class="fa fa-dollar">
                                        </i>
                                        <span
                                            class="hidden-768">Tiền hàng:
                                        </span>
                                        <label><?php echo cms_encode_currency_format($item['total_price']); ?>
                                        </label>
                                    </div>
                                    <div class="padding-left-10">
                                        <i class="fa fa-dollar">
                                        </i>
                                        <span
                                            class="hidden-768">Giảm giá:
                                        </span>
                                        <label><?php echo cms_encode_currency_format($item['coupon']); ?>
                                        </label>
                                    </div>
                                    <div class="padding-left-10">
                                        <i class="fa fa-dollar">
                                        </i>
                                        <span
                                            class="hidden-768">Tổng tiền:
                                        </span>
                                        <label><?php echo cms_encode_currency_format($item['total_money']); ?>
                                        </label>
                                    </div>
                                    <div class="padding-left-10">
                                        <i class="fa fa-clock-o"></i>
                                        <span class="hidden-768">Còn nợ: </span>
                                        <label
                                            ><?php echo cms_encode_currency_format($item['lack']); ?>
                                        </label>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered table-hover dataTable">
                                    <thead>
                                    <tr role="row">
                                        <th class="text-center">STT</th>
                                        <th class="text-left hidden-768">Mã sản phẩm</th>
                                        <th class="text-left">Tên sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-center">Giá bán</th>
                                        <th class="text-center ">Thành tiền</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $queue= 1;
                                    foreach ($list_products as $product) {
                                        $_product = cms_finding_productbyID($product['id']);
                                        $_product['quantity'] = $product['quantity'];
                                        $_product['price'] = $product['price'];
                                        ?>
                                        <tr>
                                            <td class="text-center width-5 hidden-320 ">
                                                <?php echo $queue++; ?>
                                            </td>
                                            <td class="text-left hidden-768">
                                                <?php echo $_product['prd_code']; ?>
                                            </td>
                                            <td class="text-left ">
                                                <?php echo $_product['prd_name']; ?>
                                            </td>
                                            <td class="text-center ">
                                                <?php echo $_product['quantity']; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo cms_encode_currency_format($_product['price']); ?>
                                            </td>
                                            <td class="text-center">
                                                <?php echo cms_encode_currency_format($_product['price']*$_product['quantity']); ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="9" class="text-center">Không có dữ liệu</td></tr>';
    endif;

    ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">
        Tổng số hóa đơn: <span><?php echo (isset($total_orders['quantity'])) ? $total_orders['quantity'] : 0; ?></span>
        Tổng tiền: <span><?php echo cms_encode_currency_format((isset($total_orders['total_money']) ? $total_orders['total_money'] : 0)); ?></span>
        Tổng nợ: <span><?php echo cms_encode_currency_format((isset($total_orders['total_debt']) ? $total_orders['total_debt'] : 0)); ?></span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>