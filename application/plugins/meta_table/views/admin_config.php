<?php 
$listTables = $this->Admindao->selectAllTableCanInsert();
?>

<div class="meta-box row">
	<div class="col-md-6 col-xs-12">
		<form onsubmit = 'createMetaTable(this);return false;' action="Techsystem/extra?action=<?php echo base64_encode("table=news&action=view&code=create_meta") ?>" method="POST">
			
		<table class="meta_table">
			<tr>
				<td>Chọn bảng</td>
				<td>
				<select name="table">
					<?php foreach($listTables as $table): ?>
						<option data-comment = "<?php echo $table['comment'] ?>" value="<?php echo $table['name'] ?>"><?php echo $table['name'] ?></option>
					<?php endforeach ?>
				</select>					
				</td>
			</tr>
			<tr>
				<td>Tên hiển thị</td>
				<td><input type="text" name="name"></td>
			</tr>
			<tr>
				<td>Icon hiển thị</td>
				<td><input type="text" name="icon" value="icon-camera"></td>
			</tr>
			<tr>
				<td>Vị trí hiển thị</td>
				<td>
					<select name="position">
						<?php 
	                    $arrgrmodule = $this->Admindao->getDataInTable("",'nuy_group_module', array(array('key'=>'parent','compare'=>'=','value'=>'0')),"","", "");
	                    foreach ($arrgrmodule as $key => $value) {
	                     ?> 
						<option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Bảng cha</td>
				<td><input type="text" name="table_parent" value=""></td>
			</tr>
			<tr>
				<td>Bảng con</td>
				<td><input type="text" name="table_child" value=""></td>
			</tr>
			<tr>
				<td></td>
				<td><button >Tạo File</button></td>
			</tr>
		</table>
		</form>

	</div>
	<div class="col-md-6 col-xs-12">
		<textarea class="file_content">
			
		</textarea>
	</div>
</div>
<script type="text/javascript">
	function createMetaTable(_this){
		$.ajax({
			url: $(_this).attr('action'),
			type: 'POST',
			dataType: 'HTML',
			data:$(_this).serialize()
		})
		.done(function(data) {
			$('.file_content').val(data);
		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
		
	}
	$(function() {
		$('select[name=table]').change(function(event) {
			$("input[name=name]").val($(this).find("option:selected").data('comment'));
		});
	});
</script>


<style type="text/css">
.meta-box{
	min-height: 100vh;
}
.file_content{
	width: 100%;
	min-height: 100vh;
}
	table.meta_table{
		    width: 100%;
    border-collapse: collapse;
    border: 1px solid #ccc;
    padding: 5px;
	}
	table.meta_table td{
		width: 50%;
		border-collapse: collapse;
	    border: 1px solid #ccc;
	    padding: 5px;
	}

</style>