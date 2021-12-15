<?php 
function printRecursiveMenuAdmin($titles,$lv,$arrD,$value){
    $lv++;
    for ($i=0;$i<sizeof($arrD);$i++) {
        $sub = $arrD[$i];
        $item = $sub->item;
        $checked = (is_array($value) && in_array($item['id'], $value))?' checked ':'';
        echo '<li class="itemmenu dragable level'.$lv.'" style="margin-left:'.($lv*15).'px"><a><span>'.$item['name'].'</span><i class="remove icon-remove"></i><i class="icon-plus-sign openitemmenu"></i></a>';
        echo '<div class="detailitemmenu " style="display:none"> ';
       
        foreach ($titles as $field) {
            $lb= strtolower($field['type']);
            if($lb=='primarykey') continue;
             echo '<div class="infoitemmenu flex">';
            
            
            if($field['name']=='link'){
                echo '<span>'.$field['note'].'</span><input data-col="'.$field['name'].'" name="'.$field['name'].'_'.$item['id'].'" value="'.(array_key_exists('slug', $item)?$item['slug'].getConfigSite('URL_EXT',''):"").'"/>';
            }
            else  if($field['name']=='name'){
                echo '<span>'.$field['note'].'</span><input data-col="'.$field['name'].'" name="'.$field['name'].'_'.$item['id'].'" value="'.(array_key_exists('name', $item)?$item['name']:"").'"/>';
            }
            else{
                echo '<span>'.$field['note'].'</span><input data-col="'.$field['name'].'" name="'.$field['note'].'"/>';
            }
            echo '</div> ';
            
        }
        
        echo '</div>';
        echo '</li>';
        
        if(@$sub->childs){
            printRecursiveMenuAdmin($titles,$lv,$sub->childs,$value);
        }
    }
}
function getConfigSite($key,$def){
    $CI = &get_instance();
        $configs = $CI->session->userdata('siteconfigs');
        if(@$configs && array_key_exists($key, $configs)){
            return $configs[$key];
        }
        return $def;
    }
