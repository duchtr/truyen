<?php 
function exifSettings(){
	return array(
  "title" => array("n"=>"Title","s"=>1,"note"=>""),
  "author" => array("n"=>"Author","s"=>1,"note"=>""),
  "authorsposition" => array("n"=>"Authors Position","s"=>0,"note"=>"Not used in Photoshop 7 or higher"),
  "caption" => array("n"=>"Caption","s"=>1,"note"=>"Description"),
  "captionwriter" => array("n"=>"Caption Writer","s"=>1,"note"=>"Description Writer"),
  "jobname" => array("n"=>"Jobname","s"=>0,"note"=>""),
  "copyrightstatus" => array("n"=>"Copyright Status","s"=>1,"note"=>"","v"=>array("Unknown"=>"Unknown","Copyrighted Work"=>"Copyrighted Work","Public Domain"=>"Public Domain")),
  "copyrightnotice" => array("n"=>"Copyright Notice","s"=>1,"note"=>""),
  "ownerurl" => array("n"=>"Copyright Info URL","s"=>1,"note"=>""),
  "keywords" => array("n"=>"Keywords","s"=>1,"note"=>""),
  "category" => array("n"=>"Category","s"=>1,"note"=>""),
  "supplementalcategories" => array("n"=>"Supplemental Categories","s"=>1,"note"=>""),
  "date" => array("n"=>"Date Created","s"=>1,"rv"=>date("Y-m-d"),"note"=>" Date must be YYYY-MM-DD format"),
  "city" => array("n"=>"City","s"=>1,"note"=>""),
  "state" => array("n"=>"State","s"=>1,"note"=>""),
  "country" => array("n"=>"Country","s"=>1,"note"=>""),
  "credit" => array("n"=>"Credit","s"=>1,"note"=>""),
  "source" =>array("n"=>"Source","s"=>1,"note"=>""),
  "headline" => array("n"=>"Headline","s"=>1,"note"=>""),
  "instructions" => array("n"=>"Instructions","s"=>1,"note"=>""),
  "transmissionreference" => array("n"=>"Transmission Reference","s"=>1,"note"=>""),
  "urgency" => array("n"=>"Urgency","s"=>1,"note"=>"Khẩn cấp","v"=>array(1=>"High",2=>"2",3=>"3",4=>"4",5=>"Normal",6=>"6",7=>"7",8=>"Low","none"=>"None")) );
}
function readexif($filename){
	include_once "Total.php";
        if ( ( ! isset( $new_ps_file_info_array ) ) &&
             ( isset( $filename ) ) &&
             ( is_string( $filename ) ) )
        {
                $GLOBALS['HIDE_UNKNOWN_TAGS'] = TRUE;
                $jpeg_header_data = get_jpeg_header_data( $filename );

                $Exif_array = get_EXIF_JPEG( $filename );
                $XMP_array = read_XMP_array_from_text( get_XMP_text( $jpeg_header_data ) );
                $IRB_array = get_Photoshop_IRB( $jpeg_header_data );
                $new_ps_file_info_array = get_photoshop_file_info( $Exif_array, $XMP_array, $IRB_array );


                if ( ( isset( $default_ps_file_info_array) ) &&
                     ( is_array( $default_ps_file_info_array) ) )
                {
                        if ( ( ! array_key_exists( 'date', $default_ps_file_info_array ) ) ||
                             ( ( array_key_exists( 'date', $default_ps_file_info_array ) ) &&
                               ( $default_ps_file_info_array['date'] == '' ) ) )
                        {
                                if ( ( $Exif_array != FALSE ) &&
                                     ( array_key_exists( 0, $Exif_array ) ) &&
                                     ( array_key_exists( 34665, $Exif_array[0] ) ) &&
                                     ( array_key_exists( 0, $Exif_array[0][34665] ) ) &&
                                     ( array_key_exists( 36867, $Exif_array[0][34665][0] ) ) )
                                {
                                        $default_ps_file_info_array['date'] = $Exif_array[0][34665][0][36867]['Data'][0];
                                        $default_ps_file_info_array['date'] = preg_replace( "/(\d\d\d\d):(\d\d):(\d\d)( \d\d:\d\d:\d\d)/", "$1-$2-$3", $default_ps_file_info_array['date'] );
                                }
                                else if ( ( $Exif_array != FALSE ) &&
                                     ( array_key_exists( 0, $Exif_array ) ) &&
                                     ( array_key_exists( 34665, $Exif_array[0] ) ) &&
                                     ( array_key_exists( 0, $Exif_array[0][34665] ) ) &&
                                     ( array_key_exists( 36868, $Exif_array[0][34665][0] ) ) )
                                {
                                        $default_ps_file_info_array['date'] = $Exif_array[0][34665][0][36868]['Data'][0];
                                        $default_ps_file_info_array['date'] = preg_replace( "/(\d\d\d\d):(\d\d):(\d\d)( \d\d:\d\d:\d\d)/", "$1-$2-$3", $default_ps_file_info_array['date'] );
                                }
                                else if ( ( $Exif_array != FALSE ) &&
                                     ( array_key_exists( 0, $Exif_array ) ) &&
                                     ( array_key_exists( 306, $Exif_array[0] ) ) )
                                {
                                        $default_ps_file_info_array['date'] = $Exif_array[0][306]['Data'][0];
                                        $default_ps_file_info_array['date'] = preg_replace( "/(\d\d\d\d):(\d\d):(\d\d)( \d\d:\d\d:\d\d)/", "$1-$2-$3", $default_ps_file_info_array['date'] );
                                }
                                else
                                {
                                        $default_ps_file_info_array['date'] = date ("Y-m-d", filectime( $filename ));
                                }
                        }
                        foreach( $default_ps_file_info_array as $def_key =>$default_item )
                        {
                                if ( ( strcasecmp( $def_key, "keywords" ) == 0 ) ||
                                     ( strcasecmp( $def_key, "supplementalcategories" ) == 0 ) )
                                {
                                        if ( ( count( $new_ps_file_info_array[ $def_key ] ) == 0 ) &&
                                             ( is_array( $default_item ) ) &&
                                             ( count( $default_item ) >= 0 ) )
                                        {
                                                $new_ps_file_info_array[ $def_key ] = $default_item;
                                        }
                                }
                                else if ( trim( $new_ps_file_info_array[ $def_key ] ) == "" )
                                {
                                        $new_ps_file_info_array[ $def_key ] = $default_item;
                                }

                        }
                }
        }
        else if ( ( ( !isset($new_ps_file_info_array) ) || ( ! is_array($new_ps_file_info_array) ) ) &&
                  ( ( !isset($filename) ) || ( ! is_string( $filename ) ) ) )
        {
                $new_ps_file_info_array = array(
                      "title" => "",
                      "author" => "",
                      "authorsposition" => "",
                      "caption" => "",
                      "captionwriter" => "",
                      "jobname" => "",
                      "copyrightstatus" => "",
                      "copyrightnotice" => "",
                      "ownerurl" => "",
                      "keywords" => array(),
                      "category" => "",
                      "supplementalcategories" => array(),
                      "date" => "",
                      "city" => "",
                      "state" => "",
                      "country" => "",
                      "credit" => "",
                      "source" => "",
                      "headline" => "",
                      "instructions" => "",
                      "transmissionreference" => "",
                      "urgency" => "" );
        }
       return $new_ps_file_info_array;
}
function writeexif($filename,$new_ps_file_info_array){
	include_once "Total.php";
    foreach( $new_ps_file_info_array as $var_key => $var_val )
    {
            $new_ps_file_info_array[ $var_key ] = stripslashes( $var_val );
    }
    $new_ps_file_info_array[ 'keywords' ] =explode( ",", trim( $new_ps_file_info_array[ 'keywords' ] ) );
    $new_ps_file_info_array[ 'supplementalcategories' ] = explode( ",", trim( $new_ps_file_info_array[ 'supplementalcategories' ] ) );
    $path_parts = pathinfo( $filename );
    if ( strcasecmp( $path_parts["extension"], "jpg" ) != 0 )
    {
            //"Incorrect File Type - JPEG Only\n";
        return false;
    }
    $jpeg_header_data = get_jpeg_header_data( $filename );
    $Exif_array = get_EXIF_JPEG( $filename );
    $XMP_array = read_XMP_array_from_text( get_XMP_text( $jpeg_header_data ) );
    $IRB_array = get_Photoshop_IRB( $jpeg_header_data );
    $jpeg_header_data = put_photoshop_file_info( $jpeg_header_data, $new_ps_file_info_array, $Exif_array, $XMP_array, $IRB_array );
    if ( $jpeg_header_data == FALSE )
    {
            // "Error - Failure update Photoshop File Info : $filename <br>\n";
         return false;
    }

    // Attempt to write the new JPEG file
    if ( FALSE == put_jpeg_header_data( $filename, $filename, $jpeg_header_data ) )
    {
        
        // "Error - Failure to write new JPEG : $filename <br>\n";
        return false;
    }
    return true;
}

 ?>