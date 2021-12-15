<div class="container">
                <h2 class="title_border"><a href="">Truyện mới cập nhật</a></h2>
                <div class="row">
                       
                    @foreach($list_data as $itempro2)
                    <div class="col-lg-4 col-sm-6 col-6 xs_pd_5">
                            <div class="item_story_high row mar_5 d-flex align-items-center">
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <a href="{(itempro2.slug)}" class="c-img">
                                        [[itempro2.#w#img.-1.1]]
                                    </a>
                                    @if($itempro2['status']==0)
                                        <div class="edit">
                                            <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                                            <span>
                                                <!--DBS-loop.pro_categories.4|where:act = 1,id = $itempro2['parent']|order:|limit:-->
                                                    {(itempro_categories4.short_name)}
                                                <!--DBE-loop.pro_categories.4-->
                                            </span>
                                        </div>
                                        @endif
                                </div>
                                
                                <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
                                    <div class="text">
                                        {@ 

                                         $tag = explode(',',$itempro2['tag_pro']);
                                         $id_tag = $tag[0];
                                         @}
                                        <h3 class="name"><a href="{(itempro2.slug)}" title="{(itempro2.name)}">{(itempro2.name)}</a></h3>

                                        <span class="type"><!--DBS-loop.tag_pro.1|where:id = $id_tag|order:|limit:0,6-->
                                        <a href="tags/{(itemtag_pro1.link)}" title="{(itemtag_pro1.name)}">{(itemtag_pro1.name)}</a>
                                    <!--DBE-loop.tag_pro.1--></span>
                                    <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                        <input type="hidden" name="pid" value="{(itempro2.id)}">
                                        <input type="hidden" name="table" value="pro">
                                        {@
                                            $arr=$itempro2['score'];
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
                                        <a href="{(itempro2.slug)}" class="view_more v2">Xem thêm</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>