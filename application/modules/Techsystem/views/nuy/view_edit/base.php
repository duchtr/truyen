	<div class="row margin0">

		<div class="col-md-2 col-xs-12">

			<?php echo __('note',$field) ?>

		</div>

		<div class="col-md-10 col-xs-12">

			<input value="<?php echo isset($dataitem)? $dataitem[$field['name']]:'' ?>" type="text" name="<?php echo $field['name'] ?>">

		</div>

	</div>