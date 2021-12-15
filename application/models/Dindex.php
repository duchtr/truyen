<?php
class Dindex extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->driver('cache', array('adapter' => 'apc', 'backup' => 'file'));
    }
    public function checkExistLinkRoutes($link){
        $arr_routes = $this->Dindex->getDataDetail(array(
            'table'=>'nuy_routes',
            'where' =>array(array('key'=>'link','compare'=>'=','value'=>"'".$link."'"))
        ));
        if(count($arr_routes) > 0){
            $link = $link.'-2';
            return $link;
            $this->checkExistLinkRoutes($link);
        }
        else{
            return $link;
        }
    }
    public function getData($table,$where,$n,$off){
        $resultHook = $this->hooks->call_hook(['tech5s_before_get_data',"table"=>$table,"where"=>$where,"n"=>$n,'off'=>$off]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        if(is_array($where)){
            foreach ($where as $w=>$k) {
                $this->db->where($w,$k);
            }
        }
        if($n>0 && $off>=0){
            $q =$this->db->get($table,$off,$n);
        }
        else{
            $q =$this->db->get($table);
        }
        return $q->result_array();
    }
    function selectRawQuery($sql){
        $query=$this->db->query($sql);
        $dl=$query->result_array();
        return $dl;
    }
    public function getNumData($table,$where){
        if(is_array($where)){
            foreach ($where as $w=>$k) {
                $this->db->where($w,$k);
            }
        }
        $q =$this->db->get($table);
        return $q->num_rows();
    }
    public function getNumDataDetail($table,$where,$pivot=[]){
        $resultHook = $this->hooks->call_hook(['tech5s_before_get_num_data_detail',"table"=>$table,"where"=>$where,"pivot"=>$pivot]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        $condition = [];
        $table = "`".addslashes($table)."`";
        $sql = "select count(DISTINCT (%s.id)) count from %s ";
        $params = [];
        array_push($params, $table);
        array_push($params, $table);
        if(count($pivot)>0){
            $fieldmap = $pivot["field"];
            $searchValue = $pivot["value"];
            $ptable = $pivot["ptable"];
            $psql = " inner join pivots on %s.id = pivots.sub_id where pivots.field_map = ? and pivots.parent_table = ? and pivots.parent_id in ? ";
            $psql = sprintf($psql,$table);
            array_push($condition, $fieldmap);
            array_push($condition, $ptable);
            array_push($condition, is_array($searchValue)?$searchValue:[$searchValue]);
            $sql .=$psql;
        }
        else{
            $sql .= " where 1 = 1 ";
        }
        if(is_array($where)){
            foreach ($where as $w=>$subwhere) {
                $swhere = $subwhere['value'];
                $skey = $subwhere['key'];
                $skey = (strpos($skey, ".")===FALSE && strpos(strtolower($skey), "find")===FALSE)?sprintf("%s.%s",$table,$skey):$skey;
                if($subwhere['compare']=='like'){
                    $sql .=" and %s like ?";
                    array_push($params, $skey);
                    array_push($condition,"%".addslashes($swhere)."%");
                }
                else if($subwhere['compare']=='like_end'){
                    $sql .=" and %s like ?";
                    array_push($params, $skey);
                    array_push($condition,addslashes($swhere)."%");
                }
                else if($subwhere['compare']=='like_start'){
                    $sql .=" and %s like ?";
                    array_push($params, $skey);
                    array_push($condition,"%".addslashes($swhere));
                }
                else{
                    $sql .=" and %s %s ?";
                    array_push($params, $skey);
                    array_push($params, $subwhere['compare']);
                    array_push($condition, $swhere);
                }
            }
        }
        $sql = vsprintf($sql, $params);
        $q =$this->db->query($sql,$condition);
        $results = $q->result_array();
        $result =  count($results)>0?(int)$results[0]["count"]:0;
        $resultHook = $this->hooks->call_hook(['tech5s_after_get_num_data_detail',"table"=>$table,"where"=>$where,"pivot"=>$pivot,"result"=>$result]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        return $result;
    }
    public function getNumTH($id,$table){
        $table = "`".addslashes($table)."`";
        $sql = "select * from $table where 1=1 and FIND_IN_SET(?,parent)";
        $q =$this->db->query($sql,[$id]);
        return $q->num_rows();
    }
    public function getDataFindInSet($id,$table,$con='',$limit=0){
        $limit = (int)$limit;
        $table = "`".addslashes($table)."`";
        $sql = "select * from $table where 1=1 and FIND_IN_SET('$id',parent) order by id desc".($limit!=0?" limit 0,".$limit:"");
        $q =$this->db->query($sql);
        return $q->result_array();
    }
    public function getDataDetail($options){
        $default = array(
            'table'=>'',
            'input'=>'*',
            'order'=>'id',
            'where'=>array(),
            'limit'=>'',
            'escape'=>0,
            'group_by'=>'',
            'pivot'=>[]
        );
        if(is_array($options)){
            $options = array_replace($default, $options);
            $resultHook = $this->hooks->call_hook(['tech5s_before_get_data_detail',"options"=>$options]);
            if(!is_bool($resultHook) && is_array($resultHook)){
                extract($resultHook);
            }
            if(isNull($options['table'])) return;
            $input = addslashes($options['input']);
            $table = "`".addslashes($options['table'])."`";
            $input = $input==='*'?sprintf("%s.*",$table):$input;
            $sql = "select distinct %s from %s ";
            $params = [];
            array_push($params, $input);
            array_push($params, $table);
            $pivot = $options['pivot'];
            $condition = [];
            if(count($pivot)>0){
                $fieldmap = $pivot["field"];
                $searchValue = $pivot["value"];
                $ptable = $pivot["ptable"];
                $psql = " inner join pivots on %s.id = pivots.sub_id where pivots.field_map = ? and pivots.parent_table = ? and pivots.parent_id in ? ";
                $psql = sprintf($psql,$table);
                array_push($condition, $fieldmap);
                array_push($condition, $ptable);
                array_push($condition, is_array($searchValue)?$searchValue:[$searchValue]);
                $sql .=$psql;
            }
            else{
                $sql .= " where 1 = 1 ";
            }
            
            
            if(is_array($options['where'])){
                foreach ($options['where'] as $subwhere) {
                    $swhere = $subwhere['value'];
                    if($options['escape']==1){
                        $swhere = $this->db->escape($swhere);
                    }
                    $skey = $subwhere['key'];
                    $skey = (strpos($skey, ".")===FALSE && strpos(strtolower($skey), "find")===FALSE)?sprintf("%s.%s",$table,$skey):$skey;
                    $con = 'and';
                    if($subwhere['compare']=='like'){
                        $sql .= " %s %s like ?";
                        array_push($params, $con);
                        array_push($params, $skey);
                        array_push($condition, '%'.addslashes($swhere).'%');
                    }
                    else if($subwhere['compare']=='like_end'){
                        $sql .= " %s %s like ?";
                        array_push($params, $con);
                        array_push($params, $skey);
                        array_push($condition, addslashes($swhere).'%');
                    }
                    else if($subwhere['compare']=='like_start'){
                        $sql .= " %s %s like ?";
                        array_push($params, $con);
                        array_push($params, $skey);
                        array_push($condition,  '%'.addslashes($swhere));
                    }
                    else{
                        $sql .= " %s %s %s ? ";
                        array_push($params, $con);
                        array_push($params, $skey);
                        array_push($params, $subwhere['compare']);
                        array_push($condition, $swhere);
                    }
                }
            }
            if(!isNull($options['group_by'])){
                $sql .=" group by %s";
                array_push($params, $options['group_by']);
            }
            if(!isNull($options['order'])){
                $sql .=" order by %s";
                array_push($params, $options['order']);
            }
            if(!isNull($options['limit'])){
                $sql .=" limit %s";
                array_push($params, $options['limit']);
            }
            $sql = vsprintf($sql,$params);
            $q = $this->db->query($sql,$condition);
            $results =  $q->result_array();
            $resultHook = $this->hooks->call_hook(['tech5s_after_get_data_detail',"options"=>$options,"results"=>$results]);
            if(!is_bool($resultHook) && is_array($resultHook)){
                extract($resultHook);
            }
            return $results;
        }
    }
    public function getDataOrder($table,$where,$n,$off,$order){
        if(is_array($where)){
            foreach ($where as $w=>$k) {
                $this->db->where($w,$k);
            }
        }
        if($order!=null && strlen($order)>0){
            $this->db->order_by($order);
        }
        if($n>=0 && $off>=0){
            $q =$this->db->get($table,$off,$n);
        }
        else{
            $q =$this->db->get($table);
        }
        return $q->result_array();
    }
    function insertDataRet($table,$dataUpdate){
        $resultHook = $this->hooks->call_hook(['tech5s_before_insert_data_ret',"table"=>$table,"dataUpdate"=>$dataUpdate]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        $this->db->trans_start();
        $this->db->insert($table,$dataUpdate);
        $lastId = $this->db->insert_id();
        $this->db->trans_complete();
        $result  =-1;
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $result = -1;
        }
        else
        {
            $this->db->trans_commit();
            $result =  $lastId;
        }
        $resultHook = $this->hooks->call_hook(['tech5s_after_insert_data_ret',"table"=>$table,"dataUpdate"=>$dataUpdate,"result"=>$result]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        return $result ;
    }
    public function getSettings($key){
        $set = $this->cache->get('website_setting');
        if ( !@$set )
        {
            $q= $this->db->get('configs');
            $setting = $q->result_array();
            $set = array();
            foreach ($setting as $k => $v) {
                $set[strtoupper($v["keyword"])] = $v;
            }
            $this->cache->save('website_setting', $set, $this->config->item('tech5s_time_cache_setting'));
        }
        $lang = $this->session->has_userdata('lang')?$this->session->userdata('lang'):'';
        $default_language = $this->config->item( 'default_language' );
        $add = (isNull($lang)?$default_language:$lang)."_"; 
        $result = "";
        $key = strtoupper($key);
        if(array_key_exists($key, $set)){
            $fieldLang = @$set[$key]['lang']?$set[$key]['lang']:$default_language;
            $fieldLang = explode(',', $fieldLang);
            if(!in_array($lang, $fieldLang)){
                $lang = $default_language;
                $add = $default_language."_"; 
            }
            $result = $set[$key][$add."value"];
        }
        else{
            $result = $key;
        }
        $resultHook = $this->hooks->call_hook(['tech5s_get_setting',"result"=>$result,"key"=>$key,"set"=>$set]);
        if(!is_bool($resultHook)){
            extract($resultHook);
        }
        if($result==$key){
            $result = "";
        }
        return $result;
    }
    public function getSettingImage($key,$webp=false,$size='',$isShow = false){
        $resultHook = $this->hooks->call_hook(['tech5s_before_get_setting_image',"key"=>$key,"webp"=>$webp,"size"=>$size,"isShow"=>$isShow]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        if(function_exists("webpGetSettingImage")){
            return webpGetSettingImage($key,$webp,$size,$isShow);
        }
        if(!$webp && function_exists("__checkBrowserWebp")){
            $webp = __checkBrowserWebp();
        }
        $tmp = explode("#", $key);
        $key = $tmp[0];
        $subkey = count($tmp)>1?$tmp[1]:"";
        $tmpValue = $this->getSettings($key);
        $tmpValue = json_decode($tmpValue,true);
        $tmpValue = @$tmpValue?$tmpValue:[];
        if(count($tmpValue)==0) return '';
        if($subkey==""){
            $returnPath =base_url('theme/admin/images/no-image.svg');
            if(array_key_exists('path', $tmpValue) && array_key_exists('file_name', $tmpValue)){
                $path = '';
                if($size=='-1' ||$size==''){
                    $path = $tmpValue['path'].$tmpValue['file_name'];
                    $path = changeImgWebpExt($path,$webp);
                }
                else{
                    $path = $tmpValue['path'].$size.'/'.$tmpValue['file_name'];
                    $path = changeImgWebpExt($path,$webp);
                    $def = $tmpValue['path'].$tmpValue['file_name'];
                    $def = changeImgWebpExt($def,$webp);
                    if(!file_exists($path)){
                        $path = $def;
                    }
                }
                if(file_exists($path)){
                    $returnPath= base_url($path);
                }
            }
            if($isShow){
                $str = "<img src='%s' alt='%s' title='%s' class='img-fluid' />";
                echo sprintf($str,$returnPath,$this->getSettingImage($key."#alt",$webp,$size,false),$this->getSettingImage($key."#title",$webp,$size,false));
            }
            else{
                return $returnPath;
            }
        }
        else{
            return array_key_exists($subkey, $tmpValue)?$tmpValue[$subkey]:'';
        }
    }
    function recursiveTableOrder($input="",$table,$field,$basefield,$fieldValue,$where,$order){
        $sql = "select ".(strlen($input)>0?$input:" * ")." from ".$table." where 1= 1 ";
        if($fieldValue!="-1"){
            $sql .=" and ".$field." = '".$fieldValue."'";
        }
        if(is_array($where)){
            foreach ($where as $w) {
                $sql .=" and ".$w["key"]." = ".$w["value"]."";
            }
        }
        if(!isNull($order)){
            $sql .=" order by ".$order;
        }
        $q = $this->db->query($sql);
        $arr = $q->result_array();
        $r = array();
        foreach ($arr as $item) {
            $obj = new stdClass();
            $obj->item = $item;
            if(@$item[$basefield]){ 
                $obj->childs= $this->recursiveTable($input,$table,$field,$basefield,$item[$basefield],$where);
            }
            else{
                $obj->childs=array();
            }
            array_push($r, $obj);
        }
        return $r;
    }
    function recursiveTable($input="",$table,$field,$basefield,$fieldValue,$where){
        $returnDefault = true;
        $result = "";
        $resultHook = $this->hooks->call_hook(['tech5s_recursive_table',"input"=>$input,"table"=>$table,"field"=>$field,"basefield"=>$basefield,"fieldValue"=>$fieldValue,"where"=>$where,"returnDefault"=>$returnDefault]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        if($returnDefault){
            return $this->recursiveTableOrder($input,$table,$field,$basefield,$fieldValue,$where," ord ");
        }
        else{
            return $result;
        }
    }
    function getRelateItem($id,$parent, $table,$limit = null,$pivot=[]){
        $resultHook = $this->hooks->call_hook(['tech5s_before_get_related',"id"=>$id,"parent"=>$parent,"table"=>$table,"limit"=>$limit]);
        if(!is_bool($resultHook)){
            extract($resultHook);
        }
        $params = [];
        $condition = [];
        $table = "`".addslashes($table)."`";
        $sql = "select %s.* from %s ";
        array_push($params, $table);
        array_push($params, $table);
        if(is_array($pivot) && count($pivot)>0){
            $fieldmap = $pivot["field"];
            $searchValue = $pivot["value"];
            if(is_string($searchValue) && strlen($searchValue)==0){
                $searchValue = explode(",", $parent);
            }
            $ptable = $pivot["ptable"];
            $psql = " inner join pivots on %s.id = pivots.sub_id where pivots.field_map = ? and pivots.parent_table = ? and pivots.parent_id in ? ";
            $psql = sprintf($psql,$table);
            array_push($condition, $fieldmap);
            array_push($condition, $ptable);
            array_push($condition, is_array($searchValue)?$searchValue:[$searchValue]);
            $sql .=$psql;
        }
        else{
            $sql .="where 1=1 and act=1 ";
            if(strlen($parent)>0 && is_string($parent)){
                $sql.= " and FIND_SET_EQUALS(?,%s.parent)";
                array_push($condition, $parent);
                array_push($params, $table);
            }
            
            else if(strlen($parent)>0 && is_numeric($parent)){
                $sql .= " and %s.parent = ? ";
                array_push($condition, $parent);
                array_push($params, $table);
            }
        }
        $sql .=" and %s.id != ?";
        array_push($params, $table);
        array_push($condition, $id);
        $sql .=" order by %s.id desc";
        array_push($params, $table);
        if($limit){
        	$sql .= " limit %s";
            array_push($params, $limit);
        }
        $sql = vsprintf($sql, $params);
        $resultHook = $this->hooks->call_hook(['tech5s_after_get_related',"sql"=>$sql,"id"=>$id,"parent"=>$parent,"table"=>$table,"limit"=>$limit]);
        if(!is_bool($resultHook)){
            extract($resultHook);
        }
        $q = $this->db->query($sql,$condition);
        return $q->result_array();
    }
    function updateData($table,$data,$where){
        $resultHook = $this->hooks->call_hook(['tech5s_before_update_data',"table"=>$table,"data"=>$data,"where"=>$where]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        $this->db->trans_start();
        if(is_array($where)){
            foreach ($where as $w=>$k) {
                $this->db->where($w,$k);
            }
        }
        foreach ($data as $key=>$val) {
            $this->db->set($key, $val, FALSE);
        }
        
        $this->db->update($table);
        $this->db->trans_complete();
        $result = false;
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $result =  false;
        }
        else
        {
            $this->db->trans_commit();
            $result =  true;
        }
        $resultHook = $this->hooks->call_hook(['tech5s_after_update_data',"table"=>$table,"data"=>$data,"where"=>$where,"result"=>$result]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        return $result;
    }
    function updateDataFull($table,$data,$where){
        $resultHook = $this->hooks->call_hook(['tech5s_before_update_data_full',"table"=>$table,"data"=>$data,"where"=>$where]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        $this->db->trans_start();
        if(is_array($where)){
            foreach ($where as $w=>$k) {
                $this->db->where($w,$k);
            }
        }
        
        $this->db->update($table,$data);
        $this->db->trans_complete();
        $result = false;
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $result = false;
        }
        else
        {
            $this->db->trans_commit();
            $result = true;
        }
        $resultHook = $this->hooks->call_hook(['tech5s_after_update_data_full',"table"=>$table,"data"=>$data,"where"=>$where,"result"=>$result]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        return $result;
    }
    function updateDataFullPrint($data){
        $this->db->trans_start();
        foreach ($data as $key => $value) {
            $this->db->where('id',$value['id']);
            $this->db->update('pro',['order_status'=>1]);
        }
        
        $this->db->trans_complete();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }
    function updateDataFullOrderCancel($data){
        $this->db->trans_start();
        foreach ($data as $key => $value) {
            $this->db->where('id',$value['id']);
            $this->db->update('order',['payment_status'=>2]);
        }
        
        $this->db->trans_complete();
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }
    function insertData($table,$data){
        $this->db->trans_start();
        $this->db->insert($table,$data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }
    function insertData1($dataUpdate,$table){
        $resultHook = $this->hooks->call_hook(['tech5s_before_insert_data1',"dataUpdate"=>$dataUpdate,"table"=>$table]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        $this->db->trans_start();
        $this->db->insert($table,$dataUpdate);
        $lastId = $this->db->insert_id();
        $this->db->trans_complete();
        $result = -1;
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            $result = -1;
        }
        else
        {
            $this->db->trans_commit();
            $result = $lastId;
        }
        $resultHook = $this->hooks->call_hook(['tech5s_after_insert_data1',"dataUpdate"=>$dataUpdate,"table"=>$table,"result"=>$result]);
        if(!is_bool($resultHook) && is_array($resultHook)){
            extract($resultHook);
        }
        return $result;
    }
    function getBreadcrumb($table,$pid,$currentName){
        $arrBreadcrumbs =array_reverse($this->getBreadcrumbFull($table,$pid,""));
        $obj = new stdClass();
        $obj->name=$currentName;
        $obj->url=current_url();
        array_push($arrBreadcrumbs, $obj);
        $resultHook = $this->hooks->call_hook(['tech5s_get_breadcrumb',"arrBreadcrumbs"=>$arrBreadcrumbs,"table"=>$table,"pid"=>$pid,"currentName"=>$currentName]);
        if(!is_bool($resultHook)){
            extract($resultHook);
        }
        echo '<ul class="breadcrumb clearfix" itemscope itemtype="http://schema.org/BreadcrumbList" >';
        for ($i=0;$i<count($arrBreadcrumbs);$i++) {
            $item = $arrBreadcrumbs[$i];
            echo '<li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            if($item->url=="#"){
                echo '<span itemprop="name">'.$item->name.'</span>';
            }
            else{
                echo '<a itemprop="item"  href="'.$item->url.'">';
                echo '<span itemprop="name">'.$item->name.'</span></a>';
            }
            echo '<meta itemprop="position" content="'.($i+1).'">';
            echo '</li>';
        }
        echo "</ul>";
    }
    
    function getBreadcrumbFull($table, $pid,$div){
        $ret = array();
        if(strlen($pid)<=0 || $pid==0) {
            // if($table=='pro_categories' || $table=='pro') {
            //     $obj2 = new stdClass();
            //     $obj2->url = 'san-pham';
            //     $obj2->name = 'Sản phẩm';
            //     array_push($ret, $obj2);
            // }
            $obj = new stdClass();
            $obj->url = base_url();
            $obj->name = lang("HOME");
            array_push($ret, $obj);
            return $ret;
        }
        if(is_string($pid)){
            $sub = explode(',', $pid);
            $this->db->where('id',$sub[0]);
        }
        else{
            $this->db->where('id',$pid);
        }
        $q= $this->db->get($table);
        $arr = $q->result_array();
        if(count($arr)>0){
            if(array_key_exists('parent', $arr[0])){
                $_ret = $this->getBreadcrumbFull($table,$arr[0]['parent'],"");
                $obj = new stdClass();
                $obj->url = echor($arr[0],'slug',0);
                $obj->name = echor($arr[0],'name',1);
                array_push($ret, $obj);
                $ret = array_merge($ret,$_ret);
            }
        }
        return $ret;
    }
    public function printCategories($datatable, $dataitem,$key){
        $key = isNull($key)?"table_parent":$key;
        $arrSub=array();
        $parentGet = 0;
        if(array_key_exists($key, $datatable)){
            $tablename=$datatable[$key];
            if($tablename && isset($dataitem['parent'])){
                $parent = is_string($dataitem['parent'])?explode(',', $dataitem['parent'])[0]:$dataitem['parent'];
                $arr = array();
                
                while($parent!=0){
                    $arr = $this->Dindex->getData($tablename,array('id'=>$parent),0,1);
                    if(count($arr)>0){ 
                        $parent = $arr[0]['parent'];
                    }
                    else{
                        $parent =0;
                    }
                    if($parent==0){
                        $parentGet = 0;
                    }  
                }
            }
        }
        $this->getCategories(0,$tablename,$parentGet);
    }
    public function getCategories($count,$tablename, $parentGet){
        $count++;
        $arrSub = $this->getData($tablename,array('parent'=>$parentGet),0,100);
        
        echo "<ul class='cates".$count."'>";
        foreach ($arrSub as $item) {
            echo "<li class='itemcate".$count."'>";
            echo '<a onclick="loadPageContent(\'#changeable\',\''.getExactLink(echor($item,'tag',1)).'\');return false;"';
            echo "href='".echor($item,'tag',1)."'>".echor($item,'name',1)."</a>";
            $this->getCategories($count,$tablename,$item['id']);
            echo "</li>";
        }
        echo "</ul>";
    }
    public function getVisited(){
        $sql = "select (select count(*) from (select ip from visit_online where create_time + 600>UNIX_TIMESTAMP(now()) group by ip) tmpcount) online ,";
        $sql .= " (select count(*) from visit_online) total_visit,";
        $sql .= " (select count(*) from visit_online where month(from_unixtime(create_time)) = MONTH(NOW()) and";
        $sql .= " year(from_unixtime(create_time)) = year(NOW()) and day(from_unixtime(create_time)) = day(NOW())) today,";
        $sql .= " (select count(*) from visit_online where month(from_unixtime(create_time)) = MONTH(NOW())) this_month,";
        $sql .= " (select count(*) from visit_online where week(from_unixtime(create_time)) = week(NOW())) this_week,";
        $sql .= " (select count(*) from visit_online where year(from_unixtime(create_time)) = year(NOW())) this_year";
        $q = $this->db->query($sql);
        return $q->result_array();
    }
    public function deleteData($table,$where){
        $this->db->trans_start();
        if(is_array($where)){
            foreach ($where as $key => $value) {
                $this->db->where($key,$value);
            }
        }
        $this->db->delete($table);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return false;
        }
        else
        {
            $this->db->trans_commit();
            return true;
        }
    }
    public function checkCaptcha($captcha,$expiration){
        $sql = "SELECT COUNT(*) AS count FROM captcha WHERE word = ? AND ip_address = ? AND captcha_time > ?"; 
        $binds = array($captcha, $this->input->ip_address(), $expiration); 
        $query = $this->db->query($sql, $binds); 
        $row = $query->row(); 
        return $row->count >0;
    }
    function getCaptcha(){
        $vals = array('img_path' => './captchas/', 
            'img_url' => base_url().'captchas/', 
            'font_path' => FCPATH.'captchas/MyriadProBold.otf',
            'img_width' => '120',
            'img_height' => '45',
            'word_length'=>'4',
            'expiration' => 7200);
        $cap = create_captcha($vals);
        $data = array('captcha_time' => $cap['time'], 'ip_address' => $this->input->ip_address(), 'word' => $cap['word']);
        
        $b_SaveData = $this->insertData('captcha',$data);
        return $cap['image'];
    }
    function getBanner($table,$id){
        $this->db->where('id',$id);
        $query=$this->db->get($table);
        $dl=$query->result_array();
        if(@$dl[0]['img']) return $dl[0]['img'];
        else return 0;
    }
    function getParent($table,$id){
        $this->db->where('id',$id);
        $query=$this->db->get($table);
        $dl=$query->result_array();
        if(@$dl[0]['parent']) return $dl[0]['parent'];
        else return 0;
    }
    function getAllParentCategories($id){
        $sql = "select id from pro_categories where FIND_IN_SET(?,parent)>0";
        $q = $this->db->query($sql,[$id]);
        $arr = $q->result_array();
        if(count($arr)>0){
            $id .=",";
            for ($i=0; $i < count($arr); $i++) { 
                $id .=$arr[$i]['id'];
                if($i<count($arr)-1){
                    $id.=",";
                }
            }
            $this->getAllParentCategories($id);
        }
        return $id;
    }
    function showLinkCate($id,$arr=array()) {
        // $sql = "select name, slug from news_categories where FIND_IN_SET(id,'$id')>0 limit 0,1";
        // $q = $this->db->query($sql);
        // $arr = $q->result_array();
        $arrid = explode(',',$id);
        $str = '';
        if(count($arr)>0) {
            for ($i=0; $i < count($arr); $i++) {
                for($j=0; $j<count($arrid); $j++) {
                    if($arrid[$j]==$arr[$i]['id']) {
                        $str = '<a class="post-cate" href="'.base_url().$arr[$i]['slug'].'" title="">'.$arr[$i]['name'].'</a>';
                        break;
                    }
                }
            }
        }
        return $str;
    }
    function showTagNews($id,$arr=array()) {
        $arrid = explode(',',$id);
        $str = '';
        if(count($arr)>0) {
            for ($i=0; $i < count($arr); $i++) {
                for($j=0; $j<count($arrid); $j++) {
                    if($arrid[$j]==$arr[$i]['id']) {
                        $str .= '<a class="smooth" href="'.base_url().'tag/'.$arr[$i]['link'].'" title="">'.$arr[$i]['name'].'</a>';
                        break;
                    }
                }
            }
        }
        return $str;
    }
    function showTitleCate($id=0,$name='',$arr=array()) {
        $str = '<h1 class="title">'.$name.'</h1>';
        if(count($arr)>0 && $id!=0) {
            for ($i=0; $i < count($arr); $i++) {
                if($id==$arr[$i]['id']) {
                    $str = '<h1 class="title"><span>'.$arr[$i]['name'].':</span> '.$name.'</h1>';
                    break;
                }
            }
        }
        return $str;
    }
    function getCateSon($table,$id){
        $this->db->where('parent',$id);
        $query=$this->db->get($table);
        return $query->result_array();
    }
    function getAllCate(){
        $this->db->where('act',1);
        $query=$this->db->get('news_categories');
        return $query->result_array();
    }
    function getAllTopic(){
        $this->db->where('act',1);
        $query=$this->db->get('tag');
        return $query->result_array();
    }
    function getInfoTable($table,$id){
        $this->db->where('id',$id);
        $query=$this->db->get($table);
        $dl=$query->result_array();
        return $dl;
    }
    function getNewsByCategory($parent){
        $sql = "select * from news where FIND_IN_SET(?,parent)>0 limit 0,10";
        $query=$this->db->query($sql,[$parent]);
        $dl=$query->result_array();
        return $dl;
    }
    
    function getNameCatById($id,$table){
        $table = "`".addslashes($table)."`";
        $sql = "select name from $table where id = ?";
        $q = $this->db->query($sql,[$id]);
        $kq = $q->result_array();
        if(count($kq)>0)
            return $kq[0]['name'];
        else
            return "";
    }
    function getYoutubeIdFromUrl($url) {
        $parts = parse_url($url);
        if(isset($parts['query'])){
            parse_str($parts['query'], $qs);
            if(isset($qs['v'])){
                return $qs['v'];
            }else if(isset($qs['vi'])){
                return $qs['vi'];
            }
        }
        if(isset($parts['path'])){
            $path = explode('/', trim($parts['path'], '/'));
            return $path[count($path)-1];
        }
        return "";
    }
    function getVideoHot($limit=1) {
        $where2=array();
        array_push($where2, array("key"=>"hot",'compare'=>'=','value'=>1));
        array_push($where2, array('key'=>'act','compare'=>'=','value'=>1));
        $list_video_hot = $this->Dindex->getDataDetail(array(
            'table'=>'videos',
            'where'=>$where2,
            'limit'=>"0,".$limit,
            'order'=>'id desc'
        ));
        return $list_video_hot;
    }
    /*add more code*/
    function countPage($totalPages, $currentPage)
    {
        if ($totalPages <= 10) {
            // less than 10 total pages so show all
            $startPage = 1;
            $endPage = $totalPages;
        } else {
            // more than 10 total pages so calculate start and end pages
            if ($currentPage <= 6) {
                $startPage = 1;
                $endPage = 10;
            } elseif ($currentPage + 4 >= $totalPages) {
                $startPage = $totalPages - 9;
                $endPage = $totalPages;
            } else {
                $startPage = $currentPage - 5;
                $endPage = $currentPage + 4;
            }
        }
        $pages = [];
        for ($i = $startPage; $i<$endPage + 1; $i++) {
            array_push($pages, $i);
        }
        return ['pages'=>$pages];
    }
    function generateBreadCrumbForPage($pageName)
    {
        echo '
        <ul class="breadcrumb" itemscope="" itemtype="http://schema.org/BreadcrumbList">
        <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="'.base_url().'"><span itemprop="name">Trang chủ</span></a>
        <meta itemprop="position" content="1">
        </li>
        <li itemprop="itemListElement" itemscope="" itemtype="http://schema.org/ListItem">
        <a itemprop="item" href="javascript:void(0)"><span itemprop="name">'.$pageName.'</span></a>
        <meta itemprop="position" content="2">
        </li>
        </ul>
        ';
    }
    public function isMobile() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
    }
    public function getActiveMenu($link, $id_menu){
        $this->db->where(array('link'=>$link,'menu'=>$id_menu));
        $q = $this->db->get('nuy_routes')->result_array();
        if(count($q) > 0){
            return 'active';
        }
        return '';
    }
}
?>