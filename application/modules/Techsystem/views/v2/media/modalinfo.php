<input type="hidden" value="<?php echo $file["id"]; ?>" name="id">
	<div class="row">
		<div class="col-md-6 col-xs-12 form-group">
			<label for="">Title</label>
			<input name="title" type="text" class="form-control" value="<?php echo $file["title"] ?>" placeholder="Title">
		</div>
		<div class="col-md-6 col-xs-12 form-group">
			<label for="">Caption</label>
			<input name="caption" type="text" class="form-control" value="<?php echo $file["caption"] ?>" placeholder="Caption">
		</div>
		<div class="col-md-6 col-xs-12 form-group">
			<label for="">Alt</label>
			<input name="alt" type="text" class="form-control" value="<?php echo $file["alt"] ?>" placeholder="Alt">
		</div>
		<div class="col-md-6 col-xs-12 form-group">
			<label for="">Description</label>
			<input name="description" type="text" class="form-control" value="<?php echo $file["description"] ?>" placeholder="Description">
		</div>
	</div>
