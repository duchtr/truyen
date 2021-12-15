<style>
    #image_file {
        width: 300px;
        border: 1px rgb(221, 221, 221) solid;
        background-color: #fff;
        float: left;
        padding: 5px;
    }
    #import-button {
        height: 32px;
        line-height: 18px;
    }
    .content {
        background-color: #fff;
        min-height: 50px;
        padding: 20px;
    }
</style>
<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Admin"><i class="icon-home" style="font-size:14px;"></i> Trang chủ</a></li>
        <li class="SecondLast"><a href="javascript:void(0)">Quản lý import</a></li>
    </ul>
</div>
<div id="cph_Main_ContentPane" class="content">
<form action="Techsystem/doImport" class="fileUpload" onsubmit="return validateFormImport();" enctype="multipart/form-data" method="post">
        <input type="file" name="file_excel" id="image_file"/>
        <button type="submit" class="btn btn-primary" id="import-button"/>Import</button>
    </form>
</div>
<script type="text/javascript">

    function validateFormImport(){
        var $val = $('input[name=file_excel]').val();
        if($val == ''){
            alert('Vui lòng chọn file upload!');
            return false;
        }
        return true;
    }
</script>