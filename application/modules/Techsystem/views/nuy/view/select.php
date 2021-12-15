<td data-title="<?php echo __('note',$currentvalue) ?>">
<?php 
$defaultData = $currentvalue['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = $defaultData['config'];
if(!@$config)return;
if(array_key_exists('type', $config) && $config['type']=="simple"){
	?>
<select data-primary='<?php echo json_encode($primarykey) ?>' name="<?php echo $currentvalue['name'] ?>" <?php echo (array_key_exists("multiplie", $config) && $config['multiplie']==1)?"multiple='multiple'":"" ?> class="form-control <?php echo $currentvalue['editable']==1?'editable':'' ?>" id="<?php echo @$typeextra?$typeextra:""; ?>select2<?php echo $value['name'].(@$is_dialog?$is_dialog:"") ?>">
				<?php 
				$valueDb = $currentitem[$currentvalue['name']];
				$valueDb = explode(',', $valueDb);
				if($source==="static"){
					$values = $defaultData['data']['value'];
					foreach ($values as $value) {
						foreach ($value as $k =>$v) {
							echo "<option ".(in_array($k, $valueDb)?"selected":"")." value='".$k."'>".$v."</option>";
						}
						
					}
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
					
					printRecursiveSelect(0,$arr, (count($valueDb)>0?$valueDb[0]:0));
				}
				?>
			</select>
<?php
}
else{
if($source==="database"){
?>
	<?php 
	$valuesFromDefaultData = $defaultData['data']['value'];
	$input = $defaultData['data']['value']['select'];
	$inputs = array_filter(explode(',', $input));
	$tableFromDefaultData = array_key_exists('table', $valuesFromDefaultData) ?$valuesFromDefaultData['table']:"";
	$arr =$this->Admindao-> getDataInTable("",$tableFromDefaultData, array(
	array('key'=>'FIND_IN_SET(id,"'.$currentitem[$currentvalue['name']].'")','compare'=>'>','value'=>0)
	) ,"","", "");
	if(count($arr)>0){
		foreach ($arr as $key => $value) {
			echo "<a onclick='changeSearchIfExist".$currentvalue['name'].$currentitem['id']."(".$value['id'].");return false;' style='  background-color: #E2E1E1;padding: 3px;border-radius: 5px;margin: 2px;display: block;text-align: center;margin-bottom: 5px;width: 100%;' href='#'>".(in_array('name', $inputs) ? $value['name'] : (!empty($inputs[1]) ? $value[$inputs[1]] : ''))."</a>";
		
		} ?>
			<script type="text/javascript">
				function changeSearchIfExist<?php echo $currentvalue['name'].$currentitem['id'] ?>($value){
					var item = $(".itemsearch #select2<?php echo $currentvalue['name'] ?>");
					if(item!=undefined && $(item)){
						$(item).select2('val',$value);	
					}
					
				}
			</script>
	<?php }
	?>
<?php  } else{
	$value = $currentitem[$currentvalue['name']];
	$values = explode(',', $value);
		foreach ($values as $v) {
			$jsons = $defaultData['data']['value'];
			$nameshow= $v;
			foreach ($jsons as $json) {
				$br = false;
				foreach ($json as $j => $p) {
					if($j == $v)
					{
						$nameshow = $p;
						$br = true;
						break;
					}
				}
				if($br) break;
			}
			echo "<a onclick='changeSearchIfExist".$currentvalue['name'].$currentitem['id']."(".$v.");return false;' style='  background-color: #E2E1E1;padding: 3px;border-radius: 5px;margin: 2px;display: block;text-align: center;margin-bottom: 5px;width: 100%;' href='#'>".$nameshow."</a>";	
		}
		
	?>
	<script type="text/javascript">
		function changeSearchIfExist<?php echo $currentvalue['name'].$currentitem['id'] ?>($value){
			var item = $(".itemsearch #select2<?php echo $currentvalue['name'] ?>");
			if(item!=undefined && $(item)){
				$(item).select2('val',$value);	
			}
			
		}
	</script>
<?php
} } ?>
</td>