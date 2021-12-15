<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view_plugins/picture_helper">Cấu hình Plugin Picture</a></li>
    </ul>
</div>
<?php 
    $this->db->where("name","SIZE_IMAGE");
    $q = $this->db->get("nuy_config");
    $nuy_config = $q->result_array();
    $imagesizes = [];
    if(count($nuy_config)>0){
        $nuy_config = $nuy_config[0]["value"];
        $nuy_config = json_decode($nuy_config,true);
        $imagesizes = isset($nuy_config)?$nuy_config:[];
    }
    $config = isset($config)?$config:[];
   
?>
<div id="cph_Main_ContentPane " class="picture_helper">

    <div class="row list_item">
        <div class="col-xs-12 hidden base_item">
            <div class="col-xs-12">
                <input type="" class="group_name" placeholder="Tên Group">
                <button class="btnadmin btn add_item" type="button">Thêm</button>
                <button class="btnadmin btn remove_item" type="button">Xóa</button>
            </div>  

            <div class="col-md-4 col-xs-12 sub_item">
                 <select class="size">
                    <option value="0">Không xác định</option>
                    <option value="-1">Ảnh gốc</option>
                    <?php foreach($imagesizes as $size): ?>
                    <option  value ="<?php echo $size['name'] ?>"><?php echo $size['name'] ?></option>
                    <?php endforeach ?>
                </select>
                <input type="" class="value" placeholder="Min width">
            </div>
        </div>

        <?php foreach($config as $conf): ?>
                <div class="col-xs-12 item">
                    <div class="col-xs-12">
                        <input type="" class="group_name" value="<?php echo $conf['name'] ?>">
                        <button class="btnadmin btn add_item" type="button">Thêm</button>
                        <button class="btnadmin btn remove_item" type="button">Xóa</button>
                    </div>  
                    <?php foreach($conf['items'] as $sub): ?>
                        <div class="col-md-4 col-xs-12 sub_item">
                             <select class="size">
                                <option value="0">Không xác định</option>
                                <option <?php echo -1==$sub['size']?'selected':'' ?> value="-1">Ảnh gốc</option>
                                <?php foreach($imagesizes as $size): ?>
                                <option <?php echo $size['name']==$sub['size']? 'selected':'' ?> value ="<?php echo $size['name'] ?>"><?php echo $size['name'] ?></option>
                                <?php endforeach ?>
                            </select>
                            <input type="" class="value" value="<?php echo $sub['media'] ?>">
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endforeach; ?>
    </div>
    <div class="row text-center">
        <button class="btnadmin btn add_group">Thêm Group</button>
    </div>
    <br>
    <div class="row text-center">
        <form action="" method="post">
            <textarea name="config" class="hidden"><?php echo json_encode($config) ?></textarea>
            
            <button class="btn" type="submit">Lưu thông tin cấu hình</button>
        </form>
    </div>
</div>
</div>
<script type="text/javascript">
    $(document).on('input', '.picture_helper input', function(event) {
        event.preventDefault();
        calculatePictureHelper();
    });
    $(document).on('click', '.picture_helper span.delete', function(event) {
        event.preventDefault();
        $(this).parents(".sub_item").remove();
        calculatePictureHelper();
    });
    $(document).on('change', '.picture_helper select.size', function(event) {
        event.preventDefault();
        calculatePictureHelper();
    });
    $(".picture_helper .add_group").click(function(event) {
        var clone = $(".picture_helper .base_item").clone();
        clone.removeClass('hidden').removeClass('base_item').addClass('item');
        $(".picture_helper .list_item").append(clone[0].outerHTML);
        calculatePictureHelper();
    });
    $(document).on('click', '.picture_helper .add_item', function(event) {
        event.preventDefault();
         var clone = $(".picture_helper .base_item .sub_item").clone();
         clone.append("<span class='delete'>X</span>");
        $(this).parents(".item").append(clone[0].outerHTML);
        calculatePictureHelper();

    });
    $(document).on('click', '.picture_helper .remove_item', function(event) {
        event.preventDefault();
        $(this).parents(".item").remove();
        calculatePictureHelper();

    });
    function calculatePictureHelper(){
        var items = $(".picture_helper .item");
        var json = [];
        for (var i = 0; i < items.length; i++) {
            var item = $(items[i]);
            var group = item.find(".group_name");
            var objGroup = {};
            objGroup.name = group.val();
            objGroup.items = [];
            var subs = item.find(".sub_item");
            for (var j = 0; j < subs.length; j++) {
                var sub = $(subs[j]);
                var input = sub.find("input");
                var select = sub.find("select");
                if(select.val()=="0") continue;
                var subObj = {};
                subObj.media = input.val();
                subObj.size = select.val();
                objGroup.items.push(subObj);
            }
            json[objGroup.name]= objGroup;
        }
        $(".picture_helper textarea[name=config]").val(JSON.stringify({...json}));
    }
</script>
<style type="text/css">

  .picture_helper .item select,
	.picture_helper .item input{
    height: 25px;
    width: 49%;
    margin:0;
     margin-bottom: 2px;
  }
  .picture_helper .item span.delete{
        position: absolute;
    background: red;
    color: #fff;
    padding: 7px;
    line-height: 16px;
    right: 0;
    top: 0;
    cursor: pointer;
  }
</style>