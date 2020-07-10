<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-3 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Đơn hàng &raquo;<?php echo $data['_order']['output_code']; ?></h2>
                </div>
            </div>
            <div class="col-md-7">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <?php if($data['_order']['order_status'] ==0){ ?>
                        <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,0)">
                            <i class="fa fa-floppy-o"></i> Lưu khởi tạo
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,2)"><i
                                class="fa fa-check"></i> Xác nhận
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,3)"><i
                                class="fa fa-taxi"></i> Đang giao
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,4)"><i
                                class="fa fa-check-square-o"></i> Đã giao
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,1)"><i
                                class="fa fa-check-circle-o"></i> Thành công
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,5)"><i
                                class="fa fa-check-circle-o"></i> Hủy
                        </button>
                        <button type="button" class="btn-back btn btn-default"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                class="fa fa-arrow-left"></i> Thoát
                        </button>
                        <?php }else{?>
                            <button type="button" class="btn btn-primary" onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,6)"><i
                                    class="fa fa-check-circle-o"></i> Lưu
                            </button>
                            <button type="button" class="btn-back btn btn-default"
                                    onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Thoát
                            </button>
                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="orders-content check-order">
    <div class="row">
        <div class="col-md-8">
            <div class="order-search" style="margin: 10px 0px; position: relative;">
                <input type="text" class="form-control" placeholder="Nhập mã sản phẩm hoặc tên sản phẩm"
                       id="search-pro-box">
            </div>
            <script>
                $(function () {
                    $("#search-pro-box").autocomplete({
                        minLength: 1,
                        source: 'orders/cms_autocomplete_products/',
                        focus: function (event, ui) {
                            $("#search-pro-box").val(ui.item.prd_code);
                            return false;
                        },
                        select: function (event, ui) {
                            cms_select_product_sell(ui.item.ID);
                            $("#search-pro-box").val('');
                            return false;
                        }
                    }).keyup(function (e) {
                        if (e.which === 13) {
                            cms_autocomplete_enter_sell();
                            $("#search-pro-box").val('');
                            $(".ui-menu-item").hide();
                        }
                    })
                        .autocomplete("instance")._renderItem = function (ul, item) {
                        return $("<li>")
                            .append("<div>" + item.prd_code + " - " + item.prd_name + "</div>")
                            .appendTo(ul);
                    };
                });
            </script>
            <div class="product-results">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Mã hàng</th>
                        <th>Tên sản phẩm</th>
                        <th>Hình ảnh</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">ĐVT</th>
                        <th class="text-center">Giá bán</th>
                        <th class="text-center">Thành tiền</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="pro_search_append">
                    <?php $seq=1; foreach($_list_products as $product): ?>
                        <tr data-id="<?php echo $product['ID'] ?>">
                            <td class="text-center seq"><?php echo $seq++; ?></td>
                            <td><?php echo $product['prd_code']; ?></td>
                            <td><?php echo $product['prd_name']; ?></td>
                            <td class="text-center zoomin"><img height="30"
                                                         src="public/templates/uploads/<?php echo $product['prd_image_url']; ?>">
                            </td>
                            <td class="text-center" style="max-width: 30px;"><input style="max-height: 22px;" type="text" class="txtNumber form-control quantity_product_order text-center" value="<?php echo $product['quantity']; ?>"></td>
                            <td class="text-center"><?php echo $product['prd_unit_name']; ?> </td>
                            <td style="max-width: 100px;" class="text-center output">
                                <input type="text" <?php if($product['prd_edit_price']==0) echo 'disabled'; ?> style="max-height: 22px;"
                                       class="txtMoney form-control text-center price-order"
                                       value="<?php echo cms_encode_currency_format($product['prd_sell_price']); ?>">
                            </td>
                            <td class="text-center total-money"><?php echo cms_encode_currency_format($product['quantity']*$product['prd_sell_price']); ?></td>
                            <td class="text-center"><i class="fa fa-trash-o del-pro-order"></i></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="alert alert-success" style="margin-top: 30px;" role="alert">Gõ mã hoặc tên sản phẩm vào hộp
                    tìm kiếm để thêm hàng vào đơn hàng
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="morder-info" style="padding: 4px;">
                        <div class="tab-contents" style="padding: 8px 6px;">
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>Khách hàng</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="col-md-10 padd-0" style="position: relative;">
                                        <input id="search-box-cys" class="form-control" type="text"
                                               placeholder="<?php echo cms_getNamecustomerbyID($data['_order']['customer_id']); ?>"
                                               style="border-radius: 3px 0 0 3px !important;"><span
                                            style="color: red; position: absolute; right: 5px; top:5px; "
                                            class="del-cys"></span>
                                        <div id="cys-suggestion-box"
                                             style="border: 1px solid #444; display: none; overflow-y: auto;background-color: #fff; z-index: 2 !important; position: absolute; left: 0; width: 100%; padding: 5px 0px; max-height: 400px !important;">
                                            <div class="search-cys-inner"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 padd-0">
                                        <button type="button" data-toggle="modal" data-target="#create-cust"
                                                class="btn btn-primary"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none; padding: 7px 11px;">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>Ngày bán</label>
                                </div>
                                <div class="col-md-8">
                                    <input id="date-order" class="form-control datepk" type="text" placeholder="Hôm nay"
                                           style="border-radius: 0 !important;" value="<?php echo $data['_order']['sell_date']; ?>">
                                </div>
                                <script>$('#date-order').datetimepicker({
                                        autoclose: true
                                    });
                                </script>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>NV bán hàng</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="sale_id">
                                        <option value="">--Chọn--</option>
                                        <?php foreach ($data['user'] as $item) { ?>
                                            <option <?php if($item['id']==$data['_order']['sale_id']) echo ' selected ' ?>
                                                value="<?php echo $item['id']; ?>"><?php echo $item['display_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>Ghi chú</label>
                                </div>
                                <div class="col-md-8">
                                    <textarea id="note-order" cols="" class="form-control" rows="3"
                                              style="border-radius: 0;""><?php echo $data['_order']['notes']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <h4 class="lighter" style="margin-top: 0;">
                        <i class="fa fa-info-circle blue"></i>
                        Thông tin thanh toán
                    </h4>
                    <div class="morder-info" style="padding: 4px;">
                        <div class="tab-contents" style="padding: 8px 6px;">
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Hình thức</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="radio" class="payment-method" name="method-pay"
                                               value="1" <?php echo ($data['_order']['payment_method'] == 1) ? 'checked' : ''; ?>>
                                        Tiền mặt &nbsp;
                                        <input type="radio" class="payment-method" name="method-pay"
                                               value="2" <?php echo ($data['_order']['payment_method'] == 2) ? 'checked' : ''; ?>>
                                        Thẻ&nbsp;
                                        <input type="radio" class="payment-method" name="method-pay"
                                               value="3" <?php echo ($data['_order']['payment_method'] == 3) ? 'checked' : ''; ?>>
                                        CK&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>VAT</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="vat">
                                        <?php $list = cms_getListVAT();
                                        foreach ($list as $key=>$val) { ?>
                                            <option <?php if($data['_order']['vat']==$key) echo 'selected' ?>
                                                value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Tiền hàng</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="total-money">
                                        <?php echo cms_encode_currency_format($data['_order']['total_price']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Giảm giá</label>
                                </div>
                                <div class="col-md-8" style="display: flex;">
                                    <button onclick="cms_change_discount_order()" class="toggle-discount-order">vnđ</button>
                                    <button onclick="cms_change_discount_order()" style="display: none;" class="toggle-discount-order">%</button>
                                    <input type="text"
                                           class="toggle-discount-order form-control text-right discount-percent-order"
                                           placeholder="0" style="display:none;border-radius: 0 !important;">
                                    <input type="text"
                                           class="toggle-discount-order form-control text-right txtMoney discount-order"
                                           placeholder="0" style="border-radius: 0 !important;" value="<?php echo cms_encode_currency_format($data['_order']['coupon']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Tổng cộng</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="total-after-discount">
                                        <?php echo cms_encode_currency_format($data['_order']['total_price']-$data['_order']['coupon']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-right-0">
                                    <label>Khách đưa</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text"
                                           class="form-control text-right txtMoney customer-pay"
                                           placeholder="0" style="border-radius: 0 !important;" value="<?php echo cms_encode_currency_format($data['_order']['customer_pay']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label class="debt">Còn nợ</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="debt"><?php echo cms_encode_currency_format($data['_order']['lack']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    cms_selected_cys(<?php echo $data['_order']['customer_id']; ?>);
</script>