<div id="dragzone" class="modal fade" role="dialog">

  <div class="modal-dialog modal-lg " >



    <!-- Modal content-->

    <div class="modal-content">

      <div class="modal-header">

        <button type="button" class="close" data-dismiss="modal">&times;</button>

        <h4 class="modal-title">Đăng nhanh</h4>

      </div>

      <div class="modal-body" style="text-align:center;  max-height: 80vh;

  overflow: scroll;">

        <div class="publicdragzone">

          <h4>Cấu hình thông tin chung cho upload nhanh</h4>

          

        </div>

        <div class="dragzoneinfo">

          

        </div>

        <input type="file" id="myFileInput" multiple style="display:none;" />

        <button style="  color: #fff;font-size: 150px;line-height: 150px;display: inline;border: none;background-color: rgb(184, 184, 184);width: 150px;height: 150px;border-radius: 50%;" onclick="document.getElementById('myFileInput').click()">+</button>

        <p>Kéo thả file vào đây để upload</p>

      </div>

      <div class="modal-footer">

        <button type="button" class="btn btn-default" onclick="getAllImageQuickPost()">Save</button>

      </div>

    </div>



  </div>

</div>



<script>

      var csrftokenname= '<?php echo $this->security->get_csrf_token_name(); ?>';

      var csrftokenvalue= '<?php echo $this->security->get_csrf_hash(); ?>';

function getAllImageQuickPost(){

  var arr = $('#dragzone .dragzoneinfo .statusbar');

  var send = [];

  for (var i = 0; i < arr.length; i++) {

    var item = arr[i];

    var inputs = $(item).find('input,select');

    var obj = {};

    for (var j = 0; j < inputs.length; j++) {

      var input = $(inputs[j]);

      obj[input.attr('name')] = input.val();

    };

    send[i] = obj;

  };

  $.ajax({

    url: "Techsystem/doQuickPost/<?php echo $table[0]['name'] ?>",

    type: 'POST',

    data: {data:JSON.stringify(send)},

  })

  .done(function() {

    $('.dragzoneinfo').html('');

  })

  .fail(function() {

    

  })

  .always(function() {

    

  });

  

}

function sendFileToServer(formData,status)

{

  var uploadURL ="Techsystem/quickpost/<?php echo $table[0]['name'] ?>"; //Upload URL

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

                        }

                        //Set progress

                        status.setProgress(percent);

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

          status.setProgress(100);

          status.setImage(data);

    }

    }); 



  status.setAbort(jqXHR);

}



var rowCount=0;

function createStatusbar()

{

    var obj = $('.dragzoneinfo');

   rowCount++;

   this.statusbar = $('.dataStatusCopy .statusbar').clone(true);

   this.statusbar.css('display', 'block');

   this.statusbar.appendTo(obj);

   this.progressBar = this.statusbar.find('.progressBar');

   this.image = this.statusbar.find('img').first();

   this.abort = this.statusbar.find('.abort');





    

    

    this.setProgress = function(progress)

    {   

    var progressBarWidth =progress;  

    this.progressBar.find('div').animate({ width: progressBarWidth +'%' }, 10).html(progress + "%&nbsp;");

    if(parseInt(progress) >= 100)

    {

      this.abort.hide();

    }

  }

  this.setImage= function(str){

    this.image.attr('src',str);

    this.image.parent().find('input[class=hiddenimage]').val(str);

  }

  this.setAbort = function(jqxhr)

  {

    var sb = this.statusbar;

    this.abort.click(function()

    {

      jqxhr.abort();

      sb.hide();

    });

  }

}

function handleFileUpload(files,obj)

{

   for (var i = 0; i < files.length; i++) 

   {

      var fd = new FormData();

      fd.append('file', files[i]);

      var status = new createStatusbar(obj); 

      fd.append(csrftokenname, csrftokenvalue);

      sendFileToServer(fd,status);

   

   }

}

$(document).ready(function() 

{

    var obj = $("#dragzone");

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



        handleFileUpload(files, obj);

    });

    $('#dragzone').on('dragenter', function(e) 

    {

        e.stopPropagation();

        e.preventDefault();

    });

    $('#dragzone').on('dragover', function(e) 

    {

        e.stopPropagation();

        e.preventDefault();

        obj.css('border', '2px dotted #0B85A1');

    });

    $('#dragzone').on('drop', function(e) 

    {

        e.stopPropagation();

        e.preventDefault();

    });



/*Clone select*/

    var arrSelect = $('.statusbar select');

    for (var i = 0; i < arrSelect.length; i++) {

      var select = $(arrSelect[i]);

      var item = $(arrSelect[i]).parent().parent().clone();

      $('.publicdragzone').append(item);

    };

    /*Event cho select */

    $('.publicdragzone').on('change', 'select', function(event) {

      var tagName = $(this).attr('name');

      $('.dragzoneinfo select[name='+tagName+']').val($(this).val());

    });



});

</script>



<div class="dataStatusCopy">

<div class='row statusbar' style="display:none;">

  <button onclick="$(this).parent().remove()" style="  margin: 4px;

  border-radius: 50%;

  border: none;

  background-color: #0BA1B5;

  color: #fff;

  width: 30px;

  height: 30px;

  text-align: center;

  padding: 0px;

"><i style="display: block;

  width: 30px;

  height: 30px;

  line-height: 30px;font-size:20px;" class='icon-trash'></i></button>

  <input type="hidden" name="tech5s_controller" value="<?php echo $table[0]['controller'] ?>"/>

  <div class='col-md-2 col-xs-12'>

               <?php 

                $quickUp=$this->Admindao->getAllFieldInTable(

                  array(

                      array("key"=>"a.id",

                          "value"=>"b.parent",

                          "compare"=>"="),

                      array('key'=>'b.is_upload','compare'=>'=','value'=>1),

                      array('key'=>'a.name','compare'=>"='",'value'=>$table[0]['name']."'")

                      )

                  );

                 

              if(count($quickUp)>0){

              ?>

              <input type='hidden' class='hiddenimage' name ='<?php echo $quickUp[0]['name'] ?>' />

              <img src='theme/admin/images/noimage.png' class='img-responsive' />

                <?php }

              ?>

            </div>

            <div class='detailquick col-md-10 col-xs-12'>

                <?php 

                  $quick=$this->Admindao->getAllFieldInTable(

                    array(

                        array("key"=>"a.id",

                            "value"=>"b.parent",

                            "compare"=>"="),

                        array('key'=>'b.quickpost','compare'=>'=','value'=>1),

                        array('key'=>'a.name','compare'=>"='",'value'=>$table[0]['name']."'")

                        )

                    );

                    foreach ($quick as $key => $value) {

                      $typeControl = strtolower($value['type']);

                      $dataControl['field'] = $value;

                      $dataControl['type'] = 'quickpost';

                      $dataControl['typeextra'] = 'quickpost';// thêm định danh cho các control

                      $dataitem[$value['name']]=round(microtime(true) * 1000);

                      $dataControl['dataitem'] = $dataitem;

                      if($typeControl=='editor'){

                        $typeControl = "textarea";

                      }



                       $this->load->view("nuy/view_edit/".$typeControl,$dataControl);

                     }

                    ?>

                <div class="row margin0">

                <div class="progressBar col-xs-11 padding0"><div></div></div>

                <div class="abort col-xs-1">Abort</div>

                </div>

                

            </div>



      </div>



</div>

