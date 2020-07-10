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
        <th class="text-center">Mã sản phẩm</th>
        <th class="text-center">Tên sản phẩm</th>
        <th class="text-center">SL bán</th>
        <th class="text-center">Chiết khấu</th>
        <th class="text-center">Tổng tiền</th>
        <th class="text-center ">Tiền vốn</th>
        <th class="text-center ">Lợi nhuận</th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_products) && count($_list_products)) :
        foreach ($_list_products as $key => $item) :
            ?>
            <tr>
                <td class="text-center"><?php echo $item['prd_code']; ?></td>
                <td class="text-center"><?php echo $item['prd_name']; ?></td>
                <td class="text-center"><?php echo $item['total_quantity'] ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_discount']); ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_money']); ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['origin_price']); ?></td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_money']-$item['origin_price']); ?></td>
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