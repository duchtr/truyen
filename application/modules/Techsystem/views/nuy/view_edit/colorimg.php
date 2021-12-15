	<div class="row margin0">
		<div class="col-md-2 col-xs-12">
			<?php echo __('note',$field) ?>
		</div>
		<div class="col-md-10 col-xs-12 colorimg">
			<div class="row listclimg">
				<?php 
					$nameField=$field['name'];
					$_value = isset($dataitem)? $dataitem[$nameField]:'';
					$arrClImgs = json_decode($_value,true);
					$arrClImgs = is_null($arrClImgs)?array():$arrClImgs;
					for ($i=0;$i<count($arrClImgs);$i++):
					$climgs = $arrClImgs[$i];
				?>
				<div class="col-md-6 col-xs-12 itemclimg">
					<?php $nameinputcolor =  $nameField .'-color-'.$i; ?>
					<input onchange="onchangeInputColor<?php echo $nameField ?>(this)" type="hidden" value="<?php echo  $climgs['icon'] ?>"  id="<?php echo $nameinputcolor ?>" class="input-color">
					<img  src="<?php echo $climgs['icon'] ?>" alt="" class="<?php echo $nameinputcolor ?> img-color">
					<div class="btnadmin">
			    		<a href="<?php echo base_url() ?>Techsystem/mediaManager?istiny=<?php echo $nameinputcolor ?>" class="btn iframe-btn" type="button">Browse File ...</a>
			    	</div>
			    	<?php $nameinputimage =  $nameField .'-image-'.$i; ?>
					<input onchange="onchangeInputColor<?php echo $nameField ?>(this)" type="hidden" value="<?php echo  $climgs['image'] ?>"  id="<?php echo $nameinputimage ?>" class="input-image">
					<img  src="<?php echo  $climgs['image'] ?>" alt="" class="<?php echo $nameinputimage ?> img-pro">
					<div class="btnadmin">
			    		<a href="<?php echo base_url() ?>Techsystem/mediaManager?istiny=<?php echo $nameinputimage ?>" class="btn iframe-btn" type="button">Browse File ...</a>
			    	</div>
			    	<button class="btnClose" onclick="removeclImg<?php echo $nameField ?>(this)" style="position: absolute;right: 5px;top: 5px;width: 30px;height: 30px;background: none;border: 1px solid #ccc;border-radius: 50%;font-size: 20px;
">
						<i class="icon-remove"></i>
					</button>
				</div>
				<?php endfor; ?>
				
			</div>
			<div class="row textcenter btnaddcl">
				<button onclick="addClImg<?php echo $nameField ?>();return false;" class="addClImg">Thêm màu xe</button>
			</div>
			<input name="<?php echo $nameField ?>" type="hidden" value='<?php echo $_value; ?>'/>
		</div>
	</div>
	<script type="text/javascript">
		function onchangeInputColor<?php echo $nameField ?>(_this){
			getclimgJson<?php echo $nameField ?>();
		}
		function getclimgJson<?php echo $nameField ?>(){
			var arr = $('.colorimg .itemclimg');
			var ret = [];
			for (var i = 0; i < arr.length; i++) {
				var item = arr[i];
				var tmp = {};
				tmp.icon = $(item).find('input.input-color').val();
				tmp.image = $(item).find('input.input-image').val();
				ret.push(tmp);
			}
			$('input[name=<?php echo $nameField ?>]').val(JSON.stringify(ret));
		}
		function addClImg<?php echo $nameField ?>(){
			var i = $('.itemclimg').length+1;
			var str = '<div class="col-md-6 col-xs-12 itemclimg">';
			var nameinputcolor =  <?php echo "'".$nameField."'" ?> +'-color-'+i;
			str +='<input onchange="onchangeInputColor<?php echo $nameField ?>(this)" type="hidden" value="theme/admin/images/noimage.png"  id="'+nameinputcolor+'" class="input-color">';
			str +='<img  src="theme/admin/images/noimage.png" alt="" class="'+nameinputcolor+' img-color">';
			str +='<div class="btnadmin">';
			str +='<a href="<?php echo base_url() ?>Techsystem/mediaManager?istiny='+nameinputcolor+'" class="btn iframe-btn" type="button">Browse File ...</a>';
			str +='</div>';
			var nameinputimage = <?php echo "'".$nameField."'" ?>+'-image-'+i;
			str +='<input onchange="onchangeInputColor<?php echo $nameField ?>(this)" type="hidden" value="theme/admin/images/noimage.png"  id="'+nameinputimage+'" class="input-image">';
			str +='<img  src="theme/admin/images/noimage.png" alt="" class="'+nameinputimage+' img-pro">';
			str +='<div class="btnadmin">';
			str +='<a href="<?php echo base_url() ?>Techsystem/mediaManager?istiny='+nameinputimage+'" class="btn iframe-btn" type="button">Browse File ...</a>';
			str +='</div>';
			str +='<button class="btnClose" onclick="removeclImg<?php echo $nameField ?>(this)" style="position: absolute;right: 5px;top: 5px;width: 30px;height: 30px;background: none;border: 1px solid #ccc;border-radius: 50%;font-size: 20px;">';
			str +='<i class="icon-remove"></i>';
			str +='</button>';
			str +='</div>';
			$('.colorimg .listclimg').append(str);
			getclimgJson<?php echo $nameField ?>();
		}
		function removeclImg<?php echo $nameField ?>(_this){
			$(_this).parent().remove();	
			getclimgJson<?php echo $nameField ?>();
		}
	</script>
	<style type="text/css">
		.colorimg .img-color{
			width:26px;
			height: 26px;
		}
		.colorimg .img-pro{
			margin-left: 10px;
			height: 100px;
		}
		.colorimg >.row{
			border: 1px solid #ababab;
			    padding: 5px;
		}
		.colorimg .btnaddcl{
			    text-align: center;
    border: none;
		}
		.itemclimg{
			border: 1px solid #ececec;
		}
	</style>