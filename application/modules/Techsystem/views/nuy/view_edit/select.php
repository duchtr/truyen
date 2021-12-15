<!-- {
    "data": {
        "source": "static",
        "value": [
            {
                "1": "Có"
            },
            {
                "0": "Không"
            }
        ]
    },
    "config": {
        "searchbox": 0
    }
} -->
<!-- {
    "data": {
        "source": "database",
        "value": {
            "table": "test",
            "select": "id,name",
            "field": "parent",
            "base_field": "id",
            "where": [
                {
                    "1": "1"
                }
            ]
        }
    },
    "config": {
        "searchbox": 0
    }
} -->
<?php 
$value = $field;
$defaultData = $value['default_data'];
$defaultData = json_decode($defaultData,true);
$source = $defaultData['data']['source'];
$config = @$defaultData['config']?$defaultData['config']:array();
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
$valueDb = isset($dataitem)? $dataitem[$field['name']]:"";
 ?>
	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<span><?php echo __('note',$field); ?>: </span>
		</div>
		<div class="col-md-10 col-xs-12">
			<select name="<?php echo $value['name']?>" <?php echo (array_key_exists("multiplie", $config) && $config['multiplie']==1)?"multiple='multiple'":"" ?> class="form-control" id="<?php echo @$typeextra?$typeextra:""; ?>select2<?php echo $value['name'].(@$is_dialog?$is_dialog:"") ?>">
				<option value="0">Không xác định</option>
				<?php 
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
		</div>
</div>