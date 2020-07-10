<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="customer-act act">
            <div class="col-md-4 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Thông tin khách hàng</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="btn btn-primary btn-hide-edit"
                                onclick="cms_edit_cusitem('customer')"><i class="fa fa-pencil-square-o"></i> sửa
                        </button>
                        <button type="button" class="btn btn-default btn-hide-edit"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                class="fa fa-arrow-left"></i> Trở về
                        </button>
                        <button type="button" class="btn btn-primary btn-show-edit" style="display:none;"
                                onclick="cms_save_edit_customer()"><i class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-default btn-show-edit" style="display:none;"
                                onclick="cms_undo_cusitem('customer')"><i class="fa fa-undo"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="customer-info col-md-12">
    <?php if (isset($_list_cus) && count($_list_cus)) : ?>
        <div class="customer-inner tr-item-customer" id="item-<?php echo $_list_cus['ID']; ?>">
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Tên khách hàng</label>

                        <div class="col-md-8">
                            <?php echo $_list_cus['customer_name']; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Mã khách hàng</label>

                        <div class="col-md-8">
                            <?php echo $_list_cus['customer_code']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Điện thoại</label>

                        <div class="col-md-8">
                            <?php echo ($_list_cus['customer_phone'] != '') ? $_list_cus['customer_phone'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Email</label>

                        <div class="col-md-8">
                            <?php echo ($_list_cus['customer_email'] != '') ? $_list_cus['customer_email'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Giới tính</label>

                        <div class="col-md-8">
                            <input type="radio" disabled
                                   name="gender" <?php echo ($_list_cus['customer_gender'] == '0') ? 'checked' : ''; ?>>Nam
                            &nbsp;
                            <input type="radio" disabled
                                   name="gender" <?php echo ($_list_cus['customer_gender'] == '1') ? 'checked' : ''; ?>>
                            Nữ
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Ngày sinh</label>

                        <div class="col-md-8">
                            <?php echo ($_list_cus['customer_birthday'] != '1970-01-01 07:00:00') ? $_list_cus['customer_birthday'] : '(chưa có)'; ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Địa chỉ</label>

                        <div class="col-md-8">
                            <?php echo ($_list_cus['customer_addr'] != '') ? $_list_cus['customer_addr'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Ghi chú</label>

                        <div class="col-md-8">
                            <?php echo ($_list_cus['notes'] != '') ? $_list_cus['notes'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="customer-inner tr-edit-item-customer" style="display: none;">
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Tên khách hàng</label>

                        <div class="col-md-8">
                            <input type="text" id="customer_name" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_name'); ?>">
                            <span style="color: red;" class="error error-customer_name"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Mã khách hàng</label>

                        <div class="col-md-8">
                            <?php echo $_list_cus['customer_code']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Điện thoại</label>

                        <div class="col-md-8">
                            <input type="text" id="customer_phone" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_phone'); ?>">
                            <span style="color: red;" class="error error-customer_phone"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Email</label>

                        <div class="col-md-8">
                            <input type="text" id="customer_email" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_email'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Giới tính</label>

                        <div class="col-md-8">
                            <input type="radio" class="customer_gender"
                                   name="gender1" <?php echo ($_list_cus['customer_gender'] == '0') ? 'checked' : ''; ?>
                                   value="0">Nam &nbsp;
                            <input type="radio" class="customer_gender"
                                   name="gender1" <?php echo ($_list_cus['customer_gender'] == '1') ? 'checked' : ''; ?>
                                   value="1"> Nữ
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Ngày sinh</label>

                        <div class="col-md-8">
                            <input type="text" class="customer_birthday" id="customer_birthday"
                                   class="txttimes form-control"
                                   value=" <?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_birthday'); ?>">
                        </div>
                        <script>
                            $('.customer_birthday').datetimepicker({
                                timepicker: false,
                                autoclose: true,
                                format: 'Y/m/d',
                                formatDate: 'Y/m/d'
                            });</script>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin-bottom: 10px;">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Địa chỉ</label>

                        <div class="col-md-8">
                            <textarea id="customer_addr"
                                      class="form-control"><?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_addr'); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-md-4 padd-0">Ghi chú</label>

                        <div class="col-md-8">
                            <textarea id="notes"
                                      class="form-control"><?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'notes'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else:
    endif;
    ?>
</div>
<div class="col-md-12 padd-0">
    <div class="col-md-6 padd-0">
        <div class="left-action text-left clearfix padd-0">
            <h3 id="order_info" class="padd-0 no-margin" style="margin-top: 10px;"></h3>
        </div>
    </div>
    <div class="col-md-6 padd-0">
<!--        <div class="col-sm-12 padd-0 receipt_debt_hide" id="receipt_order">-->
<!--            <div class="col-sm-3 padd-0 line-height34">-->
<!--                    <input type="radio" class="payment-method" name="payment-method" value="1"-->
<!--                           checked="">-->
<!--                    Tiền mặt &nbsp;-->
<!--                    <input type="radio" class="payment-method" name="payment-method" value="2">-->
<!--                    Thẻ&nbsp;-->
<!--                    <input type="radio" class="payment-method" name="payment-method" value="3">-->
<!--                    CK-->
<!--            </div>-->
<!--            <div class="col-md-2 padd-0">-->
<!--                <input id="receipt_note" class="form-control" type="text"-->
<!--                       placeholder="Ghi chú"-->
<!--                       style="border-radius: 0 !important;">-->
<!--            </div>-->
<!--            <div class="col-md-3 padd-0">-->
<!--                <input id="receipt_date" class="form-control datepk" type="text"-->
<!--                       placeholder="Ngày thu"-->
<!--                       style="border-radius: 0 !important;">-->
<!--            </div>-->
<!--            <script>$('#receipt_date').datetimepicker({-->
<!--                    autoclose: true-->
<!--                });-->
<!--            </script>-->
<!--            <div class="col-md-4 padd-0">-->
<!--                <div class="col-md-6" style="padding: 0px;">-->
<!--                    <input id="receipt_money" class="form-control txtMoney" type="text"-->
<!--                           placeholder="Số tiền thu""-->
<!--                           style="border-radius: 0 !important;">-->
<!--                </div>-->
<!--                <div class="col-md-6" style="padding: 0px;display: inline-flex">-->
<!--                    <button type="button" class="btn btn-primary" onclick="save_receipt_order();" title="Đồng ý">-->
<!--                        <i class="fa fa-plus"></i>-->
<!--                        Thu-->
<!--                    </button>-->
<!--                    <button type="button" class="btn" title="Hủy" onclick="cms_paging_order_by_customer_id('customer');">-->
<!--                        <i class="fa fa-times"-->
<!--                           style="color: white !important;"></i>-->
<!--                        Hủy-->
<!--                    </button>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
        <div class="col-sm-12 padd-0 receipt_debt_hide" id="receipt_order">
            <div class="col-sm-4 padd-0 line-height34">
                <input type="radio" class="payment-method" name="payment-method" value="1"
                       checked="">
                Tiền mặt &nbsp;
                <input type="radio" class="payment-method" name="payment-method" value="2">
                Thẻ&nbsp;
                <input type="radio" class="payment-method" name="payment-method" value="3">
                CK
            </div>
            <div class="col-md-3 padd-0">
                <input id="receipt_note" class="form-control" type="text"
                       placeholder="Ghi chú"
                       style="border-radius: 0 !important;">
            </div>
            <div class="col-md-3 padd-0">
                <input id="receipt_date" class="form-control datepk" type="text"
                       placeholder="Ngày thu"
                       style="border-radius: 0 !important;">
            </div>
            <script>$('#receipt_date').datetimepicker({
                    autoclose: true
                });
            </script>
            <div class="col-md-2 padd-0">
                <div class="col-md-12" style="padding: 0px;display: inline-flex">
                    <button type="button" class="btn" title="Hủy" onclick="cms_paging_order_by_customer_id(1);">
                        <i class="fa fa-times"
                           style="color: white !important;"></i>
                        Hủy
                    </button>
                </div>
            </div>
        </div>
        <div class="right-action text-right">
            <div class="btn-groups">
                <button type="button" class="btn btn-primary" id="receipt_debt_show" onclick="cms_paging_order_debt_by_customer_id(1)"><i
                        class="fa fa-pencil-square-o"></i> Thu nợ
                </button>
            </div>
        </div>
    </div>
</div>
<div class="orders-main-body">
</div>
<script>cms_paging_order_by_customer_id(1);</script>