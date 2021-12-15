<?php 
class HQuery{
    protected $matches;
    protected $prefix;
    protected $value;
    public function __construct($value,$matches,$prefix){
        $this->matches= $matches;
        $this->prefix= $prefix;
        $this->value= $value;
    }
    public function getQuery(){
        if(count($this->matches)>3){
            $name = $this->prefix[0];
            $arrTable = explode('.', $name);
            if(count($arrTable)>=2){
                switch ($arrTable[0]) {
                    case 'loop':
                    case 'l':
                        return $this->getQSelect($arrTable);
                    case 'menu':
                    case 'm':
                    return $this->getQMenu();
                }
            }
        }
    }
    private function getQMenu(){
    $arrCondition = array();
    for ($i=1; $i < count($this->prefix) ; $i++) { 
        $tmp = $this->prefix[$i];
        $arrTmp = explode(':', $tmp);
        if(count($arrTmp)>1){
            switch ($arrTmp[0]) {
                case 'where':
                case 'w':
                    $arrTmpp1 = explode(",", $arrTmp[1]);
                    $str ="";
                    for($t =0;$t<count($arrTmpp1);$t++){
                        $tmp2 =$arrTmpp1[$t];
                        $arrTmp1 = str_replace(";", ",", $tmp2);
                        $tmp3= explode(" ", $tmp2);
                        $compare = trim($tmp3[1]);
                        if($compare=='not_in'){
                            $compare = 'not in';
                        }
                        if(count($tmp3)>2){
                            $str.="array('key'=>'".trim($tmp3[0])."','compare'=>'".$compare."','value'=>'".trim($tmp3[2])."')";
                            if($t<count($arrTmpp1)-1) $str.=",";    
                        }
                        
                    }
                    $arrCondition['where'] = $str;
                    break;
                case 'config':
                case 'c':
                    $arrTmp2 = explode(',', $arrTmp[1]);
                    $str ="array(";
                     $ii=0;
                     foreach ($arrTmp2 as $key => $value) {
                         $exvalue = explode('=',$value);
                        $str .="'".$exvalue[0]."'=>'".$exvalue[1]."'";
                        if($ii<count($arrTmp2)-1) $str .=",";
                        $ii++;
                        
                     }
                     $str .=")";
                     $arrCondition['config'] = $str;
                break;
                case 'input':
                case 'i':
                    $arrCondition['input'] = $arrTmp[1];
                break;
            }
            
        }   
        else break;         
    }
    $config = "array()";
    if(array_key_exists('config', $arrCondition)){
        $config = $arrCondition['config'];
    }
    $where = "''";
    if(array_key_exists('where', $arrCondition)){
        $where = "array(".$arrCondition['where'].")";
    }
    $input = '"*"';
    if(array_key_exists('input', $arrCondition)){
      $input = '"'.$arrCondition['input'].'"';
    }
    $ret='<?php $arr = $this->CI->Dindex->recursiveTable('.$input.',"menu","parent","id","0",'.$where.'); ?><?php printMenu($arr,'.$config.'); ?>';
    return $ret;
    }
    private function exceptKeyWhere($key){
        $exs = ['$','array','['];
        foreach ($exs as $k => $ex) {
            if(strpos($key, $ex)!==FALSE){
                return $key;
            }
        }
        return "'".$key."'";
    }
    private function getQSelect($arrTable){
    $arrCondition = array();
    $pivots = [];
    for ($i=1; $i < count($this->prefix) ; $i++) { 
        $tmp = $this->prefix[$i];
        $arrTmp = explode(':', $tmp);

        if(count($arrTmp)>1){
            switch ($arrTmp[0]) {
                case 'where':
                case 'w':
                    $arrTmp1 = explode(",", $arrTmp[1]);
                    $str ="";
                    for($t =0;$t<count($arrTmp1);$t++){

                        $tmp2 =$arrTmp1[$t];
                        $tmp2 = str_replace(";", ",", $tmp2);
                        $tmp3= explode(" ", $tmp2);
                        if(count($tmp3)>2){
                            $vr = trim($tmp3[2]);
                            $vr = $this->exceptKeyWhere($vr);
                            $compare = trim($tmp3[1]);
                            if($compare=='not_in'){
                                $compare = 'not in';
                            }
                            $str.="array('key'=>'".trim($tmp3[0])."','compare'=>'".$compare."','value'=>".$vr.")";
                            if($t<count($arrTmp1)-1) $str.=",";    
                        }
                        
                    }
                    $arrCondition['where'] = $str;
                    break;
                case 'limit':
                case 'l':
                    $arrCondition['limit']=$arrTmp[1];
                break;
                case 'input':
                case 'i':
                    $arrCondition['input']=$arrTmp[1];
                break;
                case 'order':
                case 'o':
                    $arrCondition['order']=$arrTmp[1];
                break;
                case 'rep':
                case 'r':
                    $arrCondition['rep']=$arrTmp[1];
                    break;
                case 'pvalue':
                    $pivots["value"] =  $arrTmp[1];
                    break;
                case 'pfield':
                    $pivots["field"] =  $arrTmp[1];
                    break;
                case 'ptable':
                    $pivots["ptable"] =  $arrTmp[1];
                    break;
                break;
            }
            
        }   
        else break;         
    }
    $input = '"*"';
    if(array_key_exists('input', $arrCondition)){
        $input = "'".$arrCondition['input']."'";
    }
    $order = "'ord asc,id desc'";
    if(array_key_exists('order', $arrCondition)){
        $order = "'".$arrCondition['order']."'";
    }
    $where = "''";
    if(array_key_exists('where', $arrCondition)){
        $where = "array(".$arrCondition['where'].")";
    }
    $limit = "''";
    if(array_key_exists('limit', $arrCondition)){
        $limit = "'".$arrCondition['limit']."'";
    }
    $pivots  = '['.implode(', ', array_map(
        function ($v, $k) { return sprintf("'%s'=>%s", $k, is_string($v)&&strpos($v, '$')===FALSE?"'".$v."'":$v); },
        $pivots,
        array_keys($pivots)
    )).']';
    $nameArrayResult = $arrTable[1].(count($arrTable)>2?$arrTable[2]:"");
    if(!array_key_exists('rep', $arrCondition)){
        $ret = '<?php
            $arr'.$nameArrayResult." = $"."this->CI->Dindex->getDataDetail(
                array(
                    'input'=>".$input.",
                    'table'=>'".$arrTable[1]."',
                    'order'=>".$order.",
                    'where'=>".$where.",
                    'limit'=>".$limit.",
                    'pivot'=>".$pivots."
                )
            );
         ?>";
    }
    else{
        $ret = '<?php
            $arr'.$nameArrayResult.' = $'.$arrCondition['rep'].' ;?>';
    }
     $ret .='<?php $count'.$nameArrayResult.' = count($arr'.$nameArrayResult.');
     for ($i'.$nameArrayResult.'=0; $i'.$nameArrayResult.' < $count'.$nameArrayResult.'; $i'.$nameArrayResult.'++) { $item'.$nameArrayResult.'=$arr'.$nameArrayResult.'[$i'.$nameArrayResult.']; ?>'.
      $this->matches[3]
    .'<?php }; ?>';
    return $ret;
    }
}
?>