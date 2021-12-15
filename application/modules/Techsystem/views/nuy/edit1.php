<?php $table = sizeof($table)>0?$table[0]:NULL;
?>
<script type="text/javascript">
    FORM_GLOBAL = '#mainform';
function changeListImage(_that,inputarget){
    var arr = $(_that).find('img');
    var str =new Array();
    for (var i = 0; i < arr.length; i++) {
      var item = arr[i];
      str.push($(item).attr('src'));
  };
  str = JSON.stringify(str);
  $('input[name='+inputarget+']').val(str);
}
function close_window() {
  parent.$.fancybox.close();
} 
function tech5sFileManagerCallback(arrItem,field_id){
    if(arrItem.length==0) return;
    jQuery('#'+field_id).val(arrItem[0]).trigger('change');;
    var nxt = $('#'+field_id).next();
    if($(nxt).prop('tagName').toLowerCase()=='img'){
      $(nxt).attr('src', arrItem[0]);
  }
  else{
      if($(nxt).attr('data-type')=='libimg'){
        $name = $('#'+field_id).attr('data-name');
        for(var i=0;i<arrItem.length;i++){
            var url = arrItem[i];
            $(nxt).append('<div class="boximg">'+
              '<i onclick="var ax=$(this).parent().parent();$(this).parent().remove();changeListImage(ax,$name);" class="icon-remove-circle"></i><img style="height:85px" src="'+url+'"/>'+
              '<i onclick="$(this).parent().moveDown();changeListImage($(this).parent().parent(),$name);" class="icon-arrow-right" style="position:absolute;right:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i>'+
              '<i onclick="$(this).parent().moveUp();changeListImage($(this).parent().parent(),$name);" class="icon-arrow-left" style="position:absolute;left:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i></div>');
        }
        changeListImage(nxt,$name);
    }
}
}
function hungvtApplyCallbackFile(arrItem,field_id){
    if(arrItem.length==0) return;
    var nxt = $('#'+field_id).next();
    if($(nxt).prop('tagName').toLowerCase()=='img'){
      var item = arrItem[0];
      $('#'+field_id).val(JSON.stringify(item)).trigger('change');
      $(nxt).attr('src', item.path+item.file_name);
  }
  else if($(nxt).prop('tagName').toLowerCase()=='input'){
      var item = arrItem[0];
      $('#'+field_id).val(JSON.stringify(item)).trigger('change');
      $(nxt).val(item.path+item.file_name);
  }
  else{
      if($(nxt).attr('data-type')=='libimgv2'){
        $name = $('#'+field_id).attr('data-name');
        for(var i=0;i<arrItem.length;i++){
            var item = arrItem[i];
            var url = item.path+item.file_name;
            $(nxt).append('<div class="boximgv2 boximg">'+
              "<i onclick='var ax=$(this).parent().parent();$(this).parent().remove();changeListImageV2(ax,$name);' class='icon-remove-circle'></i><img data-file='"+JSON.stringify(item)+"' style='height:85px' src='"+url+"'/>"+
              '<i onclick="$(this).parent().moveDown();changeListImageV2($(this).parent().parent(),$name);" class="icon-arrow-right" style="position:absolute;right:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i>'+
              '<i onclick="$(this).parent().moveUp();changeListImageV2($(this).parent().parent(),$name);" class="icon-arrow-left" style="position:absolute;left:-15px;top:50%;    color: #810D0D;font-size: 30px;transform: translateY(-50%);"></i></div>');
        }
        changeListImageV2(nxt,$name);
    }
}
}
function changeListImageV2(_that,inputarget){
    var arr = $(_that).find('img');
    var str =new Array();
    for (var i = 0; i < arr.length; i++) {
      var item = arr[i];
      var tmp = JSON.parse($(item).attr('data-file'));
      str.push(tmp);
  };
  str = JSON.stringify(str);
  $('input[name='+inputarget+']').val(str);
}
$(function() {
    $('.boximg').on('click', 'i', function(event) {
      var _that = $(this).parent().parent();
      var name = $(_that).parent().find('input[data-name]').attr('data-name');
      changeListImage(_that,name);
  });
    $('.boximgv2').on('click', 'i', function(event) {
      var _that = $(this).parent().parent();
      var name = $(_that).parent().find('input[data-name]').attr('data-name');
      changeListImageV2(_that,name);
  });
    const width = 0.9*$(window).width();
    $('.iframe-btn').fancybox({ 
        'width'   : width,
        'height'  : 600,
        'type'    : 'iframe',
        'autoScale'     : false
    });
    $('.ui-tabs-nav li').click(function(event) {
      var div = $('#'+$(this).attr('aria-controls')+'');
      $('.ui-tabs-nav li[role=tab]').removeClass('ui-state-active');
      $(this).addClass('ui-state-active');
      $('div[role=tabdiv]').css({'display':'none'});
      $(div).css({
          display: 'block'
      });

  });
});
</script>
<?php 
$data = isset($data)?$data:[];
$resultHook = $this->hooks->call_hook(['tech5s_edit1_insert_top',"table"=>$table,"data"=>$data]);
if(!is_bool($resultHook)){
 extract($resultHook);
}
?>
<div id="main-content">
 <div class="container-fluid">
    <div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
        <ul>
            <li class="home"><a href="Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
            <li class="SecondLast"><a href="<?php echo base_url(''); ?>Techsystem/view/<?php echo $table['name'] ?>"><?php echo __('note',$table) ?></a></li>
            <li class="Last"><span><?php echo (@$type_title?$type_title:$type)." ".__('note',$table) ?></span></li>
        </ul>
    </div>
    <div style="clear: both;"></div>
    <div id="cph_Main_ContentPane">
       <div class="widget">
        <div class="widget-title">
         <h4><i class="icon-list-alt"></i>&nbsp;<?php echo (@$type_title?$type_title:$type) ?><?php if(@$table) echo @$table['note']?__('note',$table):$table['name'] ?></h4>
         <div class="ui-widget-content ui-corner-top ui-corner-bottom">
          <div id="toolbox">
           <div style="float:right;" class="toolbox-content">
            <table class="toolbar">
             <tbody>
              <tr>
                <?php 
                    $resultHook = $this->hooks->call_hook(['tech5s_edit1_base_button',"table"=>$table,"data"=>$data]);
                    if(!is_bool($resultHook)){
                     extract($resultHook);
                    }
                ?>
                 <td align="center">
                    <a id="cph_Main_ctl00_toolbox_rptAction_lbtAction_0" title="<?php echo alang("HELP") ?>" class="toolbar btn btn-info" href="" onclick=""><i class="icon-question-sign"></i>&nbsp;
                    <?php echo alang("HELP") ?></a>
                </td>
                <td align="center">
                    <a id="" onclick="submitForm('#mainform');return false;" title="<?php echo alang("SAVE") ?>" class="toolbar btn btn-info" ><i class="icon-ok"></i>&nbsp;
                    <?php echo alang("SAVE") ?></a>
                </td>
                <td align="center">
                    <a id="" title="<?php echo alang("BACK") ?>" class="toolbar btn btn-info" href="<?php echo isset($_GET['return'])?$_GET[
                    'return']: base_url('').'Techsystem/view/'.$table['name'] ?>"><i class="icon-chevron-left"></i>&nbsp;
                <?php echo alang("BACK") ?></a>
            </td>
        </tr>
    </tbody>
