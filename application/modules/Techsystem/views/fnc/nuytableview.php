<?php //die("Tính năng này bị hủy bỏ, vui lòng sử dụng qua Plugin!"); ?>
<style type="text/css">
   p{
      margin:0px;
   }
   div input,div select{
      width: 100%;
   }
</style>
<div id="cph_Main_ContentPane">
   <div class="widget row margin0">
      <div class="widget-title">
       
         <h4>
            <i class="icon-qrcode"></i>Thêm nhanh bảng
         </h4>
        
         <div id="hiddenToolBarScroll" class="scrollBox" style="display:none">
            <h4>
               <i class="icon-qrcode"></i>Thêm nhanh bảng
            </h4>
            
         </div><div class="clr"></div>
      </div>
<form action="Techsystem/insertNuyTable" method="post">
      <div class="widget-body">
		<h4 style="text-align: center;"><?php echo @$extra?$extra:"" ?></h4>
		<?php       
			if(sizeof($lsttable)>0){
		?>
      	<table style="width: 100%;
  text-align: center;">
      		<tr style="background-color: #F2F2F2;
  line-height: 30px;
  font-size: 14px;">
      			<td>Chọn bảng</td>
      			<td>Chức năng</td>
      		</tr>
      		<tr>
      			<td style="width:50%;  padding: 10px 0px 0px 0px;"><select onchange="onSelectChange(this)" style="  width: 100%;" name="table">
      				<?php 
      					foreach ($lsttable as $item) {
      						?>
      						<option value="<?php echo $item['name'] ?>"> <?php echo @$item['comment']?$item['comment']:$item['name'] ; ?></option>
      						<?php 
      					}
      				?>
      			</select>
				<input type="hidden" name="table_name" value="<?php echo sizeof($lsttable)>0?$lsttable[0]['comment']:"" ?>" />
      		</td>
      			<td style="width:50%">
      				<input type="submit" value="Thêm mới">
      			</td>
      		</tr>
            <tr>
               <td>
               <div class="moreinfo">
                  <h3>Thông tin cấu hình bảng</h3>
                  <div class="col-md-6 col-xs-12">
                     <p>Tên bảng</p>
                     <input type="text" name="info_name" placeholder="Tên bảng" value="<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>">
                  </div>
                  <div class="col-md-6 col-xs-12">
                     <p>Tên hiển thị</p>
                     <input type="text" name="info_note" placeholder="Tên hiển thị" value="<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>">
                  </div>
                  <div class="col-md-6 col-xs-12">
                     <p>Bảng map</p>
                     <input type="text" name="info_map_table" placeholder="Bảng map" value="<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>">
                  </div>
                  <div class="col-md-6 col-xs-12">
                     <p>Kích hoạt</p>
                     <input type="text" name="info_act" placeholder="Kích hoạt" value="1">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Phân trang</p>
                  <input type="text" name="info_pagination" placeholder="Phân trang" value="1">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Bảng cha</p>
                     <select name="info_table_parent">
                        <option value="">Không có</option>
                        <?php foreach ($lsttable as $item) { ?>
                           <option value="<?php echo $item['name'] ?>"><?php echo $item['name'] ?></option>
                        <?php } ?>
                        
                     </select>
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Bảng con</p>
                  <select name="info_table_child">
                     <option value="">Không có</option>
                        <?php foreach ($lsttable as $item) { ?>
                           <option value="<?php echo $item['name'] ?>"><?php echo $item['name'] ?></option>
                        <?php } ?>
                        
                     </select>
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Controller</p>
                  <input type="text" name="info_controller" value="<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>/view.php" placeholder="Controller">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Số bản ghi 1 trang - Admin</p>
                  <input type="text" name="info_rpp_admin" value="10" placeholder="Số bản ghi 1 trang - Admin">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Số bản ghi 1 trang - Frontend</p>
                  <input type="text" name="info_rpp_view" value="10" placeholder="Số bản ghi 1 trang - Frontend">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Loại</p>
                  <input type="text" name="info_type" value="1" placeholder="Loại">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có Insert</p>
                  <input type="text" name="info_insert" value="1" placeholder="Có Insert">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có Xóa</p>
                  <input type="text" name="info_delete" value="1"  placeholder="Có Xóa">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có sửa</p>
                  <input type="text" name="info_edit" value="1"  placeholder="Có sửa">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có thông tin trợ giúp</p>
                  <input type="text" name="info_help" value="1"  placeholder="Có thông tin trợ giúp">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có tìm kiếm</p>
                  <input type="text" name="info_search" value="1"  placeholder="Có tìm kiếm">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có copy</p>
                  <input type="text" name="info_copy" value="1"  placeholder="Có copy">
                  </div>
                   <div class="col-md-6 col-xs-12">
                     <p>Có thể thêm vào menu hiển thị</p>
                  <input type="text" name="info_showinmenu"  value="0"  placeholder="Có thể thêm vào menu hiển thị">
               </div>
               </div>
</td>
            <td>
                <h3>Thông tin cấu hình quyền</h3>
               <div class="col-md-6 col-xs-12">
                  <p>Tên bảng</p>
                  <input type="text" name="role_name" value="<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>" placeholder="Tên bảng">
               </div>
               <div class="col-md-6 col-xs-12">
                  <p>Tên hiển thị bảng</p>
                  <input type="text" name="role_note" value="<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>" placeholder="Tên hiển thị bảng">
               </div>
               <div class="col-md-6 col-xs-12">
                  <p>Link</p>
                  <input type="text" name="role_link" value="view/<?php echo count($lsttable)>0?$lsttable[0]['name']:"" ?>" placeholder="Link">
               </div><div class="col-md-6 col-xs-12">
                  <p>Cha</p>
                  <select name="role_parent">
                    <?php 
                    $arrgrmodule = $this->Admindao->getDataInTable("",'nuy_group_module', array(array('key'=>'parent','compare'=>'=','value'=>'0')),"","", "");
                    foreach ($arrgrmodule as $key => $value) {
                     ?> 
                    <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option> 
                    <?php }
                     ?>
                    
                  </select>
               </div>
            </td>
            </tr>
      	</table>
      	<?php }?>
      </div>
  </form>
      <script type="text/javascript">
      function onSelectChange(_this){
         var name = $(_this).find('option:selected').text();
      	 $('input[name=table_name]').val(name);
         
         $('input[name=info_note]').val(name);
         $('input[name=role_note]').val(name);
         
         $('input[name=info_name]').val($(_this).find('option:selected').val());
         $('input[name=role_name]').val($(_this).find('option:selected').val());
         $('input[name=role_link]').val('view/'+$(_this).find('option:selected').val());
         $('input[name=info_controller]').val($(_this).find('option:selected').val()+'/view.php');
         $('input[name=info_map_table]').val($(_this).find('option:selected').val());
      }
      </script>
   </div>
</div>