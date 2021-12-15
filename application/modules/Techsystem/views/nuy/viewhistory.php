<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Admin"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view/historyAccess"><?php echo alang("VISIT_HISTORIES") ?></a></li>
    </ul>
</div>
<div id="cph_Main_ContentPane">
   <div class="widget row margin0">
      <div class="widget-title">
       
         <h4>
            <i class="icon-qrcode"></i>&nbsp;<?php echo alang("VISIT_HISTORIES") ?>
         </h4>
         <div class="ui-widget-content ui-corner-top ui-corner-bottom">
            <div id="toolbox">
               <div style="float:right;" class="toolbox-content">
                  <table class="toolbar">
                     <tbody>
                        <tr>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="clr"></div>
            </div>
         </div>
         <div id="hiddenToolBarScroll" class="scrollBox" style="display:none">
            <h4>
               <i class="icon-qrcode"></i>&nbsp;<?php if(@$table) echo __('note',$table); ?>
            </h4>
            <div class="FloatMenuBar">
               <div class="ui-widget-content ui-corner-top ui-corner-bottom">
                  <div id="toolbox">
                     <div style="float:right;" class="toolbox-content">
                        <table class="toolbar">
                           <tbody>
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
        
         <div class="row margin0">
            <div class="col-md-3 col-xs-12 padding0">
               <div style="">
                  Tổng số : <span style="color: #A52A2A;"><?php echo $total_rows; ?> <?php echo alang("RECORDS") ?></span>
               </div>
            </div>
            <div class="col-md-9 col-xs-12 padding0">
               <div class="pagination pagination-small pagination-right">
              <?php
                echo $this->pagination->create_links();
                ?>
               </div>
               <div class="clr"></div>
            </div>
         </div>
         <div class="row margin0">
       <div id="no-more-tables">
            <table class="col-md-12 table-bordered padding0 table-striped table-condensed cf">
            <thead class="cf">
              <tr>
                    <th scope="col">#</th>
                     <th align="left" scope="col">
                        <input id="" type="checkbox" name="cball" dt-delete="" class="cball" onclick="">
                     </th>
                     <?php 
                     $titles = array(1=>array('type'=>'TEXT','note'=>'Tên','name'=>'name','view'=>1),
                      2=>array('type'=>'TEXT','note'=>'Ghi chú','name'=>'note','view'=>1),
                      3=>array('type'=>'TEXT','note'=>'Thời gian','name'=>'create_time','view'=>1),
                      4=>array('type'=>'TEXT','note'=>'IP','name'=>'ip','view'=>1)
                      );
                     foreach ($titles as $key => $item) {
                       if($item['type']!='PRIMARYKEY' && $item['view']==1){
                         echo "<th>".$item['note']."</th>";
                       }
                     }
                     ?>
              </tr>
            </thead>
            <tbody>
                    <?php
                     $i =0; foreach ($lstData as $key => $item) {
                      $primarykey = array();
                      $i++;
                      ?>
                      <tr> 
                      <td><?php echo $i; ?></td>   
                        <td><input id="" type="checkbox" class="cbone" dt-value="<?php echo $item['id'] ?>" name="cb<?php echo $item['id'] ?>" onclick=""></td>
                        <?php 
                        foreach ($titles as $val) {
                          
                          ?>
                          <?php
                          $datai['value'] = $item[$val['name']];
                          $datai['currentvalue'] = $val;
                          if($val['name']=='create_time'){
                            $datai['value'] = date('d/m/Y H:i:s',$item[$val['name']]);
                          }?>
                          <td data-title="<?php echo __('note',$datai['currentvalue']) ?>">
                          <?php echo strip_tags($datai['value']); ?>
                        </td>
                        <?php }
                        ?>
                      </tr>
                    <?php } ?>
                      
            </tbody>
          </table>
        </div>
          
         </div>
         <div class="pagination pagination-small pagination-right">
          
            <?php
                echo $this->pagination->create_links();
                ?>
            <div class="clr"></div>
         </div>
      </div>
   </div>
</div>
