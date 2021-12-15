<?php $__env->startSection('content'); ?>
<div class="banner page bg relative" data-background="<?php echo $this->CI->Dindex->getSettingImage('BG_TRUYEN',1,'-1',false); ?>" data-background-webp="<?php echo $this->CI->Dindex->getSettingImage('BG_TRUYEN',1,'-1',false); ?>">
    <div class="text">
        <div class="container">
            <h1 class="title_pro"><?php echom($dataitem,'name',1); ?></h1>
            <?php $this->CI->Dindex->getBreadcrumb((isset($datatable)&& array_key_exists("table_parent", $datatable))?$datatable["table_parent"]:array(),@$dataitem["parent"]?$dataitem["parent"]:0,echor($dataitem,"name","1")); ?>
        </div>
    </div>
</div>
<div class="story_edit_new pro_detail">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-12 col-12 mb20qa">
                <div class="cover banner_qaaa relative">
                    <a class="c-img">
                        <img src="<?php echo imgv2($dataitem,'img','350x0',false) ; ?>" alt="<?php echom($dataitem,'#i#img#alt',1); ?>" title="<?php echom($dataitem,'#i#img#title',1); ?>" class="img-fluid" />
                    </a>
                    <div class="edit">
                        <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                        <span>
                            <?php
            $arrpro_categories4 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro_categories',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'id','compare'=>'=','value'=>$dataitem['parent'])),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro_categories4 = count($arrpro_categories4);
     for ($ipro_categories4=0; $ipro_categories4 < $countpro_categories4; $ipro_categories4++) { $itempro_categories4=$arrpro_categories4[$ipro_categories4]; ?>
                            <?php echo e($itempro_categories4['short_name']); ?>

                            <?php }; ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-md-6 col-sm-12 col-12">
                <h1 class="title_pro_detail" data-id="<?php echom($dataitem,'id',1); ?>"><?php echom($dataitem,'name',1); ?></h1>
                <div class="star-rating on line wow fadeInUp margin15 relative"> 
                    <input type="hidden" name="pid" value="<?php echom($dataitem,'id',1); ?>">
                    <input type="hidden" name="table" value="<?php  echo $masteritem['table'];  ?>">
                    <?php 
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
                    <div class="info_pro_detail">
                        <span><i class="fa fa-upload" aria-hidden="true"></i><?php echom($dataitem,'uploader',1); ?></span>
                        <span><i class="fa fa-check" aria-hidden="true"></i><?php if($dataitem['status']=='full'): ?> Đã hoàn thành <?php else: ?> Đang phát hành <?php endif; ?></span>
                        <?php
            $arrchapter3 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'parent','compare'=>'=','value'=>$dataitem['id'])),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter3 = count($arrchapter3);
     for ($ichapter3=0; $ichapter3 < $countchapter3; $ichapter3++) { $itemchapter3=$arrchapter3[$ichapter3]; ?>
                        <span class="comment"><i class="fa fa-comments" aria-hidden="true"></i> <a class="fb-comments-count" data-href="<?php echo e(base_url().$itemchapter3['slug']); ?>"></a> Bình luận</span>
                        <?php }; ?>
                        <span><i class="fa fa-eye" aria-hidden="true"></i> <?php echom($dataitem,'count',1); ?> Lượt xem</span>
                    </div>
                    <div class="info_pro_detail">
                        <span><i class="fa fa-pencil" aria-hidden="true"></i><?php echom($dataitem,'editor',1); ?></span>
                    </div>
                    <div class="info_pro_detail d-flex align-items-center justify-content-start">
                        <span class="icon_tag"><i class="fa fa-user" aria-hidden="true"></i></span>
                        <span class="tag">
                            <?php echom($dataitem,'author',1); ?>
                        </span>
                    </div>
                    <div class="info_pro_detail ">
                        <div class="cover_item_tag d-flex align-items-center justify-content-start">
                            <span class="icon_tag"><i class="fa fa-bookmark" aria-hidden="true"></i>
                            </span>
                            <span class="tag">
                                <?php
            $arrtag_pro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag_pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'FIND_IN_SET(id,\''.$dataitem['tag_pro'].'\')','compare'=>'>','value'=>'0')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag_pro1 = count($arrtag_pro1);
     for ($itag_pro1=0; $itag_pro1 < $counttag_pro1; $itag_pro1++) { $itemtag_pro1=$arrtag_pro1[$itag_pro1]; ?>
                                <a class="tag_pro" href="tags/<?php echom($itemtag_pro1,'link',1); ?>" title="<?php echom($itemtag_pro1,'name',1); ?>"><?php echom($itemtag_pro1,'name',1); ?></a>
                                <?php }; ?>
                            </span>
                        </div>
                    </div>
                    <?php
            $arrchapter2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'parent','compare'=>'=','value'=>$dataitem['id'])),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter2 = count($arrchapter2);
     for ($ichapter2=0; $ichapter2 < $countchapter2; $ichapter2++) { $itemchapter2=$arrchapter2[$ichapter2]; ?>
                    <a class="view_more_pro v2" href="<?php echom($itemchapter2,'slug',1); ?>">Đọc truyện</a>
                    <?php }; ?>
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
                    <?php echom($dataitem,'content',1); ?>
                </div>
            </div>
            <div class="tab-pane fade active show chapter_ajax" id="profile" role="tabpanel" aria-labelledby="profile-tab">
            </div>
        </div>
        <div class="story_highlight">
            <div class="container">
                <h2 class="title_border"><a href="">Có thể bạn sẽ thích</a></h2>
                <div class="row xs_mar_5 row10">
                    <?php $parent = @$dataitem["parent"]?$dataitem["parent"]:"";
                    $arrRelated = $this->CI->Dindex->getRelateItem($dataitem["id"],$parent,$masteritem["table"],"0,20",[]); ?>
                    <?php  $arrR = array_slice($arrRelated,0,3); ?>
                    <?php foreach($arrR as $itempro2): ?>
                    <div class="col-lg-4 col-sm-6 col-12 xs_pd_5 pad10">
                        <div class="item_story_high row mar_5">
                            <div class="col-lg-6 col-sm-6 col-6 pd_5 relative">
                                <a href="<?php echom($itempro2,'slug',1); ?>" class="c-img">
                                    <?php echo imgv2($itempro2,'#w#img','pro_204x0',false) ; ?>                                </a>
                                <?php if($itempro2['status']=='edit'): ?>
                                <div class="edit">
                                    <img src="theme/frontend/images/edit.png" title="" alt="" class="img-fluid smooth">
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-lg-6 col-sm-6 col-6 pd_5 relative">
                                <div class="text">
                                    <?php  
                                       $tag = explode(',',$itempro2['tag_pro']);
                                       $id_tag = $tag[0];
                                        ?>
                                       <h3 class="name"><a href="<?php echom($itempro2,'slug',1); ?>" title="<?php echom($itempro2,'name',1); ?>"><?php echom($itempro2,'name',1); ?></a></h3>
                                       <span class="type">    <?php
            $arrtag2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'tag',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'=','value'=>$id_tag)),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $counttag2 = count($arrtag2);
     for ($itag2=0; $itag2 < $counttag2; $itag2++) { $itemtag2=$arrtag2[$itag2]; ?>
                                        <?php echom($itemtag2,'name',1); ?>
                                        <?php }; ?>
                                    </span>
                                    <div class="star-rating on line wow fadeInUp margin15 relative"> 
                                        <div class="star-base ">
                                            <div class="star-rate" style="width: <?php echo  $itempro2['score']*16  ?>px;"></div> 
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
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('modal_confirm', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php $__env->stopSection(); ?>
    <?php $__env->startSection('js'); ?>
    <script type="text/javascript" src="theme/frontend/js/offline.js" defer></script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>