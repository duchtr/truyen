@foreach($list_data as $itempro_home2)
<h4 class="title_story"><a href="{(itempro_home2.slug)}" title="{(itempro_home2.name)}">{(itempro_home2.name)}</a></h4>
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
                                        <span>Tác giả: {(itempro_home2.name)}</span>
                                        <span>Editor: {(itempro_home2.editor)}</span>
                                        {@ 

                                         $tag = explode(',',$itempro_home2['tag_pro']);
                                         $id_tag = $tag[0];
                                         @}
                                        <span>Thể loại: <!--DBS-loop.tag.2|where:id = $id_tag|order:|limit:-->
                                        {(itemtag2.name)}
                                    <!--DBE-loop.tag.2--></span>
                                        <span class="des_small">
                                            {(itempro_home2.short_content)}
                                        </span>
@endforeach