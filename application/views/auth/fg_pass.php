<div class="login-container col-md-4 col-md-offset-4" id="password-sent">
    <div class="login-frame clearfix">
        <h3 class="heading col-md-10 col-md-offset-1 padd-0"><i class="fa fa-lock"></i>Quên mật khẩu</h3>
        <?php if (!empty(validation_errors())) : ?>
            <div class="alert alert-danger col-md-10 col-md-offset-1">
                <?php echo validation_errors(); ?>
            </div>
        <?php endif; ?>

        <p class="col-md-10 col-md-offset-1 padd-0">Nếu bạn là nhân viên, vui lòng liên hệ chủ cửa hàng để lấy lại mật
            khẩu!</p>

        <div class="col-md-10 col-md-offset-1">
            <form class="form-horizontal login-form" method="post" action="authentication/fg_password">
                <div class="form-group input-icon">
                    <label for="inputEmail3" class="sr-only control-label">Email</label>
                    <input type="text" name="data[email]"
                           value="<?php echo cms_common_input(isset($_post) ? $_post : [], 'email'); ?>"
                           class="form-control" id="inputEmail3" placeholder="Nhập email của bạn">
                    <i class="fa fa-sitemap icon-right"></i>
                </div>
                <div class="form-group">
                    <input type="submit" name="forgot" value="Lấy Lại Mật khẩu" class="btn btn-primary btn-sm"/>
                    <!-- <button type="submit" name="forgot" class="btn btn-primary btn-sm btn-smf"><i class="fa fa-key"></i>Lấy lại mật khẩu</button>
                    <div class="action-none" style="display: none;">
                        <input type="submit" name="forgot" value="Lấy Lại Mật khẩu" class="btn-sm-after"/>
                    </div> -->
                </div>
            </form>
        </div>
    </div>
    <div class="link-action text-center">
        <div class="col-sm-6 col-xs-12">
            <a href="authentication" style="display:inline-block; margin-top: 5px;" class="login">Đăng nhập</a>
        </div>
        <div class="col-sm-6 col-xs-12">
            <a href="authentication/register" style="display:inline-block; margin-top: 5px;" class="register">Đăng
                kí</a>
        </div>

    </div>
</div>