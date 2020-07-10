<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-4 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Chỉnh sửa phiếu nhập &raquo; <?php echo $data['_input']['input_code']; ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="btn btn-primary" onclick="cms_update_input(<?php echo $data['_input']['ID']; ?>)"><i
                                class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-default"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                class="fa fa-arrow-left"></i> Hủy
                        </button>
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
                <script>
                    $(function () {
                        $("#search-pro-box").autocomplete({
                            minLength: 1,
                            source: 'input/cms_autocomplete_products/',
                            focus: function (event, ui) {
                                $("#search-pro-box").val(ui.item.prd_code);
                                return false;
                            },
                            select: function (event, ui) {
                                cms_select_product_import(ui.item.ID);
                                $("#search-pro-box").val('');
                                return false;
                            }
                        }).keyup(function (e) {
                            if (e.which === 13) {
                                cms_autocomplete_enter_import();
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
            </div>
            <div class="product-results">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Mã hàng</th>
                        <th>Tên sản phẩm</th>
                        <th class="text-center">Hình ảnh</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">ĐVT</th>
                        <th class="text-center">Giá nhập</th>
                        <th class="text-center">Thành tiền</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="pro_search_append">
                    <?php if (isset($_list_products) && count($_list_products)) :
                        $nstt = 1;
                        foreach ($_list_products as $product) :
                            ?>
                            <tr data-id="<?php echo $product['ID']; ?>">
                                <td class="text-center seq"><?php echo $nstt++; ?></td>
                                <td><?php echo $product['prd_code']; ?></td>
                                <td><?php echo $product['prd_name']; ?></td>
                                <td class="text-center zoomin"><img height="30"
                                                                    src="public/templates/uploads/<?php echo $product['prd_image_url']; ?>">
                                </td>
                                <td class="text-center" style="max-width: 30px;"><input style="max-height: 22px;" type="text"
                                                                                        class="txtNumber form-control quantity_product_import text-center"
                                                                                        value="<?php echo $product['quantity']; ?>"></td>
                                <td class="text-center"><?php echo $product['prd_unit_name']; ?> </td>
                                <td class="text-center" style="max-width: 120px;">
                                    <input style="max-height: 22px;" type="text" class="txtMoney form-control text-center price-input"
                                           value="<?php echo number_format($product['price']); ?>">
                                </td>
                                <td class="text-center total-money"><?php echo number_format($product['price']*$product['quantity']); ?></td>
                                <td class="text-center"><i class="fa fa-trash-o del-pro-input"></i></td>
                            </tr>
                        <?php endforeach; endif; ?>
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
                                    <label>Nhà cung cấp</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="col-md-10 padd-0" style="position: relative;">
                                        <input id="search-box-mas" class="form-control" type="text"
                                               placeholder="<?php echo cms_getNamesupplierbyID($data['_input']['supplier_id']) ?>"
                                               style="border-radius: 3px 0 0 3px !important;"><span
                                            style="color: red; position: absolute; right: 5px; top:5px; "
                                            class="del-mas"></span>

                                        <div id="mas-suggestion-box"
                                             style="border: 1px solid #444; display: none; overflow-y: auto;background-color: #fff; z-index: 2 !important; position: absolute; left: 0; width: 100%; padding: 5px 0px; max-height: 400px !important;">
                                            <div class="search-mas-inner"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 padd-0">
                                        <button type="button" data-toggle="modal" data-target="#create-sup"
                                                class="btn btn-primary"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none; padding: 7px 11px;">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>Ngày nhập</label>
                                </div>
                                <div class="col-md-8">
                                    <input id="date-order" class="form-control datepk" type="text" placeholder="Hôm nay"
                                           style="border-radius: 0 !important;" value="<?php echo $data['_input']['input_date']; ?>">
                                </div>
                                <script>$('#date-order').datetimepicker({
                                        autoclose: true
                                    });
                                </script>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>Ghi chú</label>
                                </div>
                                <div class="col-md-8">
 <textarea id="note-order" cols="" class="form-control" rows="3"
           style="border-radius: 0;"><?php echo $data['_input']['notes']; ?></textarea>
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
                                               value="1" <?php echo ($data['_input']['payment_method'] == 1) ? 'checked' : ''; ?>>
                                        Tiền mặt &nbsp;
                                        <input type="radio" class="payment-method" name="method-pay"
                                               value="2" <?php echo ($data['_input']['payment_method'] == 2) ? 'checked' : ''; ?>>
                                        Thẻ&nbsp;
                                        <input type="radio" class="payment-method" name="method-pay"
                                               value="3" <?php echo ($data['_input']['payment_method'] == 3) ? 'checked' : ''; ?>>
                                        CK&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Tiền hàng</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="total-money">
                                        0
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-right-0">
                                    <label>Chiết khấu</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text"
                                           class="form-control text-right txtMoney discount-import"
                                           placeholder="0" style="border-radius: 0 !important;" value="<?php echo cms_encode_currency_format($data['_input']['discount']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Tổng cộng</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="total-after-discount">
                                        0
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-right-0">
                                    <label>Thanh toán</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text"
                                           class="form-control text-right txtMoney customer-pay"
                                           placeholder="0" style="border-radius: 0 !important;" value="<?php echo cms_encode_currency_format($data['_input']['payed']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4">
                                    <label>Còn nợ</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="debt">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn-groups pull-right" style="margin-bottom: 50px;">
                        <button type="button" class="btn btn-primary" onclick="cms_update_input(<?php echo $data['_input']['ID']; ?>)"><i
                                class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-default btn-back"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                class="fa fa-arrow-left"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    cms_load_infor_import();
    cms_selected_mas(<?php echo $data['_input']['supplier_id'] ?>);
</script>