<div class="quick-info report row" style="margin-bottom: 15px;">
    <div class="col-md-12 padd-0">
<!--        <div class="col-md-3 padd-right-0">-->
<!--            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">-->
<!--                <div class="infobox-icon">-->
<!--                    <i class="fa fa-tag blue" style="font-size: 45px;" aria-hidden="true"></i>-->
<!--                </div>-->
<!--                <div class="infobox-data">-->
<!--                    <h3 class="infobox-title blue"-->
<!--                        style="font-size: 25px;">--><?php //echo cms_encode_currency_format((isset($total_orders['total_discount']) ? $total_orders['total_discount'] : 0)); ?><!--</h3>-->
<!--                    <span class="infobox-data-number text-center"-->
<!--                          style="font-size: 14px; color: #555;">Chiết khấu</span>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
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
                            <i class="fa fa-undo blue" style="font-size: 45px;" aria-hidden="true"></i>
                        </div>
                        <div class="infobox-data">
                            <h3 class="infobox-title blue"
                                style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['return_money']) ? $total_orders['return_money'] : 0)); ?></h3>
                            <span class="infobox-data-number text-center"
                                  style="font-size: 14px; color: #555;">Trả hàng</span>
                        </div>
                    </div>
                </div>
        <div class="col-md-3 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-money cred" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cred"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['total_origin_price']) ? $total_orders['total_origin_price'] : 0)); ?></h3>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Tiền vốn</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 padd-right-0">
            <div class="report-box " style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-dollar orange" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title orange"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format($total_orders['total_money']-$total_orders['total_origin_price']-$total_orders['return_money']); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Lợi nhuận</span>
                </div>
            </div>
        </div>
    </div>
</div>
<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th></th>
        <th class="text-center">Mã đơn hàng</th>
        <th class="text-center">Kho xuất</th>
        <th class="text-center">Ngày bán</th>
        <th class="text-center">Thu ngân</th>
        <th class="text-center">Khách hàng</th>
        <th class="text-center">Số lượng</th>
        <th class="text-center">Chiết khấu</th>
        <th class="text-center" style="background-color: #fff;">Doanh số</th>
        <th class="text-center">Tiền vốn</th>
        <th class="text-center">Lợi nhuận</th>
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
                    onclick="cms_detail_order(<?php echo $item['ID']; ?>)"><?php echo $item['output_code']; ?></td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                <td class="text-center"><?php echo ($item['sell_date'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $item['sell_date'])) + 7 * 3600) : '-'; ?></td>
                <td class="text-center"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                <td class="text-center"><?php echo cms_getNamecustomerbyID($item['customer_id']); ?></td>
                <td class="text-center"><?php echo $item['total_quantity']; ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['coupon']); ?></td>
                <td class="text-center"
                    style="background-color: #F2F2F2;"><?php echo cms_encode_currency_format($item['total_money']); ?></td>
                <td class="text-center"
                    style="background: #fff;"><?php echo cms_encode_currency_format($item['total_origin_price']); ?></td>
                <td class="text-center"
                    style="background: #fff;"><?php echo cms_encode_currency_format($item['total_money']-$item['total_origin_price']); ?></td>
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
        echo '<tr><td colspan="10" class="text-center">Không có dữ liệu</td></tr>';
    endif;
    ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>