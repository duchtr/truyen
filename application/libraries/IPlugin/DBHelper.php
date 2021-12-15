<?php
class DBHelper{
	public static function addColumnSql($table,$column,$sql){
		$CI = &get_instance();
		$_sql = "SELECT column_name 
          FROM information_schema.COLUMNS 
          WHERE 
          TABLE_SCHEMA = database() 
          AND TABLE_NAME = '%s' 
          AND COLUMN_NAME = '%s'";
         $_sql = sprintf($_sql,$table,$column);
        $count = $CI->db->query($_sql)->num_rows();
        if($count==0){
        	$CI->db->query($sql);
        }
	}
	public static function removeColumn($table,$column){
		$CI = &get_instance();
		$_sql = "SELECT column_name 
          FROM information_schema.COLUMNS 
          WHERE 
          TABLE_SCHEMA = database() 
          AND TABLE_NAME = '%s' 
          AND COLUMN_NAME = '%s'";
         $_sql = sprintf($_sql,$table,$column);
        $count = $CI->db->query($_sql)->num_rows();
        if($count>0){
        	$CI->db->query(sprintf("ALTER TABLE %s DROP COLUMN %s",$table,$column));
        }
	}
}