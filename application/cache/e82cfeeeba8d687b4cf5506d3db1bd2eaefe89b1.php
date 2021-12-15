<header>
    <?php $pp = $this->CI->uri->segment(1,0); ?>
    
    <div class="top_header md_hidden">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-2">
                    <!-- <audio id="player" autoplay loop class="title_top_header" >
                        <source src="<?php echo e(base_url()); ?>uploads/demo/nhac-nen/<?php echo $this->CI->Dindex->getSettings('MUSIC_HOME'); ?>" type="audio/mp3">
                            Nhạc nền không hỗ trợ trình duyệt này
                        </audio>
                        <i class="fa fa-volume-up" aria-hidden="true"></i>
                        <div class="wed_music_content">
                            <div class="wed_music_container" id="music">
                              <i class="ti ti-music"></i>
                          </div>
                          <div class="wed_music_container2" id="music2">
                              <i class="ti ti-music"></i>
                          </div>
                      </div> -->
                  </div>
                  <div class="col-lg-10">
                    <marquee  onmouseover="this.stop();" onmouseout="this.start();">
                        <?php
            $arrsay_high1 = $this->CI->Dindex->getDataDetail(
                array(
                    'input'=>"*",
                    'table'=>'say_high',
                    'order'=>'',
                    'where'=>array(array('key'=>'act','compare'=>'=','value'=>'1')),
                    'limit'=>'',
                    'pivot'=>[]
                )
            );
         ?><?php $countsay_high1 = count($arrsay_high1);
     for ($isay_high1=0; $isay_high1 < $countsay_high1; $isay_high1++) { $itemsay_high1=$arrsay_high1[$isay_high1]; ?>
                        <span class="say_high"><?php echom($itemsay_high1,'name',1); ?> <a href="<?php echom($itemsay_high1,'link',1); ?>"> << <?php echom($itemsay_high1,'story',1); ?> >> </a></span>
                        <?php }; ?>
                    </marquee>
                </div>
                
            </div>
        </div>
    </div>
    <div class="mid_header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-6 col-sm-5 col-md-5 col-lg-3 mb-lg-0">
                    <a class="logo" href="">
                        <?php echo $this->CI->Dindex->getSettingImage('logo',1,'-1','1'); ?>                    </a>
                </div>
                <div class="col-6 col-sm-7 col-md-7 col-lg-2 px-lg-0 d-lg-block d-flex align-items-center position-static justify-content-between seach_qaa">
                     <form action="tim-kiem" method="get" class="align-items-center justify-content-between frm-search">
                        <input type="text" name="q" class="" placeholder="Tìm kiếm tên truyện..." value="<?php if(isset($keyword)): ?><?php echo e($keyword); ?><?php endif; ?>">
                        <button class="d-block">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </form>
                    <span class="show-search d-lg-none d-block  btm_search_qa">
                        <i class="fa fa-search" aria-hidden="true"></i>
                    </span>
                    <button class="btn-menu ml-3" type="button"><i class="fa fa-bars" ></i></button> 
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-7">
                    <nav class="main-nav d-flex justify-content-end">
                        <?php $arr = $this->CI->Dindex->recursiveTable("*","menu","parent","id","0",array(array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'group_id','compare'=>'=','value'=>'1'),)); ?><?php printMenu($arr,array()); ?>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>