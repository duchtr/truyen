<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view_plugins/pwa">Cấu hình Plugin PWA</a></li>
    </ul>
</div>
<?php $config = @$config?$config:[]; ?>
<div id="cph_Main_ContentPane " class="pwa">
    <div class="row">
        <form action="" method="post">
            <textarea name="config" class="hidden"><?php echo json_encode($config) ?></textarea>
         
            <div class="col-xs-12 col-md-3">
                <input type="checkbox" <?php echo array_key_exists('reset_config', $config) && $config['reset_config']['value']==1?'checked':'' ?> id="<?php echo 'reset_config' ?>" value="1">
                <label for="<?php echo 'reset_config' ?>">Reset lại config?</label>
            </div>
            <div class="col-xs-12">
            <button class="btn btn-primary" type="submit">Khởi tạo lại Manifest</button>
            </div>
        </form>
    </div>
</div>
</div>
<script type="text/javascript">
    $(".pwa input[type=checkbox]").change(function(event) {
        var json = {};
        var checkboxs = $(".pwa input[type=checkbox]");
        for (var i = 0; i < checkboxs.length; i++) {
            var checkbox = $(checkboxs[i]);
            var id = checkbox.attr("id");
            var value = checkbox.is(":checked")?1:0;;
            json[id] = {"name":id,"value":value};
        }
        $(".pwa textarea[name=config]").val(JSON.stringify(json));
    });
</script>
<style type="text/css">
    .pwa input[type=checkbox] + label {
  display: block;
  margin: 0.2em;
  cursor: pointer;
  padding: 0.2em;
}
.pwa input[type=checkbox] {
  display: none !important;
}
.pwa input[type=checkbox] + label:before {
  content: "\2714";
  border: 0.1em solid #000;
  border-radius: 0.2em;
  display: inline-block;
  width: 20px;
  height: 20px;
  padding-left: 0.2em;
  padding-bottom: 0.3em;
  margin-right: 0.2em;
  vertical-align: bottom;
  color: transparent;
  transition: .2s;
}
.pwa input[type=checkbox] + label:active:before {
  transform: scale(0);
}
.pwa input[type=checkbox]:checked + label:before {
  background-color: MediumSeaGreen;
  border-color: MediumSeaGreen;
  color: #fff;
}
.pwa input[type=checkbox]:disabled + label:before {
  transform: scale(1);
  border-color: #aaa;
}
.pwa input[type=checkbox]:checked:disabled + label:before {
  transform: scale(1);
  background-color: #bfb;
  border-color: #bfb;
}
</style>