function printMenuExistDb($titles,$lv,$arrMenu){
    $lv++;
    for ($i=0; $i < count($arrMenu); $i++) { 
        $item = $arrMenu[$i]->item;
        echo '<li class="itemmenu ui-sortable-handle">';
        echo '  <a><span>'.$item['name'].'</span><i class="remove icon-remove"></i><i class="icon-plus-sign openitemmenu"></i></a>';
        echo '  <div class="detailitemmenu " style="display:none">';
        foreach ($titles as $key => $value) {
          echo '  <div class="infoitemmenu flex"><span>'.$value['note'].'</span>';
          echo '    <input data-col="'.$value['name'].'" name="'.$value['name'].'" value="'.$item[$value['name']].'">';
          echo '  </div>';
        }
        echo '  </div>';
        echo '<ul class="droppable ui-sortable">';
        printMenuExistDb($titles,$lv,$arrMenu[$i]->childs);
        echo '</ul>';
        echo '</li>';
    }
}
function printDataView6($lv,$obj,$lstData,$titles,$table){
    $lv++;
        for ($i=0; $i < count($lstData); $i++) { 
            $itemCha = $lstData[$i];
            $item = $itemCha->item;
            $primarykey = array();
        echo '<tr dtpad='.$lv.'>';
        echo '<td>'.$i.'</td>';
        echo '  <td><input id="" type="checkbox" class="cbone" dt-value="'.$item['id'].'" name="cb '.$item['id'].' " onclick=""></td>';
            
            foreach ($titles as $val) {
              
            
              $datai['value'] = $item[$val['name']];
              $typeControlView =strtolower($val['type']);
              if($typeControlView=='primarykey'){
                array_push($primarykey, array($val['name']=>$item[$val['name']]));
              }
              if(file_exists(realpath(dirname(__FILE__)).'/../views/nuy/view/'.$typeControlView.'.php')){
                $datasub['primarykey']= $primarykey;
                $datasub['currentitem']= $item;
                $datasub['currentvalue'] = $val;
                $obj->load->view('view/'.$typeControlView,$datasub);
              }
              else{
                $obj->load->view('view/base',$datai);
              }
              
            
            }
          
             echo '<td data-title="Chức năng" align="center" style="  text-align: center;vertical-align: middle;">';
             if(isset($item['slug']) && $item['slug'] != ''){
                echo '<a href="'.$item['slug'].'" target="_blank" class="edit fnc" title="Xem demo"><i class="icon-eye-open"></i></a>';
             }
                 if($table['insert']){ 
             echo '   <a href="Techsystem/copy/'.$table['name'].'/'.$item['id'].'" class="edit fnc" ><i class="icon-copy"></i></a>';
                 } 
                 if($table['edit']){ 
            echo '    <a href="Techsystem/edit/'.$table['name'].'/'.$item['id'].'" class="edit fnc" ><i class="icon-edit"></i></a>';
                 } 
                 if($table['delete']){ 
             echo '   <a href="#" dt-delete=\''.json_encode($primarykey).'\' onclick = "javascript:deleteItem(this);return false;" class="delete fnc"><i class="icon-trash"></i></a>';
                 } 
            echo ' </td> ';
        echo ' </tr>';
        printDataView6($lv,$obj,$itemCha->childs,$titles,$table);
   }
}
function recusiveDir($addpath ="theme/frontend"){
  $arr = dirToArray(FCPATH.$addpath);
  foreach ($arr as $key => $value) {
    echo "<li class='".($value->isDir?"dir":"file")."'>";
    echo "<i class='".($value->isDir?'icon-folder-close':'icon-file')."'></i>";
    echo "<i class='".(!$value->isDir && ($value->name=="styles.min.css" || $value->name=="scripts.min.js")?"icon-trash deletefile":"")."'></i>";
    echo "            <a data-path='".$addpath. '/'.$value->name."' >".$value->name."</a>";
    echo "              <ul>";
    recusiveDir($addpath. '/'.$value->name);
    echo "              </ul>";
    echo "          </li>";
    
  }
}
function dirToArray($dir) {    
   $result = array(); 
   if(!is_dir($dir)) return array();
   $cdir = scandir($dir); 
   foreach ($cdir as $key => $value) 
   { 
      if (!in_array($value,array(".",".."))) 
      { 
         if (is_dir($dir . '/' . $value)) 
         { 
            $obj = new stdClass();
            $obj->isDir = 1;
            $obj->name = $value;
            $obj ->childs  = dirToArray($dir . '/' . $value); 
            $result[$value] = $obj;
         } 
         else 
         { 
            if(strripos($value,'css') == strlen($value)-3 ||strripos($value,'php') == strlen($value)-3||strripos($value,'js') == strlen($value)-2){
              $obj = new stdClass();
              $obj->isDir = 0;
              $obj->name = $value;
              $result[$value] = $obj;
            }
         } 
      } 
   } 
   
   return $result; 
}
function human_filesize($bytes, $decimals = 2) {
  $sz = 'BKMGTP';
  $factor = floor((strlen($bytes) - 1) / 3);
  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . (" ".@$sz[$factor]."B");
}
function getMediaFile($filename,$file_path){
  $CI = & get_instance();
  $CI->load->config('filemanager');
  $extimgs = $CI->config->item('ext_img');
  $extvideos = $CI->config->item('ext_video');
  $extfiles = $CI->config->item('ext_file');
  $extmusic = $CI->config->item('ext_music');
  $pathuploads = $CI->config->item('path_uploads');
  $basepath = $CI->config->item('base_path');
  $obj = new stdClass();
  $obj->name = $filename;
  $obj->file = $file_path;
  $obj->extension = substr(strrchr($filename,'.'),1);
  $obj->size= human_filesize(filesize($file_path));
  $obj->date = filemtime($file_path);
  
  $obj->isfile = is_file($file_path)?1:0;
  $onlyDir =  substr($file_path,0, strrpos($file_path, "/")+1);
  $onlyDir = str_replace(FCPATH."/", "", $onlyDir);
  $obj->path = $onlyDir.$filename;
  if(in_array($obj->extension, $extimgs)){
    $imagedetails = getimagesize($file_path);
    $obj->width = $imagedetails[0];
    $obj->height= $imagedetails[1];
    if(file_exists(FCPATH.$onlyDir.'thumbs/'.$filename)){
      $obj->thumb = $basepath.$onlyDir.'thumbs/'.$filename;
    }
    else{
      $obj->thumb = $basepath.$onlyDir.$filename;
    }
  }
  else if(in_array($obj->extension, $extvideos)){
    if(file_exists(FCPATH."theme/admin/images/ico/".$obj->extension.".jpg")){
      $obj ->thumb = $basepath."theme/admin/images/ico/".$obj->extension.".jpg";
    }
    else{
      $obj->thumb = $basepath."theme/admin/images/noimage.png";
    }
  }
  else if(in_array($obj->extension, $extfiles)){
    if(file_exists(FCPATH."theme/admin/images/ico/".$obj->extension.".jpg")){
      $obj ->thumb = $basepath."theme/admin/images/ico/".$obj->extension.".jpg";
    }
    else{
      $obj->thumb = $basepath."theme/admin/images/noimage.png";
    }
  }
  else if(in_array($obj->extension, $extmusic)){
    if(file_exists(FCPATH."theme/admin/images/ico/".$obj->extension.".jpg")){
      $obj ->thumb = $basepath."theme/admin/images/ico/".$obj->extension.".jpg";
    }
    else{
      $obj->thumb = $basepath."theme/admin/images/noimage.png";
    }
  }
  else{
    $obj->thumb="NO";
  }
  return $obj;
}
function current_full_url()
{
    $CI =& get_instance();
    $url = $CI->config->site_url($CI->uri->uri_string());
    return $_SERVER['QUERY_STRING'] ? $url.'?'.$_SERVER['QUERY_STRING'] : $url;
}
function getParameter(){
  $url = parse_url($_SERVER['REQUEST_URI']);
  if(array_key_exists("query", $url)){
    parse_str($url['query'], $params);
    return $params;  
  }
  else return array();
  
}
function getLinkForDir($dir){
  $prs = getParameter();
  if(array_key_exists("folder", $prs)){
    $prs["folder"].=",".$dir;
  }
  else{
    $prs["folder"]=$dir;
  }
  return buildParameter($prs);
}
function buildParameter($params){
  $url_parts['query'] = http_build_query($params);
  return current_url()."?". $url_parts["query"];
}
function getViewAdmin($view,$currentFile,$type = 'view'){
    $view= strtolower($view);
    if(strpos($view, '.')!==FALSE){
        $views = explode('.', $view);
        $views = array_merge(array_slice($views, 0, 1), [$type], array_slice($views, 1));

        $dir = $views[0];
        unset($views[0]);
        $file = PLUGIN_PATH.'/'.$dir.'/views/'.implode('/', $views).".php";
        if(file_exists($file)){
            $views = [$dir]+$views;
            $view = implode('.',  $views);
            return $view;
        }
    }
    else{
        if(file_exists(realpath(dirname($currentFile)).'/'.$type.'/'.$view.'.php')){
            return $type.'/'.$view;
        }
    }
    return false;
}
 ?>