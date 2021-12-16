<?php $__env->startSection('content'); ?>
<div class="banner page bg relative" data-background="<?php echo $this->CI->Dindex->getSettingImage('BG_TRUYEN',1,'-1',false); ?>" data-background-webp="<?php echo $this->CI->Dindex->getSettingImage('BG_TRUYEN',1,'-1',false); ?>">
    <div class="text">
        <div class="container">
            <h1 class="title_pro"><?php echom($dataitem,'name',1); ?></h1>
            <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="<?php echo $this->CI->Dindex->getSettings('HOME'); ?>"><span itemprop="name">Trang chủ</span></a>
                    <meta itemprop="position" content="1">
                </li>
                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                    <a itemprop="item"><span itemprop="name"><?php echom($dataitem,'name',1); ?></span></a>
                    <meta itemprop="position" content="3">
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="story_edit_new story_categories">
    <div class="container">

        <div class="row xs_mar_5 row100 static">
            <?php foreach($list_data as $itempro_home2): ?>
            <div class="col-lg-3 col-6 item_pro xs_pd_5 pad100 static">
                <div class="item_cover_pro" data-id="<?php echom($itempro_home2,'id',1); ?>">
                    <div class="item_pro_small">
                        <a href="<?php echom($itempro_home2,'slug',1); ?>" class="c-img">
                            <?php echo imgv2($itempro_home2,'#w#img','procate_300x0',false) ; ?>                        </a>
                        <div class="star-rating on line wow fadeInUp margin15 relative"> 
                            <input type="hidden" name="pid" value="<?php echom($dataitem,'id',1); ?>">
                            <input type="hidden" name="table" value="<?php  echo $masteritem['table'];  ?>">
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
                            <?php if($itempro_home2['status']=='edit'): ?>
                            <div class="edit">
                                <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="item_pro_hover">
                            <h4 class="title_story"><a href="<?php echom($itempro_home2,'slug',1); ?>" title="<?php echom($itempro_home2,'name',1); ?>"><?php echom($itempro_home2,'name',1); ?></a></h4>
                            <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                <input type="hidden" name="pid" value="<?php echom($dataitem,'id',1); ?>">
                                <input type="hidden" name="table" value="<?php  echo $masteritem['table'];  ?>">
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
                                <?php  

                                   $tag = explode(',',$itempro_home2['tag_pro']);
                                   $id_tag = $tag[0];
                                    ?>
                                   <p class="d-flex align-items-center">
                                    <span>Thể loại :</span>
                                    <?php
            $arrtag2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'=','value'=>$id_tag)),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag2 = count($arrtag2);
     for ($itag2=0; $itag2 < $counttag2; $itag2++) { $itemtag2=$arrtag2[$itag2]; ?>
                                        <span class="mr-2"><?php echom($itemtag2,'name',1); ?></span>
                                    <?php }; ?>
                                </p>
                                <span class="des_small">
                                    <?php echom($itempro_home2,'short_content',1); ?>
                                </span>
                            </div> 
                        </div>
                    </div>
                    <?php endforeach; ?>




                </div>
                <div class="pagination_t">
                    <?php  echo $this->CI->pagination->create_links(); ?>
                </div>
            </div>
        </div>
        <?php $__env->stopSection(); ?>


<?php echo $__env->make('index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>