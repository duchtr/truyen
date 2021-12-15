<?php
  $table = $table[0];
?>
<link rel="stylesheet" type="text/css" href="theme/admin/static/ui/jquery-ui.min.css">
<script type="text/javascript" src="theme/admin/static/ui/jquery-ui.min.js"></script>
<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view/<?php echo $table['name'] ?>"><?php echo __('note',$table) ?></a></li>
    </ul>
</div>
<div id="cph_Main_ContentPane">
  
   <div class="widget row margin0" style="  padding: 10px;">
      <div class="col-md-9 col-xs-12">
        <?php $groupMenu =$this->Admindao->getDataInTable("",'group_menu', "","","", ""); 
        foreach ($groupMenu as $key => $value) {
          ?>
          <div class="menuregion boxmenu">
            <div class="title"><h3><?php echo  $value['name'] ?></h3><i data-group="<?php echo $value['id'] ?>" onclick ="getDataToSave(this)" class="save icon-save"></i> <i class="arrow icon-arrow-down"></i></div>
            
            <ul class="droppable rootdroppable" style="  min-height: 50vh;">
              <?php 
                 $arrRecur = $this->Admindao->recursiveTable("*",'menu','parent','id',0,array('group_id'=>$value['id']),"ord"); 
                 printMenuExistDb($titles,-1,$arrRecur);
              ?>
            </ul>
          </div>
        <?php } 
        ?>
      </div>
      <div class="col-md-3 col-xs-12">
          <?php 
          $arrTablesCan =$this->Admindao->getDataInTable("",'nuy_table', array(array('key'=>'showinmenu','compare'=>'=','value'=>'1'),array('key'=>'act','compare'=>'=','value'=>'1')),"","", "");
          ?>
          <div class="boxmenu">
            <div class="title"><h3>Link mới</h3> <i class="arrow icon-arrow-down"></i></div>
            <ul class="table_null padding0 scrollbar scrollchoosemenu">
                <?php 
                $arrRecur = $this->Admindao->recursiveTable("*","table_null",'parent','id',-1,""); 
                
printRecursiveMenuAdmin($titles,-1,$arrRecur,array());
                ?>
            </ul>
          </div>
          <?php
          foreach ($arrTablesCan  as $tc) {
              ?>
              <div class="boxmenu">
                <div class="title"><h3><?php echo $tc['note'] ?></h3> <input style="display:none;" type="text" class="inputlocalsearch" name="<?php echo $tc['name'] ?>"/> <i class="localsearch icon-search"></i> <i class="arrow icon-arrow-down"></i></div>
                <ul class="<?php echo $tc['name'] ?> padding0 scrollbar scrollchoosemenu">
                    <?php 
                    $idparent = (array_key_exists('table_parent', $tc) && !isNull($tc['table_parent'])) ? 0:'-1';
                    
                    $arrRecur = $this->Admindao->recursiveTable("*",$tc['name'],'parent','id',$idparent,""); 
                    
printRecursiveMenuAdmin($titles,-1,$arrRecur,array());
                    ?>
                </ul>
              </div>
          <?php }
          ?>
      </div>
   </div>
</div>
<script type="text/javascript">
  $(function() {
    //Dragable
     $('.dragable').draggable({
        cursor: 'move',
        snap:true,
        refreshPositions: true,
        helper:'clone',
        start:function( event, ui ) {
          $(ui.helper.context).addClass('item-menu-dragable-drag');
        },
        stop:function(event,ui){
          $(ui.helper.context).removeClass('item-menu-dragable-drag');
        }
      });
     var options = {
        items: "li:not(.placeholder)",
        connectWith:'.droppable',
        placeholder: "sortable-placeholder",
          revert: true,
          beforeStop: function( event, ui ) {
            var current = $(ui.helper.context);
            if(!current.has('.droppable')){
              current.append('<ul class="droppable"></ul>');
            }
          },
          over : function(){
              $(this).addClass('sortable-hover');
         },
         out : function(){
              $(this).removeClass('sortable-hover');
         }
      }
      $('.droppable' ).droppable({
        activeClass: "droppable-hover",
        hoverClass: "droppable-active",
        accept: ":not(.ui-sortable-helper)",
        tolerance: 'touch',
        drop: function( event, ui ) {
          var newelement = $( "<li class='itemmenu'></li>" ).html( ui.draggable.html() );
          newelement.append('<ul class="droppable"></ul>')
          newelement.appendTo( this );
          var arr = $('.droppable');
          for (var i = 0; i < arr.length; i++) {
            var item = arr[i];
            if($(item).data( "ui-sortable" )){
              $(item).sortable('destroy');
            }
          }
          $('.droppable').sortable(options);
        }
      }).sortable(options);
    $('body').on('click', '.openitemmenu', function(event) {
      event.preventDefault();
      var p = $(this).parent().next();
      if(p.is(":visible")){
        $(p).slideUp(400);
        $(this).addClass('icon-plus-sign');
         $(this).removeClass('icon-minus-sign');
      }
      else{
         $(p).slideDown(400);
        $(this).removeClass('icon-plus-sign');
        $(this).addClass('icon-minus-sign');
      }
    });
    $('body').on('input', 'input[name^=name]', function(event) {
      $(this).parent().parent().prev().find('span').first().text($(this).val());
    });
    $('body').on('click', '.boxmenu .title i.arrow', function(event) {
      var content = $(this).parent().next();
      if($(content).is(":visible")){
        $(content).slideUp(400);
        $(this).removeClass('icon-arrow-down');
         $(this).addClass('icon-arrow-right');
        
      }
      else{
        $(content).slideDown(400);
        $(this).addClass('icon-arrow-down');
         $(this).removeClass('icon-arrow-right');
      }
    });
    $('body').on('click', '.menuregion i.remove', function(event) {
      var content = $(this).parent().parent();
      content.fadeOut('400', function() {
        $(this).remove();
      });
    });
    $('body').on('click', 'i.localsearch', function(event) {
      event.preventDefault();
      var input = $(this).prev();
      if(input.is(":visible")){
        input.fadeOut(400);
      }
      else{
        input.fadeIn(400);
      }
    });
    $('body').on('input', '.inputlocalsearch', function(event) {
      event.preventDefault();
      if($(this).val()==""){
        $(this).parent().next().find('li').show();
        return;
      }
      var arr = $(this).parent().next().find('li');
      for (var i = 0; i < arr.length; i++) {
        var item = $(arr[i]);
        if(item.text().toLowerCase().indexOf($(this).val().toLowerCase())==-1){
          item.hide();
        }
        else{
          item.show();
        }
      };
    });
  });
