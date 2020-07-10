<div class="products">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="products-act">
                <div class="col-md-4 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2>Danh sách sản phẩm</h2>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="right-action text-right">
                        <div class="btn-groups">
                            <button type="button" class="btn btn-primary" onclick="cms_vcrproduct('1')"><i
                                    class="fa fa-plus"></i> Tạo sản phẩm
                            </button>
<!--                            <button type="button" class="btn btn-success"><i class="fa fa-download"></i> Xuất Excel-->
<!--                            </button>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-space orders-space"></div>

    <div class="products-content">
        <div class="product-sear panel-sear">
            <div action="" class="">
                <div class="form-group col-md-5 padd-0">
                    <input type="text" class="form-control" placeholder="Nhập mã sản phẩm hoặc tên sản phẩm"
                           id="product-search">
                </div>
                <div class="form-group col-md-7 ">
                    <div class="col-md-3 padd-0" style="margin-right: 10px;">
                        <select class="form-control" id="search-option-1">
                            <option value="0">Đang kinh doanh</option>
                            <option value="1">Đã ngừng kinh doanh</option>
                            <option value="2">Đã xóa</option>
                        </select>
                    </div>
                    <div class="col-md-3 padd-0" style="margin-right: 10px;">
                        <select class="form-control search-option-2" id="prd_group_id">
                            <option value="-1" selected="selected">--Danh mục--</option>
                        </select>
                    </div>
                    <div class="col-md-3 padd-0" style="margin-right: 10px;">
                        <select class="form-control search-option-3" id="prd_manufacture_id">
                            <option value="-1" selected="selected">--Nhà sản xuất--</option>
                            <optgroup label="Chọn nhà sản xuất">
                                <?php if (isset($data['_prd_manufacture']) && count($data['_prd_manufacture'])):
                                    foreach ($data['_prd_manufacture'] as $key => $val) :
                                        ?>
                                        <option
                                            value="<?php echo $val['ID']; ?>"><?php echo $val['prd_manuf_name']; ?></option>
                                    <?php
                                    endforeach;
                                endif;
                                ?>
                            </optgroup>
                            <optgroup label="------------------------">
                                <option value="product_manufacture" data-toggle="modal" data-target="#list-prd-manufacture">Tạo mới
                                    Nhà sản xuất
                                </option>
                            </optgroup>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary btn-large btn-ssup"  onclick="cms_paging_product(1)"><i
                            class="fa fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </div>
        </div>
        <div class="product-main-body">
        </div>
    </div>
</div>