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
                           <?php echo lang("ADMIN_CREATE_HELP") ?> <a href="19020152@vnu.edu.vn" rel="nofollow" target="_blank" title="Yêu cầu hỗ trợ"><?php echo lang("ADMIN") ?></a>
                        </div>
                        <div class="f14" style="margin-bottom:-5px;">
                           <?php echo lang("ADMIN_SUPPORT_CENTER") ?><?php echo lang("ADMIN_SUPPORT_SOON") ?> &nbsp;
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
   </div>
</div>