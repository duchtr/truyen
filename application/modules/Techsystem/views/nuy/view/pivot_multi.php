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
	Container::setData($tableFromDefaultData,function() use($tableFromDefaultData){
		$CI = &get_instance();
		$results = $CI->db->get($tableFromDefaultData)->result_array();
		$data = [];
		foreach ($results as $k => $item) {
			$data[$item['id']] = $item;
		}
		return $data;
	});
	
	$valueDb = $currentitem[$currentvalue['name']];
	$valueDb = json_decode($valueDb,true);
	$valueDb = @$valueDb ?$valueDb:[];
	$arr = array_key_exists("data", $valueDb)?$valueDb["data"]:[];
	if(count($arr)>0){
		foreach ($arr as $key => $value) {
			echo "<a onclick='changeSearchIfExist".$currentvalue['name'].$currentitem['id']."(".$value.");return false;' style='  background-color: #E2E1E1;padding: 3px;border-radius: 5px;margin: 2px;display: block;text-align: center;margin-bottom: 5px;width: 100%;' >".(Container::getSubItem($tableFromDefaultData,$value,'name'))."</a>";
		}
	}
}	?>
</td>
