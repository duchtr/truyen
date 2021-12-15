<?php
  $table = $table[0];
?>
<script type="text/javascript">
$= jQuery.noConflict();
  $(document).ready(function() {
    $('.cbone').change(function(event) {
      
      $('.cball').prop('checked', false);
      if($(this).is(':checked')){
        var tr = $(this).parent().parent();
        tr.css({ 'background-color':'rgb(8, 111, 166)','color':'#fff'})
        tr.find('a').css({'color':'#fff'});
      }
      else{
        var tr = $(this).parent().parent();
        tr.css({ 'background-color':'','color':''})
        tr.find('a').css({'color':''});
      }
      getDataDeleteAll();
    });
    $('.cball').change(function(event) {
      
      if($(this).is(':checked')){
        $('td input.cbone').prop('checked', true);
        var tr = $('td input.cbone').parent().parent();
        tr.css({ 'background-color':'rgb(8, 111, 166)','color':'#fff'})
        tr.find('a').css({'color':'#fff'});
      }
      else{
         $('td input.cbone').prop('checked', false);
         var tr = $('td input.cbone').parent().parent();
        tr.css({ 'background-color':'','color':''})
        tr.find('a').css({'color':''});
         
      }
      getDataDeleteAll();
    });
    function getDataDeleteAll(){
      var arr = $('td input.cbone:checked');
      var str = "";
      for (var i = 0; i < arr.length; i++) {
        var item = arr[i];
        str += $(item).attr('dt-value');
        if(i<arr.length-1){
          str+=",";
        }
      }
      $('.cball').attr('dt-delete', str);
    }
    function updateOneField(_this){
      var datapri = $(_this).attr('data-primary');
      var uName = $(_this).attr('name');
      var uValue = $(_this).val();
      if($(_this).attr('type')=='checkbox'){
        uValue = $(_this).is(':checked')?1:0;
      }
      checkReload=false;
      $.ajax({type:'POST', 
      url: "Techsystem/updateOneField/<?php echo $table['name'] ?>", data:{where:datapri,name:uName,newValue:uValue}, global:true,
      success: function(response) {
      }});
    }
      var currentEditableInput = undefined;
      $('input[type=text].editable').dblclick(function(event) {
        $(this).prop('readonly',false);
        $(this).css('background','#fff');
        currentEditableInput = this;
      });  
      $(document).click(function(e){
         if( currentEditableInput !=undefined &&  !$(currentEditableInput).is( e.target ) ) {
            $(currentEditableInput).prop('readonly',true);
            $(this).css('background','');
            updateOneField(currentEditableInput);
            currentEditableInput = undefined;
         } 
      }); 
      $('input[type=checkbox].editable').click(function(event) {
          updateOneField(event.target);
      }); 
  });
    function deleteAll(){
       var datawhere = $('input.cball').attr('dt-delete');
       if(datawhere.length>0){
          bootbox.confirm("<?php echo sprintf(alang('WANT_DELETE'),__('note',$table)) ?>", function(ret){
            if(!ret)return;
            checkReload=false;
            
              $.ajax({
                      url: 'Techsystem/deleteAll',
                      type: 'POST',
                      data: {ids:datawhere,table:"<?php echo $table['name']; ?>"},
                    })
                  .done(function(e) {
                    try{
                      var json = $.parseJSON(e);
                      if(json.code==SUCCESS){
                        window.location.href="Techsystem/view/<?php echo $table['name']; ?>";
                      }
                    }
                    catch(e){
                    }
                  })
                  .fail(function() {
                    console.log("error");
                  })
                  .always(function() {
                    console.log("complete");
                  });
          });
        }
        else{
          bootbox.alert("<?php echo sprintf(alang('DONT_CHOOSE'),__('note',$table)) ?>");
        }
    }
    function deleteItem(_this){
       bootbox.confirm("<?php echo sprintf(alang('WANT_DELETE'),__('note',$table)) ?>", function(ret){
            if(!ret)return;
        checkReload=false;
        var datawhere =$(_this).attr('dt-delete');
        $.ajax({type:'POST', 
          url: "Techsystem/delete/<?php echo $table['name'] ?>", data:{where:datawhere},
          success: function(response) {
              try{
                var jsdata = $.parseJSON(response);
                if(jsdata.code==SUCCESS){
                  $(_this).parent().parent().hide();
                }
              }
              catch(e){
                console.log(e);
              }
          }});
      });
    }
