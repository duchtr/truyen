<?php 
$value = $field;
$defaultData = $value['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = $defaultData['config'];
		?>
<?php 
$valueDb = isset($dataitem)? $dataitem[$field['name']]:"";
$settingsajax = $defaultData['data']['value'];
 ?>
<div class="row margin0">
	<div class="col-md-2 col-xs-12">
		<span><?php echo __('note',$field); ?>: </span>
	</div>
	<div class="col-md-10 col-xs-12">
		<input value="<?php echo $valueDb; ?>" name="<?php echo $field['name'] ?>" type="hidden"/>
		<div class="search-multi-ajax search-multi-ajax-<?php echo $field['name'] ?>">
			<input type="text" placeholder="Gõ từ khóa tìm kiếm <?php echo $field['note'] ?>">
			<div class="show-result-multi-ajax" style="display: none">
			<ul class="" style="">
			</ul>
			<div class="multi-ajax-control">
				<button onclick="$(this).parent().parent().slideUp();" type="button"><?php echo alang("CLOSE") ?></button>
			</div>
			</div>
		</div>
		<ul style="max-width: 80%" class="listitem multiselect padding0 scrollbar listitem<?php echo $field['name'] ?>">
			<?php 
				if($source==="static"){
					
				}
				else if($source==="database"){
					$values = $defaultData['data']['value'];
					$input = array_key_exists('select', $values) ?$values['select']:"";
					$table = array_key_exists('table', $values) ?$values['table']:"";
					if($valueDb!=""){
						$where = array(array("key"=>" id ","compare"=>" in ","value"=>"(".$valueDb.")"));	
						$arr = $this->Admindao->getDataInTable($input,$table, $where,"","", "order by name asc");
						foreach ($arr as $k => $v) {
							echo "<li><label><input checked type='checkbox' value='".$v["id"]."'/>".$v["name"]."</label></li>";
						}
					}
				}
			 ?>
		</ul>
		<style type="text/css">
			.search-multi-ajax{
			    position: relative;
			}
			.search-multi-ajax .show-result-multi-ajax{
				position: absolute;
			    background: #efefef;
			    width: 80%;
			    padding: 5px 0px;
			    z-index: 98;
			}
			.search-multi-ajax .show-result-multi-ajax ul{
				padding:0;
				    max-height: 200px;
    overflow-y: scroll;
			}
			.search-multi-ajax .show-result-multi-ajax li{
				cursor: pointer;
				background: #efefef;
    			padding: 2px 0px 2px 10px;
			}
			.search-multi-ajax .show-result-multi-ajax li label{
				display: block;
			}
			.search-multi-ajax .show-result-multi-ajax input[type=checkbox]{
				width: auto !important;
				margin-right: 5px;
			}
			.search-multi-ajax .show-result-multi-ajax li:hover{
				background: #00923f;
				color:#fff;
			}
			.search-multi-ajax .multi-ajax-control{
				    border-top: 1px solid #ccc;
    padding-top: 5px;
			}
			.search-multi-ajax .multi-ajax-control button{
				float: right;
    margin-right: 5px;
    background: #00923f;
    color: #fff;
    border: none;
    padding: 3px 10px;
			}
		</style>
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
				$(document).on('input', '.search-multi-ajax-<?php echo $field['name'] ?> input', function(event) {
					event.preventDefault();
					var val = $(this).val();
					if(val!=undefined && val.length>1){
						$.ajax({
							url: 'Techsystem/ajx_select2_multi_search',
							type: 'POST',
							dataType: 'json',
							data: {source: '<?php echo $source ?>',q:val,settings:'<?php echo json_encode($settingsajax) ?>'},
						})
						.done(function(data) {
							var items = data.items;
							var str ="";
							for (var i = 0; i < items.length; i++) {
								var item = items[i];
								str+= "<li><label><input type='checkbox' value='"+item.id+"'/>"+item.name+"</label></li>";
							}
							$(".search-multi-ajax-<?php echo $field['name'] ?> .show-result-multi-ajax ul").html(str);
							
						});
						
					}
				});
				$(document).on('focus', '.search-multi-ajax-<?php echo $field['name'] ?> input', function(event) {
					event.preventDefault();
					$(".search-multi-ajax-<?php echo $field['name'] ?> .show-result-multi-ajax").slideDown();
				});
				$(document).on('click', ".search-multi-ajax-<?php echo $field['name'] ?> .show-result-multi-ajax ul li input[type=checkbox]", function(event) {
					
					var str= "<li><label><input type='checkbox' value='"+$(this).val()+"'/>"+$(this).parent().text()+"</label></li>";
					if($('.listitem<?php echo $field["name"] ?> input[value='+$(this).val()+']').length==0){
						$('.listitem<?php echo $field["name"] ?> .mCSB_container').append(str);
						
					}
					$('.listitem<?php echo $field["name"] ?> input[value='+$(this).val()+']').trigger("click");
					
				});
			});
		</script>
	</div>
</div>