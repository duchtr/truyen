<?php 
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Firewall { 
     
    protected $msg_proxy = 'Hệ Thống Đang Chặn Proxy'; 
     
    protected $ip_address; 
     
    protected $CI; 
     
    protected $folder; 
     
    function load() 
    { 
         
        $this->CI = & get_instance(); 

        $this->CI->config->load('firewall'); 
         
        $this->CI->load->helper('file'); 
         
         
        $this->ip_address = $this->getIp(); 
         
        $this->folder = $this->CI->config->item('folder'); 
    } 
     
    function blockProxy(){//kiểm tra coi có sử dụng Proxy         
        $proxy_headers = array(   
            'HTTP_VIA',   
            'HTTP_X_FORWARDED_FOR',   
            'HTTP_FORWARDED_FOR',   
            'HTTP_X_FORWARDED',   
            'HTTP_FORWARDED',   
            'HTTP_CLIENT_IP',   
            'HTTP_FORWARDED_FOR_IP',   
            'VIA',   
            'X_FORWARDED_FOR',   
            'FORWARDED_FOR',   
            'X_FORWARDED',   
            'FORWARDED',   
            'CLIENT_IP',   
            'FORWARDED_FOR_IP',   
            'HTTP_PROXY_CONNECTION'   
        ); 
        foreach($proxy_headers as $x){ 
            if (isset($_SERVER[$x]))  
                return true; 
            else 
                return false; 
        } 
    } 
     
    function getIp(){//lấy IP 
         
        $do_check = 0; 
        $addrs = array(); 
     
        if( $do_check ) 
        { 
            foreach( array_reverse(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])) as $x_f ) 
            { 
                $x_f = trim($x_f); 
                 
                if( preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $x_f) ) 
                { 
                    $addrs[] = $x_f; 
                } 
            }     
            $addrs[] = $_SERVER['HTTP_CLIENT_IP']; 
            $addrs[] = $_SERVER['HTTP_PROXY_USER']; 
        }     
        $addrs[] = $_SERVER['REMOTE_ADDR'];     
        foreach( $addrs as $v ){ 
            if( $v ) 
            {   
                if($v=="::1") return "127.0.0.1";
                preg_match("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$/", $v, $match); 
                if(count($match)<5) break;
                $ip = $match[1].'.'.$match[2].'.'.$match[3].'.'.$match[4]; 
     
                if( $ip && $ip != '...' ) 
                { 
                    break; 
                } 
            } 
        }     
        if( ! $ip || $ip == '...' ) 
        { 
            return false; 
        }     
        return $ip; 
    } 
     
    function open(){ 
         
        $this->load();//khởi tao những thứ cần thiết. ko dung construc để tránh làm nặng trang 
         
        $now = time(); 
         
        $wait = 0; 
         
        //kiểm tra xem ip bị khóa vĩnh viễn ko 
        if (file_exists($this->folder. $this->ip_address .'.deny')){             
            $htaccess = 'deny from '. $this->ip_address."\n"; 
                         
            write_file($this->CI->config->item('htaccess'), $htaccess,'a');//ko cần gỡ vì IP đã bị chặn 
             
            $this->msg(1,-1); 
        }elseif( file_exists($this->folder. $this->ip_address .'.lock') ){//ip bị khóa tạm thời                         
            @$time = file_get_contents($this->folder. $this->ip_address.'.lock'); 
                         
            if (file_exists($this->folder. $this->ip_address .'.lockcount')) 
                $lock_count = file_get_contents($this->folder. $this->ip_address .'.lockcount'); 
            else 
                $lock_count = 0; 
             
            $wait = (($this->CI->config->item('time_wait')*($lock_count+1)) + $time) - $now; 
             
            if ($wait > 0) //chưa hết thời gian mở khóa 
             
                $this->msg(2,$wait); 
                 
            else { // hết thời gian. mở khóa cho ip 
                 
                @unlink($this->folder. $this->ip_address.'.lock');     
                             
                write_file($this->folder. $this->ip_address, "1|".$now,'w'); 
                 
            } 
        }else {//Neu chua bi khoa vinh vien va tam thoi 
            //Kiem tra xem IP nay da tung truy cap chua 
            if (file_exists($this->folder. $this->ip_address)){ 
            //Neu IP nay da tung truy cap thi kiem tra so ket noi     
                @$c=file_get_contents($this->folder. $this->ip_address); 
                     
                $explode = explode("|",$c); 
                 
                if ( ($explode[0]+1) >= $this->CI->config->item('max_connect') &&  
                     ($now - $explode[1]) <= $this->CI->config->item('time_limit') ){ 
                     
                    write_file($this->folder. $this->ip_address.'.lock',$now,'w');//vượt giới hạn tạo file lock 
                     
                    if (file_exists($this->folder. $this->ip_address.".lockcount"))                         
                        @$lock_count = file_get_contents($this->folder. $this->ip_address.".lockcount"); 
                    else 
                        $lock_count = 0; 
                     
                    if ( ($lock_count+1) >= $this->CI->config->item('max_lockcount') ){//kiểm tra số lần vi phạm 
                     
                        write_file($this->folder. $this->ip_address.'.lockcount',($lock_count+1)."|".$now,'w'); 
                         
                        write_file($this->folder. $this->ip_address.'.deny',null,'w'); 
                         
                        $htaccess = 'deny from '. $this->ip_address."\n"; 
                         
                        write_file($this->CI->config->item('htaccess'), $htaccess,'a'); 
                         
                        $this->msg(1,-1); 
                         
                    }else{ 
                        write_file($this->folder. $this->ip_address.'.lockcount',($lock_count+1)."|".$now,'w'); 
                        $this->msg(2,$wait); 
                    } 
                      
                }elseif (($explode[0]+1) < $this->CI->config->item('max_connect') &&  
                         ($now - $explode[1]) >= $this->CI->config->item('time_limit') ){ 
                              
                    write_file($this->folder. $this->ip_address,"1|".$now,'w'); 
                }else 
                    write_file($this->folder. $this->ip_address,($explode[0]+1)."|".$now,'w'); 
                     
            }else//Neu IP nay chua tung truy cap                                     
                write_file($this->folder. $this->ip_address,"1|".$now,'w');     
        }//#Neu chua bi khoa vinh vien va tam thoi 
    } 
     
    function msg($err,$time = 60){ 
        /* 
        1 : khóa vĩnh viễn 
        2 : chưa hết thời gian mở khóa 
        */ 
        switch($err){ 
            case '1': 
                $err = 'Hệ thống phát hiện truy cập của bạn có vấn đề. Để đảm bảo an toàn cho hệ thống truy cập của 
                bạn bị chặn. <br> Vui lòng kiểm tra lại các phần mềm chạy ngầm <br> 
                Truy cập của bạn sẽ được tự động mở khóa sau 24h'; 
            break; 
             
            case '2': 
                $err = 'Truy cập của bạn bị tạm khóa để đảm bảo an toàn cho hệ thống.'; 
            break; 
             
            default: 
                $err = 'Địa chỉ IP của bị nghi vấn.'; 
            break; 
        } 
         
        $content = array('err'=>$err, 
                         'time'=>$time); 
         
        echo $this->CI->load->view('firewall',$content,TRUE); 
         
        die(); 
    } 
} 

?>