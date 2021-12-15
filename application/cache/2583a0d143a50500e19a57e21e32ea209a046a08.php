<?php $__env->startSection('content'); ?>
<div class="banner relative">
    <?php echo $this->CI->Dindex->getSettingImage('bg_truyen',1,'-1','1'); ?>    <div class="text">
        <div class="container">
            <h2 class="title_pro">
                <?php  $id = 0;  ?>
                <?php
            $arrpro1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'pro',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'id','compare'=>'=','value'=>$dataitem['parent'])),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countpro1 = count($arrpro1);
     for ($ipro1=0; $ipro1 < $countpro1; $ipro1++) { $itempro1=$arrpro1[$ipro1]; ?>
                <a href="<?php echom($itempro1,'slug',1); ?>"><?php echom($itempro1,'name',1); ?></a>
                <?php  $id = $itempro1['id'] ?>
                <?php }; ?>
                
            </h2>
            <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
                    <a itemprop="item" href="https://tech5s.com.vn/"><span itemprop="name">Trang chủ</span></a>
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
<div class="fixed_top_button">
    <div class="container relative">
        <div class="cover_fixed">
            <div class="button_right_function">
                <span class="op_menu toll_tip" data-name="Danh sách chương"><i class="fa fa-bars smooth" aria-hidden="true"></i></span>
                <?php
            $arrchapter2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'>','value'=>$dataitem['id']),array('key'=>'parent','compare'=>'=','value'=>$dataitem['parent'])),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter2 = count($arrchapter2);
     for ($ichapter2=0; $ichapter2 < $countchapter2; $ichapter2++) { $itemchapter2=$arrchapter2[$ichapter2]; ?>
                <span class="next toll_tip" data-name="Chương kế tiếp" >
                    <a href="<?php echom($itemchapter2,'slug',1); ?>"><i class="fa fa-arrow-right smooth" aria-hidden="true"></i></a>
                </span>
                <?php }; ?>
                <?php
            $arrchapter1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'id desc',
                    'where'=>array(array('key'=>'id','compare'=>'<','value'=>$dataitem['id']),array('key'=>'parent','compare'=>'=','value'=>$dataitem['parent'])),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter1 = count($arrchapter1);
     for ($ichapter1=0; $ichapter1 < $countchapter1; $ichapter1++) { $itemchapter1=$arrchapter1[$ichapter1]; ?>
                <span class="back toll_tip" data-name="Chương trước">
                    <a href="<?php echom($itemchapter1,'slug',1); ?>"><i class="fa fa-arrow-left smooth" aria-hidden="true"></i></a>
                </span>
                <?php }; ?>
                <span class="comment toll_tip" data-name="Bình luận"><i class="fa fa-comments smooth" aria-hidden="true"></i></span>
                <span class="up toll_tip" data-name="Lên đầu"><i class="fa fa-chevron-up smooth" aria-hidden="true"></i></span>
            </div>
            <div class="chapter_pro">
                <div class="container">
                    <div class="row">
                        <?php $i=11 ?>
                        <?php
            $arrchapter3 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'id asc',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'parent','compare'=>'=','value'=>$id),array('key'=>'id','compare'=>'>=','value'=>$dataitem['id'])),
                    'limit'=>'0,10',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter3 = count($arrchapter3);
     for ($ichapter3=0; $ichapter3 < $countchapter3; $ichapter3++) { $itemchapter3=$arrchapter3[$ichapter3]; ?>
                        <div class="col-lg-12 order-<?php echo e($i); ?>">
                            <div class="item_chapter_pro_free d-flex justify-content-between align-items-center">
                                <a href="<?php echom($itemchapter3,'slug',1); ?>" title="<?php echom($itemchapter3,'name',1); ?>"><?php echom($itemchapter3,'name',1); ?></a>
                                <span class="free">FREE</span>
                            </div>
                        </div>
                        <?php $i-- ?>
                        <?php }; ?>
                        <?php
            $arrchapter10 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'id desc',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'parent','compare'=>'=','value'=>$id),array('key'=>'id','compare'=>'<','value'=>$dataitem['id'])),
                    'limit'=>'0,10',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter10 = count($arrchapter10);
     for ($ichapter10=0; $ichapter10 < $countchapter10; $ichapter10++) { $itemchapter10=$arrchapter10[$ichapter10]; ?>
                        <div class="col-lg-12 order-12">
                            <div class="item_chapter_pro_free d-flex justify-content-between align-items-center ">
                                <a href="<?php echom($itemchapter10,'slug',1); ?>" title="<?php echom($itemchapter10,'name',1); ?>"><?php echom($itemchapter10,'name',1); ?></a>
                                <span class="free">FREE</span>
                            </div>
                        </div>
                        <?php }; ?>                       
                    </div>
                </div>       
            </div>
        </div>
    </div>
</div>
<div class="story_detail">
    <div class="container relative">
        <div class="title_cover">
            <h1 class="title_story"><?php echom($dataitem,'name',1); ?></h1>
            <a class="view_more ml-3 bg-cuz" href="">Lưu Offline</a>
            <div class="function_button">
                <a href="" class="smooth zoom_text" data-zoom="+"><i class="fa fa-plus smooth" aria-hidden="true"></i></a>
                <a href="" class="smooth zoom_text" data-zoom="-"><i class="fa fa-minus smooth" aria-hidden="true"></i></a>
                <?php
            $arrchapter2 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'chapter',
                    'order'=>'',
                    'where'=>array(array('key'=>'id','compare'=>'>','value'=>$dataitem['id']),array('key'=>'parent','compare'=>'=','value'=>$dataitem['parent'])),
                    'limit'=>'0,1',
                    'pivot'=>[]
                )
            );
         ?><?php $countchapter2 = count($arrchapter2);
     for ($ichapter2=0; $ichapter2 < $countchapter2; $ichapter2++) { $itemchapter2=$arrchapter2[$ichapter2]; ?>
                <a href="<?php echom($itemchapter2,'slug',1); ?>" class="smooth next_chapter">Tiếp <i class="fa fa-arrow-right smooth" aria-hidden="true"></i></a>
                <?php }; ?>
                
            </div>
        </div>
        
        <div class="content_story s-content" data-font='16' style="font-size:'16px';">
            <?php echom($dataitem,'content',1); ?>
        </div>
        <div class="author_editor">
            <div class="text">
                <p><?php echom($dataitem,'style',1); ?></p>
                <h3><?php echom($dataitem,'people',1); ?></h3>    
            </div>
        </div>
        <div class="comment_fb">
            <h2>Bình luận Facebook</h2>
            <div class="fb-comments" data-href="<?php echom($dataitem,'slug',1); ?>" data-width="100%" data-numposts="5"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('index', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>