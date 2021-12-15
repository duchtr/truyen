<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <?php echo CMS_TITLE(isset($dataitem)?$dataitem:NULL,isset($masteritem)?$masteritem:NULL,isset($datatable)?$datatable:NULL); ?>
    <link href="theme/frontend/css/bootstrap.min.css" type="text/css" rel="stylesheet">

    <link href="theme/frontend/css/style.css" type="text/css" rel="stylesheet">
    <link href="theme/frontend/scss/style.css" type="text/css" rel="stylesheet">
    <link href="theme/frontend/scss/mobile.css" type="text/css" rel="stylesheet">
    <link href="theme/frontend/css/mobie.css" type="text/css" rel="stylesheet">
    <link href="theme/frontend/css/thuc.css" type="text/css" rel="stylesheet">
    <!-- Font awesome -->

</head>
<body>
    <div class="text-center">
        <a class="logo" href="<?php echo e(base_url()); ?>">
            <img style="max-height: 150px;" src="<?php echo $this->CI->Dindex->getSettingImage('logo',1,'-1','0'); ?>" alt="Logo" class="img-fluid">
        </a>
        <p>Bạn có thể xem thêm nhiều truyện khác khi có mạng Internet tại <a href="<?php echo e(base_url()); ?>"><strong>trang chủ</strong></a></p>
    </div>
    <div id="stories_offline"></div>
    
    <script src="theme/frontend/js/jquery-2.2.1.min.js"></script>
    <script type="text/javascript" src="theme/frontend/js/offline.js" defer></script>
    <?php echo $__env->yieldContent('js'); ?>
</body>
</html>