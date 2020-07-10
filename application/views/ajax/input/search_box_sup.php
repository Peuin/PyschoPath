<?php if (isset($data['suppliers']) && count($data['suppliers'])) : ?>
    <ul class="list-unstyled">
        <?php
        foreach ($data['suppliers'] as $key => $val) :
            ?>
            <li style="cursor: pointer;" onclick="cms_selected_mas(<?php echo $val['ID']; ?>)">
                <ul class="list-unstyled">
                    <li style="padding: 3px 10px;" class="data-cys-name-<?php echo $val['ID']; ?>"><i class="fa fa-user"
                                                                                                      style="color: #0B87C9;"
                                                                                                      aria-hidden="true"></i> <?php echo $val['supplier_name']; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-barcode"
                                                      style="color: #0B87C9;"></i> <?php echo $val['supplier_code']; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-phone" style="color: #0B87C9;"
                                                      aria-hidden="true"></i> <?php echo (!empty($val['supplier_phone'])) ? $val['supplier_phone'] : 'Không có'; ?>
                    </li>
                </ul>
            </li>
            <hr style="color: #0B87C9; margin: 10px 0;"/>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
