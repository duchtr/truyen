@extends('index')
@section('content')
<div class="slider  tiny-slider d-flex" data-slide-by ='page' data-items='2' data-auto_height="true" data-mouse-drag='true' data-controls='false' data-autoplay-button-output='false' data-nav='true' data-center='true' data-autoplay='true' data-responsive ='{"1":{"items":"2"},"480":{"items":"2"},"767":{"items":"1"},"991":{"items":"2"}}' >
  <!--DBS-loop.pro.1|where:act = 1,home = 1,slide = 1|order:|limit:-->
  <div class="item_slide" >
    <div class="img_slide relative">
      <div class="cover_img">
        <a class="c-img img-center" title="{(itempro1.name)}" href="{(itempro1.slug)}">
          [[itempro1.#W#img.slide_-1]]
        </a> 
      </div>
    </div>
    <div class="text relative">
      <h3 class="title_sider hv-yl"><a href="{(itempro1.slug)}" title="{(itempro1.name)}">{(itempro1.name)}</a></h3>
      {@ 

       $tag = explode(',',$itempro1['tag_pro']);
       $id_tag = $tag[0];
       @}
       <span>Tác giả: {(itempro1.author)}</span>
       <span>Editor: {(itempro1.editor)}</span>
        <p class="d-flex align-items-center mb-2 flex-wrap">
            Thể loại : 
              <!--DBS-loop.tag_pro.3|where:FIND_IN_SET(id;\''.$itempro1['tag_pro'].'\') > 0|limit:0,4-->
                  <a href="tags/{(itemtag_pro3.link)}" title="{(itemtag_pro3.name)}" class="ml-2 hv-yl">{(itemtag_pro3.name)}</a>
              <!--DBE-loop.tag_pro.3-->    
        </p>
       <div class="star-rating on line wow fadeInUp margin15 relative"> 
        <input type="hidden" name="pid" value="{(itempro1.id)}">
        <input type="hidden" name="table" value="pro">
        {@
          $arr=$itempro1['score'];
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
        <span class="des_small user-none">
          {(itempro1.short_content)}
        </span>
        <a class="view_more" href="{(itempro1.slug)}" title="{(itempro1.name)}">Xem thêm</a>
      </div>
    </div>
    <!--DBE-loop.pro.1-->


  </div>
  <div class="story_categrories">
    <div class="container">
      <div class="tiny-slider" data-slide-by ='page' data-items='4' data-mouse-drag='true' data-autoplay ='true' data-controls='false' data-autoplay-button-output='false' data-nav='true' data-center='false' data-responsive ='{"1":{"items":"2"},"480":{"items":"2"},"767":{"items":"3"},"991":{"items":"4"}}'>
        <!--DBS-loop.pro_categories.1|where:act = 1,home = 1|order:|limit:-->
        <div class="">
          <div class="item_pro_cate">
            <a href="{(itempro_categories1.slug)}" class="img_pro_cate">
              [[itempro_categories1.#W#img.pro_204x0]]
            </a>
            <a href="{(itempro_categories1.slug)}" class="text_pro_cate smooth">{(itempro_categories1.name)} <i class="fa fa-angle-down" aria-hidden="true"></i></a>
          </div>
        </div>
        <!--DBE-loop.pro_categories.1-->

      </div>
    </div>
  </div>
  <div class="story_highlight">
    <div class="container">
      <h2 class="title_border">Truyện nổi bật</h2>
      <div class="row xs_mar_5">
        <!--DBS-loop.pro.2|where:act = 1,hot = 1,home = 1|order:|limit:-->
        <div class="col-lg-4 col-sm-6 col-6 xs_pd_5">
          <div class="item_story_high row mar_5 d-flex align-items-center">
            <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
              <a href="{(itempro2.slug)}" class="c-img">
                [[itempro2.#w#img.pro_204x0]]
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

                <h3 class="name"><a href="{(itempro2.slug)}" title="{(itempro2.name)}">{(itempro2.name)}</a></h3>
                {@ 

                 $tag = explode(',',$itempro2['tag_pro']);
                 $id_tag = $tag[0];
                 @}
                 <span class="type">
                  <!--DBS-loop.tag_pro.1|where:id = $id_tag|order:|limit:0,6-->
                  <a href="tags/{(itemtag_pro1.link)}" title='{(itemtag_pro1.name)}'>{(itemtag_pro1.name)}
                  </a>
                  <!--DBE-loop.tag_pro.1-->
                </span>
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
       <!--DBE-loop.pro.2-->
     </div>
   </div>
 </div>
 <!--DBS-loop.pro_categories.1|where:act = 1,home = 1|order:|limit:0,3-->
 <!--DBS-loop.pro.4|where:act = 1,home = 1,parent = $itempro_categories1['id']|order:|limit:0,7-->
 <!--DBE-loop.pro.4-->
 {@
  $arr1 = array_slice($arrpro4,0,1);
  $arr2 = array_slice($arrpro4,1,6);

  @}
  @if(!empty($arr1))
  <div class="story_edit_new ">

    <div class="container">
      <div class="cover_title ">
        <h2 class="title_border v2"><a href="{(itempro_categories1.slug)}" title="itempro_categories1.title">{(itempro_categories1.name)}</a></h2>
        <a href="{(itempro_categories1.slug)}" class="xemthem">Xem thêm </a>    
      </div>


      <div class="row xs_mar_5 roww10">
        <div class="col-lg-4 col-md-6 col-12 paddd10">
          @foreach($arr1 as $itempro_home)
          <div class="item_pro_big box_shadow">
            <a href="{(itempro_home.slug)}" class="c-img">
              [[itempro_home.#w#img.slide_-1]]
            </a>
            <div class="text">
              <h3 class="title_big_story"><a href="{(itempro_home.slug)}" title="{(itempro_home.name)}" >{(itempro_home.name)}</a></h3>
              @if($itempro_home['status']==0)
              <div class="edit">
                <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                <span>{(itempro_categories1.short_name)}</span>
              </div>
              @endif
              <div class="star-rating on line wow fadeInUp margin15 relative"> 
               <input type="hidden" name="pid" value="{(itempro_home.id)}">
               <input type="hidden" name="table" value="pro">
               {@
                $arr=$itempro_home['score'];
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
             {@ 

               $tag = explode(',',$itempro2['tag_pro']);
               $id_tag = $tag[0];
               @}
               <span class="type"><!--DBS-loop.tag_pro.1|where:id = $id_tag|order:|limit:0,6-->
                <a href="tags/{(itemtag_pro1.link)}" title='{(itemtag_pro1.name)}'>{(itemtag_pro1.name)}</a>
                <!--DBE-loop.tag_pro.1--></span>
                <span class="des_small">
                  {(itempro_home.short_content)}
                </span>
                <a href="{(itempro_home.slug)}" class="view_more v2" title="{(itempro_home.name)}">Xem thêm</a>
              </div>
            </div>
            @endforeach
          </div>
          <div class="col-lg-8 col-md-12 col-12 static paddd10">
            <div class="row xs_mar_5 row-10">
              {@ $i = 1;@}
              @foreach($arr2 as $itempro_home2)
              <div class="col-lg-4 col-6 item_pro xs_pd_5 paddd10 static">
                <div class="item_cover_pro ">
                  <div class="item_pro_small relative">
                    <a href="{(itempro_home2.slug)}" class="c-img">
                      [[itempro_home2.#w#img.pro_204x0]]
                    </a>
                    <div class="star-rating on line wow fadeInUp margin15 relative"> 
                     <input type="hidden" name="pid" value="{(itempro_home2.id)}">
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

                 </div>
                 <div class="item_pro_hover">
                  <h4 class="title_story"><a href="{(itempro_home2.slug)}" title="{(itempro_home2.name)}">{(itempro_home2.name)}</a></h4>
                  <div class="star-rating on line wow fadeInUp margin15 relative"> 
                   <input type="hidden" name="pid" value="{(itempro_home2.id)}">
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
                 <span>Thể loại: <!--DBS-loop.tag.2|where:id = $id_tag|order:|limit:-->
                  {@ 

                   $tag = explode(',',$itempro2['tag_pro']);
                   $id_tag = $tag[0];
                   @}
                   <span class="type"><!--DBS-loop.tag_pro.1|where:id = $id_tag|order:|limit:0,6-->
                    <a href="tags/{(itemtag_pro1.link)}" title='{(itemtag_pro1.name)}'>{(itemtag_pro1.name)}</a>
                    <!--DBE-loop.tag_pro.1--></span>
                    <span class="des_small">
                      {(itempro_home2.short_content)}
                    </span>
                  </div> 
                </div>
              </div>
              {@$i++@}
              @endforeach

            </div>

          </div>
        </div>

      </div>

    </div>
    @endif
    <!--DBE-loop.pro_categories.1-->
    <div class="story_highlight story_update">

    </div>    
    <div class="review relative">
      <div class="container">
        <h2 class="title_border"><a href="">Góc Review</a></h2>
        <div class="slider_review tiny-slider  d-block" data-gutter="30" data-slide-by ='page' data-items='2' data-mouse-drag='true' data-autoplay ='true' data-controls='true'  data-autoplay-button-output='false' data-nav='false'  data-controls-text='["«","»"]' data-responsive ='{"1":{"items":"1"},"480":{"items":"1"},"767":{"items":"1"},"991":{"items":"2"}}'>

          <!--DBS-loop.review_kn.1|where:|order:|limit:-->
          <div class="item_review_c">


            <div class="item_review">
              <div class="name_av d-flex justify-content-start relative align-items-center">
                <a href="" class='img_av'>
                  <img src="[[itemreview_kn1.img.350x0]]" alt="{(itemreview_kn1.#i#img#alt)}" title="{(itemreview_kn1.#i#img#title)}" class="img-fluid" />
                </a>
                <div class="text">
                  <h4 class="name">{(itemreview_kn1.name)}</h4>
                  <span>{(itemreview_kn1.position)}</span>    
                </div>

                <img src="[[itemreview_kn1.mxh.350x0]]" alt="{(itemreview_kn1.#i#mxh#alt)}" title="{(itemreview_kn1.#i#mxh#title)}" class="img-fluid img_mxh" />
              </div>
              <span class="des_review">
                {(itemreview_kn1.content)}
              </span>
              <img src="theme/frontend/images/phay.jpg" title="" alt="" class="img-fluid smooth phay">
            </div>
          </div>
          <!--DBE-loop.review_kn.1-->


        </div>
      </div>
    </div>

    @endsection