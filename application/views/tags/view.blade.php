@extends('index')
@section('content')
        <div class="banner relative">
            {<bg_truyen.1.-1.1>}
            <div class="text">
                <div class="container">
                    <h1 class="title_pro">{(name)}</h1>
                        <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                            <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                                <a itemprop="item" href="{[HOME]}"><span itemprop="name">Trang chủ</span></a>
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
        <div class="story_edit_new story_categories">
            <div class="container">
               
                <div class="row xs_mar_5 static">
                    @foreach($list_data as $itempro_home2)
                            <div class="col-lg-3 col-6 item_pro xs_pd_5 static">
                                <div class="item_cover_pro" data-id="{(itempro_home2.id)}">
                                    <div class="item_pro_small">
                                        <a href="{(itempro_home2.slug)}" class="c-img">
                                            [[itempro_home2.#w#img.-1.1]]
                                        </a>
                                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                            <input type="hidden" name="pid" value="{(id)}">
                                            <input type="hidden" name="table" value="pro">
                                            {@
                                                $arr=$itempro_home2['score'];
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
                                        <h4 class="title_story"><a href="{(itempro_home2.slug)}" title="{(itempro_home2.name)}">{(itempro_home2.name)}</a></h4>
                                        @if($itempro_home2['status']=='edit')
                                        <div class="edit">
                                            <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                                        </div>
                                        @endif
                                    </div>
                                    <div class="item_pro_hover">
                                        <h4 class="title_story"><a href="{(itempro_home2.slug)}" title="{(itempro_home2.name)}">{(itempro_home2.name)}</a></h4>
                                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                            <input type="hidden" name="pid" value="{(id)}">
                                            <input type="hidden" name="table" value="pro">
                                            {@
                                                $arr=$itempro_home2['score'];
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
                                        <span>Tác giả: {(itempro_home2.name)}</span>
                                        <span>Editor: {(itempro_home2.editor)}</span>
                                        {@ 

                                         $tag = explode(',',$itempro_home2['tag_pro']);
                                         $id_tag = $tag[0];
                                         @}
                                        <span>Thể loại:
                                           
                                            
                                         <!--DBS-loop.tag_pro.2|where:id = $id_tag-->
                                        <a href="tags/{(itemtag_pro2.link)}">{(itemtag_pro2.name)}</a>
                                    <!--DBE-loop.tag_pro.2--></span>
                                        <span class="des_small">
                                            {(itempro_home2.short_content)}
                                        </span>
                                    </div> 
                                </div>
                            </div>
                            @endforeach
                   
                    



                </div>
                <div class="pagination_t">
                    {%PAGINATION%}
                </div>
            </div>
        </div>
@endsection

    