@extends('index')
@section('content')
        <div class="banner relative">
            <img src="theme/frontend/images/bg_ct.jpg" title="" alt="" class="img-fluid smooth">
            <div class="text">
                <div class="container">
                    <h1 class="title_pro">{(name)}</h1>
                        <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                            <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                                <a itemprop="item" href="https://tech5s.com.vn/"><span itemprop="name">Trang chủ</span></a>
                                <meta itemprop="position" content="1">
                            </li>
                            <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                                <a itemprop="item"><span itemprop="name">Tìm kiếm</span></a>
                                <meta itemprop="position" content="3">
                            </li>
                        </ul>
                </div>
            </div>
            
        </div>
        <div class="story_edit_new story_categories">
            <div class="container">
               
                <div class="row xs_mar_5">
                    @if(empty($list_data) )
                        <p class="kq_search">Không tìm thấy truyện có tên "{{$keyword}}"</p>
                    @else
                    <div class="col-lg-12">
                        <p class="kq_search">Kết quả tìm kiếm cho "{{$keyword}}"</p>
                    </div>
                    @foreach($list_data as $itempro_home2)
                            <div class="col-lg-3 col-6 item_pro xs_pd_5">
                                <div class="item_cover_pro" data-id="{(itempro_home2.id)}">
                                    <div class="item_pro_small">
                                        <a href="{(itempro_home2.slug)}" class="c-img">
                                            [[itempro_home2.#w#img.procate_300x0]]
                                        </a>
                                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                            <div class="star-base ">
                                                <div class="star-rate" style="width: {@echo $itempro_home2['score']*16 @}px;"></div> 
                                                <a dt-value="1" href="#1"></a> 
                                                <a dt-value="2" href="#2"></a> 
                                                <a dt-value="3" href="#3"></a> 
                                                <a dt-value="4" href="#4"></a> 
                                                <a dt-value="5" href="#5"></a>
                                            </div>
                                        </div>
                                        <h4 class="title_story"><a href="{(itempro_home2.slug)}" title="{(itempro_home2.name)}">{(itempro_home2.name)}</a></h4>
                                        @if($itempro_home2['status']=='edit')
                                        <div class="edit">
                                            <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                                        </div>
                                        @endif
                                    </div>
                                    
                                </div>
                            </div>
                            @endforeach
                    @endif



                </div>
            </div>
        </div>
@endsection

    