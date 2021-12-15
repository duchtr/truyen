<div id="folder<?php echo $info['id'] ?>" class="col-md-2 col-sm-2 col-xs-4 media-it fold fileitem pad-2" data-file='<?php echo $info["extra"] ?>'>
	<div class="media-item">
		<div class="dp-table">
			<a class="mdi-img" title="" href="<?php echo (isset($trash) && $trash == 1)?'jascript:void(0)': getLinkForDir($info['id']) ?>">
				<span class="folder"><span></span></span>
			</a>
		</div>
		<?php if(isset($trash) && $trash == 1): ?>
			<div class="mdi-btn clearfix">
				<a onclick="MediaManager.restore(this);return false;" dt-id="<?php echo $info['id'] ?>" href="#" data-toggle="tooltip" title="Khôi phục file"><i class="fa fa-backward"></i></a>
				<a href="#" dt-id="<?php echo $info["id"] ?>" onclick="MediaManager.deleteFolderPermanent(this);return false;" data-toggle="tooltip" title="Xóa"><i class="fa fa-trash-o"></i></a>
			</div>
		<?php else: ?>
			<div class="mdi-btn clearfix">
				<a class="name-edit" href="#" data-toggle="tooltip" title="Đổi tên"><i class="fa fa-pencil"></i></a>
				<a href="#" dt-id="<?php echo $info["id"] ?>" onclick="MediaManager.deleteFolder(this);return false;" data-toggle="tooltip" title="Xóa"><i class="fa fa-trash-o"></i></a>
			</div>
		<?php endif; ?>
		<div class="mdi-title"><?php echo $info["name"] ?></div>
		<span class="mdi-date"><?php echo date("d/m/Y h:i:s",$info["create_time"]); ?></span>
		<span class="mdi-size"></span>
	</div>
</div>