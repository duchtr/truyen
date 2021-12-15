<td data-title="<?php echo __('note',$currentvalue) ?>">
<?php 
$defaultData = $currentvalue['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = $defaultData['config'];
if(!@$config)return;
if($source=="database"){
	$valuesFromDefaultData = $defaultData['data']['value'];
	$tableFromDefaultData = array_key_exists('table', $valuesFromDefaultData) ?$valuesFromDefaultData['table']:"";
	$arr =$this->Admindao-> getDataInTable("",$tableFromDefaultData, array(
	array('key'=>'FIND_IN_SET(id,"'.$currentitem[$currentvalue['name']].'")','compare'=>'>','value'=>0)
	) ,"","", "");
	if(count($arr)>0){
		foreach ($arr as $key => $value) {
			echo "<a onclick='changeSearchIfExist".$currentvalue['name'].$currentitem['id']."(".$value['id'].");return false;' style='  background-color: #E2E1E1;padding: 3px;border-radius: 5px;margin: 2px;display: block;text-align: center;margin-bottom: 5px;width: 100%;' href='#'>".$value['name']."</a>";
		}
	}
} else {
	$value = $currentitem[$currentvalue['name']];
	$values = explode(',', $value);
	if(is_array($values) && @$value && @$value[0]){
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
	}
}	?>
</td>
