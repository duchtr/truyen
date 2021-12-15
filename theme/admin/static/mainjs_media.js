function launchEditor(id, src,imageEditName) {
    return false;
}
/* CLEAR*/
      
$(document).click(function(event) { 
    if(!$(event.target).closest('.custom-menu').length) {
        if($('.custom-menu').is(":visible")) {
            $('.custom-menu').hide()
        }
    }        
})
var csfrData = {};
csfrData[csrftokenname]= csrftokenvalue;
$.ajaxSetup({
       data: csfrData
});
var istiny = getParameterByName('istiny');
$(function() {
  var globalObjectFile = undefined;
$("img.lazy").lazyload();
$(document).bind("contextmenu", function (event) {
    event.preventDefault();
    var ele = document.elementFromPoint(event.clientX, event.clientY);
    ele = $(ele).closest('.fileitem');
    var datafile =  $(ele).attr('data-file');
    if(datafile == undefined) return;
    try{
      var objfile = $.parseJSON(datafile);
      globalObjectFile = objfile;
      if(!objfile.hasOwnProperty('isfile') || objfile.isfile ==0 ){
        return;
      }
      var str="";
      if(extimgs.indexOf(objfile.extension)!=-1){
        str+="<li data-action='editimage'><i class='icon-picture'></i>Chỉnh sửa ảnh</li>";  
      }
      
      str+="<li data-action='showpath'><i class='icon-link'></i>Hiện đường dẫn file</li>";
      str+="<li data-action='duplicate'><i class='icon-paper-clip'></i>Nhân đôi file</li>";
      str+="<li data-action='copy'><i class='icon-copy'></i>Copy</li>";
      str+="<li data-action='cut'><i class='icon-cut'></i>Cut</li>";
      str += "<li><p style='text-align:center'>THÔNG TIN FILE</p></li>"
      str+="<li><i class='icon-tag'></i>"+objfile.extension+"</li>";
      if(objfile.hasOwnProperty('width') && objfile.hasOwnProperty('height')){
        str += "<li><i class='icon-screenshot'></i>"+objfile.width+'x'+objfile.height+"</li>";  
      }
      str+= "<li><i class='icon-fire'></i>"+objfile.size+"</li>";
      var t = new Date(1970,0,1);
      t.setSeconds(objfile.date);
      str+= "<li><i class='icon-calendar'></i>"+ (t.getDate()<10?"0"+t.getDate():t.getDate()) +"/"+ (t.getMonth()<9?"0"+(t.getMonth()+1):(t.getMonth()+1))+"/"+t.getFullYear()+" "+ (t.getHours()<10?"0"+t.getHours():t.getHours())+":"+(t.getMinutes()<10?"0"+t.getMinutes():t.getMinutes())+":"+(t.getSeconds()<10?"0"+t.getSeconds():t.getSeconds())+"</li>";
      
      $('ul.custom-menu').html(str);
    }
    catch(ex){
    }
    $(".custom-menu").show()
.          css({
        top: event.pageY + "px",
        left: event.pageX + "px"
    });
    
});
  $('#listfolder .alldir').click(function(event) {
    event.stopPropagation();
    event.preventDefault();
    $('#listfolder .alldir').removeClass('active');
    $(this).addClass('active');
    if($(this).find('>ul > li').length>0){
      var ul = $(this).find('>ul');
      if(!ul.is(":visible")){
        $(this).find('>a>i').removeClass().addClass('icon-folder-open');
        ul.slideDown(400);  
      }
      else{
        $(this).find('>a>i').removeClass().addClass('icon-folder-close');
       ul.slideUp(400);
      }
      
    }
  });
  $('#copyfile').click(function(event) {
    $.ajax({
      url: 'Techsystem/pfCopy',
      type: 'POST',
      data: {oldfile: globalObjectFile.name,despath:$('.alldir.active').attr('dt-path')},
    })
    .done(function(e) {
      try{
          var json = $.parseJSON(e);
          // bootbox.alert(json.message,function(){
            window.location.href=globalFullUrl;
          // });  
        }
        catch(ex){
          bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
        }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
    
  });
  $('#movefile').click(function(event) {
    $.ajax({
      url: 'Techsystem/pfCopy',
      type: 'POST',
      data: {oldfile: globalObjectFile.name,despath:$('.alldir.active').attr('dt-path'),ismove:1},
    })
    .done(function(e) {
      try{
          var json = $.parseJSON(e);
          // bootbox.alert(json.message,function(){
            window.location.href=globalFullUrl;
          // });  
        }
        catch(ex){
          bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
        }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
    
  });
   $(document).ajaxStart(function() {
    $('#bg-load').fadeIn(500);
  });
  $(document).ajaxComplete(function(event, xhr, settings) {
    $('#bg-load').delay(500).fadeOut(500);
  });
  $('.fancybox').fancybox();
  $('.custom-menu').on('click', '>li', function(event) {
      $(this).parent().hide();
      var action = $(this).attr('data-action');
      switch(action){
        case 'editimage':
        $('#aviary-image').attr('src',globalBaseUrl+globalObjectFile.path);
        launchEditor('aviary-image',$('#aviary-image').attr('src'),globalObjectFile.name);
        break;
        case 'showpath':
          bootbox.dialog({
            title: "Đường dẫn tệp tin",
            message: "<input style='width:100%' value='"+globalObjectFile.path+"'/>"
          });
        break;
        case 'duplicate':
        duplicateFile(globalObjectFile.name);
        break;
        case 'copy':
        $('#listfolder').modal();
        break;
        case 'cut':
        $('#listfolder').modal();
        break;
      }
      // globalObjectFile = undefined;
    });
  function duplicateFile(oldfile){
    $.ajax({
        url: 'Techsystem/pfDuplicate',
        type: 'POST',
        data: {oldfile: oldfile},
      })
      .done(function(e) {
        try{
          var json = $.parseJSON(e);
          // bootbox.alert(json.message,function(){
            window.location.href=globalFullUrl;
          // });  
        }
        catch(ex){
          bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
        }
        
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
  }
  /*Thêm mới folder*/
  $('.newfolder').click(function(event) {
    event.preventDefault();
    bootbox.prompt("Nhập tên folder", function(e){
      if(e==null ||e.trim().length==0) return;
      $.ajax({
        url: 'Techsystem/pfCreateFolder',
        type: 'POST',
        data: {folder_name: e},
      })
      .done(function(e) {
        try{
          var json = $.parseJSON(e);
          // bootbox.alert(json.message,function(){
            window.location.href=globalFullUrl;
          // });  
        }
        catch(ex){
          bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
        }
        
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      
    })
  });
  /*Xóa*/
  $('i.delete').click(function(event) {
    event.stopPropagation();
    var name = $(this).attr('dt-name');
    bootbox.confirm("Bạn có muốn xóa "+name+" ?",function(e){
      if(e){
        $.ajax({
          url: 'Techsystem/pfDelete',
          type: 'POST',
          data: {name: name},
        })
        .done(function(e) {
          try{
          var json = $.parseJSON(e);
          // bootbox.alert(json.message,function(){
            window.location.href=globalFullUrl;
            // });  
          }
          catch(ex){
            bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
          }
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
        
      }
    })
  });
  /*Edit name*/
  $('i.editname').click(function(event) {
    event.preventDefault();
    event.stopPropagation();
    var oldname = $(this).attr('dt-name');
    bootbox.prompt("Bạn muốn đổi tên "+oldname+" thành: ",function(newname){
      if(newname==null ||newname.trim().length==0) return;
        $.ajax({
          url: 'Techsystem/pfRename',
          type: 'POST',
          data: {oldname: oldname,newname:newname},
        })
        .done(function(e) {
          try{
          var json = $.parseJSON(e);
          // bootbox.alert(json.message,function(){
            window.location.href=globalFullUrl;
            // });  
          }
          catch(ex){
            bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
          }
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
        
    })
  });
  $('i.download').click(function(event) {
            var name = $(this).attr('dt-name');
            bootbox.confirm("Bạn có muốn download "+name+" ?",function(e){
              if(e){
                window.location.href=globalBaseUrl+"Techsystem/pfForceDownload?name="+name;
                
              }
            })
          });
  $('.fileitem ').click(function(event) {
    var datafile =  $(this).attr('data-file');
    if(datafile == undefined) return;
    try{
      var objfile = $.parseJSON(datafile);
      if(!objfile.hasOwnProperty('isfile') || objfile.isfile ==0){
        var existFolder = getParameterByName('folder')
        if(existFolder.trim().length==0){
          var href = window.location.href;
          if(href.indexOf('?')!=-1){
            window.location.href= href+"&folder="+objfile.name;
          }
          else{
            window.location.href=globalBaseUrl+"Techsystem/mediaManager?folder="+objfile.name;
          }
        }
        else{
          var currentHref= window.location.href;
          if(currentHref.lastIndexOf(objfile) != currentHref.length- objfile.name.length){
            window.location.href=window.location.href+","+objfile.name;  
          }
          
        }
        return;
      }
    }
    catch(ex){
    }
    var checkbox = $(this).find('input[type=checkbox]');
    if(checkbox.is(":checked")){
      checkbox.prop('checked', false);  
    }
    else{
      checkbox.prop('checked', true);  
    }
    
  });
  $('.filter.files').click(function(event) {
    event.preventDefault();
    var arrFiles = $('.fileitem');
    var hasClass = $(this).hasClass('active');
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      if(!objfile.isfile){
        if(hasClass){
          $(item).show();  
        }
        else{
          $(item).hide();
        }
      }
    };
    !hasClass?$(this).addClass('active'):$(this).removeClass('active');
  });
  $('.filter.images').click(function(event) {
    event.preventDefault();
    var arrFiles = $('.fileitem');
    var hasClass = $(this).hasClass('active');
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      if(extimgs.indexOf(objfile.extension) ==-1){
        if(hasClass){
          $(item).show();  
        }
        else{
          $(item).hide();
        }
      }
    };
    !hasClass?$(this).addClass('active'):$(this).removeClass('active');
  });
  $('.filter.archive').click(function(event) {
    event.preventDefault();
    var arrFiles = $('.fileitem');
    var hasClass = $(this).hasClass('active');
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      if(extmisc.indexOf(objfile.extension) ==-1){
        if(hasClass){
          $(item).show();  
        }
        else{
          $(item).hide();
        }
      }
    };
    !hasClass?$(this).addClass('active'):$(this).removeClass('active');
  });
  $('.filter.videos').click(function(event) {
    event.preventDefault();
    var hasClass = $(this).hasClass('active');
    var arrFiles = $('.fileitem');
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      if(extvideos.indexOf(objfile.extension) ==-1){
        if(hasClass){
          $(item).show();  
        }
        else{
          $(item).hide();
        }
      }
    };
   !hasClass?$(this).addClass('active'):$(this).removeClass('active');
  });
  $('.filter.musics').click(function(event) {
    event.preventDefault();
    var arrFiles = $('.fileitem');
    var hasClass = $(this).hasClass('active');
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      if(extmusic.indexOf(objfile.extension) ==-1){
        if(hasClass){
          $(item).show();  
        }
        else{
          $(item).hide();
        }
        
      }
    };
    !hasClass?$(this).addClass('active'):$(this).removeClass('active');
  });
  $('button.search').click(function(event) {
    var arrFiles = $('.fileitem');
    var textsearch = $('input[name=textsearch]').val();
    if(textsearch.trim().length==0) {
      $('.fileitem').show();
      return;
    }
    var hasClass = $(this).hasClass('active');
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      if(objfile.name.toLowerCase().indexOf(textsearch)==-1){
        $(item).hide();
      }
    }
  });
  $('.deleteAll').click(function(event) {
    var arrFiles = $('.fileitem');
    var html = [];
    for (var i = 0; i < arrFiles.length; i++) {
      var item = arrFiles[i];
      var datafile =  $(item).attr('data-file');
      var objfile = $.parseJSON(datafile);
      var checked = $(item).find('input[type=checkbox]').is(':checked');
      if(checked){
        html.push(objfile.name);
      }
    }
    if(html.length==0 || html.length==undefined){
      bootbox.alert('Chưa chọn file để xóa!');
    }
    else{
      bootbox.confirm("Bạn muốn xóa thư mục/file đã chọn?",function(e){
        if(e){
          $.ajax({
              url: 'Techsystem/pfDeleteAll',
              type: 'POST',
              data: {name: JSON.stringify(html)},
            })
            .done(function(e) {
              try{
                var json = $.parseJSON(e);
                // bootbox.alert(json.message,function(){
                  window.location.href=globalFullUrl;
                  // });  
                }
                catch(ex){
                  bootbox.alert('Xảy ra lỗi trong quá trình thực hiện!');
                }
            })
            .fail(function() {
              console.log("error");
            })
            .always(function() {
              console.log("complete");
            });
        }
      });
      
      
    }
  });
  $('.apply').click(function(event) {
    
    var arrFiles = $('.fileitem');
    if(istiny==1){
       var html = "";
      for (var i = 0; i < arrFiles.length; i++) {
        var item = arrFiles[i];
        var datafile =  $(item).attr('data-file');
        var objfile = $.parseJSON(datafile);
        var checked = $(item).find('input[type=checkbox]').is(':checked');
        if(checked){
          html +="<img class='img-responsive' src='"+objfile.path+"'/></br>";
        }
      }
      top.tinymce.activeEditor.windowManager.getParams().oninsert(html);
    }
    else if(istiny==2){
      if(arrFiles.length>0){
        var item = arrFiles[0];
        var datafile =  $(item).attr('data-file');
        var objfile = $.parseJSON(datafile);
        for (var i = 0; i < arrFiles.length; i++) {
          var item = arrFiles[i];
          var datafile =  $(item).attr('data-file');
          var objfile = $.parseJSON(datafile);
          var checked = $(item).find('input[type=checkbox]').is(':checked');
          if(checked){
            top.tinymce.activeEditor.windowManager.getParams().setUrl(objfile.path);
            break;
          }
        }
        
      }
    }
    else{
      var arrItem = [];
      for (var i = 0; i < arrFiles.length; i++) {
        var item = arrFiles[i];
        var datafile =  $(item).attr('data-file');
        var objfile = $.parseJSON(datafile);
        var checked = $(item).find('input[type=checkbox]').is(':checked');
        if(checked){
          arrItem.push(objfile.path);
        }
      }
      parent.enuyFileManagerCallback(arrItem,istiny);
      parent.close_window();
    }
    
  });
});
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
$(function() {
  $('#uploadmultifile').ajaxForm({
          global:false,
          beforeSend: function() {
            $('#uploadfile .progressbar').css({'width':0+'%'});
          },
          uploadProgress: function(event, position, total, percentComplete) {
            $('#uploadfile .progressbar').css({'width':percentComplete+'%'});
            $('#uploadfile .progressbar span').text(percentComplete+'%');
          },
          success: function() {
          },
          complete: function(xhr) {
          }
  });
   var obj = $("#uploadfile");
    obj.on('dragenter', function(e) 
    {
        e.stopPropagation();
        e.preventDefault();
        $(this).find('.modal-body').css('border', '2px solid #0B85A1');
    });
    obj.on('dragover', function(e) 
    {
        e.stopPropagation();
        e.preventDefault();
    });
    obj.on('drop', function(e) 
    {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        $('#uploadfile .countfile').text(files.length+" files");
        handleFileUpload(files,obj);
    });
});
function handleFileUpload(files,obj)
{
  var fd = new FormData();
   for (var i = 0; i < files.length; i++) 
   {
      
      fd.append('browsefile[]', files[i]);
    
   }
   fd.append(csrftokenname, csrftokenvalue);
   fd.append('field','browsefile');
   sendFileToServer(fd);
}
function sendFileToServer(formData)
{
  var uploadURL ="Techsystem/uploadMultiFile"; //Upload URL
  var extraData ={}; //Extra Data.
  var jqXHR=$.ajax({
          xhr: function() {
            var xhrobj = $.ajaxSettings.xhr();
            if (xhrobj.upload) {
                    xhrobj.upload.addEventListener('progress', function(event) {
                        var percent = 0;
                        var position = event.loaded || event.position;
                        var total = event.total;
                        if (event.lengthComputable) {
                            percent = Math.ceil(position / total * 100);
                             $('#uploadfile .progressbar').css({'width':percent+'%'});
            $('#uploadfile .progressbar span').text(percent+'%');
                        }
                    }, false);
                }
            return xhrobj;
        },
      url: uploadURL,
      type: "POST",
    contentType:false,
    processData: false,
        cache: false,
        data: formData,
        success: function(data){
    }
    }); 
}
