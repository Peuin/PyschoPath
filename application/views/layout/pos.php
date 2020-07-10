<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo CMS_BASE_URL; ?>"/>
    <link rel="shortcut icon" type="image/png" href="public/templates/images/check.png"/>
    <title><?php echo isset($seo['title']) ? $seo['title'] : 'Phần mềm quản lý bán hàng'; ?></title>
    <link href="public/templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/templates/css/font-awesome.min.css" rel="stylesheet">
    <link href="public/templates/css/style.css" rel="stylesheet">
    <link href="public/templates/css/jquery-ui.min.css" rel="stylesheet">
</head>
<body>
<header>
    <?php $this->load->view('common/headerpos', isset($data) ? $data : NULL); ?>
</header>
<section id="pos" class="main" role="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 padd-left-0">
                <div class="main-content">
                    <?php
                    $this->load->view('common/modal', isset($data) ? $data : NULL);
                    ?>
                    <div>
                        <div class="row">
                            <div class="orders-act">
                                <div class="col-md-8">
                                    <div class="order-search" style="margin: 10px 0px; position: relative;">
                                        <input type="text" class="form-control"
                                               placeholder="Nhập mã sản phẩm hoặc tên sản phẩm (F2)"
                                               id="search-pro-box">
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
                                                <th class="text-center">Giá bán</th>
                                                <th class="text-center">Thành tiền</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="pro_search_append">
                                            </tbody>
                                        </table>
                                        <div class="alert alert-success" style="margin-top: 30px;" role="alert">Gõ mã
                                            hoặc tên sản phẩm vào hộp
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
                                                                <input id="search-box-cys" class="form-control"
                                                                       type="text"
                                                                       placeholder="Tìm khách hàng (F4)"
                                                                       style="border-radius: 3px 0 0 3px !important;"><span
                                                                    style="color: red; position: absolute; right: 5px; top:5px; "
                                                                    class="del-cys"></span>

                                                                <div id="cys-suggestion-box"
                                                                     style="border: 1px solid #444; display: none; overflow-y: auto;background-color: #fff; z-index: 2 !important; position: absolute; left: 0; width: 100%; padding: 5px 0px; max-height: 400px !important;">
                                                                    <div class="search-cys-inner"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-2 padd-0">
                                                                <button type="button" data-toggle="modal"
                                                                        data-target="#create-cust"
                                                                        class="btn btn-primary"
                                                                        style="border-radius: 0 3px 3px 0; box-shadow: none; padding: 7px 11px;">
                                                                    +
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group marg-bot-10 clearfix">
                                                        <div class="col-md-4 padd-0">
                                                            <label>NV bán hàng</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="sale_id">
                                                                <option value="">--Chọn--</option>
                                                                <?php foreach ($data['sale'] as $item) { ?>
                                                                    <option <?php if($data['user']['id']==$item['id']) echo 'selected' ?>
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
                                                                      style="border-radius: 0;">
                                                            </textarea>
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
                                                                <input type="radio" class="payment-method"
                                                                       name="method-pay" value="1" checked>
                                                                Tiền mặt &nbsp;
                                                                <input type="radio" class="payment-method"
                                                                       name="method-pay" value="2"> Thẻ&nbsp;
                                                                <input type="radio" class="payment-method"
                                                                       name="method-pay" value="3"> CK&nbsp;
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
                                                                    <option
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
                                                                0
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group marg-bot-10 clearfix">
                                                        <div class="col-md-4">
                                                            <label>Giảm giá (F7)</label>
                                                        </div>
                                                        <div class="col-md-8" style="display: flex;">
                                                            <button onclick="cms_change_discount_order()" class="toggle-discount-order">vnđ</button>
                                                            <button onclick="cms_change_discount_order()" style="display: none;" class="toggle-discount-order">%</button>
                                                            <input type="text"
                                                                   class="toggle-discount-order form-control text-right discount-percent-order"
                                                                   placeholder="0" style="display:none;border-radius: 0 !important;">
                                                            <input type="text"
                                                                   class="toggle-discount-order form-control text-right txtMoney discount-order"
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
                                                            <label>Khách đưa (F8)</label>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <input type="text"
                                                                   class="form-control text-right txtMoney customer-pay"
                                                                   placeholder="0" style="border-radius: 0 !important;">
                                                        </div>
                                                    </div>
                                                    <div class="form-group marg-bot-10 clearfix">
                                                        <div class="col-md-4">
                                                            <label class="debt">Còn nợ</label>
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
                                                <button type="button" class="btn btn-primary"
                                                        onclick="cms_save_orders(3)"><i
                                                        class="fa fa-check"></i> Lưu (F9)
                                                </button>
                                                <button type="button" class="btn btn-primary"
                                                        onclick="cms_save_orders(4)"><i class="fa fa-print"></i> Lưu và
                                                    in (F10)
                                                </button>
                                                <a href="/orders">
                                                    <button type="button" class="btn-back btn btn-default"><i
                                                            class="fa fa-arrow-left"></i> Hủy
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<script src="public/templates/js/jquery.js"></script>
<script src="public/templates/js/jquery-ui.min.js"></script>
<script src="public/templates/js/html5shiv.min.js"></script>
<script src="public/templates/js/respond.min.js"></script>
<script src="public/templates/js/bootstrap.min.js"></script>
<script src="public/templates/js/ajax.js"></script>
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

    document.addEventListener('keyup', hotkey, false);
</script>
</html>