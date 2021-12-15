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
$settingsajax = $defaultData['data']['value'];
		?>
		<?php 
$valueDb = isset($dataitem)? $dataitem[$field['name']]:"";
 ?>
<script type="text/javascript">
	$(function() {
		
		  var select = $('#select2ajax<?php echo $value['name'].(@$is_dialog?$is_dialog:"") ?>').select2({
		  	placeholder: "<?php echo $value['note'] ?>",
		  	ajax: { 
		        url: "Techsystem/ajx_select2_search",
		        dataType: 'json',
		        type:'POST',
		        quietMillis: 250,
		        data: function (term, page) {
		            return {
		                q: term, 
		                source: '<?php echo $source ?>',
		                settings:'<?php echo json_encode($settingsajax) ?>'
		            };
		        },
		        results: function (data, page) { 
		        	return {
			            results: $.map(data.items, function (item) {
			                return {
			                    text: item.name,
			                    id: item.id
			                }
			            })
			        };
		        },
		        cache: true
		    },
		    initSelection : function (element, callback) {
		    	<?php 
		    	if($source=="database"){
					$values = $defaultData['data']['value'];
					$input = array_key_exists('select', $values) ?$values['select']:"";
					$table = array_key_exists('table', $values) ?$values['table']:"";
					if($valueDb!=""){
						$vldb = $this->Admindao->getDataInTable($input,$table, array(array("key"=>"id","compare"=>"=","value"=>$valueDb)),"","", "order by name asc");
						if(count($vldb)>0){
							echo "var data = {id: '".$vldb[0]['id']."', text: '".$vldb[0]['name']."'};";
							echo "callback(data);";
						}
					}
				}
		    	?>
		        
		    },
		  	allowClear: true,
		    closeOnSelect:false,
		    minimumResultsForSearch: 2,
		    minimumInputLength:2
		    
		  }).select2('val', []);;
	});
</script>
	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<span><?php echo __('note',$field); ?>: </span>
		</div>
		<div class="col-md-10 col-xs-12">
			<input name="<?php echo $value['name']?>" <?php echo (array_key_exists("multiplie", $config) && $config['multiplie']==1)?"multiple='multiple'":"" ?> class="form-control"  id="<?php echo @$typeextra?$typeextra:""; ?>select2ajax<?php echo $value['name'].(@$is_dialog?$is_dialog:"") ?>"/>
				
		</div>
</div>