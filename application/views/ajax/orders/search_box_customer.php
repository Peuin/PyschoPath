<?php if (isset($data['customers']) && count($data['customers'])) : ?>
    <ul class="list-unstyled">
        <?php
        foreach ($data['customers'] as $key => $val) :
            ?>
            <li style="cursor: pointer;" onclick="cms_selected_cys(<?php echo $val['ID']; ?>)">
                <ul class="list-unstyled">
                    <li style="padding: 3px 10px;" class="data-cys-name-<?php echo $val['ID']; ?>"><i class="fa fa-user"
                                                                                                      style="color: #0B87C9;"
                                                                                                      aria-hidden="true"></i> <?php echo $val['customer_name']; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-barcode"
                                                      style="color: #0B87C9;"></i> <?php echo $val['customer_code']; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-phone" style="color: #0B87C9;"
                                                      aria-hidden="true"></i> <?php echo (!empty($val['customer_phone'])) ? $val['customer_phone'] : 'Không có'; ?>
                    </li>
                </ul>
            </li>
            <hr style="color: #0B87C9; margin: 10px 0;"/>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
