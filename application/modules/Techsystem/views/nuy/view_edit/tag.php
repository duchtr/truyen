<?php 
$value = $field;
$defaultData = $value['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = $defaultData['config'];
$values = $defaultData['data']['value'];
$table = array_key_exists('table', $values) ?$values['table']:"";
		?>
<?php 

$valueDb = isset($dataitem)? $dataitem[$field['name']]:"";

 ?>
<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<span><?php echo __('note',$field); ?>: </span>
	</div>
	<div class="col-md-10 col-xs-12">
		<input value="<?php echo $valueDb; ?>" name="<?php echo $field['name'] ?>" type="hidden"/>
		<div style="    min-width: 200px;max-width: 500px;display:flex">
			<input style="flex:1" class="search-<?php echo $field['name'] ?>" type="text" placeholder="Gõ từ cần tìm kiếm hoặc thêm mới"/>
			<a href="Techsystem/insertTag/<?php echo $table ?>" class="insert_tag"><i class="icon-plus"></i></a>
			<input type="hidden" value="<?php echo $table ?>.view">
		</div>
		
		<ul class="listitem multiselect padding0 scrollbar listitem<?php echo $field['name'] ?>">

			<?php 
				$valueDb = explode(',', $valueDb);
				if($source==="static"){
					$values = $defaultData['data']['value'];
					foreach ($values as $value) {
						foreach ($value as $k =>$v) {
							echo "<li><input type='checkbox' ".(in_array($k, $valueDb)?'checked':'')."  value='".$k."'/>".$v."</li>";
						}
						
					}
				}
				else if($source==="database"){
					$values = $defaultData['data']['value'];
					$input = array_key_exists('select', $values) ?$values['select']:"";
					$table = array_key_exists('table', $values) ?$values['table']:"";
					$fieldjson = array_key_exists('field', $values) ?$values['field']:"";
					$basefield = array_key_exists('base_field', $values) ?$values['base_field']:"";
					$where = array_key_exists('where', $values) ?$values['where']:"";
					$fieldValue =array_key_exists('field_value', $values) ?$values['field_value']:"";
					$w = array();
					foreach ($where as $itemwhere) {
						foreach ($itemwhere as $swhere =>$svalue) {
							$w[$swhere]= $svalue;
						}
					}
					$arr = $this->Admindao->recursiveTable($input,$table,$fieldjson,$basefield,$fieldValue,$w);
					$valueput = $valueDb;

					printRecursiveMultiSelect(0,$arr,$valueput);
				}
			 ?>

		</ul>
		<script type="text/javascript">
			$(function() {
				$('body').on('click', '.listitem<?php echo $field["name"] ?> li input', function(event) {
					var arr = $('.listitem<?php echo $field["name"] ?> li input:checked');
					var str = "";
					for (var i = 0; i < arr.length; i++) {
						var item = arr[i];
						str += $(item).val();
						if(i<arr.length-1){
							str+=",";
						}
					};
					$('input[name=<?php echo $field["name"] ?>]').val(str);
				});
				$('body').on('input', '.search-<?php echo $field['name'] ?>', function(event) {
					event.preventDefault();
					if($(this).val()==""){
						$('.listitem<?php echo $field["name"] ?> li').show();
						return;
					}
					var arr = $('.listitem<?php echo $field["name"] ?> li');
					for(var i =0;i<arr.length;i++){
						if($(arr[i]).text().indexOf($(this).val())==-1){
							$(arr[i]).hide();
						}
						else{
							$(arr[i]).show();
						}
					}
				});
			});
		</script>
	</div>
</div>