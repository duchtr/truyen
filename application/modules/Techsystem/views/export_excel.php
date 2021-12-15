<?php
// We change the headers of the page so that the browser will know what sort of file is dealing with. Also, we will tell the browser it has to treat the file as an attachment which cannot be cached.
//header("Content-type: application/octet-stream");
header("Content-type: application/vnd.ms-excel;");
header("Content-Disposition: attachment; filename=".$filename.".xlsx");
header("Pragma: no-cache");
header("Expires: 0");
?>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head >
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      </head>
      <body>
<table border="1" class="table table-striped table-bordered dataTable table-hover" cellspacing="0" id="myTable" style="border-collapse:collapse;">
<tbody>
<tr>
    <th scope="col">
        STT
    </th>
    <?php 
    foreach ($titles as $key => $value) { ?>
        <th scope="col">
        <?php echo $value['note'] ?>
    </th>
    <?php }
     ?>
</tr>
<?php
//var_dump($list_data);
$i=1;
foreach($lstData as $vl) {
	
?>
	
    <tr class="row0">
		
		<td class="Code" align="center" style="width:80px;">
            <?php echo $i ?>
        </td>
        <?php 
            foreach ($titles as $key => $value) { ?>
                <td class="Code" align="center" style="width:280px;">
            <?php echom($vl,$value['name'],'1');  ?>
        </td>
            <?php }
             ?>
    </tr>
<?php
$i++;
}
?>
</tbody></table>
</body>
</html>