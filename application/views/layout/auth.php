<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo CMS_BASE_URL; ?>"/>
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo isset($seo['title']) ? $seo['title'] : 'Phần mềm quản lý bán hàng'; ?></title>

    <!-- Bootstrap -->
    <link href="public/templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/templates/css/font-awesome.min.css" rel="stylesheet">
    <link href="public/templates/css/main.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<header id="main-header">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
               <img src="public/templates/images/logoqn.png"
                     class="img-responsive col-sm-4 col-sm-offset-4" style="padding: 15px;"/>	
					 
            </div>
			<div class="col-md-4 col-md-offset-4">
               <!-- <img src="public/templates/images/logoqn.png"
                     class="img-responsive col-sm-4 col-sm-offset-4" style="padding: 10px;"/>	 -->
					 
            </div>
        </div>
    </div>
</header>
<!-- end header -->
<section class="main" role="main">
    <div class="container">
        <div class="row">
            <?php
            // load template
            $this->load->view($template, isset($data) ? $data : NULL);
            ?>
        </div>
    </div>
</section>
<!--end .main-->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="public/templates/js/jquery-1.11.3.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="public/templates/js/bootstrap.min.js"></script>
<script src="public/templates/js/main.js"></script>

</body>
</html>