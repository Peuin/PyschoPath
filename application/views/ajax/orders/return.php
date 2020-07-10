<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-4 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Nhập trả hàng từ đơn hàng&raquo;<?php echo $data['_order']['output_code']; ?></h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="btn btn-primary" onclick="cms_save_order_return(1,<?php echo $data['_order']['ID']; ?>)"><i
                                class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_save_order_return(2,<?php echo $data['_order']['ID']; ?>)"><i
                                class="fa fa-print"></i> Lưu và in
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
            <div class="product-results">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center">STT</th>
                        <th>Mã hàng</th>
                        <th>Tên sản phẩm</th>
                        <th class="text-center">SL bán</th>
                        <th class="text-center">SL nhập trả</th>
                        <th class="text-center">Giá nhập</th>
                        <th class="text-center">Thành tiền</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="pro_search_append">
                    <?php $seq=1; foreach($data['_detail_order'] as $product): ?>
                    <tr data-id="<?php echo $product['product_id'] ?>">
                        <td class="text-center seq"><?php echo $seq++; ?></td>
                        <td><?php echo $product['prd_code']; ?></td>
                        <td><?php echo $product['prd_name']; ?></td>
                        <td class="text-center"><?php echo $product['quantity']; ?></td>
                        <td class="text-center" style="max-width: 30px;"><input style="max-height: 22px;" type="text" class="txtNumber form-control quantity_product_import text-center" value="<?php echo $product['quantity']; ?>"></td>
                        <td class="text-center" style="max-width: 120px;"><input style="max-height: 22px;" type="text" class="txtMoney form-control text-center price-input" value="<?php echo cms_encode_currency_format($product['price']); ?>">
                        </td>
                        <td class="text-center total-money"><?php echo cms_encode_currency_format($product['quantity']*$product['price']); ?></td>
                        <td class="text-center"><i class="fa fa-trash-o del-pro-input"></i></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
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
                                <div class="col-md-8" style="font-style: italic;">
                                    <?php echo cms_getNamecustomerbyID($data['_order']['customer_id']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 padd-0">
                                    <label>Ngày nhập</label>
                                </div>
                                <div class="col-md-8">
                                    <input id="date-order" class="form-control datepk" type="text" placeholder="Hôm nay"
                                           style="border-radius: 0 !important;">
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
           style="border-radius: 0;"></textarea>
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
                                        <input type="radio" class="payment-method" name="method-pay" value="1" checked>
                                        Tiền mặt &nbsp;
                                        <input type="radio" class="payment-method" name="method-pay" value="2">
                                        Thẻ&nbsp;
                                        <input type="radio" class="payment-method" name="method-pay" value="3">
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
                                           placeholder="0" style="border-radius: 0 !important;">
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
                                           placeholder="0" style="border-radius: 0 !important;">
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
                        <button type="button" class="btn btn-primary" onclick="cms_save_order_return(1,<?php echo $data['_order']['ID']; ?>)"><i
                                class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-primary" onclick="cms_save_order_return(2,<?php echo $data['_order']['ID']; ?>)"><i
                                class="fa fa-print"></i> Lưu và in
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