</table>
</div>
<div class="clr"></div>
</div>
</div>
<div id="hiddenToolBarScroll" class="scrollBox" style="display:none;">
  <h4>
   <i class="icon-list-alt"></i>&nbsp;<?php echo (@$type_title?$type_title:$type) ?> <?php if(@$table) echo @$table['note']?$table['note']:$table['name'] ?>
</h4>
<div class="FloatMenuBar">
   <div class="ui-widget-content ui-corner-top ui-corner-bottom">
    <div id="toolbox">
     <div style="float:right;" class="toolbox-content">
      <table class="toolbar">
       <tbody>
        <tr>
        <?php 
            $resultHook = $this->hooks->call_hook(['tech5s_edit1_base_button',"table"=>$table,"data"=>$data]);
            if(!is_bool($resultHook)){
             extract($resultHook);
            }
        ?>
         <td align="center">
          <a onclick="" id="" title="<?php echo alang("HELP") ?>" class="toolbar btn btn-info" href=""><i class="icon-question-sign"></i>&nbsp;
          <?php echo alang("HELP") ?></a>
      </td>
      <td align="center">
          <a id="" onclick="submitForm('#mainform');return false;" title="<?php echo alang("SAVE") ?>" class="toolbar btn btn-info" ><i class="icon-ok"></i>&nbsp;
          <?php echo alang("SAVE") ?></a>
      </td>
      <td align="center">
          <a id="cph_Main_ctl00_toolbox2_rptAction_lbtAction_4" title="<?php echo alang("BACK") ?>" class="toolbar btn btn-info" href="<?php echo base_url(''); ?>Techsystem/view/<?php echo $table['name'] ?>"><i class="icon-chevron-left"></i>&nbsp;
          <?php echo alang("BACK") ?></a>
      </td>
  </tr>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="widget-body">
  <?php 
  if(($type=='edit' && count($data)>0 ) || $type!='edit') { ?>
     <form  name="addform" id="mainform"  action="Techsystem/<?php echo $type=="edit"?"do_edit":"do_insert"; ?>/<?php echo $table['name'] ?>/<?php echo @$this->uri->segment(4)?$this->uri->segment(4):"" ?>" method="post">
      <input type="hidden" name="tech5s_controller" value="<?php echo $table['controller'] ?>">
      <input type="hidden" name="tech5s_type" value="<?php echo $table['type'] ?>">
      <div id="tabs" style="" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
       <ul class="nav nav-tabs ui-tabs-nav " role="tablist">
        <?php 
        for ($i=0;$i<sizeof($regions);$i++) {
            $region = $regions[$i];
            ?>
            <li class="<?php if($i==0) echo 'ui-state-active'; ?>" role="tab" aria-controls="tabs-<?php echo $region['id']; ?>" >
                <span class="ui-tabs-anchor" onclick="return false;" ><?php echo __('name',$region); ?></span>
            </li>
            <?php    }
            ?>
        </ul>
        <?php 
        for ($i=0;$i<sizeof($regions);$i++) {
            $region = $regions[$i];
            ?>

            <div id="tabs-<?php echo $region['id'] ?>" role="tabdiv" style="display: <?php echo  $i==0? 'block':'none' ?>;" >
                <div class="container-fluid padding0 tableedit">
                  <?php 
                  foreach ($lstFields as $field) {
                      if($field['region']!=$region['id']) continue;
                      if($field['act']==0) continue;
                      $typeControlView = strtolower($field['type']);
                      $datasub['field'] = $field;
                      $datasub['table'] = $table;
                      if($type=='edit'||$type=='copy'){
                        $datasub['dataitem'] = $data[0];  
                    }
                    $loadFile = true;
                    $resultHook = $this->hooks->call_hook(['tech5s_edit1_load_sub_view',"table"=>$table,"data"=>$data,"field"=>$field]);
                    if($resultHook === -1){
                        $loadFile = false;
                    }
                    if($loadFile){
                        $needView = getViewAdmin($typeControlView,__FILE__,'view_edit');
                        if(!$needView){
                          $this->load->view('view_edit/base',$datasub);
                        }
                        else{
                          $this->load->view($needView,$datasub);
                        }
                    }
                    
               }
               ?>
           </div>
       </div>
       <?php    }
       ?>
   </div>
</form>
<?php } ?>
</div>
</div>
</div>
</div>
</div>
</div>
<?php 
$resultHook = $this->hooks->call_hook(['tech5s_edit1_insert_bottom',"table"=>$table,"data"=>$data]);
if(!is_bool($resultHook)){
 extract($resultHook);
}
?>