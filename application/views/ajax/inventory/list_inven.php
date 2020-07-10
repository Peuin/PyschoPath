<div class="quick-info report row" style="margin-bottom: 15px;">
    <div class="col-md-12 padd-0">
        <div class="col-md-3 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-clock-o cgreen" style="font-size: 45px;" aria-hidden="true"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cgreen"
                        style="font-size: 25px;"><?php echo gmdate("d/m/Y", time() + 7 * 3600); ?></h3>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Ngày lập</span>
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
                        style="font-size: 25px;"><?php echo(isset($total_sls) ? $total_sls : '0'); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">SL tồn kho</span>
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
                        style="font-size: 25px;"><?php echo(isset($totaloinvent) ? cms_encode_currency_format($totaloinvent) : '0'); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Tổng vốn tồn kho</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-shopping-cart cred" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cred"
                        style="font-size: 25px;"><?php echo(isset($totalsinvent) ? cms_encode_currency_format($totalsinvent) : '0'); ?></h3>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Tổng giá trị tồn kho</span>
                </div>
            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center">Mã hàng</th>
        <th class="text-center">Tên sản phẩm</th>
        <th class="text-center">SL</th>
        <th class="text-center">Vốn tồn kho</th>
        <th class="text-center">Giá trị tồn</th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($data['_list_product']) && count($data['_list_product'])) :
        foreach ($data['_list_product'] as $key => $item) : ?>
            <tr>
                <td onclick="cms_view_product(<?php echo $item['ID']; ?>)"
                    style="color: #2a6496; cursor: pointer;"><?php echo $item['prd_code']; ?></td>
                <td class="text-left" onclick="cms_view_product(<?php echo $item['ID'];?>)"><?php echo $item['prd_name']; ?></td>
                <td class="text-center"><?php echo $item['quantity']; ?></td>
                <td class="text-right"><?php echo cms_encode_currency_format($item['prd_origin_price'] * $item['quantity']); ?></td>
                <td class="text-right"><?php echo cms_encode_currency_format($item['prd_sell_price'] * $item['quantity']); ?></td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>';
    endif;
    ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0"></div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>