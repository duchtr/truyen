<div id="Breadcrumb" class="Block Breadcrumb ui-widget-content ui-corner-top ui-corner-bottom">
    <ul>
        <li class="home"><a href="<?php echo base_url(''); ?>Techsystem"><i class="icon-home" style="font-size:14px;"></i> <?php echo alang("HOME") ?></a></li>
        <li class="SecondLast"><a href="Techsystem/view_plugins/compress_static">Cấu hình Plugin nén</a></li>
    </ul>
</div>
<?php $config = @$config?$config:[] ?>
<div id="cph_Main_ContentPane " class="compress_static">
	<div class="row">
		<form action="" method="post">
			<textarea name="config" class="hidden"><?php echo json_encode($config) ?></textarea>
		<div class="col-xs-12 col-md-3">
			<input type="checkbox" id="css_enable" <?php echo array_key_exists("css_enable", $config) && $config["css_enable"]==1?'checked':'' ?> value="1">
  			<label for="css_enable">Nén file Css</label>
		</div>
		<div class="col-xs-12 col-md-3">
			<input type="checkbox" id="css_inline" <?php echo array_key_exists("css_inline", $config) && $config["css_inline"]==1?'checked':'' ?> value="1">
  			<label for="css_inline">Load Css dạng inline</label>
		</div>
		<div class="col-xs-12 col-md-3">
			<input type="checkbox"  id="js_enable" <?php echo array_key_exists("js_enable", $config) && $config["js_enable"]==1?'checked':'' ?> value="1">
  			<label for="js_enable">Nén file Js</label>
		</div>
		<div class="col-xs-12 col-md-3">
			<button type="submit">Lưu</button>
		</div>
		</form>
	</div>
</div>
</div>
<script type="text/javascript">
	$(".compress_static input[type=checkbox]").change(function(event) {
		var json = {};
		json.css_enable = $("#css_enable").is(":checked")?1:0;
		json.css_inline = $("#css_inline").is(":checked")?1:0;
		json.js_enable = $("#js_enable").is(":checked")?1:0;
		$(".compress_static textarea[name=config]").val(JSON.stringify(json));
	});
</script>
<style type="text/css">
	.compress_static input[type=checkbox] + label {
  display: block;
  margin: 0.2em;
  cursor: pointer;
  padding: 0.2em;
}

.compress_static input[type=checkbox] {
  display: none;
}

.compress_static input[type=checkbox] + label:before {
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

.compress_static input[type=checkbox] + label:active:before {
  transform: scale(0);
}

.compress_static input[type=checkbox]:checked + label:before {
  background-color: MediumSeaGreen;
  border-color: MediumSeaGreen;
  color: #fff;
}

.compress_static input[type=checkbox]:disabled + label:before {
  transform: scale(1);
  border-color: #aaa;
}

.compress_static input[type=checkbox]:checked:disabled + label:before {
  transform: scale(1);
  background-color: #bfb;
  border-color: #bfb;
}
</style>