<?php 
$value = $field;
$defaultData = $value['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = $defaultData['config'];
$currentTable = $table['name'];
		?>
<?php 
$valueDb = isset($dataitem)? $dataitem[$field['name']]:"";

 ?>
<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<span><?php echo __('note',$field); ?>: </span>
	</div>
	<div class="col-md-10 col-xs-12">
		
		<textarea name="<?php echo $field['name'] ?>" class="hidden"><?php echo $valueDb; ?></textarea>
		<input type="text" class="search<?php echo $field['name'] ?>" style="max-width: 500px" placeholder="Gõ để tìm kiếm">
		<button type="button" class="hidden btnadmin choose<?php echo $field['name'] ?>">Bỏ chọn</button>
		<ul class="listitem multiselect padding0 scrollbar listitem<?php echo $field['name'] ?>">
			<?php 
				
				if($source==="static"){
					
				}
				else if($source==="database"){
					$valueDb = json_decode($valueDb,true);

					$valueDb = @$valueDb?$valueDb:[];
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
					$valueput = array_key_exists("data", $valueDb)?$valueDb["data"]:"";
					printRecursiveMultiSelect(0,$arr,$valueput);
				}
			 ?>
		</ul>
		<script type="text/javascript">
			$(function() {
				function build<?php echo $field["name"] ?>(){
					var tableParent ='<?php echo $table ?>';
					var currentTable ='<?php echo $currentTable ?>';
					var arr = $('.listitem<?php echo $field["name"] ?> li input:checked');
					$('.listitem<?php echo $field["name"] ?> li').removeClass("choose");
					var obj ={};
					obj.data = [];
					for (var i = 0; i < arr.length; i++) {
						var item = arr[i];
						$(item).closest('li').addClass('choose');
						obj.data.push($(item).val());
					}
					obj.currentTable = currentTable;
					obj.tableParent = tableParent;
					return JSON.stringify(obj);
				}
				if($('textarea[name=<?php echo $field["name"] ?>]').val().length>0){
					$(".choose<?php echo $field["name"] ?>").removeClass('hidden');
				}
				else{
					$(".choose<?php echo $field["name"] ?>").addClass('hidden');
				}
				$('body').on('click', '.listitem<?php echo $field["name"] ?> li input', function(event) {
					var str = build<?php echo $field["name"] ?>();
					$('textarea[name=<?php echo $field["name"] ?>]').val(str);
					if(str.length>0){
						$(".choose<?php echo $field["name"] ?>").removeClass('hidden');
					}
					else{
						$(".choose<?php echo $field["name"] ?>").addClass('hidden');
					}
				});
				$('.choose<?php echo $field['name'] ?>').click(function(event) {
					event.preventDefault();
					var arr = $('.listitem<?php echo $field["name"] ?> li input').prop("checked",false);
					$('.listitem<?php echo $field["name"] ?> li').removeClass("choose");
					$('textarea[name=<?php echo $field["name"] ?>]').val('');
					$(this).addClass('hidden');
				});
				$('body').on('input', '.search<?php echo $field['name'] ?>', function(event) {
					event.preventDefault();
					var val = $(this).val().toLowerCase();
					if(val ==""){
						$(this).next().find("li").show();	
					}
					else{
						var lis = $(this).next().find("li");
						for (var i = 0; i < lis.length; i++) {
							var li = $(lis[i]);
							var text = li.text().toLowerCase();
							if(text.indexOf(val)!=-1){
								li.show();
							}
							else{
								li.hide();
							}
						}
					}
					
				});
			});
		</script>
	</div>
</div>
<style type="text/css">
	.listitem<?php echo $field["name"] ?> li.choose{
		    background: #d9d9d9ab;
	}
</style>