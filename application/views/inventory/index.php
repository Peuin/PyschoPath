<div class="inventory">
    <div class="inventory-content">
        <div class="product-sear panel-sear">
            <div>
                <div class="form-group col-md-4 padd-0">
                    <input type="text" class="form-control txt-sinventory"
                           placeholder="Nhập tên hoặc mã sản phẩm để tìm kiếm">
                </div>
                <div class="form-group col-md-8 padd-0" style="padding-left: 5px;">
                    <div class="col-md-12 padd-0">
                        <div class="col-md-9 padd-0">
                            <div class="col-md-4 padd-0">
                                <select class="form-control" id="prd_group_id">
                                    <option value="-1" selected='selected'>-- Danh mục --</option>
                                    <optgroup label="Chọn danh mục">
                                        <?php if (isset($data['_prd_group']) && count($data['_prd_group'])):
                                            foreach ($data['_prd_group'] as $key => $item) :
                                                 ?>
                                                <option
                                                    value="<?php echo $item['id']; ?>"><?php echo $item['prd_group_name']; ?></option>
                                            <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </optgroup>
                                    <optgroup label="------------------------">
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-4 padd-0">
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
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-4 padd-0">
                                <select class="form-control" id="option_inventory">
                                    <option value="0">--Tất cả--</option>
                                    <option value="1" selected="selected">Chỉ lấy hàng tồn</option>
                                    <option value="2">Hết Hàng</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 padd-0" style="padding-left: 5px;">
                            <button style="box-shadow: none;" type="button" class="btn btn-primary btn-large"
                                    onclick="cms_paging_inventory(1)"><i class="fa fa-search"></i> Xem
                            </button>
<!--                            <button type="button" class="btn btn-success"  onclick="cms_export_inventory()"><i-->
<!--                                    class="fa fa-download"></i> Excel-->
<!--                            </button>-->
                        </div>
                    </div>
                    <div class="col-md-1 padd-0" style="padding-left: 1px;">
                    </div>
                </div>
            </div>
        </div>
        <div class="inventory-main-body">
        </div>
    </div>
</div>