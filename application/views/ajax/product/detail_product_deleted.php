<?php if (isset($_detail_product) && count($_detail_product))  : ?>
<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="products-act">
            <div class="col-md-4 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Chi tiết sản phẩm</h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="btn btn-primary " onclick="cms_edit_product(<?php echo $_detail_product['ID']; ?>)"><i
                                class="fa fa-pencil-square-o"></i> Sửa
                        </button>
                        <button type="button" class="btn btn-danger"
                                onclick="cms_restore_product_deleted_bydetail(<?php echo $_detail_product['ID']; ?>)"><i
                                class="fa fa-trash-o"></i> Khôi phục
                        </button>
                        <button type="button" class="btn btn-default"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                class="fa fa-arrow-left"></i> Trở về
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="products-content" style="margin-bottom: 25px;">
    <div class="basic-info">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-4 padd-0">
                    <h4>Thông tin cơ bản</h4>
                    <small>Nhập tên và các thông tin cơ bản của sản phẩm</small>
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="form-group clearfix">
                            <div class="col-md-6 padd-left-0">
                                <label>Tên sản phẩm</label>

                                <div><?php echo $_detail_product['prd_name']; ?></div>
                            </div>
                            <div class="col-md-6 padd-right-0">
                                <label>Mã sản phẩm</label>

                                <div><?php echo $_detail_product['prd_code']; ?></div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-md-6 padd-left-0">
                                <label>Số lượng</label>
                                <div><?php echo $_detail_product['prd_sls']; ?></div>
                            </div>
                            <div class="col-md-6">
                                <div style="padding-bottom: 5px; font-weight: 700; color: #9d9d9d; "><span>Theo dõi tồn kho :</span> <?php echo (isset($_detail_product['prd_inventory']) && ($_detail_product['prd_inventory'] == '1')) ? '<span class="yes">Có</span>' : '<span class="no">Không</span>'; ?>
                                </div>
                                <div style="padding-bottom: 5px; font-weight: 700; color: #9d9d9d; "><span>Cho phép bán âm :</span> <?php echo (isset($_detail_product['prd_allownegative']) && ($_detail_product['prd_allownegative'] == '1')) ? '<span class="yes">Có</span>' : '<span class="no">Không</span>'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-md-6 padd-left-0">
                                <label>Giá vốn</label>

                                <div><?php echo number_format($_detail_product['prd_origin_price']); ?></div>
                            </div>
                            <div class="col-md-6 padd-right-0">
                                <label>Giá bán</label>

                                <div><?php echo number_format($_detail_product['prd_sell_price']); ?></div>
                            </div>
                        </div>
                        <div class="form-group clearfix">
                            <div class="col-md-6 padd-left-0">
                                <label>Danh mục</label>

                                <div class="col-md-12 padd-0">
                                    <div><?php echo (cms_getNamegroupbyID($_detail_product['prd_group_id']) == 'Chưa có') ? 'Chưa có danh mục' : cms_getNamegroupbyID($_detail_product['prd_group_id']); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 padd-right-0">
                                <label>Nhà sản xuất</label>

                                <div class="col-md-12 padd-0">
                                    <div><?php echo (cms_getNamemanufacturebyID($_detail_product['prd_manufacture_id']) == 'Chưa có') ? 'Chưa có Nhà sản xuất' : cms_getNamemanufacturebyID($_detail_product['prd_manufacture_id']); ?></div>
                                </div>
                            </div>

                        </div>
                        <!--                            <div class="form-group clearfix">-->
                        <!--                                <div class="col-md-6 padd-0">-->
                        <!--                                    <label>Thuế VAT</label>-->
                        <!---->
                        <!--                                    <div>-->
                        <?php //echo $_detail_product['prd_vat'] . '%'; ?><!--</div>-->
                        <!--                                </div>-->
                        <!--                            </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="expand-info">
        <div class="row">
            <div class="col-md-12">
                <h4 style="border-bottom: 1px solid #0B87C9; padding-bottom: 10px;"><i
                        class="fa fa-th-large blue"></i> <a style="color: #0B87C9; text-decoration: none;"
                                                            data-toggle="collapse" href="#collapseproductinfo"
                                                            aria-expanded="false" aria-controls="collapseExample">Thông
                        tin mở rộng(
                        <small> Nhấn để thêm các thông tin cho thuộc tính web</small>
                        )</a></h4>
            </div>
            <div class="col-md-12">
                <div style="margin-top: 5px;"></div>
                <div class="collapse" id="collapseproductinfo">
                    <div class="col-md-3 padd-0">
                        <h4>Mô tả chi tiết</h4>
                        <small
                        ">Hình ảnh và mô tả sản phẩm.</small>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-12 padd-0">
                                <div class="jumbotron text-center" id="img_upload"
                                     style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">
                                    <h3>Upload hình ảnh sản phẩm</h3>
                                    <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Để
                                        tải và hiện thị nhanh, mỗi ảnh lên có dung lượng 500KB. Tải tối đa 10MB.)
                                    </small>
                                    <p>
                                        <button class="btn" style="background-color: #337ab7; "
                                                onclick="browseKCFinder('img_upload','image');return false;"><i
                                                class="fa fa-picture-o" style="font-size: 40px;color: #fff; "></i>
                                        </button>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-12 padd-0">
                                <h4 style="margin-top: 0;">Mô tả
                                    <small style="font-style: italic;">(Nhập thông tin mô tả chi tiết hơn để khách
                                        hàng hiểu hàng hoá của bạn)
                                    </small>
                                </h4>
                                    <textarea readonly class="tiny_editor"
                                              id="prd_description"><?php echo $_detail_product['prd_descriptions']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 padd-0">
                        <h4>Thông tin cho web</h4>
                        <small
                        ">Hiện thị trên trang web, tối ưu SEO.</small>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="checkbox-group" style="margin-top: 20px;">
                                <label class="checkbox"><input type="checkbox"
                                                               disabled <?php echo ($_detail_product['display_website'] == 1) ? 'checked' : ''; ?>
                                                               class="checkbox" id="display_website"><span></span>
                                    Hiện thị ra website</label>
                                <br>
                                <label class="checkbox"><input type="checkbox"
                                                               disabled <?php echo ($_detail_product['prd_highlight'] == 1) ? 'checked' : ''; ?>
                                                               class="checkbox"><span></span> Nổi bật</label>&nbsp;&nbsp;<label
                                    class="checkbox"><input type="checkbox" disabled
                                                            class="checkbox" <?php echo ($_detail_product['prd_new'] == 1) ? 'checked' : ''; ?>><span></span>
                                    Hàng mới</label>&nbsp;&nbsp;&nbsp;<label class="checkbox"><input type="checkbox"
                                                                                                     disabled <?php echo ($_detail_product['prd_hot'] == 1) ? 'checked' : ''; ?>
                                                                                                     class="checkbox"
                                                                                                     id="prd_hot"><span></span>
                                    Đang bán chạy</label>
                            </div>
                        </div>
                        <div class="btn-groups pull-right" style="margin-top: 15px;">
                            <button type="button" class="btn btn-primary " onclick=""><i
                                    class="fa fa-pencil-square-o"></i> Sửa
                            </button>
                            <button type="button" class="btn btn-danger"
                                    onclick="cms_restore_product_deleted_bydetail(<?php echo $_detail_product['ID']; ?>)"><i
                                    class="fa fa-trash-o"></i> Khôi phục
                            </button>
                            <button type="button" class="btn btn-default btn-back"
                                    onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Trở về
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

