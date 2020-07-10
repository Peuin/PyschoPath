<div class="login-container col-md-4 col-md-offset-4" id="password-sent">
    <div class="login-frame clearfix">
        <h3 class="heading col-md-10 col-md-offset-1 padd-0"><i class="fa fa-refresh"></i>Khôi phục mật khẩu!</h3>
        <?php if (!empty(validation_errors())) : ?>
            <div class="alert alert-danger col-md-10 col-md-offset-1">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>

        <div class="col-md-10 col-md-offset-1">
            <form class="form-horizontal login-form" method="post" action="">
                <div class="form-group input-icon">
                    <label for="inputEmail3" class="sr-only control-label">Email</label>
                    <input id="pass1" type="text" name="data[email]"
                           value="<?php echo cms_common_input(isset($_post) ? $_post : [], 'email'); ?>"
                           class="form-control" placeholder="Email đăng nhập">
                    <i class="fa fa-sitemap icon-right"></i>
                </div>
                <div class="form-group input-icon">
                    <label for="inputEmail3" class="sr-only control-label">Mật khẩu</label>
                    <input id="pass1" type="password" name="data[password]"
                           value="<?php echo cms_common_input(isset($_post) ? $_post : [], 'password'); ?>"
                           class="form-control" placeholder="Mật khẩu mới">
                    <i class="fa fa-lock icon-right"></i>
                </div>
                <div class="form-group input-icon">
                    <label for="inputEmail3" class="sr-only control-label">Mật khẩu</label>
                    <input id="pass2" type="password" name="data[password2]"
                           value="<?php echo cms_common_input(isset($_post) ? $_post : [], 'password2'); ?>"
                           class="form-control" placeholder="Xác nhận mật khẩu mới">
                    <i class="fa fa-lock icon-right"></i>
                </div>
                <div class="form-group">
                    <button type="submit" name="reset" class="btn btn-primary btn-sm btn-smf"><i class="fa fa-key"></i>Thực
                        hiện
                    </button>
                    <div class="action-none" style="display: none;">
                        <input type="submit" name="reset" value="Lấy Lại Mật khẩu" class="btn-sm-after"/>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
