<div class="products">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="products-act">
                <div class="col-md-4 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2><i class="fa fa-refresh" style="font-size: 14px; cursor: pointer;"
                               onclick="cms_vcrproduct('1')"></i>Sửa sản phẩm</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="right-action text-right">
                        <div class="btn-groups">
                            <button type="button" class="btn btn-primary"
                                    onclick="cms_update_product(<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['ID'] ?>);">
                                <i
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
                                <div class="col-md-12 padd-left-0">
                                    <label>Tên sản phẩm</label>
                                    <input type="text" id="prd_name"
                                           value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_name'] ?>"
                                           class="form-control"
                                           placeholder="Nhập tên sản phẩm"/>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="col-md-6 padd-left-0">
                                    <label>Số lượng</label>
                                    <input disabled="disabled" type="text" id="prd_sls"
                                           value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_sls'] ?>"
                                           placeholder="0"
                                           class="form-control text-right txtNumber"/>
                                </div>
                                <div class="col-md-6 padd-right-0">
                                    <label>Mã sản phẩm</label>
                                    <input disabled="disabled" type="text" id="prd_code" class="form-control"
                                           value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_code'] ?>"
                                           placeholder="Nếu không nhập, hệ thống sẽ tự sinh."/>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="col-md-6 padd-left-0">
                                    <label>Đơn vị tính</label>
                                    <div class="col-md-11 padd-0">
                                        <select class="form-control" id="prd_unit_id">
                                            <optgroup label="Chọn đơn vị tính">
                                                <?php $unit_id = 0;
                                                if (isset($data['_detail_product']))
                                                    $unit_id = $data['_detail_product']['prd_unit_id'];
                                                echo $unit_id;
                                                ?>
                                                <?php if (isset($data['_prd_unit']) && count($data['_prd_unit'])):
                                                    foreach ($data['_prd_unit'] as $key => $val) :
                                                        ?>
                                                        <option <?php if ($unit_id == $val['ID']) echo 'selected ' ?>
                                                            value="<?php echo $val['ID']; ?>"><?php echo $val['prd_unit_name']; ?></option>
                                                    <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </optgroup>
                                            <optgroup label="------------------------">
                                                <option value="product_unit" data-toggle="modal"
                                                        data-target="#list-prd-unit">Tạo mới
                                                    Đơn vị tính
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-md-1 padd-0">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#list-prd-unit"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none;">...
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 padd-right-0">
                                    <label class="checkbox" style="display: block;"><input type="checkbox"
                                                                                           id="prd_inventory"
                                                                                           class="checkbox"
                                                                                           name="confirm"
                                                                                           value="1"
                                            <?php if (isset($data['_detail_product']) and $data['_detail_product']['prd_inventory'] == 1) echo 'checked="checked"' ?>
                                            ><span></span> Theo dõi tồn kho?</label>
                                    <label class="checkbox"><input type="checkbox"
                                                                   id="prd_allownegative"
                                                                   class="checkbox"
                                                                   name="confirm"
                                                                   value="1"
                                            <?php if (isset($data['_detail_product']) and $data['_detail_product']['prd_allownegative'] == 1) echo 'checked="checked"' ?>>
                                        <span></span> Cho phép bán âm?</label>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="col-md-6 padd-left-0">
                                    <label>Giá vốn</label>
                                    <input type="text" id="prd_origin_price"
                                           value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_origin_price'] ?>"
                                           class="form-control text-right txtMoney" placeholder="Nhập giá vốn"/>
                                </div>
                                <div class="col-md-6 padd-right-0">
                                    <label>Giá bán</label>
                                    <input type="text" id="prd_sell_price"
                                           value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_sell_price'] ?>"
                                           class="form-control txtMoney text-right" placeholder="0"/>
                                </div>
                            </div>
                            <div class="form-group clearfix">
                                <div class="col-md-6 padd-left-0">
                                    <label>Danh mục</label>
                                    <div class="col-md-11 padd-0">
                                        <select class="form-control" id="prd_group_id">
                                            <optgroup label="Chọn danh mục">
                                                <?php $group_id = 0;
                                                if (isset($data['_detail_product']))
                                                    $group_id = $data['_detail_product']['prd_group_id'];
                                                ?>
                                                <?php if (isset($data['_prd_group']) && count($data['_prd_group'])):
                                                    foreach ($data['_prd_group'] as $key => $item) :
                                                        ?>
                                                        <option <?php if ($group_id == $item['id']) echo 'selected ' ?>
                                                            value="<?php echo $item['id']; ?>"><?php echo $item['prd_group_name']; ?></option>
                                                    <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </optgroup>
                                            <optgroup label="------------------------">
                                                <option value="product_group" data-toggle="modal"
                                                        data-target="#list-prd-group">Tạo mới danh
                                                    mục
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-md-1 padd-0">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#list-prd-group"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none;">...
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6 padd-right-0">
                                    <label>Nhà sản xuất</label>

                                    <div class="col-md-11 padd-0">
                                        <select class="form-control" id="prd_manufacture_id">
                                            <optgroup label="Chọn nhà sản xuất">
                                                <?php $manufacture_id = 0;
                                                if (isset($data['_detail_product']))
                                                    $manufacture_id = $data['_detail_product']['prd_manufacture_id'];
                                                echo $manufacture_id;
                                                ?>
                                                <?php if (isset($data['_prd_manufacture']) && count($data['_prd_manufacture'])):
                                                    foreach ($data['_prd_manufacture'] as $key => $val) :
                                                        ?>
                                                        <option <?php if ($manufacture_id == $val['ID']) echo 'selected ' ?>
                                                            value="<?php echo $val['ID']; ?>"><?php echo $val['prd_manuf_name']; ?></option>
                                                    <?php
                                                    endforeach;
                                                endif;
                                                ?>
                                            </optgroup>
                                            <optgroup label="------------------------">
                                                <option value="product_manufacture" data-toggle="modal"
                                                        data-target="#list-prd-manufacture">Tạo mới
                                                    Nhà sản xuất
                                                </option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="col-md-1 padd-0">
                                        <button type="button" class="btn btn-primary" data-toggle="modal"
                                                data-target="#list-prd-manufacture"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none;">...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12 padd-20">
                                <div class="zoomin jumbotron text-center" id="img_upload"
                                     style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">
                                    <img src="public/templates/uploads/<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_image_url'] ?>" height="200">
                                    <h3>Upload hình ảnh sản phẩm</h3>
                                    <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Để
                                        tải và hiện thị nhanh, mỗi ảnh lên có dung lượng 500KB. Tải tối đa 10MB.)
                                    </small>
                                    <p>
                                    <center>
                                        <div id='img_preview' style="display: none;"></div>
                                        <form id="image_upload_form" method="post" enctype="multipart/form-data"
                                              action='product/upload_img' autocomplete="off">
                                            <div class="file_input_container">
                                                <div class="upload_button"><input type="file" name="photo" id="photo"
                                                                                  class="file_input"/></div>
                                            </div>
                                            <br clear="all">
                                        </form>
                                    </center>
                                    </p>
                                </div>
                            </div>
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
<!--                        <div class="col-md-12">-->
<!--                            <div class="row">-->
<!--                                <div class="col-md-12 padd-20">-->
<!--                                    <div class="jumbotron text-center" id="img_upload"-->
<!--                                         style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">-->
<!--                                        <h3>Upload hình ảnh sản phẩm</h3>-->
<!--                                        <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Để-->
<!--                                            tải và hiện thị nhanh, mỗi ảnh lên có dung lượng 500KB. Tải tối đa 10MB.)-->
<!--                                        </small>-->
<!--                                        <p>-->
<!--                                            <button class="btn" style="background-color: #337ab7; "-->
<!--                                                    onclick="browseKCFinder('img_upload','image');return false;"><i-->
<!--                                                    class="fa fa-picture-o" style="font-size: 40px;color: #fff; "></i>-->
<!--                                            </button>-->
<!--                                        </p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!---->
<!--                            </div>-->
<!--                        </div>-->
                        <div class="col-md-12 padd-20">
                            <h4 style="margin-top: 0;">Mô tả
                                <small style="font-style: italic;">(Nhập thông tin mô tả chi tiết hơn để khách
                                    hàng hiểu hàng hoá của bạn)
                                </small>
                            </h4>
                            <!--                                    <textarea id="ck_editor" id="prd_description"></textarea>-->
                            <div id="ckeditor"><?php echo $data['_detail_product']['prd_descriptions']; ?></div>
                        </div>
                        <div class="col-md-3 padd-20">
                            <h4>Thông tin cho web</h4>
                            <small
                            ">Hiện thị trên trang web, tối ưu SEO.</small>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="checkbox-group" style="margin-top: 20px;">
                                    <label class="checkbox"><input type="checkbox" class="checkbox"
                                                                   id="display_website"><span></span> Hiện thị ra
                                        website</label>
                                    <br>
                                    <label class="checkbox"><input type="checkbox" id="prd_highlight"
                                                                   class="checkbox"><span></span> Nổi bật</label>&nbsp;&nbsp;<label
                                        class="checkbox"><input type="checkbox" class="checkbox"
                                                                id="prd_new"><span></span> Hàng mới</label>&nbsp;&nbsp;&nbsp;<label
                                        class="checkbox"><input type="checkbox" class="checkbox"
                                                                id="prd_hot"><span></span> Đang bán chạy</label>
                                </div>
                            </div>
                            <div class="btn-groups pull-right" style="margin-top: 15px;">
                                <button type="button" class="btn btn-primary"
                                        onclick="cms_update_product(<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['ID'] ?>);">
                                    <i
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
    </div>
</div>
<script>
    initSample();
</script>
