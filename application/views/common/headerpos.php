<nav id="navbar-container" class="navbar navbar- navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle menu-toggler pull-left" onclick="$('#sidebar').toggleClass('hidden-xs hidden-sm hidden-md')">
                <span class="sr-only">Toggle sidebar</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-header hidden-768 col-xs-4 col-md-6 text-right" style="line-height:45px;height:45px;vertical-align:middle;">
            <span class="white" style="color: white">PYCHO PATH SHOP Việt Nam</span>            
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <?php if(isset($data['store'])){ ?>
                <li>
                    <label style="margin: 13px 15px; color: white">
                        <a target="_blank" href="/" ><span class="white"><i class="fa fa-tachometer"></i> Tổng quan</span></a>
                    </label>
                </li>
                <li style="border-right: 1px solid #E1E1E1; padding-right: 15px;">
                    <select id="store-id" class="form-control" style="margin: 8px auto">
                        <?php foreach ($data['store'] as $key => $item) :?>
                            <option <?php if($item['ID']==$data['store_id']) echo 'selected '; ?> value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
                        <?php endforeach;?>
                    </select>
                </li>
                <?php } ?>
                <li class="dropdown user-profile">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false"><span class="hello">Xin chào, </span><?php echo (isset($user)) ?
                            $user['display_name'] : $user['username']; ?><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="account"><i class="fa fa-user"></i>Tài khoản</a></li>
                        <li><a href="home"><i class="fa fa-power-off"></i>Thoát</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>