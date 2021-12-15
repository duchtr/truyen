<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
   <head >
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
          <base href="<?php echo base_url() ?>"/>
      <title><?php echo lang("ADMIN_TITLE") ?></title>
      <meta name="robots" content="noindex, nofollow">
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/font-awesome.css">
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/fancybox/source/jquery.fancybox.css">
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/combined1.css">
      <link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>theme/admin/static/datetimepicker/jquery.datetimepicker.css">
      <script type="text/javascript" src="<?php echo base_url(); ?>theme/admin/static/jquery.min.js"></script>
      <script type="text/javascript">        var $ = jQuery.noConflict();</script>
      <style type="text/css">
        .alert {
            margin: 0px auto;
            max-width: 478px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }
        .alert-danger {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        .alert-success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
    </style>
   </head>
   <body class="fixed-top">
    <?php if($this->session->flashdata('error')){ ?>
    <div class="alert alert-danger">
        <?php echo $this->session->flashdata('error'); ?>
    </div>
    <?php } ?>
    <form method="post" action="" id="">
    <script type="text/javascript">var $j = jQuery.noConflict();</script>
    <div style="width: 100%; margin: 0 auto;">
        <div class="content">
            <div style="width: 100%; margin: 0 auto;">
                <div id="cphMain_Content">
                    <script type="text/javascript">
                        function sizeBox() {
                            var w = $j(window).width();
                            var h = document.getElementsByTagName('html')[0].clientHeight
                            $j('#box').css('position', 'absolute');
                            $j('#box').css('top', h / 2 - ($j('#box').height() / 2) - 30);
                            $j('#box').css('left', w / 2 - ($j('#box').width() / 2));
                        }
                        $j(document).ready(function () {
                            sizeBox();
                            $j('#txtUserName').focus();
                        });
                        $j(window).resize(function () {
                            sizeBox();
                        });
                    </script>
                    <div id="box" style="position: absolute; top: 56px; left: 385px;">
                        <div id="cphMain_ctl00_Passport_SignIn"  style="width:100%;">
                            <!-- BEGIN LOGIN -->
                            <div id="login">
                                <div id="loginform" class="form-vertical no-padding no-margin">
                                        <?php 
                                        $logoImage = "https://tech5s.com.vn/theme/frontend/images/logo3.png";
                                        $logo = ' <img style="max-height: 80px;" src="'.$logoImage.'" title="Tech5s" alt="Tech5s">';
                                        $resultHook = $this->hooks->call_hook(['tech5s_admin_logo',"logo"=>$logo]);
                                        if(!is_bool($resultHook)){
                                             extract($resultHook);
                                         } 
                                        ?>
                                        <div class="logo">
                                            <?php echo $logo ?>
                                        </div>
                                            <div class="controls">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="icon-user"></i></span>
                                                    <input name="username" type="text" maxlength="50" id="cphMain_ctl00_txtUserName" placeholder="<?php echo lang("ADMIN_USERNAME") ?>">
                                                </div>
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="icon-user"></i></span>
                                                    <input name="email" type="text" maxlength="50" id="cphMain_ctl00_txtUserName" placeholder="<?php echo lang("ADMIN_REMAIL") ?>">
                                                </div>
                                            </div>
                                    <input type="submit" name="submit" value="<?php echo lang("ADMIN_RESEND_PASSWORD") ?>" id="cphMain_ctl00_Btn_Login" class="btn btn-block login-btn">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
   </body>
</html>