@extends('index')
@section('content')
<div class="banner relative">
    {<bg_truyen.1.-1.1>}
    <div class="text">
        <div class="container">
            <h2 class="title_pro">
                {@ $id = 0; @}
                <!--DBS-loop.pro.1|where:act = 1,id = $dataitem['parent']|order:|limit:-->
                <a href="{(itempro1.slug)}">{(itempro1.name)}</a>
                {@ $id = $itempro1['id']@}
                <!--DBE-loop.pro.1-->
                
            </h2>
            <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="https://tech5s.com.vn/"><span itemprop="name">Trang chủ</span></a>
                    <meta itemprop="position" content="1">
                </li>
                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                    <a itemprop="item"><span itemprop="name">{(name)}</span></a>
                    <meta itemprop="position" content="3">
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="fixed_top_button">
    <div class="container relative">
        <div class="cover_fixed">
            <div class="button_right_function">
                <span class="op_menu toll_tip" data-name="Danh sách chương"><i class="fa fa-bars smooth" aria-hidden="true"></i></span>
                <!--DBS-loop.chapter.2|where:id > $dataitem['id'],parent = $dataitem['parent']|order:|limit:0,1-->
                <span class="next toll_tip" data-name="Chương kế tiếp" >
                    <a href="{(itemchapter2.slug)}"><i class="fa fa-arrow-right smooth" aria-hidden="true"></i></a>
                </span>
                <!--DBE-loop.chapter.2-->
                <!--DBS-loop.chapter.1|where:id < $dataitem['id'],parent = $dataitem['parent']|order:id desc|limit:0,1-->
                <span class="back toll_tip" data-name="Chương trước">
                    <a href="{(itemchapter1.slug)}"><i class="fa fa-arrow-left smooth" aria-hidden="true"></i></a>
                </span>
                <!--DBE-loop.chapter.1-->
                <span class="comment toll_tip" data-name="Bình luận"><i class="fa fa-comments smooth" aria-hidden="true"></i></span>
                <span class="up toll_tip" data-name="Lên đầu"><i class="fa fa-chevron-up smooth" aria-hidden="true"></i></span>
            </div>
            <div class="chapter_pro">
                <div class="container">
                    <div class="row">
                        {@$i=11@}
                        <!--DBS-loop.chapter.3|where:act = 1,parent = $id,id >= $dataitem['id']|order:id asc|limit:0,10-->
                        <div class="col-lg-12 order-{{$i}}">
                            <div class="item_chapter_pro_free d-flex justify-content-between align-items-center">
                                <a href="{(itemchapter3.slug)}" title="{(itemchapter3.name)}">{(itemchapter3.name)}</a>
                                <span class="free">FREE</span>
                            </div>
                        </div>
                        {@$i--@}
                        <!--DBE-loop.chapter.3-->
                        <!--DBS-loop.chapter.10|where:act = 1,parent = $id,id < $dataitem['id']|order:id desc|limit:0,10-->
                        <div class="col-lg-12 order-12">
                            <div class="item_chapter_pro_free d-flex justify-content-between align-items-center ">
                                <a href="{(itemchapter10.slug)}" title="{(itemchapter10.name)}">{(itemchapter10.name)}</a>
                                <span class="free">FREE</span>
                            </div>
                        </div>
                        <!--DBE-loop.chapter.10-->                       
                    </div>
                </div>       
            </div>
        </div>
    </div>
</div>
<div class="story_detail">
    <div class="container relative">
        <div class="title_cover">
            <h1 class="title_story">{(name)}</h1>
            <a class="view_more ml-3 bg-cuz" href="">Lưu Offline</a>
            <div class="function_button">
                <a href="" class="smooth zoom_text" data-zoom="+"><i class="fa fa-plus smooth" aria-hidden="true"></i></a>
                <a href="" class="smooth zoom_text" data-zoom="-"><i class="fa fa-minus smooth" aria-hidden="true"></i></a>
                <!--DBS-loop.chapter.2|where:id > $dataitem['id'],parent = $dataitem['parent']|order:|limit:0,1-->
                <a href="{(itemchapter2.slug)}" class="smooth next_chapter">Tiếp <i class="fa fa-arrow-right smooth" aria-hidden="true"></i></a>
                <!--DBE-loop.chapter.2-->
                
            </div>
        </div>
        
        <div class="content_story s-content" data-font='16' style="font-size:'16px';">
            {(content)}
        </div>
        <div class="author_editor">
            <div class="text">
                <p>{(style)}</p>
                <h3>{(people)}</h3>    
            </div>
        </div>
        <div class="comment_fb">
            <h2>Bình luận Facebook</h2>
            <div class="fb-comments" data-href="{(slug)}" data-width="100%" data-numposts="5"></div>
        </div>
    </div>
</div>
@endsection