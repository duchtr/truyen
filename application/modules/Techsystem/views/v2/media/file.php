<div id="file<?php echo $info['id'] ?>" class="col-md-2 col-sm-2 col-xs-4 media-it file fileitem  pad-2" data-file='<?php echo json_encode($info) ?>'>
<?php $extra = json_decode($info["extra"],true); ?>
	<div class="media-item">
		<div class="dp-table">
			<a class="mdi-img" title="" href="<?php echo (isset($trash) && $trash == 1)?'javascript:void(0)': $info["path"].$info["file_name"] ?>" rel="mdi">
				<img class="lazy" src="<?php echo $extra["thumb"] ?>" alt="">
			</a>
		</div>
		<div class="mdi-check">
			<label><input class="selectfile" value="<?php echo $info['id'] ?>" type="checkbox"><i class="fa fa-check-square-o"></i></label>
		</div>
		<?php if(isset($trash) && $trash ==1): ?>
			<div class="mdi-btn clearfix">
				<a onclick="MediaManager.restore(this);return false;" dt-id="<?php echo $info['id'] ?>" href="#" data-toggle="tooltip" title="Khôi phục file"><i class="fa fa-backward"></i></a>
				<a href="#" dt-id="<?php echo $info['id'] ?>" onclick="MediaManager.deleteFilePermanent(this);return false;" data-toggle="tooltip" title="Xóa vĩnh viễn"><i class="fa fa-trash-o"></i></a>
			</div>
		<?php else: ?>
			<div class="mdi-btn clearfix">
				<a download href="<?php echo $info["path"].$info["file_name"] ?>" data-toggle="tooltip" title="Tải về"><i class="fa fa-download"></i></a>
				<a href="<?php echo $info["path"].$info["file_name"] ?>" rel="gallery-box" class="preview"><i class="fa fa-eye"></i></a>
				<a class="name-edit" dt-id="<?php echo $info['id'] ?>" href="#" data-toggle="tooltip" title="Đổi tên"><i class="fa fa-pencil"></i></a>
				<a onclick="MediaManager.getDetailFile(<?php echo $info['id'] ?>);return false;" dt-id="<?php echo $info['id'] ?>" href="#" data-toggle="tooltip" title="Thông tin file"><i class="fa fa-info-circle"></i></a>
				<a href="#" dt-id="<?php echo $info['id'] ?>" onclick="MediaManager.deleteFile(this);return false;" data-toggle="tooltip" title="Xóa"><i class="fa fa-trash-o"></i></a>
			</div>
		<?php endif; ?>
		<p class="mdi-title"><?php echo substr($info["name"],0,strrpos($info["name"],".")) ?></p>	
		<span class="mdi-date"><?php echo date("m/d/Y h:i:s",$info["create_time"]); ?></span>
		<span class="mdi-size"><?php echo $extra["size"] ?></span>
	</div>
</div>