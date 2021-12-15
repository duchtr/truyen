@extends('index')
@section('content')
<div class="banner page bg relative" data-background="{<BG_TRUYEN.1.-1>}" data-background-webp="{<BG_TRUYEN.1.-1>}">
    <div class="text">
        <div class="container">
            <h1 class="title_pro">{(name)}</h1>
            {%BREADCRUMB%}
        </div>
    </div>
</div>
<div class="story_edit_new pro_detail">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 col-12 mb20qa">
                <div class="cover banner_qaaa relative">
                    <a class="c-img">
                        <img src="[[dataitem.img.350x0]]" alt="{(dataitem.#i#img#alt)}" title="{(dataitem.#i#img#title)}" class="img-fluid" />
                    </a>
                    <div class="edit">
                        <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                        <span>
                            <!--DBS-loop.pro_categories.4|where:act = 1,id = $dataitem['parent']|order:|limit:0,1-->
                            {{$itempro_categories4['short_name']}}
                            <!--DBE-loop.pro_categories.4-->
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-sm-12 col-12">
                <h1 class="title_pro_detail" data-id="{(id)}">{(name)}</h1>
                <div class="star-rating on line wow fadeInUp margin15 relative"> 
                    <input type="hidden" name="pid" value="{(id)}">
                    <input type="hidden" name="table" value="{@ echo $masteritem['table']; @}">
                    {@
                        $arr=$dataitem['score'];
                        $r = json_decode($arr,true);
                        $r = (array)$r;
                        $score = 0;
                        $total= 0;
                        foreach($r as $k => $v)
                        {
                            $score+=$v* str_replace("s-", "", $k);
                            $total += $v;
                        }
                        $lastScore = $score/($total>0?$total:1);
                        $w=$lastScore*100/5;
                        @}
                        <div class="star-base">
                            <div class="star-rate" style="width:{@ echo $w @}%"></div>
                            <a dt-value="1" href="#1"></a>
                            <a dt-value="2" href="#2"></a>
                            <a dt-value="3" href="#3"></a>
                            <a dt-value="4" href="#4"></a>
                            <a dt-value="5" href="#5"></a>
                        </div>
                    </div>
                    <div class="info_pro_detail">
                        <span><i class="fa fa-upload" aria-hidden="true"></i>{(uploader)}</span>
                        <span><i class="fa fa-check" aria-hidden="true"></i>@if($dataitem['status']=='full') Đã hoàn thành @else Đang phát hành @endif</span>
                        <!--DBS-loop.chapter.3|where:act = 1,parent = $dataitem['id']|order:|limit:0,1-->
                        <span class="comment"><i class="fa fa-comments" aria-hidden="true"></i> <a class="fb-comments-count" data-href="{{base_url().$itemchapter3['slug']}}"></a> Bình luận</span>
                        <!--DBE-loop.chapter.3-->
                        <span><i class="fa fa-eye" aria-hidden="true"></i> {(count)} Lượt xem</span>
                    </div>
                    <div class="info_pro_detail">
                        <span><i class="fa fa-pencil" aria-hidden="true"></i>{(editor)}</span>
                    </div>
                    <div class="info_pro_detail d-flex align-items-center justify-content-start">
                        <span class="icon_tag"><i class="fa fa-user" aria-hidden="true"></i></span>
                        <span class="tag">
                            {(author)}
                        </span>
                    </div>
                    <div class="info_pro_detail ">
                        <div class="cover_item_tag d-flex align-items-center justify-content-start">
                            <span class="icon_tag"><i class="fa fa-bookmark" aria-hidden="true"></i>
                            </span>
                            <span class="tag">
                                <!--DBS-loop.tag_pro.1|where:FIND_IN_SET(id;\''.$dataitem['tag_pro'].'\') > 0|order:|limit:-->
                                <a class="tag_pro" href="tags/{(itemtag_pro1.link)}" title="{(itemtag_pro1.name)}">{(itemtag_pro1.name)}</a>
                                <!--DBE-loop.tag_pro.1-->
                            </span>
                        </div>
                    </div>
                    <!--DBS-loop.chapter.2|where:act = 1,parent = $dataitem['id']|order:|limit:0,1-->
                    <a class="view_more_pro v2" href="{(itemchapter2.slug)}">Đọc truyện</a>
                    <!--DBE-loop.chapter.2-->
                    <p class="notify_offline">
                        <small>Bạn có thể ấn biểu tượng Download <i class='fa fa-download'></i> bên dưới để lưu truyện về đọc Offline khi không có Internet!</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="content_pro">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Văn án</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Danh sách chương</a>
            </li>
        </ul>
        <div class="tab-content container" id="myTabContent">
            <div class="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="s-content">
                    {(content)}
                </div>
            </div>
            <div class="tab-pane fade active show chapter_ajax" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            </div>
        </div>
        <div class="story_highlight">
            <div class="container">
                <h2 class="title_border"><a href="">Có thể bạn sẽ thích</a></h2>
                <div class="row xs_mar_5 row10">
                    {%RELATED.20.[]%}
                    {@ $arrR = array_slice($arrRelated,0,3);@}
                    @foreach($arrR as $itempro2)
                    <div class="col-lg-4 col-sm-6 col-12 xs_pd_5 pad10">
                        <div class="item_story_high row mar_5">
                            <div class="col-lg-6 col-sm-6 col-6 pd_5 relative">
                                <a href="{(itempro2.slug)}" class="c-img">
                                    [[itempro2.#w#img.pro_204x0]]
                                </a>
                                @if($itempro2['status']=='edit')
                                <div class="edit">
                                    <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                                </div>
                                @endif
                            </div>
                            <div class="col-lg-6 col-sm-6 col-6 pd_5 relative">
                                <div class="text">
                                    {@ 
                                       $tag = explode(',',$itempro2['tag_pro']);
                                       $id_tag = $tag[0];
                                       @}
                                       <h3 class="name"><a href="{(itempro2.slug)}" title="{(itempro2.name)}">{(itempro2.name)}</a></h3>
                                       <span class="type">    <!--DBS-loop.tag.2|where:id = $id_tag|order:|limit:0,1-->
                                        {(itemtag2.name)}
                                        <!--DBE-loop.tag.2-->
                                    </span>
                                    <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                        <div class="star-base ">
                                            <div class="star-rate" style="width: {@echo $itempro2['score']*16 @}px;"></div> 
                                            <a dt-value="1" href="#1"></a> 
                                            <a dt-value="2" href="#2"></a> 
                                            <a dt-value="3" href="#3"></a> 
                                            <a dt-value="4" href="#4"></a> 
                                            <a dt-value="5" href="#5"></a>
                                        </div>
                                    </div>
                                    <a href="{(itempro2.slug)}" class="view_more v2">Xem thêm</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @include('modal_confirm')
    @stop
    @section('js')
    <script type="text/javascript" src="theme/frontend/js/offline.js" defer></script>
    @stop