</script>
<style>
#dragandrophandler
{
border: 2px dashed #92AAB0;
width: 650px;
height: 200px;
color: #92AAB0;
text-align: center;
vertical-align: middle;
padding: 10px 0px 10px 10px;
font-size:200%;
display: table-cell;
}
.progressBar {
  height: 22px;
  border: 1px solid #ddd;
  border-radius: 5px; 
  overflow: hidden;
  display:inline-block;
  vertical-align:top;
}
.progressBar div {
  height: 100%;
  color: #fff;
  text-align: right;
  line-height: 22px; /* same as #progressBar height if we want text middle aligned */
  width: 0;
  background-color: #0ba1b5; border-radius: 3px; 
}
.statusbar
{
  border-top:1px solid #A9CCD1;
  min-height:25px;
}
.statusbar:nth-child(odd){
    background:#EBEFF0;
}
.filename
{
display:inline-block;
vertical-align:top;
width:250px;
}
.filesize
{
display:inline-block;
vertical-align:top;
color:#30693D;
width:100px;
margin-left:10px;
margin-right:5px;
}
.abort{
  background-color:#A8352F;
  -moz-border-radius:4px;
  -webkit-border-radius:4px;
  border-radius:4px;display:inline-block;
  color:#fff;
  font-family:arial;font-size:13px;font-weight:normal;
  padding:4px 15px;
  cursor:pointer;
  vertical-align:top
  }
  .dragzoneinfo input,.dragzoneinfo select,.dragzoneinfo textarea{
    width: 100%;
    margin:3px 0px;
  }
  .detailquick{
    text-align: left;
  }
</style>
<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view/<?php echo $table['name'] ?>"><?php echo __('note',$table) ?></a></li>
    </ul>
