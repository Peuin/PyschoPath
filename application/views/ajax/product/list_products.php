<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th class="text-center"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                  class="checkbox chkAll"><span
                    style="width: 15px; height: 15px;"></span></label></th>
        <th class="text-center">Tên sản phẩm</th>
        <th class="text-center">Mã sản phẩm</th>
        <th class="text-center">SL</th>
        <th class="text-center" style="background-color: #fff;">Giá bán</th>
        <th class="text-center">Danh mục</th>
        <th class="text-center">Nhà sản xuất</th>
<!--        <th class="text-center">Hình</th>-->
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($data['_list_product']) && count($data['_list_product'])) :
        foreach ($data['_list_product'] as $key => $item) : ?>
            <tr>
                <td class="text-center"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                          value="<?php echo $item['ID']; ?>"
                                                                                          class="checkbox chk"><span
                            style="width: 15px; height: 15px;"></span></label>
                </td>
                <td class="text-left prd_name"
                    onclick="<?php if (isset($data['option'])) {
                        if ($data['option'] == 1)
                            echo 'cms_detail_product_deactivated';
                        elseif ($data['option'] == 2)
                            echo 'cms_detail_product_deleted';
                        else
                            echo 'cms_detail_product';
                    } else
                        echo 'cms_detail_product'; ?>
                        (<?php echo $item['ID']; ?>)"
                    style="color: #2a6496; cursor: pointer;"><?php echo $item['prd_name']; ?></td>
                <td class="text-center"><?php echo $item['prd_code']; ?></td>
                <td class="text-center"><?php echo $item['prd_sls']; ?></td>
                <td class="text-right"
                    style="font-weight: bold;"><?php echo number_format($item['prd_sell_price']); ?></td>
                <td><?php echo cms_getNamegroupbyID($item['prd_group_id']); ?></td>
                <td><?php echo cms_getNamemanufacturebyID($item['prd_manufacture_id']); ?></td>
<!--                <td class="text-center"-->
<!--                    style="width: 35px;">--><?php //echo (!empty($item['prd_image_url'])) ? "<img src='{$item['prd_image_url']}' alt='{$item['prd_name']}' width='22' height='22' style='border-radius: 15px; padding: 3px; border: 1px solid #ccc;'/>" : '<i class="fa fa-cloud-upload" style="font-size: 18px; color: #337ab7; cursor: pointer; "></i>' ?><!--</td>-->
                <td class="text-center">
                    <i title="Copy" onclick="cms_clone_product(<?php echo $item['ID']; ?>);" class="fa fa-files-o blue"
                       style="margin-right: 5px;"></i>
                    <?php
                    if (isset($data['option'])){
                        if ($data['option'] == 1){
                        ?>
                        <i title="Khôi phục" class="fa fa-repeat"
                           onclick="cms_restore_product_deactivated(<?php echo $item['ID'].','.$data['page']; ?>);"
                           style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                        <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                           onclick="cms_delete_product(<?php echo $item['ID'].','.$data['page']; ?>)"></i>
                        <?php
                        } elseif ($data['option'] == 2) {
                            ?>
                                <i title="Khôi phục" class="fa fa-repeat"
                                   onclick="cms_restore_product_deleted(<?php echo $item['ID'].','.$data['page']; ?>);"
                                   style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                            <?php
                        } else {
                            ?>
                                <i title="Ngừng kinh doanh" class="fa fa-pause"
                                   onclick="cms_deactivate_product(<?php echo $item['ID'].','.$data['page']; ?>);"
                                   style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                                <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                                   onclick="cms_delete_product(<?php echo $item['ID'].','.$data['page']; ?>)"></i>
                                <?php
                        }
                    }else{
                        ?>
                        <i title="Ngừng kinh doanh" class="fa fa-pause"
                           onclick="cms_deactivate_product(<?php echo $item['ID'].','.$data['page']; ?>);"
                           style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                        <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                           onclick="cms_delete_product(<?php echo $item['ID'].','.$data['page']; ?>)"></i>
                        <?php
                    }

                ?>
                </td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="9" class="text-center">Không có dữ liệu</td></tr>';
    endif;

    ?>

    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">SL sản phẩm/SL nhà sản xuất:
        <span><?php echo (isset($data['_sl_product'])) ? $data['_sl_product'] : 0; ?>
            /<?php echo (isset($data['_sl_manufacture'])) ? $data['_sl_manufacture'] : 0; ?></span></div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>
