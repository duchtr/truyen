@extends('index')
@section('content')
        <div class="banner relative">
            <img src="theme/frontend/images/bg_ct.jpg" title="" alt="" class="img-fluid smooth">
            <div class="text">
                <div class="container">
                    <h2 class="title_pro">Truyện Edit</h2>
                    <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                        <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                            <a itemprop="item" href="{[HOME]}"><span itemprop="name">Trang chủ</span></a>
                            <meta itemprop="position" content="1">
                        </li>
                        <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                            <a itemprop="item"><span itemprop="name">Chi tiết sách</span></a>
                            <meta itemprop="position" content="3">
                        </li>
                    </ul>
                </div>
            </div>
            
        </div>
        <div class="story_edit_new pro_detail">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="cover relative">
                            <img src="theme/frontend/images/anhbiasach1.jpg" title="" alt="" class="img-fluid smooth">
                            <div class="edit">
                                <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 col-12">
                        <h1 class="title_pro_detail">Sơn dực thiên sứ</h1>
                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                            <div class="star-base ">
                                <div class="star-rate" style="width: 70px;"></div> 
                                <a dt-value="1" href="#1"></a> 
                                <a dt-value="2" href="#2"></a> 
                                <a dt-value="3" href="#3"></a> 
                                <a dt-value="4" href="#4"></a> 
                                <a dt-value="5" href="#5"></a>
                            </div>
                        </div>
                        <div class="info_pro_detail">
                            <span><i class="fa fa-check" aria-hidden="true"></i> Đã hoàn thành</span>
                            <span><i class="fa fa-comments" aria-hidden="true"></i> 0 Bình luận</span>
                            <span><i class="fa fa-eye" aria-hidden="true"></i> 99 Lượt xem</span>
                        </div>
                        <div class="info_pro_detail">
                            <span><i class="fa fa-user" aria-hidden="true"></i> Mị cốt</span>
                            
                        </div>
                        <div class="info_pro_detail">
                            <span><i class="fa fa-bookmark" aria-hidden="true"></i> Hiện đại, Minh tinh, Xuyên không</span>
                        </div>
                        <a class="view_more v2" href="">Đọc truyện</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="content_pro">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Văn án</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Danh sách chương</a>
                </li>
            </ul>
            <div class="tab-content container" id="myTabContent">
                <div class="tab-pane fade  active show" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <div class="s-content">
                        Một con Hỏa hồ vốn là thiên chi kiêu tử, tương lai đứng đầu tộc Hồ lại bị bà ngoại đánh giá thế này:

                        “Ngươi không biết chừng chính là kết quả năm đó hồ lão mụ cùng con Husky dưới núi kết hợp mà thành. Dám tự tiện xuống núi không chừng có người ăn thịt, vào vườn bách thú cũng chẳng có người nhận.”

                        Nó cho rằng đó không phải là sự thật liền trốn xuống núi. Kết quả bị bắt và rao bán tại cửa tiệm chó trưng bày một bảng hiệu lớn: “Bán hạ giá hồ ly lai chó chính tông, giá 100 đồng, không mặc cả.”

                        Về sau, nàng gặp một nữ nhân so với hồ ly tinh còn hồ ly tinh hơn. Thật là bi ai…
                    </div>
                </div>
                <div class="tab-pane fade " id="profile" role="tabpanel" aria-labelledby="profile-tab">
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                            <div class="item_chapter">
                                <h2 class="name_chapter item_chapter_detail"><a href="">Phần 1 - Cứu vớt công tử tiểu bạch hoa</a></h2>
                                <div class="free_chapter item_chapter_detail">
                                    <span>Free</span>
                                </div>
                                <div class="time_chapter item_chapter_detail">
                                    18/10/2019
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="pagination_t">
                        <a href="">1</a>
                        <a href="">2</a>
                        <a href="">3</a>
                        <a href="">4</a>
                        <strong>5</strong>
                    </div>
                </div>
            </div>
            <div class="story_highlight">
                <div class="container">
                    <h2 class="title_border"><a href="">Có thể bạn sẽ thích</a></h2>
                    <div class="row xs_mar_5">

                        <div class="col-lg-4 col-sm-12 col-12 xs_pd_5">
                            <div class="item_story_high row mar_5">
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <a href="" class="c-img">
                                        <img src="theme/frontend/images/anhbiasach1.jpg" title="" alt="" class="img-fluid smooth">
                                    </a>
                                </div>

                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <div class="text">
                                        <h3 class="name"><a href="">Nữ thần thú vị</a></h3>
                                        <span class="type">Hiện đại</span>
                                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                            <div class="star-base ">
                                                <div class="star-rate" style="width: 70px;"></div> 
                                                <a dt-value="1" href="#1"></a> 
                                                <a dt-value="2" href="#2"></a> 
                                                <a dt-value="3" href="#3"></a> 
                                                <a dt-value="4" href="#4"></a> 
                                                <a dt-value="5" href="#5"></a>
                                            </div>
                                        </div>
                                        <a href="" class="view_more v2">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 col-12 xs_pd_5">
                            <div class="item_story_high row mar_5">
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <a href="" class="c-img">
                                        <img src="theme/frontend/images/anhbiasach1.jpg" title="" alt="" class="img-fluid smooth">
                                    </a>
                                </div>

                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <div class="text">
                                        <h3 class="name"><a href="">Nữ thần thú vị</a></h3>
                                        <span class="type">Hiện đại</span>
                                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                            <div class="star-base ">
                                                <div class="star-rate" style="width: 70px;"></div> 
                                                <a dt-value="1" href="#1"></a> 
                                                <a dt-value="2" href="#2"></a> 
                                                <a dt-value="3" href="#3"></a> 
                                                <a dt-value="4" href="#4"></a> 
                                                <a dt-value="5" href="#5"></a>
                                            </div>
                                        </div>
                                        <a href="" class="view_more v2">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12 col-12 xs_pd_5">
                            <div class="item_story_high row mar_5">
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <a href="" class="c-img">
                                        <img src="theme/frontend/images/anhbiasach1.jpg" title="" alt="" class="img-fluid smooth">
                                    </a>
                                </div>

                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <div class="text">
                                        <h3 class="name"><a href="">Nữ thần thú vị</a></h3>
                                        <span class="type">Hiện đại</span>
                                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                            <div class="star-base ">
                                                <div class="star-rate" style="width: 70px;"></div> 
                                                <a dt-value="1" href="#1"></a> 
                                                <a dt-value="2" href="#2"></a> 
                                                <a dt-value="3" href="#3"></a> 
                                                <a dt-value="4" href="#4"></a> 
                                                <a dt-value="5" href="#5"></a>
                                            </div>
                                        </div>
                                        <a href="" class="view_more v2">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection