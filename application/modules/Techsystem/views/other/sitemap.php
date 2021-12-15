<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Admin"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/viewSitemap"><?php echo alang("SITEMAP") ?></a></li>
    </ul>
</div>
<div id="cph_Main_ContentPane">
   <div class="widget row margin0">
      <div class="widget-title">
       
         <h4>
            <i class="icon-qrcode"></i>&nbsp; <?php echo alang("SITEMAP") ?>
         </h4>
         <div class="ui-widget-content ui-corner-top ui-corner-bottom">
            <div id="toolbox">
               <div style="float:right;" class="toolbox-content">
                  <table class="toolbar">
                     <tbody>
                        <tr>
                          <td align="center">
                                    <a onclick="createSitemap();return false;" id="" title="Trợ giúp" class="toolbar btn btn-info" ><i class="icon-refresh"></i>&nbsp;
                                    <?php echo alang("REFRESH") ?></a>
                                 </td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="clr"></div>
            </div>
         </div>
         <div id="hiddenToolBarScroll" class="scrollBox" style="display:none">
            <h4>
               <i class="icon-qrcode"></i>&nbsp;<?php echo alang("SITEMAP") ?>
            </h4>
            <div class="FloatMenuBar">
               <div class="ui-widget-content ui-corner-top ui-corner-bottom">
                  <div id="toolbox">
                     <div style="float:right;" class="toolbox-content">
                        <table class="toolbar">
                           <tbody>
                              <tr>
                                 <td align="center">
                                    <a onclick="createSitemap();return false;" id="" title="Trợ giúp" class="toolbar btn btn-info" ><i class="icon-refresh"></i>&nbsp;
                                    <?php echo alang("REFRESH") ?></a>
                                 </td>
                              </tr>
                           </tbody>
                        </table>
                     </div>
                     <div class="clr"></div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="widget-body">
        <textarea name="contentdata" style="width:100%;height:400px;"><?php echo $contentdata ?></textarea>
        
     </div>
     <script type="text/javascript">
     function createSitemap(){
      checkReload=true;
      $.ajax({
        url: 'Techsystem/createSitemap',
        type: 'POST',
        data: {param1: 'value1'},
      })
      .done(function(e) {
        window.location.href = '<?php echo base_url() ?>Techsystem/viewSitemap';
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
     }
     </script>
    
      </div>
   </div>
</div>
