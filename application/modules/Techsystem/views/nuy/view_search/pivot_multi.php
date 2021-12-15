<?php 
$defaultData = $value['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = $defaultData['config'];
		?>
<script type="text/javascript">
	$(function() {
		  var select = $('#select2<?php echo $value['name'].(@$is_dialog?$is_dialog:"") ?>').select2({
		  	placeholder: "<?php echo $value['note'] ?>",
		  	allowClear: true,
		    closeOnSelect:false,
		    <?php if(array_key_exists('searchbox',$config) && $config['searchbox']!=1){
		    		echo "minimumResultsForSearch: Infinity";	
		    } ?>
		    
		  });
	});
</script>
<?php 
	$gtsearch = (isset($datasearch) && array_key_exists("search_".$value['name'], $datasearch))?$datasearch["search_".$value['name']] : "";
	$gtsearch = explode(',', $gtsearch);
	 ?>
<div class="col-md-4 col-xs-12 itemsearch" >
	<span><?php echo __('note',$value) ?>: </span>
	<select name="<?php echo "search_".$value['name'] ?>" <?php echo (array_key_exists("multiplie", $config) && $config['multiplie']==1)?"multiple='multiple'":"" ?> class="form-control" id="select2<?php echo $value['name'].(@$is_dialog?$is_dialog:"") ?>">
		<?php 
		if($source==="static"){
			
		}
		else if($source==="database"){
			$values = $defaultData['data']['value'];
			$input = array_key_exists('select', $values) ?$values['select']:"";
			$table = array_key_exists('table', $values) ?$values['table']:"";
			$field = array_key_exists('field', $values) ?$values['field']:"";
			$basefield = array_key_exists('base_field', $values) ?$values['base_field']:"";
			$where = array_key_exists('where', $values) ?$values['where']:"";
			$fieldValue =array_key_exists('field_value', $values) ?$values['field_value']:"";
			$w = array();
			foreach ($where as $itemwhere) {
				foreach ($itemwhere as $swhere =>$svalue) {
					$w[$swhere]= $svalue;
				}
			}
			$arr = $this->Admindao->recursiveTable($input,$table,$field,$basefield,$fieldValue,$w);
			echo '<option value="-1">Tất cả</option>';
			printRecursiveSelect(0,$arr,(count($gtsearch)>0?$gtsearch[0]:0));
		}
		?>
	</select>
</div>