<?php
function auth(){
    $ci = & get_instance();
    if($ci->session->has_userdata('_frontend_user')){
        return $ci->session->userdata('_frontend_user');
    }
    else return null;
}
/* try catch option */
function tryCatchset(){
    set_error_handler(
        create_function(
            '$severity, $message, $file, $line',
            'throw new ErrorException($message, $severity, $severity, $file, $line);'
        )
    );
}
function echoJSON($code,$message){
    $obj = new stdClass();
    $obj->code= $code;
    $obj->message= $message;
    echo json_encode($obj);
}
function echoJSONdata($code,$message,$data){
    $obj = new stdClass();
    $obj->code= $code;
    $obj->message= $message;
    $obj->data= $data;
    echo json_encode($obj);
}
function getImageThumb($img){
    if(@$img){
        $pos = strrpos($img, "/");
        $imgthumb = substr($img, 0,$pos+1)."thumbs/".substr($img, $pos+1);
         if(isNull($imgthumb) || !file_exists($imgthumb)) return "theme/admin/images/no-image.svg";
        return $imgthumb;
    }
}
function echom($arr,$key,$check=1){
    echo echor($arr,$key,$check);
}
function imgSingle($json,$folder,$checkWebp=false){
    $webp = false;
    if($checkWebp && function_exists("__checkBrowserWebp")){
        $webp = __checkBrowserWebp();
    }
    $result = base_url("theme/admin/images/no-image.svg");
    if(!array_key_exists("path", $json)){
        $result =  base_url("theme/admin/images/no-image.svg");
        return $result;
    }
    if($folder=="-1"||$folder==""){
        $file = $json["path"].$json["file_name"];
        $file = changeImgWebpExt($file,$webp);
        
        if(!file_exists($file)){
            $result =  base_url("theme/admin/images/no-image.svg");
        }
        else{
            $result =  base_url($file);
        }
    }
    else{
        $folder = "thumbs/".$folder."/";
        $def = $json["path"].$json["file_name"];
        $def = changeImgWebpExt($def,$webp);
        $ret = $json["path"].$folder.$json["file_name"];
        $ret = changeImgWebpExt($ret,$webp);
        if(file_exists($ret)){
            $result =  base_url($ret);
        }
        else{
            if(file_exists($def)){
                $result =  base_url($def);
            }
            else{
                $result =  base_url("theme/admin/images/no-image.svg");
            }
        }
    }
    return $result;
}
//Mảng, img/logo/banner
function imgv2($arr,$key,$folder="",$webp = false){
    $returnNow = false;
    $result = "";
    $ci = & get_instance();
    $resultHook = $ci->hooks->call_hook(['tech5s_before_imgv2',"arr"=>$arr,"key"=>$key,"folder"=>$folder,"webp"=>$webp,"returnNow"=>$returnNow,"result"=>$result]);
    if(!is_bool($resultHook) && is_array($resultHook)){
        extract($resultHook);
    }
    if($returnNow){
        return $result;
    }


    $isPicture = false;
    if(strpos($key, "#W#") ===0 ||strpos($key, "#w#") ===0 ){
        $isPicture = true;
        $key = substr($key, 3);
    }
    if(!$isPicture){
        $json = array_key_exists($key, $arr)?$arr[$key]:"";
        $json = json_decode($json,true);
        $json = @$json?$json:array();
        $result = imgSingle($json,$folder,$webp);
    }
    else{
        if(function_exists("webpImg")){
            $result =  webpImg($arr,$key,false,$folder);
        }
    }
    return $result;
}
function changeImgWebpExt($file,$webp){
    if(!$webp) return $file;
    $path = pathinfo($file);
    $dirname = $path["dirname"];
    $extension = $path["extension"];
    $filename = $path["filename"];
    $tmpfile = $dirname."/".$filename.".webp";
    if(file_exists($tmpfile)){
        return $tmpfile;
    }
    return $file;
}
function _ee($arr,$key,$check=1){
    $CI = & get_instance();
    $default_language = $CI->config->item( 'default_language' );
    $lang = @$CI->session->userdata('lang')?$CI->session->userdata('lang'):$default_language;
    if(strpos($key, "#i")==0){
        //#i#img#id
        $tmp = explode("#", $key);
        if(count($tmp)<3) return "";
        if(is_numeric($tmp[2])){
            if($tmp[2]==0){
                $val = $arr;
            }
            else $val =$arr[$tmp[2]];
        }
        else if(is_string($tmp[2])){
            if(!array_key_exists($tmp[2],$arr))
                return "";
            $val = $arr[$tmp[2]];
            $val = json_decode($val,true);
        }
        if( is_array($val) && array_key_exists($tmp[3], $val)){
            $x = $val[$tmp[3]];
            if($tmp[3]=="alt"||$tmp[3]=="title"){
                if(strlen(trim($x))==0){
                    if(@$arr['name'] || @$arr['name_en']){
                        if(array_key_exists('name_'.$lang, $arr)){
                            $x= $arr["name_".$lang];
                        }
                        else if($lang == $default_language){
                            $x= $arr["name"];
                        }
                    }
                    else{
                        $x = '';
                    }
                }
            }
            return $x;
        }
    }
    return "";
}
function getTailLang(){
    $CI = &get_instance();
    if(!$CI->session->has_userdata("lang")) return "";
    $default_language = $CI->config->item( 'default_language' );
    $lang = $CI->session->has_userdata("lang")?$CI->session->userdata('lang'):$default_language;
    if($default_language==$lang){
        return "";
    }
    return "?lang=".$lang;
}
function getLang(){
    $ci = & get_instance();
    if($ci->session->has_userdata('lang')){
        return $ci->session->userdata('lang');
    }
    return "";
}
function echor($arr,$key,$check=1){
    $ci = & get_instance();
    $resultHook = $ci->hooks->call_hook(['tech5s_before_input_echor',"arr"=>$arr,"key"=>$key,"check"=>$check]);
    if(!is_bool($resultHook) && is_array($resultHook)){
        extract($resultHook);
    }
    $result = "";
    if(!is_array($arr)){ echo $arr;return;}
    if(!array_key_exists($key, $arr) && $key!="img_thumb"){
        $result =  _ee($arr,$key,$check);
    }
    else if(is_array($arr)){
        if($check==1){
            $CI = & get_instance();
            $default_language = $CI->config->item( 'default_language' );
            $k = @$CI->session->userdata('lang')?$CI->session->userdata('lang'):$default_language;
            if(array_key_exists($key."_".$k, $arr)){
                $result =  $arr[$key."_".$k];
            }
            else{
                if($key=='create_time'||$key=='update_time'){
                    $result =  date('d/m/Y H:i:s',$arr[$key]);
                }
                else if($key=='slug' || $key == 'link_static'){
                    $result = base_url($arr[$key].getTailLang());
                }
                else if($key=='price'||$key=='price_sale'){
                    if((double)$arr[$key]==0)
                    $result = lang('LIENHE');
                    else
                    $result = number_format((double)$arr[$key],0,',','.')." đ";
                }
                else if($key=='img_thumb'){
                    $key = 'img';
                    if(isNull($arr[$key]) || !file_exists($arr[$key])){
                        $result = "theme/admin/images/no-image.svg";
                    }
                    else if($arr[$key]!=null && strpos($arr[$key], 'http')===FALSE && strpos($arr[$key], 'theme/frontend')===FALSE){
                        $result = getImageThumb($arr[$key]);    
                    }
                    else{
                        $result = $arr[$key];
                    }
                }
                else if($key=='img'){
                    if(isNull($arr[$key]) || !file_exists($arr[$key])) {
                        $result = "theme/admin/images/no-image.svg";
                    }
                    else{
                        $result = $arr['img'];
                    }
                }
                else{
                    $result = $arr[$key];
                }
            }
        }
        else{
            $result = $arr[$key];
        }
    }
    else{
        $result = $arr;
    }
    $resultHook = $ci->hooks->call_hook(['tech5s_before_echor',"result"=>$result,"arr"=>$arr,"key"=>$key,"check"=>$check]);
    if(!is_bool($resultHook) && is_array($resultHook)){
        extract($resultHook);
    }
    return $result;
}
function isNull($str){
    return  $str==NULL || (is_string($str) && strlen(trim($str))==0);
}
function subString($body,$length){
    $line=$body;
    if (preg_match('/^.{1,'.$length.'}\b/s', $body, $match))
    {
        $line=$match[0];
    }
    return $line;
}
function getExactLink($link){
    if(($link!=NULL && strlen($link)>0 &&  strpos($link, 'http')!==FALSE) || $link == 'javascript:void(0);'){
        return $link;
    }
    else return base_url().$link.getTailLang();
}
/* send mail */
function sendMail($email,$tieude,$noidung,$email_bcc = false,$email_cc = false){
    tryCatchset();
    $CI = &get_instance();
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 0;     
    $mail->isSMTP();    
    $mail->Host = 'smtp.gmail.com'; 
    $mail->SMTPAuth = true;                              
    $mail->Username = $CI->Dindex->getSettings("MAIL_USER");                 
    $mail->Password = $CI->Dindex->getSettings("MAIL_PASS");                        
    $mail->SMTPSecure = 'tls';                           
    $mail->Port = 587;                                   
    $mail->setFrom($CI->Dindex->getSettings("MAIL_USER"), $CI->Dindex->getSettings("MAIL_NAME"));
    $mail->addAddress($email, $email);    
    $mail->isHTML(true);                                 
    $mail->Subject = $tieude;
    $mail->Body    = $noidung;
    $mail->AltBody = strip_tags($noidung);
    if($email_cc){
        $mail->AddCC($email_cc);
    }
    if($email_bcc){
        $mail->AddBCC($email_bcc);
    }
    if(!$mail->send()) {
        return false;
    } else {
        return true;
    }
}
function replaceURL($string){
    $string=strtolower($string);
    $str = str_replace('-', ' ', $string);
    $utf8characters = 'à|a, ả|a, ã|a, á|a, ạ|a, ă|a, ằ|a, ẳ|a, ẵ|a,  ắ|a, ặ|a, â|a, ầ|a, ẩ|a, ẫ|a, ấ|a, ậ|a, đ|d, è|e, ẻ|e, ẽ|e, é|e, ẹ|e,  ê|e, ề|e, ể|e, ễ|e, ế|e, ệ|e, ì|i, ỉ|i, ĩ|i, í|i, ị|i, ò|o, ỏ|o, õ|o,  ó|o, ọ|o, ô|o, ồ|o, ổ|o, ỗ|o, ố|o, ộ|o, ơ|o, ờ|o, ở|o, ỡ|o, ớ|o, ợ|o,  ù|u, ủ|u, ũ|u, ú|u, ụ|u, ư|u, ừ|u, ử|u, ữ|u, ứ|u, ự|u, ỳ|y, ỷ|y, ỹ|y,  ý|y, ỵ|y, À|a, Ả|a, Ã|a, Á|a, Ạ|a, Ă|a, Ằ|a, Ẳ|a, Ẵ|a, Ắ|a, Ặ|a, Â|a,  Ầ|a, Ẩ|a, Ẫ|a, Ấ|a, Ậ|a, Đ|d, È|e, Ẻ|e, Ẽ|e, É|e, Ẹ|e, Ê|e, Ề|e, Ể|e,  Ễ|e, Ế|e, Ệ|e, Ì|i, Ỉ|i, Ĩ|i, Í|i, Ị|i, Ò|o, Ỏ|o, Õ|o, Ó|o, Ọ|o, Ô|o,  Ồ|o, Ổ|o, Ỗ|o, Ố|o, Ộ|o, Ơ|o, Ờ|o, Ở|o, Ỡ|o, Ớ|o, Ợ|o, Ù|u, Ủ|u, Ũ|u,  Ú|u, Ụ|u, Ư|u, Ừ|u, Ử|u, Ữ|u, Ứ|u, Ự|u, Ỳ|y, Ỷ|y, Ỹ|y, Ý|y, Ỵ|y, "|,  &|';
    $replacements = array();
    $items = explode(',', $utf8characters);
    foreach ($items as $item) {
        @list($src, $dst) = explode('|', trim($item));
        $replacements[trim($src)] = trim($dst);
    }
    $str = trim(strtr($str, $replacements));
    $str = preg_replace('/(\s|[^A-Za-z0-9\-])+/', '-', $str);
    $str = trim($str, '-');
    return $str;
}
function printRecursiveSelect($lv,$arrD,$value){
    $lv++;
    for ($i=0;$i<sizeof($arrD);$i++) {
        $sub = $arrD[$i];
        $item = $sub->item;
        $inputs = array_keys($item);
        echo '<option '.($value==$item['id']?' selected ':'').' value="'.$item['id'].'">└'.str_repeat("---", $lv).(in_array('name', $inputs) ? $item['name'] : (!empty($inputs[1]) ? $item[$inputs[1]] : '')).'</option>';
        printRecursiveSelect($lv,$sub->childs,$value);
    }
}
function printRecursiveSelectWithTag($lv,$arrD,$value){
    $lv++;
    for ($i=0;$i<sizeof($arrD);$i++) {
        $sub = $arrD[$i];
        $item = $sub->item;
        echo '<option dt-slug="'.$item['slug'].'" '.($value==$item['id']?' selected ':'').' value="'.$item['id'].'">└'.str_repeat("---", $lv).$item['name'].'</option>';
        printRecursiveSelectWithTag($lv,$sub->childs,$value);
    }
}
function printRecursiveMultiSelect($lv,$arrD,$value){
    $lv++;
    for ($i=0;$i<sizeof($arrD);$i++) {
        $sub = $arrD[$i];
        $item = $sub->item;
        $inputs = array_keys($item);
        $checked = (is_array($value) && in_array($item['id'], $value))?' checked ':'';
        $class = (is_array($value) && in_array($item['id'], $value))?' choose ':'';
        echo '<li class="'.$class.'" style="  font-size: 15px;color:#1D1D1D;margin: 2px 0px;padding-left:'.(($lv-1)*20).'px;"><label>'.($lv>1?'└----':'').'<input type="checkbox" '.$checked.' value="'.$item['id'].'"/>'.(in_array('name', $inputs) ? $item['name'] : (!empty($inputs[1]) ? $item[$inputs[1]] : '')).'</label></li>';
        if(@$sub->childs){
            printRecursiveMultiSelect($lv,$sub->childs,$value);
        }
    }
}
function printRecursiveMultiSelect2($lv,$arrD,$value){
    $value = json_decode($value, true);
    $lv++;
    for ($i=0;$i<sizeof($arrD);$i++) {
        $sub = $arrD[$i];
        $item = $sub->item;
        $checked = (is_array($value) && array_key_exists($item['id'], $value))?' checked ':'';
        echo '<li style="  font-size: 15px;color:#1D1D1D;margin: 2px 0px;margin-left:'.(($lv-1)*20).'px;">'.($lv>1?'└----':'').'<input type="checkbox" '.$checked.' value="'.$item['id'].'"/>'.$item['name'].'<br>';
        echo '<input style="width: 100%;" type="text" value="'.((is_array($value) && array_key_exists($item['id'], $value)) ? $value[$item['id']] : '').'" placeholder="Link sản phẩm trên '.$item['name'].'">';
        echo '</li>';
        if(@$sub->childs){
            printRecursiveMultiSelect($lv,$sub->childs,$value);
        }
    }
}
function printMenu($arrD,$arrSetting){
    $CI= & get_instance();
    $runDefault = true;
    $resultHook = $CI->hooks->call_hook(['tech5s_print_menu',"arrD"=>$arrD,"arrSetting"=>$arrSetting,"runDefault"=>$runDefault]);
    if(!is_bool($resultHook) && is_array($resultHook)){
        extract($resultHook);
    }
    if($runDefault){
        printMenuC($arrD,$arrSetting,0);
    }
}
function printMenuC($arrD,$arrSetting,$count){
    $CI= & get_instance();
    $link = $CI->uri->segment(1);
    $count++;
    $arrDef = array(
        'classli'=>'',
        'classa'=>'',
        'classul'=>'',
        'divajax'=>'',
        'divclr'=>1
        );
    $arrDefault = array_replace($arrDef, $arrSetting);
    $div = $arrDefault['divajax'];
    $home = 0;
    for ($i=0;$i<sizeof($arrD);$i++) {
        $sub = $arrD[$i];
        $item = $sub->item;
        $exactLink = getExactLink($item["link"]);
        if($i==0){ echo "<ul class='".$arrDefault['classul']."'>";}
        echo "<li class=' clli".$count." ".$arrDefault['classli']."'><a rel='".(($item['nofollow'] == 1)?'nofollow':'')."' class='".getMenuActive($item)." clli".$count."".$arrDefault['classa']." menu".$item['id']."' href='".$exactLink."' ";
        if(strlen($div)>0){
            echo "onclick= \"loadPageContent('$div','$exactLink');return false;\" ";
        }
        echo " href='".$item['link']."'>".echor($item,'name',1)."</a>";
        printMenuC($sub->childs,$arrSetting,$count);
        echo "</li>";
        if($i==sizeof($arrD)-1){
            /*if($arrDefault['divclr']==1) echo "<div class='clr'></div>";*/
            echo "</ul>";
        }
        $home++;
    }
}
function getImageAnyTime($item,$key){
    return (@$item && !isNull($item[$key]))?$item[$key]:'theme/frontend/img/noimage.png';
}
function fakeEval($phpCode) {
    $tmpfname = tempnam("/tmp", "fakeEval");
    $handle = fopen($tmpfname, "w+");
    fwrite($handle, "<?php\n" . $phpCode);
    fclose($handle);
    include $tmpfname;
    unlink($tmpfname);
    return get_defined_vars();
}
function getImgYoutube($video) {
    $var = explode("=",$video);
    if(@$var[1]) return $a="http://img.youtube.com/vi/".$var[1]."/0.jpg";
    else return 0;
}
function getImgYoutubeEm($video) {
    $var = explode("/",$video);
    $n=count($var);
    if($var[$n-1]) return $a="http://img.youtube.com/vi/".$var[$n-1]."/0.jpg";
    else return 0;
}
function getRegexYTImg($link){
    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $link, $matches);
    return "http://img.youtube.com/vi/".$matches[1]."/0.jpg";
}
function getYoutubeId($link){
    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $link, $matches);
    if(isset($matches[1]) && $matches[1] !=""){
        return $matches[1];
    }
    else{
        return 0;
    }
}
function fetch_highest_res($videoid) {
    $resolutions = array('maxresdefault', 'hqdefault', 'mqdefault');     
    foreach($resolutions as $res) {
        $imgUrl = "http://i.ytimg.com/vi/$videoid/$res.jpg";
        if(@getimagesize(($imgUrl))) 
            return $imgUrl;
    }
}
function wlimit($str,$n) {
    echo word_limiter(strip_tags($str),$n);
}
function getEmYoutube($video) {
    $var = explode("=",$video);
    if(@$var[1]) return $a="https://www.youtube.com/embed/".$var[1];
    else return 0;
}
/*Lấy ảnh của banner danh mục khi không có lấy banner trong config*/
function getBanner($table,$id){
    $CI= & get_instance();
    $ban=$CI->Dindex->getBanner($table,$id);
    if(trim($ban)=="" && $id !=0){
        $parent=$CI->Dindex->getParent($table,$id);
        getBanner($table,$parent);
    }
    if($ban!="")
    return $ban;
    else return $CI->Dindex->getSettings('BANNERC');
}
/*Lấy cấp con của danh mục không có thì lấy 6 sản phẩm random*/
function getRandom($table,$tablechil,$id){
    $CI= & get_instance();
    $dl=$CI->Dindex->getRandom($table,$id);
    $n=count($dl);
    if($n!=0)
    return $dl;
    else {
        $dl2=$CI->Dindex->getRandomChild($tablechil,$id);
        return $dl2;
    }
}
function getCateSon($table,$id){
    $CI= & get_instance();
    return $CI->Dindex->getCateSon($table,$id);
}
function getFieldTable($table,$id,$field){
    $CI= & get_instance();
    $pa=$CI->Dindex->getInfoTable($table,$id);
    if(@$pa[0][$field]) return  $pa[0][$field];
    else return " ";
}
function convertArrayToMap($arr){
    $ret = array();
    foreach ($arr as $key => $value) {
        $ret[$value['id']] = $value;
    }
    return $ret;
}
function getNext($id){
    $CI = &get_instance();
    $q  = $CI->db->query("select slug,name from news where id > ".$id." and act = 1 limit 0,1");
    return $q->result_array();
}
function getPrev($id){
    $CI = &get_instance();
    $q  = $CI->db->query("select slug,name from news where id < ".$id." and act = 1 limit 0,1");
    return $q->result_array();
}
function getPrice($dataitem){
    $price=$dataitem['price'];
    $price_sale= $dataitem['price_sale'];
    $arr_price = array();
    $percent = 0;
    $sotienduocgiam = 0;
    if($price > 0 && $price_sale > 0){
        if($price_sale < $price){
            $arr_price['price_sale'] = $price_sale;
            $arr_price['price'] = $price;
            $percent = ceil(($price - $price_sale)/$price*100);
            $sotienduocgiam = $price - $price_sale;
        }else{
            $arr_price['price'] = $price_sale;
            $arr_price['price_sale'] = $price;
            $percent = ceil(($price_sale - $price)/$price_sale*100);
            $sotienduocgiam = $price_sale - $price;
        }
    }
    else{
        if($price_sale > 0){
            $arr_price['price_sale'] = $price_sale;
        }
        elseif($price > 0){
            $arr_price['price_sale'] = $price;
        }
        else{
            $arr_price['price_sale'] = 0;
        }
    }
    $arr_price['percent'] = $percent;
    $arr_price['sotienduocgiam'] = $sotienduocgiam;
    return $arr_price;
}

