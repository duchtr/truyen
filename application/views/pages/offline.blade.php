<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    {%HEADER%}
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
        <a class="logo" href="{{base_url()}}">
            <img style="max-height: 150px;" src="{<logo.1.-1.0>}" alt="Logo" class="img-fluid">
        </a>
        <p>Bạn có thể xem thêm nhiều truyện khác khi có mạng Internet tại <a href="{{base_url()}}"><strong>trang chủ</strong></a></p>
    </div>
    <div id="stories_offline"></div>
    
    <script src="theme/frontend/js/jquery-2.2.1.min.js"></script>
    <script type="text/javascript" src="theme/frontend/js/offline.js" defer></script>
    @yield('js')
</body>
</html>