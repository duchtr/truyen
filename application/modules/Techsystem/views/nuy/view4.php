<?php $table = $table[0] ;
$is_server = @$this->session->userdata()?$this->session->userdata('user_from_sv'):0;
?>
<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view/<?php echo $table['name'] ?>"><?php echo alang("note",$table) ?></a></li>
    </ul>
</div>
<div id="cph_Main_ContentPane">
  
   <div class="widget row margin0" style="  padding: 10px;">
    <h4><?php echo alang("LIST_GROUP_USERS") ?></h4>
      <select onchange="getRole()" name="groupuser">
        <?php $arrGroupUser =  $this->Admindao->getAllGroupUser(); ?>
        <?php foreach ($arrGroupUser as $key => $value) {
          ?> 
          <option data-name="<?php echo $value['name'] ?>" value="<?php echo $value['id'] ?>"><?php echo $value['note'] ?></option>
        <?php } ?>
        
      </select>
      <div class="btn fr"><button onclick="saveRole()"><?php echo alang("SAVE") ?></button></div>
      <h4><?php echo alang("LIST_ROLE") ?></h4>
      
      
      <?php $arrGroupModuleBig =  $this->Admindao->getDataInTable("",'nuy_group_module', array(array('key'=>'parent','compare'=>'=','value'=>'0')),"","", ""); ?>
      <?php foreach ($arrGroupModuleBig as $big) {
        echo "<div class='aclr groupmodule'><h4>".__('note',$big)."</h4>";
        ?>
      <?php $arrwhere = $is_server == 1 ?array(array('key'=>'parent','compare'=>'=','value'=>$big['id'])):array(array('key'=>'parent','compare'=>'=','value'=>$big['id']),array('key'=>'act','compare'=>'=','value'=>'1'),array('key'=>'is_server','compare'=>'=','value'=>'0')); ?>
              <?php $arrGroupModule =  $this->Admindao->getDataInTable("",'nuy_group_module', $arrwhere,"","", ""); ?>
      <?php foreach ($arrGroupModule as $key => $value) {
        ?>
        <ul id="module<?php echo $value['id'] ?>" data-value="<?php echo $value['id'] ?>" class="module fl">
          <h5><?php echo __('note',$value) ?></h5>
          <?php $arrModule =  $this->Admindao->getAllModuleAccessByUser($value['id']); 
            foreach ($arrModule as $module) {
          ?> 
          <li><input data-value = "<?php echo $module['code'] ?>" type="checkbox" name="<?php echo $module['name'] ?>"/><a href="javascript:void(0)"><?php echo __("note",$module) ?></a></li>
          <?php } ?>
        </ul>
      <?php } ?>
        <?php echo "</div>";
      } ?>
      <script type="text/javascript">
      $(function() {
        getRole();
      });
      function getRole(){
        var val = $('select[name=groupuser]').val();
        if(val==undefined) return;
        $.ajax({
          url: 'Techsystem/getRole',
          type: 'POST',
          data: {groupuser: val},
        })
        .done(function(e) {
          try{
            var json = $.parseJSON(e);
            if(json instanceof Array){
              for (var j = 0; j < json.length; j++) {
var                item = json[j];
                var arrcheckbox  = $('#module'+item.group_module_id+' input[type=checkbox]');
                for (var i = 0; i < arrcheckbox.length; i++) {
                  var itemcb = arrcheckbox[i];
                  
                    $(itemcb).prop('checked', (parseInt($(itemcb).attr('data-value')) & parseInt(item.role)) >0 );
                  
                };
              };
            }
          }
          catch(ex){
          }
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
        
      }
      function saveRole(){
        var arr = $('ul[id^=module]');
        var send = [];
        for (var j = 0; j < arr.length; j++) {
          var item = arr[j];
          var obj = new Object();
          var arrInput = $(item).find('input[type=checkbox]');
          obj.id = $(item).attr('data-value');
          obj.code = 0;
          for (var i = 0; i < arrInput.length; i++) {
            var input = arrInput[i];
            if($(input).is(":checked")){
              obj.code += parseInt($(input).attr('data-value'));
            }
          };
          send[j] = obj;
        };
       
        $.ajax({
          url: 'Techsystem/do_edit/<?php echo $table['name'] ?>',
          type: 'POST',
          data: { groupuser:$('select[name=groupuser]').val(), role:JSON.stringify(send),'tech5s_controller':'','tech5s_type':<?php echo $table['type'] ?>},
        })
        .done(function(e) {
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
