<button type="button" class="btn btn-default btn-hide-edit" onclick="cms_paging_order_by_customer_id(1)"><i class="fa fa-arrow-left"></i> Trở về
</button>
<div class="row">
    <div class="col-md-8">
        <table class="table table-bordered table-striped" style="margin-top: 30px;">
            <thead>
            <tr>
                <th class="text-center">STT</th>
                <th>Mã hàng</th>
                <th>Tên sản phẩm</th>
                <th class="text-center">Số lượng</th>
                <th class="text-center">Giá bán</th>
                <th class="text-center">Thành tiền</th>
            </tr>
            </thead>
            <tbody>
            <?php if (isset($_list_products) && count($_list_products)) :
                $nstt = 1;
                foreach ($_list_products as $product) :
                    ?>
                    <tr data-id="<?php echo $product['ID']; ?>">
                        <td class="text-center"><?php echo $nstt++; ?></td>
                        <td><?php echo $product['prd_code']; ?></td>
                        <td><?php echo $product['prd_name']; ?></td>
                        <td class="text-center" style="max-width: 30px;"><?php echo $product['quantity']; ?> </td>
                        <td class="text-center price-order"><?php echo cms_encode_currency_format($product['price']); ?></td>
                        <td class="text-center total-money"><?php echo cms_encode_currency_format($product['price'] * $product['quantity']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12">
                <div class="morder-info" style="padding: 4px;">
                    <div class="tab-contents" style="padding: 8px 6px;">
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Mã phiếu</label>
                            </div>
                            <div class="col-md-7">
                                <?php echo $data['_order']['output_code']; ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Khách hàng</label>
                            </div>
                            <div class="col-md-7" style="font-style: italic;">
                                <?php echo cms_getNamecustomerbyID($data['_order']['customer_id']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Ngày bán</label>
                            </div>
                            <div class="col-md-7">
                                <?php echo ($data['_order']['sell_date'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $data['_order']['sell_date'])) + 7 * 3600) : '-'; ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>NV bán hàng</label>
                            </div>
                            <div class="col-md-7">
                                <?php echo cms_getNameAuthbyID($data['_order']['sale_id']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Ghi chú</label>
                            </div>
                            <div class="col-md-7">
                                    <textarea readonly id="note-order" cols="" class="form-control" rows="3"
                                              style="border-radius: 0;"><?php echo $data['_order']['notes']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <h4 class="lighter" style="margin-top: 0;">
                    <i class="fa fa-info-circle blue"></i>
                    Thông tin thanh toán | Lịch sử
                </h4>

                <div class="morder-info" style="padding: 4px;">
                    <div class="tab-contents" style="padding: 8px 6px;">
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Hình thức</label>
                            </div>
                            <div class="col-md-7">
                                <div class="input-group">
                                    <input disabled type="radio" class="payment-method" name="method-pay"
                                           value="1" <?php echo ($data['_order']['payment_method'] == 1) ? 'checked' : ''; ?>>
                                    Tiền mặt &nbsp;
                                    <input disabled type="radio" class="payment-method" name="method-pay"
                                           value="2" <?php echo ($data['_order']['payment_method'] == 2) ? 'checked' : ''; ?>>
                                    Thẻ&nbsp;
                                    <input disabled type="radio" class="payment-method" name="method-pay"
                                           value="3" <?php echo ($data['_order']['payment_method'] == 3) ? 'checked' : ''; ?>>
                                    CK&nbsp;
                                </div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Tiền hàng</label>
                            </div>
                            <div class="col-md-7">
                                <div class="">
                                    <?php echo cms_encode_currency_format($data['_order']['total_money']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Chiết khấu</label>
                            </div>
                            <div class="col-md-7">
                                <div><?php echo cms_encode_currency_format($data['_order']['coupon']); ?></div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Tổng cộng</label>
                            </div>
                            <div class="col-md-7">
                                <div class="">
                                    <?php echo cms_encode_currency_format($data['_order']['total_money']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5 padd-right-0">
                                <label>Khách đưa</label>
                            </div>
                            <div class="col-md-7 orange">
                                <?php echo cms_encode_currency_format($data['_order']['customer_pay']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-5">
                                <label>Còn nợ</label>
                            </div>
                            <div class="col-md-7">
                                <div
                                    class=""><?php echo cms_encode_currency_format($data['_order']['lack']); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>