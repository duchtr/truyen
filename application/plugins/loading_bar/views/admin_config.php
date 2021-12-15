<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view_plugins/loading_bar">Cấu hình Plugin Picture</a></li>
    </ul>
</div>
<?php 

    $config = isset($config)?$config:['line3color'];
    $config = count($config)>0?$config[0]:'line3color';
?>
<div id="cph_Main_ContentPane " class="loading_bar">
    
    <div class="row list_item">
        <div class="col-xs-12">
            <form action="" method="post">
                <textarea name="config" class="hidden"></textarea>
            <select class="select_type">
                <option <?php echo $config=='line3color'?'selected':'' ?> value="line3color">Line 3 Color</option>
                <option <?php echo $config=='cube'?'selected':'' ?> value="cube">Cube</option>
                <option <?php echo $config=='line'?'selected':'' ?> value="line">Line 1 Color</option>
                <option <?php echo $config=='wave'?'selected':'' ?> value="wave">Wave</option>
            </select>
            <button class="btn" type="submit">Lưu thông tin cấu hình</button>
        </form>
        </div>
    </div>
    <br>
</div>
</div>
<script type="text/javascript">
    $(document).on('change', '.loading_bar select.select_type', function(event) {
        event.preventDefault();
        $('.loading_bar textarea[name=config]').val('["'+$(this).val()+'"]');
    });
    $('.loading_bar select.select_type').trigger('change');
</script>
<style type="text/css">

</style>