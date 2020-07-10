<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-4 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Phiếu chuyển kho &raquo;<span
                            style="font-style: italic; font-weight: 400; font-size: 16px;"><?php echo $data['_transfer']['transfer_code']; ?></span>
                    </h2>
                </div>
            </div>
            <div class="col-md-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                            <button type="button" class="btn btn-primary"
                                    onclick="cms_print_transfer(4,<?php echo $data['_transfer']['ID']; ?>)"><i
                                    class="fa fa-print"></i> In phiếu chuyển kho
                            </button>
                        <?php if($data['_transfer']['transfer_status']==0){?>
                        <button type="button" class="btn btn-primary"
                                onclick="cms_accept_transfer(<?php echo $data['_transfer']['ID']; ?>)"><i
                                class="fa fa-print"></i> Xác nhận nhập kho
                        </button>
                        <?php }?>
                            <button type="button" class="btn btn-default"
                                    onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Quay lại
                            </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main-space orders-space"></div>
<div class="orders-content">
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered table-striped" style="margin-top: 30px;">
                <thead>
                <tr>
                    <th class="text-center">STT</th>
                    <th>Mã hàng</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-center">Số lượng</th>
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
                                    <?php echo $data['_transfer']['transfer_code']; ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Tình trạng</label>
                                </div>
                                <div class="col-md-7" style="font-style: italic;">
                                    <?php echo $data['_transfer']['transfer_status']==0 ? 'Đang chuyển' : 'Hoàn thành'; ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Kho xuất</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo cms_getNamestockbyID($data['_transfer']['from_store']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Kho nhận</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo cms_getNamestockbyID($data['_transfer']['to_store']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Người chuyển</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo cms_getNameAuthbyID($data['_transfer']['user_init']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Ngày chuyển</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo ($data['_transfer']['created'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $data['_transfer']['created'])) + 7 * 3600) : '-'; ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Người nhận</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo cms_getNameAuthbyID($data['_transfer']['user_upd']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Ngày nhận</label>
                                </div>
                                <div class="col-md-7">
                                    <?php echo ($data['_transfer']['updated'] != '0000-00-00 00:00:00') ? gmdate("H:i d/m/Y", strtotime(str_replace('-', '/', $data['_transfer']['updated'])) + 7 * 3600) : '-'; ?>
                                </div>
                            </div>

                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5">
                                    <label>Ghi chú</label>
                                </div>
                                <div class="col-md-7">
                                    <textarea readonly id="note-order" cols="" class="form-control" rows="3"
                                              style="border-radius: 0;"><?php echo $data['_transfer']['notes']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>