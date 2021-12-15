<div style="width: 100%; margin: 0 auto;">
<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
   <ul>
      <li class="Last"><span><i class="icon-home" style="font-size:14px;"></i> <?php echo lang("ADMIN_HOME") ?></span></li>
   </ul>
</div>
<div style="clear: both;"></div>
<div id="cph_Main_ContentPane">
   <div class="">
      <!-- widget Thông báo -->
      <div class="row margin0">
         <div class="col-sm-6 col-xs-12" style="padding-left: 0px;">
            <?php 
            $defaultShow = true;
            $resultHook = $this->hooks->call_hook(['tech5s_admin_widget_notify']);

            if($resultHook == -1){
               $defaultShow = false;
            }
            ?>
            <?php if($defaultShow): ?>
            <div class="widget">
               <div class="widget-title row margin0">
                  <h4>
                     <i class="icon-bullhorn"></i>&nbsp;<?php echo lang("ADMIN_NOTIFY") ?>
                  </h4>
               </div>
               <div class="widget-body" style="height: 193px;">
                  <ul class="hotIconList thongbao">
                     <?php 
                        if(@$tech5s && property_exists($tech5s, 'data')){
                           foreach ($tech5s->data->hot as $key => $value) {
                              echo '<li class="thongbaohot"><i class="icon-fire"></i>';
                              echo '<a target="_blank" href="'.$tech5s->base_url.$value->slug.'">'.$value->name.'</a>';
                              echo '</li>';
                           }
                        }
                         ?>
                  </ul>
               </div>
            </div>
            <?php endif; ?>
         </div>
         <!-- widget hỗ trợ khách hàng -->
         <div class="col-sm-6 col-xs-12"  style="padding-right: 0px;">
            <?php 
            $defaultShow = true;
            $resultHook = $this->hooks->call_hook(['tech5s_admin_widget_help']);
            if($resultHook == -1){
               $defaultShow = false;
            }
            ?>
            <?php if($defaultShow): ?>
            <div class="widget">
               <div class="widget-title row margin0">
                  <h4>
                     <i class="icon-bullhorn"></i>&nbsp;<?php echo lang("ADMIN_HELP") ?>
                  </h4>
               </div>
               <div class="widget-body inlineblock heightSupport">
                  <div class="supportInfoNew support-info homecontainer row margin0">
                     <div class="icon iconFullSize">
                        <a href="" rel="nofollow" target="_blank" title="Yêu cầu hỗ trợ">
                        <img src="theme/admin/static/support-icon_03.png" alt="support icon">
                        </a>
                     </div>
                     <div class="info infoMarginLeft" style="color:#606060;">
                        <div class="f14">
                           <h4 style="font-size:16px;font-weight:bold;margin-top:16px;"><?php echo lang("ADMIN_NEED_HELP") ?></h4>
                        </div>
                        <div class="f14">
                           <?php echo lang("ADMIN_CREATE_HELP") ?> <a href="mailto:kythuat.tech5s@gmail.com" rel="nofollow" target="_blank" title="Yêu cầu hỗ trợ"><?php echo lang("ADMIN_HERE") ?></a>
                        </div>
                        <div class="f14" style="margin-bottom:-5px;">
                           <?php echo lang("ADMIN_SUPPORT_CENTER") ?> <?php echo @$tech5s&& @$tech5s->name?$tech5s->name:"" ?> <?php echo lang("ADMIN_SUPPORT_SOON") ?> &nbsp;
                        </div>
                     </div>
                     <div class="clear">
                     </div>
                  </div>
               </div>
            </div>
            <?php endif; ?>
         </div>
      </div>
      <!-- widget báo cáo -->
      <div class="row margin0">
         <!-- Thống kê đơn hàng -->
         <?php 
         $defaultShow = true;
         $resultHook = $this->hooks->call_hook(['tech5s_admin_widget_statis']);
         if($resultHook == -1){
            $defaultShow = false;
         }
         ?>
         <?php if($defaultShow): ?>
         <div class="widget ">
            <div class="widget-body">
               <div class="tabs tabbable tabbable-custom row margin0">
                  <div class="statistic col-md-12 col-xs-12">
                     <!-- Khách hàng -->
                     <?php $color =['purple','turquoise','red','green','gray','blue'];
                        $icon = ['user','tags','shopping-cart','comments-alt','sitemap','file'];
                         $arr = $this->Admindao-> getDataInTable("","nuy_table", array(array('key'=>'dashboard','compare'=>'=','value'=>1),array('key'=>'act','compare'=>'=','value'=>1)),6,0, "");
                           $count = count($arr);
                           for ($i=0; $i < $count; $i++) { 
                              if(!$this->Admindao->checkPermisstionModule(array("name"=>$arr[$i]['map_table']))) continue;
                              ?>
                     <div class="col-sm-2 col-xs-12 responsive" data-desktop="span2" data-tablet="span3">
                        <div class="circle-wrap">
                           <a href="Techsystem/view/<?php echo $arr[$i]['name'] ?>">
                              <div class="stats-circle <?php echo $color[$i%count($color)] ?>-color">
                                 <i class="icon-<?php echo $icon[$i%count($color)] ?>  "></i>
                              </div>
                              <p>
                                 <strong>
                                 <?php echo  $this->Admindao-> getNumDataInTable("",$arr[$i]['map_table'], "");
                                    ?>
                                 </strong>
                                 <?php echo $arr[$i]['note'] ?>
                              </p>
                           </a>
                        </div>
                     </div>
                     <?php  }
                        ?>
                  </div>
               </div>
            </div>
         </div>
         <?php endif; ?>
         <!-- widget truy cập nhanh -->
         <div class="col-md-6 col-xs-12 NoMarginLeft"  style="padding-left: 0px;">
         <?php 
         $defaultShow = true;
         $resultHook = $this->hooks->call_hook(['tech5s_admin_widget_quick_access']);
         if($resultHook == -1){
            $defaultShow = false;
         }
         ?>
         <?php if($defaultShow): ?>
            <div class="widget">
               <div class="widget-title row margin0">
                  <h4>
                     <i class="icon-bolt"></i>&nbsp;<?php echo lang("ADMIN_QUICK_ACCESS") ?>
                  </h4>
               </div>
               <div class="widget-body maxheight row margin0">
                  <div class="square-state">
                     <div class="row-fluid row">
                        <?php $arr = $this->Admindao-> getDataInTable("","nuy_table", array(array('key'=>'quickaccess','compare'=>'=','value'=>1),array('key'=>'act','compare'=>'=','value'=>1)),6,0, ""); ?>
                        <?php $count = count($arr);
                           for ($i=0; $i < $count; $i++) { 
                              if(!$this->Admindao->checkPermisstionModule(array("name"=>$arr[$i]['map_table']))) continue;
                              ?>
                        <div class="col-sm-3 col-xs-6">
                           <a href="Techsystem/view/<?php echo $arr[$i]['name'] ?>" class="icon-btn ">
                              <i class="icon-<?php echo $icon[$i%count($color)] ?>"></i>
                              <div>
                                 <?php echo $arr[$i]['note'] ?>
                              </div>
                           </a>
                        </div>
                        <?php if($arr[$i]['insert']==1 && $this->Admindao-> checkPermissionAction($arr[$i]['name'],'insert')){
                           ?>
                        <div class="col-sm-3 col-xs-6">
                           <a href="Techsystem/insert/<?php echo $arr[$i]['name'] ?>" class="icon-btn">
                              <i class="icon-plus-sign"></i>
                              <div>
                                 <?php echo alang("ADD") ?> <?php echo $arr[$i]['note'] ?>
                              </div>
                           </a>
                        </div>
                        <?php } ?>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
         <?php endif; ?>
         </div>
         <!-- widget các vấn đề thường gặp, video hướng dẫn nhanh, giúp bạn bán hàng nhiều hơn-->
         <div class="col-md-6 col-xs-12"  style="padding-right: 0px;">
         <?php 
         $defaultShow = true;
         $resultHook = $this->hooks->call_hook(['tech5s_admin_widget_more']);
         if($resultHook == -1){
            $defaultShow = false;
         }
         ?>
         <?php if($defaultShow): ?>
            <div class="widget">
               <script type="text/javascript">
                  $= jQuery.noConflict();
                  $(document).ready(function() {
                  $('.nav-tabs li a').click(function(event) {
                     event.preventDefault();
                     $('div[id^=contentTab]').hide();
                     $('#'+$(this).attr('data-tab')).css({'display':'block'});
                     $('.nav-tabs .active').removeClass('active');
                     $(this).parent().addClass('active');
                  });
                  });
               </script>
               <div class="widget-title titleheight">
                  <ul class="nav nav-tabs">
                     <li class="active"><a data-tab="contentTab_1" id="controlTab_1" href="#" class="NoBorderRadius NoborderTop NoBorderLeft first current titleHeader nowrap">
                        <i class=" icon-warning-sign"></i>&nbsp;Tech5s <?php echo alang("ADVISORY") ?></a>
                     </li>
                     <li><a data-tab="contentTab_3" id="controlTab_3" href="" class="NoBorderRadius NoborderTop titleHeader nowrap">
                        <i class="icon-shopping-cart"></i>&nbsp;<?php echo alang("HELP") ?></a>
                     </li>
                  </ul>
               </div>
               <div class="widget-body maxheight">
                  <div id="contentTab_1" style="display: block;">
                     <div id="rssfeedsSupport" class="rssFeedDisplay" style="overflow-y:scroll">
                        <?php 
                           if(@$tech5s && property_exists($tech5s, 'data')){
                              foreach ($tech5s->data->hotc as $key => $value) {
                                 echo '<div class="item">';
                                 echo '<a class="titlefield" target="_new" href="'.$tech5s->base_url.$value->slug.'" rel="nofollow">'.$value->name.'</a><span class="datefield"></span>';
                                 echo '</div>';
                              }
                           }
                           ?>
                     </div>
                  </div>
                  <div id="contentTab_3" style="display: none;">
                     <div id="rssfeedsBlog" class="rssFeedDisplay" style="overflow-y:scroll">
                        <?php 
                           if(@$tech5s && property_exists($tech5s, 'data')){
                              foreach ($tech5s->data->hotf as $key => $value) {
                                 echo '<div class="item">';
                                 echo '<a class="titlefield" target="_new" href="'.$tech5s->base_url.$value->slug.'" rel="nofollow">'.$value->name.'</a><span class="datefield"></span>';
                                 echo '</div>';
                              }
                           }
                           ?>
                     </div>
                  </div>
               </div>
            </div>
         <?php endif; ?>
         </div>
      </div>
   </div>
</div>