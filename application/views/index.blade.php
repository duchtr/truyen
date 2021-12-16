<!doctype html>
<html lang="vi">
<head>
    <!-- required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <!-- bootstrap css -->
    {@
        $seg1 = $this->CI->uri->segment(1);
        $seg2 = $this->CI->uri->segment(2);
        if(isset($seg1) && $seg1 != 'tag' && isset($seg2) && $seg2 != ''){
        $seg = $seg2;
    }
    @}
    <?php $idx = ((isset($dataitem) && isset($dataitem["noindex"]) && $dataitem["noindex"]==0) || (!isset($seg) && isset($dataitem) && !isset($dataitem["noindex"])) || !isset($dataitem))?"index":"noindex";
    $follow = ((isset($dataitem) && isset($dataitem["nofollow"]) && $dataitem["nofollow"]==0) || (!isset($seg) && isset($dataitem) && !isset($dataitem["nofollow"])) || !isset($dataitem))?"follow":"nofollow";
    $idx = $idx.",".$follow;
    ?>
    <meta name="robots" content="" />
    <title>Truyện Hay VN</title>
    {!!$_meta_noindex or ''!!}
    <link rel="stylesheet" href="theme/frontend/css/bootstrap.min.css">
    <link rel="stylesheet" href="theme/frontend/css/tiny-slider.css">

    <link rel="stylesheet" href="theme/frontend/css/style.css">
    <link rel="stylesheet" href="theme/frontend/css/loading-bar.min.css">
    <link rel="stylesheet"  href="theme/frontend/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="theme/frontend/scss/style.css">
    <link rel="stylesheet" href="theme/frontend/scss/mobile.css">
    <link rel="stylesheet" href="theme/frontend/css/mobie.css">
    <link rel="stylesheet" href="theme/frontend/css/thuc.css">
    <!-- Font awesome -->

</head>
<body>
    <div class="loading">
        <a href="" class="logo_loading">
            {@ 
                $logo = $this->CI->Dindex->getSettings('logo_loading');
                $file = json_decode($logo,true);

            @}
            <img src="{{$file['path']}}{{$file['name']}}" title="" alt="" class="img-fluid smooth">

            
        </a>
        
        <a href="" title="" class="btn-call">
            <span></span>
        </a>

    </div>
    @include('header')
    <content>
       @yield('content')   
    </content>
    
    @include('footer')
    
    <script src="theme/frontend/js/jquery-2.2.1.min.js"></script>
    <script src="theme/frontend/js/bootstrap.min.js"></script>
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v5.0&appId=2709589652403610&autoLogAppEvents=1"></script>
    <script src="theme/frontend/js/tiny-slider.js"></script>
    <script src="theme/frontend/js/loading-bar.min.js"></script>
    <script src="theme/frontend/js/tiny-slider-support.js"></script>
    
    <script src="theme/frontend/js/script.js"></script>
    <script type="text/javascript" src="theme/frontend/js/offline.js" defer></script>
    @yield('js')
</body>
</html>