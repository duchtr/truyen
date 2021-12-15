	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field) ?>
		</div>
		<div class="col-md-10 col-xs-12">
			<input value="<?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?>" type="text" name="<?php echo $field['name'] ?>">
			<div class="col-xs-12 padding0 boxlink">
				<?php $arrTablesCan =$this->Admindao->getDataInTable("",'nuy_table', array(array('key'=>'showinmenu','compare'=>'=','value'=>'1'),array('key'=>'act','compare'=>'=','value'=>'1')),"","", ""); ?>
				<?php foreach ($arrTablesCan as $tc) {
					$idparent = (array_key_exists('table_parent', $tc) && !isNull($tc['table_parent']) && array_key_exists('table_child', $tc) && !isNull($tc['table_child'])) ? 0:'-1';
					$arrRecur = $this->Admindao->recursiveTable("*",$tc['name'],'parent','id',$idparent,""); 
					?>
					<div class="col-xs-12 col-md-6 col-lg-4 padding0">
						<label style="display: block;margin: 0px;" for=""><?php echo $tc['note'] ?></label>
						<select class="select2 chooselink<?php echo $field['name'] ?> <?php echo $tc['name'] ?>">
							<option value=""><?php echo alang("CHOOSE") ?></option>
							<?php printRecursiveSelectWithTag(0,$arrRecur,"-1"); ?>
						</select>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<style type="text/css">
	.boxlink{
		max-width: 80%;
	    border: 1px solid;
	    margin: 10px 0px;
	    padding: 5px;
	}
	</style>
	<script type="text/javascript">
	$(function() {
		var val = $('input[name=<?php echo $field['name'] ?>]').val();
		$('select.chooselink<?php echo $field['name'] ?>').change(function(event) {
			var slug = $(this).find('option:selected').attr('dt-slug');
			$('input[name=<?php echo $field['name'] ?>]').val(slug);
		});
		var select = $('select.select2').select2();
	});
	</script>