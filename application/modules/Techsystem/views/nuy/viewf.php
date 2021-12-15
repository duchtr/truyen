<?php
  $table = $table[0];

?>






<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Admin"><i class="icon-home" style="font-size:14px;"></i> Trang chủ</a></li>
        <li class="SecondLast"><a href="Techsystem/viewf/editcode">Chỉnh sửa code </a></li>
    </ul>
</div>

<div id="cph_Main_ContentPane">




   <div class="widget row margin0">
      <div class="widget-title">
       
         <h4>
            <i class="icon-qrcode"></i>&nbsp; Chỉnh sửa code <span class="codefile" style="color:#2294D2"></span>
         </h4>
         <div class="ui-widget-content ui-corner-top ui-corner-bottom">

            <div id="toolbox">
               <div style="float:right;" class="toolbox-content">
                  <table class="toolbar">
                     <tbody>
                        <tr>
                          <?php if($table['help']==1){ ?>
                           <td align="center">
                              <a onclick="" id="" onclick="" title="Trợ giúp" class="toolbar btn btn-info" href=""><i class="icon-question-sign"></i>&nbsp;
                              Trợ giúp</a>
                           </td>

                           <?php } ?>
                           <?php if($table['edit']==1) { ?>
                           <td align="center">
                              <a id="" title="Lưu" class=" updatefile toolbar btn btn-info" href="<?php echo base_url().'Techsystem/'.'updateFileCode'?>"><i class="icon-plus"></i>&nbsp;
                              Lưu lại</a>
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
               <i class="icon-qrcode"></i>&nbsp;Chỉnh sửa code
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
                                    <a onclick="" id="" title="Trợ giúp" class="toolbar btn btn-info" href=""><i class="icon-question-sign"></i>&nbsp;
                                    Trợ giúp</a>
                                 </td>
                                 <?php } ?>
                                 <?php if($table['insert']==1){ ?>
                                 <td align="center">
                                    <a id="" title="Lưu" class="updatefile toolbar btn btn-info" href="<?php echo base_url().'Techsystem/'.'updateFileCode'?>"><i class="icon-plus"></i>&nbsp;
                                    Lưu</a>
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
       

     
         <div class="row margin0">

          <div class="col-md-3 col-xs-12">
            
            <ul class="directory">
              <li class='dir'>
                <i class='icon-folder-close'></i><a style="text-transform: uppercase;color: #000;font-weight: bold;">Theme</a>
                <ul>
                  <?php 
                recusiveDir();
                 ?>
                </ul>
              </li>

                <li class='dir'>
                  <i class='icon-folder-close'></i><a style="text-transform: uppercase;color: #000;font-weight: bold;">Code</a>
                  <ul>
                    <?php 
                  recusiveDir("application/views");
                   ?>
                  </ul>
                </li>
            </ul>
          </div>
          <div class="col-md-9 col-xs-12">
            <pre id="editor">Nội dung file sửa</pre>
<i onclick="toggleFullScreen();" class="fullscreen icon-fullscreen"></i>
<script src="theme/admin/static/ace/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/eclipse");
    editor.session.setMode("ace/mode/css");
</script>
          </div>
          
         </div>
      </div>
   </div>
</div>
<style type="text/css">
i.icon-folder-close,i.icon-folder-open{
  color:#F8B864;
}
#editor{
  min-height: 500px;
}
ul.directory{
  padding: 10px;
    border: 1px solid #8A8A8A;
    min-height: 500px;
}
  ul.directory,ul.directory ul{
    padding: 0px;
    padding-left: 10px;
  }
  ul.directory ul{
    display: none;
  }
  ul.directory li a{
    cursor: pointer;
  }
  i.fullscreen{
    position: absolute;
    top: 0px;
    right: -7px;
    z-index: 99999999999;
    font-size: 25px;

  }
  :-webkit-full-screen {
  width: 100%;
  height: 100%;
}

</style>
<script type="text/javascript">
  function requestFullScreen(){
    var elem = document.getElementById("editor");
    if (elem.requestFullscreen) {
      elem.requestFullscreen();
    } else if (elem.msRequestFullscreen) {
      elem.msRequestFullscreen();
    } else if (elem.mozRequestFullScreen) {
      elem.mozRequestFullScreen();
    } else if (elem.webkitRequestFullscreen) {
      elem.webkitRequestFullscreen();
    }
  }
  function toggleFullScreen() {
    var elem = document.getElementById("editor");
  if (!document.fullscreenElement &&    
      !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement ) {  
    if (document.documentElement.requestFullscreen) {
      elem.requestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) {
      elem.msRequestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) {
      elem.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) {
      elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
    }
  } else {
    if (document.exitFullscreen) {
      document.exitFullscreen();
    } else if (document.msExitFullscreen) {
      document.msExitFullscreen();
    } else if (document.mozCancelFullScreen) {
      document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
      document.webkitExitFullscreen();
    }
  }
}
  $(function() {
    $('ul.directory li.dir >a').click(function(event) {
      if($(this).next().is(":hidden")){
        $(this).prev().removeClass();
        $(this).prev().addClass('icon-folder-open');
        $(this).next().slideDown(400);
      }
      else{
        $(this).prev().removeClass();
        $(this).prev().addClass('icon-folder-close');
        $(this).next().slideUp(400);
      }
    });
    $('.updatefile').click(function(event) {
      event.preventDefault();
      checkReload = false;
      $.ajax({
        url: $(this).attr('href'),
        type: 'POST',
        data: {contentdata: editor.getValue()},
      })
      .done(function() {
      })
      .fail(function() {
      })
      .always(function() {
      });
      
    });
    $('ul.directory li.dir ').each(function(index, el) {
      var arr = $(this).find('li.file');
      if(arr.length==0){
         $(this).remove();
      }
    });;
        $('li.file .deletefile').click(function(event) {
        $.ajax({
          url: 'Techsystem/deleteFileCode',
          global:false,
          type: 'POST',
          data: {name:$(this).next().text()},
        })
        .done(function(e) {
          window.location.href='<?php echo current_url(); ?>'
        })
        .fail(function() {
        })
        .always(function() {
        });
    });
    $('ul.directory li.file >a').click(function(event) {
      var file = $(this).attr('data-path');
      var pos = file.lastIndexOf('.');
      var ext = file.substring(pos+1).toLowerCase();
      if(ext=='js'){
        ext = "javascript";
      }
      editor.session.setMode("ace/mode/"+ext);
      $('.codefile').html(file);
      checkReload= false;
      $.ajax({
        url: 'Techsystem/readFile',
        type: 'POST',
        data: {filename:$(this).attr('data-path')},
      })
      .done(function(e) {
        try{
          var json = $.parseJSON(e);
         editor.setValue(json.content);
         editor.clearSelection();
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
      
    });
  });

</script>

