<!DOCTYPE html>
<html lang="en">
<head>
    <title>Facebook Gallery</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="PHP Facebook Album Gallery">
    <meta name="author" content="John Veldboom">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/css/prettyPhoto.css"/>
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/bootstrap-social.css">
    <style>
        body { padding-top: 70px; }
        .thumbnail img {
            overflow: hidden;
            height: 100px;
            /*width: 100%;*/
        }
    </style>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<?php  session_start(); ?>
<body style="background-color: #f2f2f2">
<nav class="navbar navbar-inverse navbar-fixed-top" style="background-color: #2d4373; border-color: rgba(0,0,0,0.2);">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php" style="color:#fff;"><span style="font-size: 26px;" class="fa fa-facebook"> </span></a>
        </div>
        <?php if (isset($_SESSION['facebook_access_token'])) { ?>
            <ul class="nav navbar-nav navbar-right">
              <li class="active"><a style="background-color: #2d4373;" href="index.php?logout=1">Logout<span class="sr-only">(current)</span></a></li>
            </ul>
        <?php } ?>
    </div>
</nav>

<div class="container-fluid">

    <?php
    //session_start();
    if(empty($_GET['fid'])){$_GET['fid'] = 'tacobell';} // force if empty for demo

    require('class.facebook-gallery.php');

    $config = array(
        'page_name' => $_GET['fid'],
        'app_id' => '276837436476269',
        'app_secret' => '1b89b235745f25f378c6bb60fcc820ba',
        'breadcrumbs' => true,
        'cache' => array(
            'location' => 'cache', // ensure this directory has permission to read and write
            'time' => 7200
        )
    );

    $gallery = new FBGallery($config);

   if(@$_GET['logout']!=1){
    echo $gallery->display();
	} else {
		 echo $gallery->logout_account();
	}

    ?>
     
</div><!-- /.container -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prettyPhoto/3.1.6/js/jquery.prettyPhoto.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
$(function () {
    $("a[rel^='prettyPhoto']").prettyPhoto({theme: 'dark_rounded',social_tools:'',deeplinking: false});
    $("[rel=tooltip]").tooltip();
});
</script>
</body>
</html>
