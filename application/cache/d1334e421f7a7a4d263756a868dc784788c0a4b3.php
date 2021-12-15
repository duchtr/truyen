<?php $__env->startSection('content'); ?>
<div class="slider  tiny-slider d-flex" data-slide-by ='page' data-items='2' data-auto_height="true" data-mouse-drag='true' data-controls='false' data-autoplay-button-output='false' data-nav='true' data-center='true' data-autoplay='true' data-responsive ='{"1":{"items":"2"},"480":{"items":"2"},"767":{"items":"1"},"991":{"items":"2"}}' >
  <?php
            $arrpro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'home','compare'=>'=','value'=>'1'),array('key'=>'slide','compare'=>'=','value'=>'1')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro1 = count($arrpro1);
     for ($ipro1=0; $ipro1 < $countpro1; $ipro1++) { $itempro1=$arrpro1[$ipro1]; ?>
  <div class="item_slide" >
    <div class="img_slide relative">
      <div class="cover_img">
        <a class="c-img img-center" title="<?php echom($itempro1,'name',1); ?>" href="<?php echom($itempro1,'slug',1); ?>">
          <?php echo imgv2($itempro1,'#W#img','slide_-1',false) ; ?>        </a> 
      </div>
    </div>
    <div class="text relative">
      <h3 class="title_sider hv-yl"><a href="<?php echom($itempro1,'slug',1); ?>" title="<?php echom($itempro1,'name',1); ?>"><?php echom($itempro1,'name',1); ?></a></h3>
      <?php  

       $tag = explode(',',$itempro1['tag_pro']);
       $id_tag = $tag[0];
        ?>
       <span>Tác giả: <?php echom($itempro1,'author',1); ?></span>
       <span>Editor: <?php echom($itempro1,'editor',1); ?></span>
        <p class="d-flex align-items-center mb-2 flex-wrap">
            Thể loại : 
              <?php
            $arrtag_pro3 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag_pro',
                    'order'=>'ord asc,id desc',
                    'where'=>array(array('key'=>'FIND_IN_SET(id,\''.$itempro1['tag_pro'].'\')','compare'=>'>','value'=>'0')),
                    'limit'=>'0,4',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag_pro3 = count($arrtag_pro3);
     for ($itag_pro3=0; $itag_pro3 < $counttag_pro3; $itag_pro3++) { $itemtag_pro3=$arrtag_pro3[$itag_pro3]; ?>
                  <a href="tags/<?php echom($itemtag_pro3,'link',1); ?>" title="<?php echom($itemtag_pro3,'name',1); ?>" class="ml-2 hv-yl"><?php echom($itemtag_pro3,'name',1); ?></a>
              <?php }; ?>    
        </p>
       <div class="star-rating on line wow fadeInUp margin15 relative"> 
        <input type="hidden" name="pid" value="<?php echom($itempro1,'id',1); ?>">
        <input type="hidden" name="table" value="pro">
        <?php 
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
           ?>
          <div class="star-base">
            <div class="star-rate" style="width:<?php  echo $w  ?>%"></div>
            <a dt-value="1" href="#1"></a>
            <a dt-value="2" href="#2"></a>
            <a dt-value="3" href="#3"></a>
            <a dt-value="4" href="#4"></a>
            <a dt-value="5" href="#5"></a>
          </div>
        </div>
        <span class="des_small user-none">
          <?php echom($itempro1,'short_content',1); ?>
        </span>
        <a class="view_more" href="<?php echom($itempro1,'slug',1); ?>" title="<?php echom($itempro1,'name',1); ?>">Xem thêm</a>
      </div>
    </div>
    <?php }; ?>


  </div>
  <div class="story_categrories">
    <div class="container">
      <div class="tiny-slider" data-slide-by ='page' data-items='4' data-mouse-drag='true' data-autoplay ='true' data-controls='false' data-autoplay-button-output='false' data-nav='true' data-center='false' data-responsive ='{"1":{"items":"2"},"480":{"items":"2"},"767":{"items":"3"},"991":{"items":"4"}}'>
        <?php
            $arrpro_categories1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro_categories',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'home','compare'=>'=','value'=>'1')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro_categories1 = count($arrpro_categories1);
     for ($ipro_categories1=0; $ipro_categories1 < $countpro_categories1; $ipro_categories1++) { $itempro_categories1=$arrpro_categories1[$ipro_categories1]; ?>
        <div class="">
          <div class="item_pro_cate">
            <a href="<?php echom($itempro_categories1,'slug',1); ?>" class="img_pro_cate">
              <?php echo imgv2($itempro_categories1,'#W#img','pro_204x0',false) ; ?>            </a>
            <a href="<?php echom($itempro_categories1,'slug',1); ?>" class="text_pro_cate smooth"><?php echom($itempro_categories1,'name',1); ?> <i class="fa fa-angle-down" aria-hidden="true"></i></a>
          </div>
        </div>
        <?php }; ?>

      </div>
    </div>
  </div>
  <div class="story_highlight">
    <div class="container">
      <h2 class="title_border">Truyện nổi bật</h2>
      <div class="row xs_mar_5">
        <?php
            $arrpro2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'hot','compare'=>'=','value'=>'1'),array('key'=>'home','compare'=>'=','value'=>'1')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro2 = count($arrpro2);
     for ($ipro2=0; $ipro2 < $countpro2; $ipro2++) { $itempro2=$arrpro2[$ipro2]; ?>
        <div class="col-lg-4 col-sm-6 col-6 xs_pd_5">
          <div class="item_story_high row mar_5 d-flex align-items-center">
            <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
              <a href="<?php echom($itempro2,'slug',1); ?>" class="c-img">
                <?php echo imgv2($itempro2,'#w#img','pro_204x0',false) ; ?>              </a>
              <?php if($itempro2['status']==0): ?>
              <div class="edit">
                <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                <span>
                  <?php
            $arrpro_categories4 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro_categories',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'id','compare'=>'=','value'=>$itempro2['parent'])),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro_categories4 = count($arrpro_categories4);
     for ($ipro_categories4=0; $ipro_categories4 < $countpro_categories4; $ipro_categories4++) { $itempro_categories4=$arrpro_categories4[$ipro_categories4]; ?>
                  <?php echom($itempro_categories4,'short_name',1); ?>
                  <?php }; ?>
                </span>
              </div>
              <?php endif; ?>
            </div>

            <div class="col-lg-6 col-sm-6 col-12 pd_5 relative">
              <div class="text">

                <h3 class="name"><a href="<?php echom($itempro2,'slug',1); ?>" title="<?php echom($itempro2,'name',1); ?>"><?php echom($itempro2,'name',1); ?></a></h3>
                <?php  

                 $tag = explode(',',$itempro2['tag_pro']);
                 $id_tag = $tag[0];
                  ?>
                 <span class="type">
                  <?php
            $arrtag_pro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag_pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'=','value'=>$id_tag)),
                    'limit'=>'0,6',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag_pro1 = count($arrtag_pro1);
     for ($itag_pro1=0; $itag_pro1 < $counttag_pro1; $itag_pro1++) { $itemtag_pro1=$arrtag_pro1[$itag_pro1]; ?>
                  <a href="tags/<?php echom($itemtag_pro1,'link',1); ?>" title='<?php echom($itemtag_pro1,'name',1); ?>'><?php echom($itemtag_pro1,'name',1); ?>
                  </a>
                  <?php }; ?>
                </span>
                <div class="star-rating on line wow fadeInUp margin15 relative"> 
                 <input type="hidden" name="pid" value="<?php echom($itempro2,'id',1); ?>">
                 <input type="hidden" name="table" value="pro">
                 <?php 
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
                  ?>
                 <div class="star-base">
                   <div class="star-rate" style="width:<?php  echo $w  ?>%"></div>
                   <a dt-value="1" href="#1"></a>
                   <a dt-value="2" href="#2"></a>
                   <a dt-value="3" href="#3"></a>
                   <a dt-value="4" href="#4"></a>
                   <a dt-value="5" href="#5"></a>
                 </div>
               </div>
               <a href="<?php echom($itempro2,'slug',1); ?>" class="view_more v2">Xem thêm</a>
             </div>
           </div>
         </div>
       </div>
       <?php }; ?>
     </div>
   </div>
 </div>
 <?php
            $arrpro_categories1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro_categories',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'home','compare'=>'=','value'=>'1')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro_categories1 = count($arrpro_categories1);
     for ($ipro_categories1=0; $ipro_categories1 < $countpro_categories1; $ipro_categories1++) { $itempro_categories1=$arrpro_categories1[$ipro_categories1]; ?>
 <?php
            $arrpro4 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'home','compare'=>'=','value'=>'1'),array('key'=>'parent','compare'=>'=','value'=>$itempro_categories1['id'])),
                    'limit'=>'0,7',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro4 = count($arrpro4);
     for ($ipro4=0; $ipro4 < $countpro4; $ipro4++) { $itempro4=$arrpro4[$ipro4]; ?>
 <?php }; ?>
 <?php 
  $arr1 = array_slice($arrpro4,0,1);
  $arr2 = array_slice($arrpro4,1,6);

   ?>
  <?php if(!empty($arr1)): ?>
  <div class="story_edit_new ">

    <div class="container">
      <div class="cover_title ">
        <h2 class="title_border v2"><a href="<?php echom($itempro_categories1,'slug',1); ?>" title="itempro_categories1.title"><?php echom($itempro_categories1,'name',1); ?></a></h2>
        <a href="<?php echom($itempro_categories1,'slug',1); ?>" class="xemthem">Xem thêm </a>    
      </div>


      <div class="row xs_mar_5 roww10">
        <div class="col-lg-4 col-md-6 col-12 paddd10">
          <?php foreach($arr1 as $itempro_home): ?>
          <div class="item_pro_big box_shadow">
            <a href="<?php echom($itempro_home,'slug',1); ?>" class="c-img">
              <?php echo imgv2($itempro_home,'#w#img','slide_-1',false) ; ?>            </a>
            <div class="text">
              <h3 class="title_big_story"><a href="<?php echom($itempro_home,'slug',1); ?>" title="<?php echom($itempro_home,'name',1); ?>" ><?php echom($itempro_home,'name',1); ?></a></h3>
              <?php if($itempro_home['status']==0): ?>
              <div class="edit">
                <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                <span><?php echom($itempro_categories1,'short_name',1); ?></span>
              </div>
              <?php endif; ?>
              <div class="star-rating on line wow fadeInUp margin15 relative"> 
               <input type="hidden" name="pid" value="<?php echom($itempro_home,'id',1); ?>">
               <input type="hidden" name="table" value="pro">
               <?php 
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
                ?>
               <div class="star-base">
                 <div class="star-rate" style="width:<?php  echo $w  ?>%"></div>
                 <a dt-value="1" href="#1"></a>
                 <a dt-value="2" href="#2"></a>
                 <a dt-value="3" href="#3"></a>
                 <a dt-value="4" href="#4"></a>
                 <a dt-value="5" href="#5"></a>
               </div>
             </div>
             <?php  

               $tag = explode(',',$itempro2['tag_pro']);
               $id_tag = $tag[0];
                ?>
               <span class="type"><?php
            $arrtag_pro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag_pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'=','value'=>$id_tag)),
                    'limit'=>'0,6',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag_pro1 = count($arrtag_pro1);
     for ($itag_pro1=0; $itag_pro1 < $counttag_pro1; $itag_pro1++) { $itemtag_pro1=$arrtag_pro1[$itag_pro1]; ?>
                <a href="tags/<?php echom($itemtag_pro1,'link',1); ?>" title='<?php echom($itemtag_pro1,'name',1); ?>'><?php echom($itemtag_pro1,'name',1); ?></a>
                <?php }; ?></span>
                <span class="des_small">
                  <?php echom($itempro_home,'short_content',1); ?>
                </span>
                <a href="<?php echom($itempro_home,'slug',1); ?>" class="view_more v2" title="<?php echom($itempro_home,'name',1); ?>">Xem thêm</a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="col-lg-8 col-md-12 col-12 static paddd10">
            <div class="row xs_mar_5 row-10">
              <?php  $i = 1; ?>
              <?php foreach($arr2 as $itempro_home2): ?>
              <div class="col-lg-4 col-6 item_pro xs_pd_5 paddd10 static">
                <div class="item_cover_pro ">
                  <div class="item_pro_small relative">
                    <a href="<?php echom($itempro_home2,'slug',1); ?>" class="c-img">
                      <?php echo imgv2($itempro_home2,'#w#img','pro_204x0',false) ; ?>                    </a>
                    <div class="star-rating on line wow fadeInUp margin15 relative"> 
                     <input type="hidden" name="pid" value="<?php echom($itempro_home2,'id',1); ?>">
                     <input type="hidden" name="table" value="pro">
                     <?php 
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
                      ?>
                     <div class="star-base">
                       <div class="star-rate" style="width:<?php  echo $w  ?>%"></div>
                       <a dt-value="1" href="#1"></a>
                       <a dt-value="2" href="#2"></a>
                       <a dt-value="3" href="#3"></a>
                       <a dt-value="4" href="#4"></a>
                       <a dt-value="5" href="#5"></a>
                     </div>
                   </div>
                   <h4 class="title_story"><a href="<?php echom($itempro_home2,'slug',1); ?>" title="<?php echom($itempro_home2,'name',1); ?>"><?php echom($itempro_home2,'name',1); ?></a></h4>

                 </div>
                 <div class="item_pro_hover">
                  <h4 class="title_story"><a href="<?php echom($itempro_home2,'slug',1); ?>" title="<?php echom($itempro_home2,'name',1); ?>"><?php echom($itempro_home2,'name',1); ?></a></h4>
                  <div class="star-rating on line wow fadeInUp margin15 relative"> 
                   <input type="hidden" name="pid" value="<?php echom($itempro_home2,'id',1); ?>">
                   <input type="hidden" name="table" value="pro">
                   <?php 
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
                    ?>
                   <div class="star-base">
                     <div class="star-rate" style="width:<?php  echo $w  ?>%"></div>
                     <a dt-value="1" href="#1"></a>
                     <a dt-value="2" href="#2"></a>
                     <a dt-value="3" href="#3"></a>
                     <a dt-value="4" href="#4"></a>
                     <a dt-value="5" href="#5"></a>
                   </div>
                 </div>
                 <span>Tác giả: <?php echom($itempro_home2,'name',1); ?></span>
                 <span>Editor: <?php echom($itempro_home2,'editor',1); ?></span>
                 <span>Thể loại: <!--DBS-loop.tag.2|where:id = $id_tag|order:|limit:-->
                  <?php  

                   $tag = explode(',',$itempro2['tag_pro']);
                   $id_tag = $tag[0];
                    ?>
                   <span class="type"><?php
            $arrtag_pro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag_pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'=','value'=>$id_tag)),
                    'limit'=>'0,6',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag_pro1 = count($arrtag_pro1);
     for ($itag_pro1=0; $itag_pro1 < $counttag_pro1; $itag_pro1++) { $itemtag_pro1=$arrtag_pro1[$itag_pro1]; ?>
                    <a href="tags/<?php echom($itemtag_pro1,'link',1); ?>" title='<?php echom($itemtag_pro1,'name',1); ?>'><?php echom($itemtag_pro1,'name',1); ?></a>
                    <?php }; ?></span>
                    <span class="des_small">
                      <?php echom($itempro_home2,'short_content',1); ?>
                    </span>
                  </div> 
                </div>
              </div>
              <?php $i++ ?>
              <?php endforeach; ?>

            </div>

          </div>
        </div>

      </div>

    </div>
    <?php endif; ?>
    <?php }; ?>
    <div class="story_highlight story_update">

    </div>    
    <div class="review relative">
      <div class="container">
        <h2 class="title_border"><a href="">Góc Review</a></h2>
        <div class="slider_review tiny-slider  d-block" data-gutter="30" data-slide-by ='page' data-items='2' data-mouse-drag='true' data-autoplay ='true' data-controls='true'  data-autoplay-button-output='false' data-nav='false'  data-controls-text='["«","»"]' data-responsive ='{"1":{"items":"1"},"480":{"items":"1"},"767":{"items":"1"},"991":{"items":"2"}}'>

          <?php
            $arrreview_kn1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'review_kn',
                    'order'=>'',
                    'where'=>array(),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countreview_kn1 = count($arrreview_kn1);
     for ($ireview_kn1=0; $ireview_kn1 < $countreview_kn1; $ireview_kn1++) { $itemreview_kn1=$arrreview_kn1[$ireview_kn1]; ?>
          <div class="item_review_c">


            <div class="item_review">
              <div class="name_av d-flex justify-content-start relative align-items-center">
                <a href="" class='img_av'>
                  <img src="<?php echo imgv2($itemreview_kn1,'img','350x0',false) ; ?>" alt="<?php echom($itemreview_kn1,'#i#img#alt',1); ?>" title="<?php echom($itemreview_kn1,'#i#img#title',1); ?>" class="img-fluid" />
                </a>
                <div class="text">
                  <h4 class="name"><?php echom($itemreview_kn1,'name',1); ?></h4>
                  <span><?php echom($itemreview_kn1,'position',1); ?></span>    
                </div>

                <img src="<?php echo imgv2($itemreview_kn1,'mxh','350x0',false) ; ?>" alt="<?php echom($itemreview_kn1,'#i#mxh#alt',1); ?>" title="<?php echom($itemreview_kn1,'#i#mxh#title',1); ?>" class="img-fluid img_mxh" />
              </div>
              <span class="des_review">
                <?php echom($itemreview_kn1,'content',1); ?>
              </span>
              <img src="theme/frontend/images/phay.jpg" title="" alt="" class="img-fluid smooth phay">
            </div>
          </div>
          <?php }; ?>


        </div>
      </div>
    </div>

    <?php $__env->stopSection(); ?>
<?php echo $__env->make('index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>