function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;
    if ($etime < 1)
    {
        return '0 seconds';
    }
    $a = array( 365 * 24 * 60 * 60  =>  'Năm',
                 30 * 24 * 60 * 60  =>  'Tháng',
                      24 * 60 * 60  =>  'Ngày',
                           60 * 60  =>  'Giờ',
                                60  =>  'Phút',
                                 1  =>  'Giây'
                );
    $a_plural = array( 'Năm'   => 'Năm',
                       'Tháng'  => 'Tháng',
                       'Ngày'    => 'Ngày',
                       'Giờ'   => 'Giờ',
                       'Phút' => 'Phút',
                       'Giây' => 'Giây'
                );
    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' trước';
        }
    }
}
function getScorePro($itempro){
    $arr=$itempro['score'];
    $r = json_decode($arr,true);
    $r = (array)$r;
    $score =0;
    $total= 0;
    foreach($r as $k => $v)
    {
        $score+=$v* str_replace("s-", "", $k);
        $total += $v;
    }
    if($total <= 0){
        $total = 1;
    }
    $lastScore = $score/$total;
    $w=$lastScore*100/5;
    return $w;
}
function getScorePro1($itempro){
    $arr=$itempro['score'];
    $r = json_decode($arr,true);
    $r = (array)$r;
    $score =0;
    $total= 0;
    foreach($r as $k => $v)
    {
        $score+=$v* str_replace("s-", "", $k);
        $total += $v;
    }
    $vote = $total;
    if($total <= 0){
        $total = 1;
    }
    $lastScore = $score/$total;
    $w=$lastScore*100/5;
    return compact("w",'lastScore','vote');
}
function getParentIdProjects($id){
    $CI = &get_instance();
    $CI->db->where('id', $id);
    $q = $CI->db->get('projects')->result_array();
    $idcate = '';
    if(count($q) > 0){
        $parent = $q[0]['parent'];
        if($parent != ''){
            $p = explode(',', $parent);
            $idcate = $p[0];
        }
    }
    return $idcate;
}
function getActiveUrl($link,$name,$group_id=''){
    $CI = & get_instance(); 
    $currentSegment = $CI->uri->segment(1,"");
    if($currentSegment=="") return "";
    $list = [];
    $CI->db->where("link",$currentSegment);
    if($group_id) $CI->db->where("group_id",$group_id);
    $arr = $CI->db->get("menu")->result_array();
    if(count($arr)>0){
        foreach ($arr as $key => $item) {
            $parent = $item['parent'];
            $list[$item['link']] = $item["name"];
            $CI->db->where("id",$parent);
            $ps = $CI->db->get("menu")->result_array();
            if(count($ps)>0){
                $list[$ps[0]['link']] = $ps[0]["name"];
                $CI->db->where("id",$ps[0]['id']);
                $ps1 = $CI->db->get("menu")->result_array();
                if(count($ps1)>0){
                    $list[$ps1[0]['link']] = $ps1[0]["name"];
                }
            }
        }
    }
    $keys = array_keys($list);
    $values = array_values($list);
    if(strlen(trim($link))>0 && strlen($name)>0){
        if(in_array($link, $keys) && in_array($name, $values))
            return "active";
    }
    else if(strlen(trim($link))==0 && strlen($name)>0){
        if(in_array($name, $values))
            return "active";
    }
    return '';
}
function validatePhoneNumber($phoneNumber){
    // update số điện thoại chỉ còn 10 số và danh sách đầu số 9/2018
    if(strlen($phoneNumber) != 10){
        return false;
    }
    $viettel = '086,096,097,098,032,033,034,035,036,037,038,039,';
    $mobifone = '090,093,070,079,077,076,078,';
    $vinaphone = '091,094,083,084,085,081,082,';
    $vietnamobile = '092,056,058,';
    $gmobile = '099,059';
    $all_phone_number = $viettel.$mobifone.$vinaphone.$vietnamobile.$gmobile;
    $all_phone_numbers = explode(',', $all_phone_number);
    $dauso = substr($phoneNumber, 0, 3);
    if(!in_array($dauso, $all_phone_numbers)){
        return false;
    }
    return true;
}
function getMothVietnamese($timestamp){
    $months = [
        'Jan' => 'Th1',
        'Feb' => 'Th2',
        'Mar' => 'Th3',
        'Apr' => 'Th4',
        'May' => 'Th5',
        'Jun' => 'Th6',
        'Jul' => 'Th7',
        'Aug' => 'Th8',
        'Sept' => 'Th9',
        'Oct' => 'Th10',
        'Nov' => 'Th11',
        'Dec' => 'Th12'
    ];
    $month_input = date('M', $timestamp);
    $month_output = $months[$month_input];
    return $month_output;
}
function user(){
    $CI = & get_instance();
    $user = [];
    if($CI->session->has_userdata('_frontend_user')){
        $user = $CI->session->userdata('_frontend_user');
    }
    return $user;
}
function rand_string( $length ) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $size = strlen( $chars );
    $str = '';
    for( $i = 0; $i < $length; $i++ ) {
        $str .= $chars[ rand( 0, $size - 1 ) ];
    }
    return $str;
}
function _isMobile(){
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
function getVariations($idPro){
    $ci = & get_instance();
    $where = array(array('key'=>'act', 'compare'=>'=', 'value'=>1), array('key'=>'parent', 'compare'=>'=', 'value'=>$idPro));
    $variationArray = $ci->Dindex->getDataDetail(array(
        'table'=>'variations',
        'where' =>$where,
        'order'=>'ord asc, id desc'
    ));
    return count($variationArray) > 0 ? $variationArray : [];
}
function getLinkShop($idLinkShop){
    $ci = & get_instance();
    $where = array(array('key'=>'act', 'compare'=>'=', 'value'=>1), array('key'=>'id', 'compare'=>'=', 'value'=>$idLinkShop));
    $linkShops = $ci->Dindex->getDataDetail(array(
        'table'=>'link_shops',
        'where' =>$where,
        'order'=>'ord asc, id desc'
    ));
    return count($linkShops) > 0 ? $linkShops : [];
}
function getMemberById($id){
    $ci = & get_instance();
    $where = array(array('key'=>'act', 'compare'=>'=', 'value'=>1), array('key'=>'id', 'compare'=>'=', 'value'=>$id));
    $members = $ci->Dindex->getDataDetail(array(
        'input' => 'link, img, name, name_en, name_cn',
        'table'=>'members',
        'where' =>$where,
        'order'=>'ord asc, id desc'
    ));
    return count($members) > 0 ? $members[0] : [];
}
function getMenuActive($menus){
   return '';
}
function getConfigPlugin($pluginName){
    $CI = &get_instance();
    $config = $CI->cache->get('_cache_'.$pluginName);
    if ( !@$config )
    {
        $config = HookPlugin::getConfig($pluginName);
        $CI->cache->save('_cache_'.$pluginName, $config, $CI->config->item('tech5s_time_cache_setting'));
    }
    return $config;
}
?>