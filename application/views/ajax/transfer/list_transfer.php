<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th></th>
        <th class="text-center">Mã phiếu xuất</th>
        <th class="text-center">Kho xuất</th>
        <th class="text-center">Kho nhập</th>
        <th class="text-center">Số lượng</th>
        <th class="text-center">Tình trạng</th>
        <th class="text-center">Ngày chuyển</th>
        <th class="text-center">Người chuyển</th>
        <th class="text-center">Ngày nhận</th>
        <th class="text-center">Người nhận</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php if (isset($_list_transfer) && count($_list_transfer)) :
        foreach ($_list_transfer as $key => $item) :
                $list_products = json_decode($item['detail_transfer'], true);
            ?>
            <tr>
                <td style="text-align: center;">
                    <i style="color: #478fca!important;" title="Chi tiết phiếu chuyển kho"
                                                   onclick="cms_show_detail_transfer(<?php echo $item['ID'];?>)"
                                                   class="fa fa-plus-circle i-detail-transfer-<?php echo $item['ID']?>">

                    </i>
                    <i style="color: #478fca!important;" title="Chi tiết phiếu chuyển kho"
                       onclick="cms_show_detail_transfer(<?php echo $item['ID'];?>)"
                       class="fa fa-minus-circle i-hide i-detail-transfer-<?php echo $item['ID']?>">

                    </i>
                </td>
                <td class="text-center" style="color: #2a6496; cursor: pointer;"
                    onclick="cms_detail_transfer(<?php echo $item['ID']; ?>)"><?php echo $item['transfer_code']; ?></td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['from_store']); ?></td>
                <td class="text-center"><?php echo cms_getNamestockbyID($item['to_store']); ?></td>
                <td class="text-center"><?php echo $item['total_quantity']; ?></td>
                <td class="text-center"><?php echo $item['transfer_status']==0 ? 'Đang chuyển' : 'Hoàn thành'; ?></td>
                <td class="text-center"><?php echo ($item['created'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $item['created'])) + 7 * 3600) : '-'; ?></td>
                <td class="text-center"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                <td class="text-center"><?php echo ($item['updated'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $item['updated'])) + 7 * 3600) : '-'; ?></td>
                <td class="text-center"><?php echo cms_getNameAuthbyID($item['user_upd']); ?></td>
                <td class="text-center" style="background: #fff;">
                    <i title="In" onclick="cms_print_transfer(4,<?php echo $item['ID']; ?>)"
                       class="fa fa-print blue"
                       style="margin-right: 5px;"></i>
                    <i class="fa fa-trash-o" style="color: darkred;" title="<?php if ($option == 1)
                        echo 'Xóa vĩnh viễn';
                    else
                        echo 'Xóa'?>"
                       onclick="<?php if ($option == 1)
                           echo 'cms_del_transfer';
                       else
                           echo 'cms_del_temp_transfer'?>(<?php echo $item['ID'] . ',' . $page; ?>)"></i></td>
            <tr class="tr-hide" id="tr-detail-transfer-<?php echo $item['ID']?>">
                <td colspan="15">
                    <div class="tabbable">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a data-toggle="tab">
                                    <i class="green icon-reorder bigger-110"></i>
                                    Chi tiết phiếu chuyển kho
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active">
                                <table class="table table-striped table-bordered table-hover dataTable">
                                    <thead>
                                    <tr role="row">
                                        <th class="text-center">STT</th>
                                        <th class="text-left hidden-768">Mã sản phẩm</th>
                                        <th class="text-left">Tên sản phẩm</th>
                                        <th class="text-center">Số lượng</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $queue= 1;
                                    foreach ($list_products as $product) {
                                        $_product = cms_finding_productbyID($product['id']);
                                        $_product['quantity'] = $product['quantity'];
                                        ?>
                                        <tr>
                                            <td class="text-center width-5 hidden-320 ">
                                                <?php echo $queue++; ?>
                                            </td>
                                            <td class="text-left hidden-768">
                                                <?php echo $_product['prd_code']; ?>
                                            </td>
                                            <td class="text-left ">
                                                <?php echo $_product['prd_name']; ?>
                                            </td>
                                            <td class="text-center ">
                                                <?php echo $_product['quantity']; ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach;
    else :
        echo '<tr><td colspan="11" class="text-center">Không có dữ liệu</td></tr>';
    endif;
    ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">
        Tổng số phiếu: <span><?php echo (isset($total_rows)) ? $total_rows : 0; ?></span>
        Đang chuyển:
        <span><?php echo cms_encode_currency_format((isset($total_status0) ? $total_status0 : 0)); ?></span>
        Hoàn thành:
        <span><?php echo cms_encode_currency_format((isset($total_status1) ? $total_status1 : 0)); ?></span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>