function getDataToSave(_this){
  
  var ul = $(_this).parent().next();
  var lis = ul.find('>li');
  var result = getJsonFromLi(lis);
  $.ajax({
    url: 'Techsystem/do_edit/<?php echo $table['name'] ?>',
    type: 'POST',
    data: {groupmenu:$(_this).attr('data-group'),data: JSON.stringify(result),'tech5s_controller':'','tech5s_type':<?php echo $table['type'] ?>},
  })
  .done(function() {
    console.log("success");
  })
  .fail(function() {
    console.log("error");
  })
  .always(function() {
    console.log("complete");
  });
  
  return ;
}
function getJsonFromLi(lis){
  var result = new Array();
  for (var i = 0; i < lis.length; i++) {
    var item = $(lis[i]);
    var obj = {};
    var inputs = item.find('>.detailitemmenu >.infoitemmenu > input');
    for (var j = 0; j < inputs.length; j++) {
      var input=$(inputs[j]);
      obj[input.attr('data-col')]= input.val();
    };
    obj['children'] = getJsonFromLi(item.find('>ul > li'));
    result.push(obj);
  };
  return result;
}
</script>
<style type="text/css">
  div.title{
    position: relative;
  }
  div.title .inputlocalsearch{
        display: block;
    position: absolute;
    top: 0px;
    left: 0px;
    height: 35px;
    width: 80%;
  }
  i.localsearch{
        margin: 5px;
    font-size: 20px;
  }
</style>
<!-- <li class="itemmenu ui-sortable-handle"><a><span>1</span><i class="remove icon-remove"></i><i class="icon-plus-sign openitemmenu"></i></a>
    <div class="detailitemmenu " style="display:none">
        <div class="infoitemmenu flex"><span>Tên</span>
            <input data-col="name" name="name_68" value="1">
        </div>
        <div class="infoitemmenu flex"><span>Link</span>
            <input data-col="link" name="link_68" value="">
        </div>
        <div class="infoitemmenu flex"><span>Class</span>
            <input data-col="clazz" name="Class">
        </div>
        <div class="infoitemmenu flex"><span>Parent</span>
            <input data-col="parent" name="Parent">
        </div>
        <div class="infoitemmenu flex"><span>Order</span>
            <input data-col="ord" name="Order">
        </div>
    </div>
    <ul class="droppable ui-sortable">
        <li class="itemmenu ui-sortable-handle"><a><span>2</span><i class="remove icon-remove"></i><i class="icon-plus-sign openitemmenu"></i></a>
            <div class="detailitemmenu " style="display:none">
                <div class="infoitemmenu flex"><span>Tên</span>
                    <input data-col="name" name="name_69" value="2">
                </div>
                <div class="infoitemmenu flex"><span>Link</span>
                    <input data-col="link" name="link_69" value="">
                </div>
                <div class="infoitemmenu flex"><span>Class</span>
                    <input data-col="clazz" name="Class">
                </div>
                <div class="infoitemmenu flex"><span>Parent</span>
                    <input data-col="parent" name="Parent">
                </div>
                <div class="infoitemmenu flex"><span>Order</span>
                    <input data-col="ord" name="Order">
                </div>
            </div>
            <ul class="droppable ui-sortable"></ul>
        </li>
    </ul>
</li>
 -->