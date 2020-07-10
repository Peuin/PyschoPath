<div class="col-md-8 col-md-offset-2">
    <div class="panel panel-succcess">
        <div class="panel-heading">
            <h3 class="panel-title">Thông báo!</h3>
        </div>
        <div class="panel-body">
            <p>Cám ơn bạn đã sử dụng phần mềm quản lý bán hàng</p>

            <p>
                Email sẽ được gởi đến hộp thư <i><?php echo base64_decode($_GET['email']); ?></i> trong vòng 5 phút.
                Bạn hãy kiểm tra mail và nhấp vào nút <strong>"Lấy Lại mật khẩu" </strong>
                để hoàn tất quá trình lấy lại mật khẩu và bắt đầu sử dụng phần mềm bán hàng
            </p>

            <div role="alert" class="alert alert-warning ">
                <strong><span class="glyphicon glyphicon-edit"></span></strong>Nhớ kiểm tra hộp thư
                <strong>Spam</strong> nếu bạn không nhận được mail hoặc nhấn vào nút bên dưới để nhận lại mail kích
                hoạt.
            </div>
            <p class="text-center">
                <a href="authentication/fg_password">
                    <button class="btn btn-primary">Nhập lại email kích hoạt</button>
                </a>
            </p>
            <div class="alert text-center w-hotline" role="alert">
                <h3>Liên hệ với chúng tôi nếu cần hỗ trợ </h3>
                <span class="glyphicon glyphicon-earphone"></span>Tổng đài: <strong class="hotline">0935.559.659</strong>
                <br>
                <span class="glyphicon glyphicon-phone"></span>Hotline: <strong class="hotline">0935.559.659</strong>

            </div>
        </div>
    </div>
</div>