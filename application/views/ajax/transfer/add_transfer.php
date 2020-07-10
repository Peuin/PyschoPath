<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-4 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Phiếu chuyển kho &raquo;</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="btn btn-primary"  onclick="cms_save_transfer(1)"><i
                                class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-primary"  onclick="cms_save_transfer(2)"><i class="fa fa-print"></i> Lưu và in
                        </button>
                        <button type="button" onclick="cms_javascript_redirect( cms_javascrip_fullURL() )" class="btn-back btn btn-default"><i class="fa fa-arrow-left"></i> Hủy
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
                cms_select_product_transfer(ui.item.ID);
                $("#search-pro-box").val('');
                return false;
            }
        }).keyup(function (e) {
            if(e.which === 13) {
                cms_select_product_transfer();
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
                        <th class="text-center">Số lượng</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="pro_search_append">
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
                                    <label>Kho nhận</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="to_store">
                                        <option value="">--Chọn--</option>
                                        <?php foreach ($data as $item) { ?>
                                            <option
                                                value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
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
                                              style="border-radius: 0;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="btn-groups pull-right" style="margin-bottom: 50px;">
                        <button type="button" class="btn btn-primary"  onclick="cms_save_transfer(1)"><i
                                class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="btn btn-primary"  onclick="cms_save_transfer(2)"><i class="fa fa-print"></i> Lưu và in
                        </button>
                            <button type="button" onclick="cms_javascript_redirect( cms_javascrip_fullURL() )" class="btn-back btn btn-default"><i class="fa fa-arrow-left"></i> Hủy
                            </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>