</div>
<div id="cph_Main_ContentPane">
<?php if($table['quickpost']==1){ 
$this->load->view('other/quickpost');
}
   ?>
   <div class="widget row margin0">
      <div class="widget-title">
       
         <h4>
            <i class="icon-qrcode"></i>&nbsp; <?php echo __('note',$table) ?>
         </h4>
         <div class="ui-widget-content ui-corner-top ui-corner-bottom">
            <div id="toolbox">
               <div style="float:right;" class="toolbox-content">
                  <table class="toolbar">
                     <tbody>
                        <tr>
                        <td align="center">
                            <?php if($table['quickpost']==1){ ?>
                              <button type="button" class="btn btn-info" data-toggle="modal" data-target="#dragzone"><i class="icon-upload-alt"></i> <?php echo alang("QUICK_POST") ?></button>
                              <?php } ?>
                           </td>
                          <?php if($table['help']==1){ ?>
                           <td align="center">
                              <a onclick="" id="" onclick="" title="<?php echo alang("HELP") ?>" class="toolbar btn btn-info" href=""><i class="icon-question-sign"></i>&nbsp;
                              <?php echo alang("HELP") ?></a>
                           </td>
                           <?php } ?>
                           <?php if($table['insert']==1) { ?>
                           <td align="center">
                              <a id="" title="<?php echo alang("INSERT") ?>" class="toolbar btn btn-info" href="<?php echo base_url().'Techsystem/'.'insert/'.$table['name']?>"><i class="icon-plus"></i>&nbsp;
                              <?php echo alang("INSERT") ?></a>
                           </td>
                           <?php } ?>
                           <?php if($table['delete']==1) { ?>
                           <td align="center">
                              <a onclick="deleteAll();return false;"  id="" href="javascript:deleteAll();" title="<?php echo alang("DELETE") ?>" class="deleteall toolbar btn btn-info"><i class="icon-trash"></i>&nbsp;
                              <?php echo alang("DELETE") ?></a>
                           </td>
                          <?php } ?>
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
                              <tr>
                                <?php if($table['help']==1){ ?>
                                 <td align="center">
                                    <a onclick="" id="" title="<?php echo alang("HELP") ?>" class="toolbar btn btn-info" href=""><i class="icon-question-sign"></i>&nbsp;
                                    <?php echo alang("HELP") ?></a>
                                 </td>
                                 <?php } ?>
                                 <?php if($table['insert']==1){ ?>
                                 <td align="center">
                                    <a id="" title="<?php echo alang("INSERT") ?>" class="toolbar btn btn-info" href="<?php echo base_url().'Techsystem/'.'insert/'.$table['name']?>"><i class="icon-plus"></i>&nbsp;
                                    <?php echo alang("INSERT") ?></a>
                                 </td>
                                 <?php } ?>
                                 <?php if($table['delete']==1){ ?>
                                 <td align="center">
                                    <a onclick="deleteAll();return false;"  href="javascript:deleteAll();"  id="" title="<?php echo alang("DELETE") ?>" class="deleteall toolbar btn btn-info" href=""><i class="icon-trash"></i>&nbsp;
                                    <?php echo alang("DELETE") ?></a>
                                 </td>
                                 <?php } ?>
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
        <?php if($table['search']==1){ ?>
         <div class="row-fluid">
            <div class="">
              
              <form action="Techsystem/search/<?php echo $table['name'] ?>" class="" method="post">
                <div class="row margin0">
                  <?php 
                    foreach ($lstSimpleSearchable as $value) {
                      $typeControlView = strtolower($value['type']);
                      echo "<input type='hidden' value ='".$typeControlView."' name = 'nuytype_".$value['name']."'/>";
                      $datasub['value']=$value;
                      $needView = getViewAdmin($typeControlView,__FILE__,'view_search');
                      if(!$needView){
                        $this->load->view('view_search/base',$datasub);
                      }
                      else{
                        $this->load->view($needView,$datasub);
                      }
                    }
                    
                    ?>
                  </div>
                
                              
                <div class="row margin0">
                  <label for=""><?php echo alang("ORDER") ?></label>
                  <select name="order_by" id="">
                    <?php 
                         foreach ($titles as $key => $item) {
                           echo "<option value='".$item['name']."'>".$item['note']."</option>";
                     }
                     ?>
                  </select>
                  <label for=""><?php echo alang("BY") ?></label>
                  <select name="ord" id="">
                    <option value="ASC">A->Z</option>
                    <option value="DESC">Z->A</option>
                  </select>
                </div>
                <div class="controlsearch row margin0">
                 <button type="submit" name="submit" value="<?php echo alang("FILTER") ?>" id="" class="btn"><i class="icon-filter"></i> <?php echo alang("FILTER") ?></button>
                 <button type="reset" name="submit" onclick="window.location.href='Techsystem/view/<?php echo $table['name'] ?>'" id="" class="btn"><i class="icon-refresh"></i><?php echo alang("REFRESH") ?></button>
                 <button type="button" name="submit" data-toggle="modal" data-target="#advanceSearch" id="" class="btn"><i class="icon-search"></i><?php echo alang("ADVANCE_SEARCH") ?></button>
                </div> 
             </form>
              
            </div>
            
         </div>
        <div id="advanceSearch" class="modal fade " role="dialog">
          <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
              <form action="">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo alang("ADVANCE_SEARCH") ?></h4>
              </div>
              <div class="modal-body">
                <div class="row margin0">
                   <?php 
                    foreach ($lstSearchable as $value) {
                      
                      $typeControlView = strtolower($value['type']);
                      $datasub['value']=$value;
                      $datasub['is_dialog']=1;
                      $needView = getViewAdmin($typeControlView,__FILE__,'view_search');
                      if(!$needView){
                        $this->load->view('view_search/base',$datasub);
                      }
                      else{
                        $this->load->view($needView,$datasub);
                      }
                    }
                    
                    ?>
                </div>
                <div class="row margin0">
                  <label for="">Sắp xếp</label>
                  <select name="order_by" id="">
                    <?php 
                         foreach ($titles as $key => $item) {
                           echo "<option value='".$item['name']."'>".$item['note']."</option>";
                     }
                     ?>
                  </select>
                  <label for="">Thứ tự</label>
                  <select name="ord" id="">
                    <option value="ASC">A->Z</option>
                    <option value="DESC">Z->A</option>
                  </select>
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-default"><?php echo alang("SEARCH") ?></button>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo alang("CLOSE") ?></button>
              </div>
              </form>
            </div>
          </div>
        </div>
        <?php } ?>
         <div class="row margin0">
            <div class="col-md-3 col-xs-12 padding0">
               <div style="">
                  <?php echo alang("SUM") ?> : <span style="color: #A52A2A;"><?php echo $total_rows; ?> <?php echo alang("RECORDS") ?></span>
               </div>
            </div>
            <div class="col-md-9 col-xs-12 padding0">
               <div class="pagination pagination-small pagination-right">
              <?php
                if($table['name'] != 'menu'){
                  echo $this->pagination->create_links();
                }
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
                     foreach ($titles as $key => $item) {
                       if($item['type']!='PRIMARYKEY' && $item['view']==1){
                         echo "<th>".$item['note']."</th>";
                       }
                     }
                     ?>
                     <th style="min-width:70px" scope="col">
                        <label for=""><?php echo alang("FUNCTION") ?></label>
                     </th>
              </tr>
            </thead>
            <tbody>
              <?php 
               $lstData = $this->Admindao->recursiveTable("*",$table['name'],'parent','id',0,"");
               printDataView6(-1,$this,$lstData,$titles,$table);
              
               ?>
                      
            </tbody>
          </table>
        </div>
          
         </div>
         <div class="pagination pagination-small pagination-right">
          
            <?php
                if($table['name'] != 'menu'){
                  echo $this->pagination->create_links();
                }
                ?>
            <div class="clr"></div>
         </div>
      </div>
   </div>
</div>
<script type="text/javascript">
  $(function() {
    var arr = $('#no-more-tables > table > tbody >tr[dtpad] > td > input[name=name]');
    for (var i = 0; i < arr.length; i++) {
      var item = $(arr[i]).parent();
      var lv = item.parent().attr('dtpad');
      item.css({'padding-left':(lv*15)+'px'});
       $(arr[i]).css({'padding-left': '5px','border-left': '1px solid'});
    };
  });
</script>
