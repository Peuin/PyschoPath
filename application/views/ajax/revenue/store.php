<div class="quick-info report row" style="margin-bottom: 15px;">
    <div class="col-md-12 padd-0">
        <div class="col-md-3 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-clock-o cgreen" style="font-size: 45px;" aria-hidden="true"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cgreen"
                        style="font-size: 25px;"><?php echo (isset($total_orders['quantity'])) ? $total_orders['quantity'] : 0; ?>
                        / <?php echo (isset($total_orders['total_quantity'])) ? $total_orders['total_quantity'] : 0; ?></h3>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Số cửa hàng / Số lượng SP</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-tag blue" style="font-size: 45px;" aria-hidden="true"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title blue"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['total_discount']) ? $total_orders['total_discount'] : 0)); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Chiết khấu</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 padd-right-0">
            <div class="report-box " style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-refresh orange" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title orange"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['total_money']) ? $total_orders['total_money'] : 0)); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Doanh số</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-clock-o cred" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cred"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['total_debt']) ? $total_orders['total_debt'] : 0)); ?></h3>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Khách nợ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th></th>
        <th class="text-center">Tên cửa hàng</th>
        <th class="text-center">Tổng số đơn</th>
        <th class="text-center">Tổng chiết khấu</th>
        <th class="text-center">Tổng tiền</th>
        <th class="text-center">Tổng SP</th>
        <th class="text-center">Tổng nợ</th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_stores) && count($_list_stores)) :
        foreach ($_list_stores as $key => $item) :
            ?>
            <tr>
                <td style="text-align: center;">
                    <i style="color: #478fca!important;" title="Chi tiết đơn hàng"
                       onclick="cms_show_list_order(<?php echo $item['store_id'];?>)"
                       class="fa fa-plus-circle i-list-order-<?php echo $item['store_id']?>">
                    </i>
                    <i style="color: #478fca!important;" title="Chi tiết đơn hàng"
                       onclick="cms_show_list_order(<?php echo $item['store_id'];?>)"
                       class="fa fa-minus-circle i-hide i-list-order-<?php echo $item['store_id']?>">
                    </i>
                </td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                <td class="text-center"><?php echo $item['total_order'] ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_discount']); ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_money']); ?></td>
                <td class="text-center"><?php echo $item['total_quantity']; ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_debt']); ?></td>
            </tr>
            <tr class="tr-hide" id="tr-list-order-<?php echo $item['store_id']?>">
                <td colspan="15">
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab">
                                    <i class="green icon-reorder bigger-110"></i>
                                    Danh sách đơn hàng
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <table class="table table-striped table-bordered table-hover dataTable">
                                    <thead>
                                    <tr role="row">
                                        <th class="text-center">STT</th>
                                        <th class="text-left hidden-768">Mã đơn hàng</th>
                                        <th class="text-left">Kho xuất</th>
                                        <th class="text-center">Ngày bán</th>
                                        <th class="text-center">Thu ngân</th>
                                        <th class="text-center ">Số lượng</th>
                                        <th class="text-center ">Chiết khấu</th>
                                        <th class="text-center ">Tổng tiền</th>
                                        <th class="text-center ">Nợ</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $queue = 1;
                                    foreach ($item['_list_orders'] as $order) {
                                        ?>
                                        <tr>
                                            <td class="text-center width-5 hidden-320 "><?php echo $queue++; ?></td>
                                            <td class="text-center" style="color: #2a6496; cursor: pointer;"
                                                onclick="cms_detail_order(<?php echo $order['ID']; ?>)"><?php echo $order['output_code']; ?></td>
                                            <td class="text-center"><?php echo cms_getNamestockbyID($order['store_id']); ?></td>
                                            <td class="text-center"><?php echo ($order['sell_date'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $order['sell_date'])) + 7 * 3600) : '-'; ?></td>
                                            <td class="text-center"><?php echo cms_getNameAuthbyID($order['user_init']); ?></td>
                                            <td class="text-center"><?php echo $order['total_quantity']; ?></td>
                                            <td class="text-center"><?php echo cms_encode_currency_format($order['coupon']); ?></td>
                                            <td class="text-center"
                                                style="background-color: #F2F2F2;"><?php echo cms_encode_currency_format($order['total_money']); ?></td>
                                            <td class="text-center"
                                                style="background: #fff;"><?php echo cms_encode_currency_format($order['lack']); ?></td>
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
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>