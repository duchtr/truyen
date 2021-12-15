<?php 
function get_XMP_text( $jpeg_header_data )
{
        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP1 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP1" ) == 0 )
                {
                        // And if it has the Adobe XMP/RDF label (http://ns.adobe.com/xap/1.0/\x00) ,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "http://ns.adobe.com/xap/1.0/\x00", 29) == 0 )
                        {
                                // Found a XMP/RDF block
                                // Return the XMP text
                                $xmp_data = substr ( $jpeg_header_data[$i]['SegData'], 29 );

                                return $xmp_data;
                        }
                }
        }
        return FALSE;
}

/******************************************************************************
* End of Function:     get_XMP_text
******************************************************************************/






/******************************************************************************
*
* Function:     put_XMP_text
*
* Description:  Adds or modifies the Extensible Metadata Platform (XMP) information
*               in an App1 JPEG segment. If a XMP segment already exists, it is
*               replaced, otherwise a new one is inserted, using the supplied data.
*               Uses information supplied by the get_jpeg_header_data function
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*               newXMP - a string containing the XMP text to be stored in the XMP
*                        segment. Should be constructed using the write_XMP_array_to_text
*                        function
*
* Returns:      jpeg_header_data - the JPEG header data array with the
*                                  XMP segment added.
*               FALSE - if an error occured
*
******************************************************************************/

function put_XMP_text( $jpeg_header_data, $newXMP )
{
        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP1 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP1" ) == 0 )
                {
                        // And if it has the Adobe XMP/RDF label (http://ns.adobe.com/xap/1.0/\x00) ,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "http://ns.adobe.com/xap/1.0/\x00", 29) == 0 )
                        {
                                // Found a preexisting XMP/RDF block - Replace it with the new one and return.
                                $jpeg_header_data[$i]['SegData'] = "http://ns.adobe.com/xap/1.0/\x00" . $newXMP;
                                return $jpeg_header_data;
                        }
                }
        }

        // No pre-existing XMP/RDF found - insert a new one after any pre-existing APP0 or APP1 blocks
        // Change: changed to initialise $i properly as of revision 1.04
        $i = 0;
        // Loop until a block is found that isn't an APP0 or APP1
        while ( ( $jpeg_header_data[$i]['SegName'] == "APP0" ) || ( $jpeg_header_data[$i]['SegName'] == "APP1" ) )
        {
                $i++;
        }



        // Insert a new XMP/RDF APP1 segment at the specified point.
        // Change: changed to properly construct array element as of revision 1.04 - requires two array statements not one, requires insertion at $i, not $i - 1
        array_splice($jpeg_header_data, $i, 0, array( array(       "SegType" => 0xE1,
                                                                        "SegName" => "APP1",
                                                                        "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE1 ],
                                                                        "SegData" => "http://ns.adobe.com/xap/1.0/\x00" . $newXMP ) ) );

        // Return the headers with the new segment inserted
        return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_XMP_text
******************************************************************************/






/******************************************************************************
*
* Function:     read_XMP_array_from_text
*
* Description:  An alias for read_xml_array_from_text.
*               Parses a string containing XMP data (XML), and returns the resulting
*               tree structure array, which contains all the XMP (XML) information.
*               Note: White space and comments in the XMP data (XML) are ignored
*               Note: All text information contained in the tree structure
*                     is encoded as Unicode UTF-8. Hence text will appear as
*                     normal ASCII except where there is an extended character.
*
* Parameters:   xmptext - a string containing the XMP data (XML) to be parsed
*
* Returns:      output - the tree structure array containing the XMP (XML) information
*               FALSE - if an error occured
*
******************************************************************************/

function read_XMP_array_from_text( $xmptext )
{
        return read_xml_array_from_text( $xmptext );
}

/******************************************************************************
* End of Function:     read_XMP_array_from_text
******************************************************************************/





/******************************************************************************
*
* Function:     write_XMP_array_to_text
*
* Description:  Takes a tree structure array containing XMP (in the same format
*               as returned by read_XMP_array_from_text, and constructs a string
*               containing the equivalent XMP, including the XMP Packet header
*               and trailer. Produces XMP text which has correct indents, encoded
*               using UTF-8.
*               Note: All text information contained in the tree structure
*                     can be either 7-bit ASCII or encoded as Unicode UTF-8,
*                     since UTF-8 passes 7-bit ASCII text unchanged.
*
* Parameters:   xmparray - the tree structure array containing the information to
*                          be converted to XMP text
*
* Returns:      output_XMP_text - the string containing the equivalent XMP text
*
******************************************************************************/

function write_XMP_array_to_text( $xmparray )
{
        // Add the XMP packet header
        // The sequence 0xEFBBBF is the UTF-8 encoded version of the Unicode “zero
        // width non-breaking space character” (U+FEFF), which is used for detecting
        // whether UTF-16 or UTF-8 is being used.
        $output_XMP_text = "<?xpacket begin='\xef\xbb\xbf' id='W5M0MpCehiHzreSzNTczkc9d'?>\n";

        // Photoshop Seems to add this, but there doesn't appear to be
        // any information on what it means
        // TODO : XMP, Find out what the adobe-xap-filters tag means
        $output_XMP_text .= "<?adobe-xap-filters esc=\"CR\"?>\n";

        // Add the XML text
        $output_XMP_text .= write_xml_array_to_text( $xmparray, 0 );


        // The XMP standard recommends adding 2-4k of white space at the
        // end for in place editing, so we will add it to the XML now
        $output_XMP_text .= str_repeat("                                                                                                   \n", 30);

        // Add the XMP packet trailer
        $output_XMP_text .= "<?xpacket end='w'?>";

        // Return the resulting XMP text
        return $output_XMP_text;
}

/******************************************************************************
* End of Function:     write_XMP_array_to_text
******************************************************************************/






/******************************************************************************
*
* Function:     Interpret_XMP_to_HTML
*
* Description:  Generates html showing the information contained in an Extensible
*               Metadata Platform (XMP) tree structure array, as retrieved
*               with read_XMP_array_from_text
*
* Parameters:   XMP_array - a XMP tree structure array as from read_XMP_array_from_text
*
* Returns:      output - the HTML string
*
******************************************************************************/

function Interpret_XMP_to_HTML( $XMP_array )
{
        // Create a string to receive the output html
        $output ="";

        // Check if the XMP tree structure array is valid
        if ( $XMP_array !== FALSE )
        {
                // Check if there is a rdf:RDF tag at either the first or second level
                if ( ( $XMP_array[0]['tag'] ==  "x:xapmeta" ) && ( $XMP_array[0]['children'][0]['tag'] ==  "rdf:RDF" ) )
                {
                        // RDF found at second level - Save it's position
                        $RDF_Contents = &$XMP_array[0]['children'][0]['children'];
                }
                else if ( ( $XMP_array[0]['tag'] ==  "x:xmpmeta" ) && ( $XMP_array[0]['children'][0]['tag'] ==  "rdf:RDF" ) )
                {
                        // RDF found at second level - Save it's position
                        $RDF_Contents = &$XMP_array[0]['children'][0]['children'];
                }
                else if ( $XMP_array[0]['tag'] ==  "rdf:RDF" )
                {
                        // RDF found at first level - Save it's position
                        $RDF_Contents = &$XMP_array[0]['children'];
                }
                else
                {
                        // RDF section not found - abort
                        return "";
                }

                // Add heading to html output
                $output .= "<h2 class=\"XMP_Main_Heading\">Contains Extensible Metadata Platform (XMP) / Resource Description Framework (RDF) Information</h2>\n";

                // Cycle through each of the items in the RDF tree array, and process them
                foreach ($RDF_Contents as $RDF_Item)
                {
                        // Check if the item is a rdf:Description tag - these are the only ones that can be processed

                        if ( ( $RDF_Item['tag'] == "rdf:Description" ) && ( array_key_exists( 'children', $RDF_Item ) ) )
                        {
                                // Item is a rdf:Description tag.

                                // Cycle through each of the attributes for this tag, looking
                                // for a xmlns: attribute, which tells us what Namespace the
                                // sub-items will be in.
                                foreach( $RDF_Item['attributes'] as $key => $val )
                                {
                                        // Check for the xmlns: namespace attribute
                                        if ( substr( $key,0,6) == "xmlns:" )
                                        {
                                                // Found a xmlns: attribute
                                                // Extract the namespace string
                                                // Add heading to the HTML according to which Namespace the RDF items have
                                                switch ( substr( $key,6) )
                                                {
                                                        case "photoshop":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Photoshop RDF Segment</h3>\n";
                                                                break;
                                                        case "xapBJ":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Basic Job Ticket RDF Segment</h3>\n";
                                                                break;
                                                        case "xapMM":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Media Management RDF Segment</h3>\n";
                                                                break;
                                                        case "xapRights":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Rights Management RDF Segment</h3>\n";
                                                                break;
                                                        case "dc":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Dublin Core Metadata Initiative RDF Segment</h3>\n";
                                                                break;
                                                        case "xmp":
                                                        case "xap":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">XMP Basic Segment</h3>\n";
                                                                break;
                                                        case "xmpTPg":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">XMP Paged-Text Segment</h3>\n";
                                                                break;
                                                        case "xmpTPg":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Adobe PDF Segment</h3>\n";
                                                                break;
                                                        case "tiff":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">XMP - embedded TIFF Segment</h3>\n";
                                                                break;
                                                        case "exif":
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">XMP - embedded EXIF Segment</h3>\n";
                                                                break;
                                                        case "xapGImg":  // Sub Category - Do nothing
                                                                break;
                                                        case "stDim":  // Sub Category - Do nothing
                                                                break;
                                                        case "stEvt":  // Sub Category - Do nothing
                                                                break;
                                                        case "stRef":  // Sub Category - Do nothing
                                                                break;
                                                        case "stVer":  // Sub Category - Do nothing
                                                                break;
                                                        case "stJob":  // Sub Category - Do nothing
                                                                break;

                                                        default:
                                                                $output .= "<h3 class=\"XMP_Secondary_Heading\">Unknown RDF Segment '" . substr( $key,6) . "'</h3>\n";
                                                                break;
                                                }


                                        }

                                }

                                // Add the start of the table to the HTML output
                                $output .= "\n<table  class=\"XMP_Table\" border=1>\n";


                                // Check if this element has sub-items

                                if ( array_key_exists( 'children', $RDF_Item ) )
                                {

                                        // Cycle through each of the sub-items
                                        foreach( $RDF_Item['children'] as $child_item )
                                        {
                                                // Get an interpretation of the sub-item's caption and value
                                                list($tag_caption, $value_str) = Interpret_RDF_Item( $child_item );

                                                // Escape the text of the caption for html
                                                $tag_caption = HTML_UTF8_Escape( $tag_caption );
                                                // Escape the text of the value for html and turn newlines to <br>
                                                $value_str = nl2br( HTML_UTF8_Escape( $value_str ) );

                                                // Check if the value is empty - if it is, put a no-break-space in
                                                // to ensure the table cell gets drawn
                                                if ( $value_str == "" )
                                                {
                                                        $value_str = "&nbsp;";
                                                }
                                                // Add the table row to the output html
                                                $output .= "<tr class=\"XMP_Table_Row\"><td  class=\"XMP_Caption_Cell\">" . $tag_caption . ":</td><td  class=\"XMP_Value_Cell\">" . $value_str . "</td></tr>\n";
                                        }
                                }

                                // Add the end of the table to the html
                                $output .= "\n</table>\n";


                        }
                        else
                        {
                                // Don't know how to process tags other than rdf:Description - do nothing
                        }
                }



        }
        // Return the resulting HTML
        return $output;
}

/******************************************************************************
* End of Function:     Interpret_XMP_to_HTML
******************************************************************************/


















/******************************************************************************
*
*         INTERNAL FUNCTIONS
*
******************************************************************************/












/******************************************************************************
*
* Internal Function:     Interpret_RDF_Item
*
* Description:  Used by Interpret_XMP_to_HTML
*               Used by get_RDF_field_html_value
*               Used by interpret_RDF_collection
*               Generates a caption and text representation of the value of a
*               particular RDF item.
*
* Parameters:   Item - The RDF item to evaluate
*
* Returns:      tag_caption - the caption of the tag
*               value_str - the text representation of the value
*
******************************************************************************/

function Interpret_RDF_Item( $Item )
{

        // TODO: Many RDF items have not been tested - only photoshop 7.0 and CS items

        // Create a string to receive the HTML output
        $value_str = "";

        // Check if the item has is in the lookup table of tag captions
        if ( array_key_exists( $Item['tag'], $GLOBALS[ 'XMP_tag_captions' ] ) )
        {
                // Item is in list of captions, get the caption
                $tag_caption = $GLOBALS[ 'XMP_tag_captions' ][ $Item['tag'] ];
        }
        else
        {
                // Item has no caption - make one
                $tag_caption = "Unknown field " . $Item['tag'];
        }


        // Process specially the item according to it's tag
        switch ( $Item['tag'] )
        {

                case "photoshop:DateCreated":            // This is in year month day order
                        // Extract the year,month and day
                        list( $year, $month, $day ) = sscanf( $Item['value'], "%d-%d-%d" );
                        // Make a new date string with Day, Month, Year
                        $value_str = "$day/$month/$year";
                        break;

                default :
                        $value_str = get_RDF_field_html_value( $Item );
                        break;
        }




        // Return the captiona and value
        return array($tag_caption, $value_str);
}


/******************************************************************************
* End of Function:     Interpret_RDF_Item
******************************************************************************/





/******************************************************************************
*
* Internal Function:     get_RDF_field_html_value
*
* Description:  Attempts to build a text representation of the value of an RDF
*               item. This includes handling any collections or sub-resources.
*
* Parameters:   rdf_item - The RDF item to evaluate
*
* Returns:      output_str - the text representation of the field value
*
******************************************************************************/

function get_RDF_field_html_value( $rdf_item )
{
        // Create a string to receive the output text
        $output_str = "";

        // Check if the item has a value
        if ( array_key_exists( 'value', $rdf_item ) )
        {
                // The item does have a value - add it to the text
                $output_str .= $rdf_item['value'];
        }

        // Check if the item has any attributes
        if ( array_key_exists( 'attributes', $rdf_item ) )
        {
                // Cycle through each of the attributes
                foreach( $rdf_item['attributes'] as $key => $val )
                {
                        // Check if this attribute is rdf:parseType = 'Resource' i.e. a sub-resource indicator
                        if ( ( $key == "rdf:parseType" ) && ( $val == "Resource" ) )
                        {
                                // This item has a attribute indicating sub-resources
                                // Check that the item has sub items
                                if ( array_key_exists( 'children', $rdf_item ) )
                                {
                                        // The item does have sub-items,
                                        // Cycle through each, Interpreting them and adding the result to the output text
                                        foreach( $rdf_item['children'] as $child )
                                        {
                                                list($tag_caption, $value_str) = Interpret_RDF_Item( $child );
                                                $output_str .= "$tag_caption  =  $value_str\n";
                                        }
                                        // The output text will have an extra \n on it - remove it
                                        $output_str = rtrim( $output_str );
                                }
                        }
                }
        }
                // If the item did not have sub-resources, it may still have sub-items - check for this
        else if ( array_key_exists( 'children', $rdf_item ) )
        {
                // Non-resource Sub-items found, Cycle through each
                foreach( $rdf_item['children'] as $child_item )
                {
                        // Check if this sub-item has a tag
                        if ( array_key_exists( 'tag', $child_item ) )
                        {
                                // Sub item has a tag, Process it according to the tag
                                switch ( $child_item[ 'tag' ] )
                                {
                                        // Collections
                                        case "rdf:Alt":
                                                $output_str .= "List of Alternates:\n";
                                                $output_str .= interpret_RDF_collection( $child_item );
                                                break;

                                        case "rdf:Bag":
                                                $output_str .= "Unordered List:\n";
                                                $output_str .= interpret_RDF_collection( $child_item );
                                                break;

                                        case "rdf:Seq":
                                                $output_str .= "Ordered List:\n";
                                                $output_str .= interpret_RDF_collection( $child_item );
                                                break;

                                        // Sub-Resource
                                        case "rdf:Description":
                                                // Check that the item has sub items
                                                if ( array_key_exists( 'children', $child_item ) )
                                                {
                                                        // The item does have sub-items,
                                                        // Cycle through each, Interpreting them and adding the result to the output text
                                                        foreach( $child_item['children'] as $child )
                                                        {
                                                                list($tag_caption, $value_str) = Interpret_RDF_Item( $child );
                                                                $output_str .= "$tag_caption  =  $value_str\n";
                                                        }
                                                        // The output text will have an extra \n on it - remove it
                                                        $output_str = rtrim( $output_str );
                                                }
                                                break;

                                        // Other
                                        default:
                                                $output_str .= "Unknown Sub Item type:". $child_item[ 'tag' ]. "\n";
                                                break;
                                }
                        } // sub-item Has no tags, look for a value
                        else if ( array_key_exists( 'value', $child_item ) )
                        {
                                $output_str .= $rdf_item['value'] . "\n";
                        }
                        else
                        {
                                // no info - do nothing
                        }

                }
        }

        // return the resulting value string
        return $output_str;
}

/******************************************************************************
* End of Function:     get_RDF_field_html_value
******************************************************************************/








/******************************************************************************
*
* Internal Function:     interpret_RDF_collection
*
* Description:  Attempts to build a text representation of the value of an RDF
*               collection item. This includes handling any sub-collections or
*               sub-resources.
*
* Parameters:   rdf_item - The RDF collection item to evaluate
*
* Returns:      output_str - the text representation of the collection value
*
******************************************************************************/

function interpret_RDF_collection( $item )
{
        // Create a string to receive the output
        $output_str = "";

        // Check if the collection item has sub-items
        if ( array_key_exists( 'children', $item ) )
        {

                // Cycle through each of the sub-items
                foreach( $item['children'] as $list_item )
                {
                        // Check that the sub item has a tag, and don't process it if it doesn't
                        if ( ! array_key_exists( 'tag', $list_item ) )
                        {
                                continue 1;
                        }

                        // Check that the sub-item tag is either rdf:li or rdf:_1 ....
                        // This signifies it is a list item of the collection
                        if ( ( $list_item['tag'] == "rdf:li" ) ||
                             ( preg_match ( "rdf:_\d+", $list_item['tag'] ) == 1 ) )
                        {
                                // A List item has been found
                                // Check if there are sub-resources,
                                // starting by checking if there are attributes
                                if ( array_key_exists( 'attributes', $list_item ) )
                                {
                                        // Cycle through each of the attributes
                                        foreach( $list_item['attributes'] as $key => $val )
                                        {
                                                // Check if this attribute is rdf:parseType = 'Resource' i.e. a sub-resource indicator
                                                if ( ( $key == "rdf:parseType" ) && ( $val == "Resource" ) )
                                                {
                                                        // This item has a attribute indicating sub-resources
                                                        // Check that the item has sub items
                                                        if ( array_key_exists( 'children', $list_item ) )
                                                        {
                                                                // The item does have sub-items,
                                                                // Cycle through each, Interpreting them and adding the result to the output text
                                                                foreach( $list_item['children'] as $child )
                                                                {
                                                                        list($tag_caption, $value_str) = Interpret_RDF_Item( $child );
                                                                        $output_str .= "$tag_caption  =  $value_str\n";
                                                                }
                                                                // The output text will have an extra \n on it - remove it
                                                                $output_str = rtrim( $output_str );
                                                        }
                                                }
                                        }
                                }

                                // Check if the list item has a value
                                if ( array_key_exists( 'value', $list_item ) )
                                {
                                        // Value found, add it to the output
                                        $output_str .= get_RDF_field_html_value( $list_item ) . "\n";
                                }

                        }
                }
                // The list of sub-items formed will have a trailing \n, remove it.
                $output_str = rtrim( $output_str );

        }
        else
        {
                // No sub-items in collection - can't do anything
        }

        // Return the output value
        return $output_str;

}

/******************************************************************************
* End of Function:     interpret_RDF_collection
******************************************************************************/















/******************************************************************************
* Global Variable:      XMP_tag_captions
*
* Contents:     The Captions of the known XMP fields, indexed by their field name
*
******************************************************************************/

$GLOBALS[ 'XMP_tag_captions' ] = array (

"dc:contributor" => "Other Contributor(s)",
"dc:coverage" => "Coverage (scope)",
"dc:creator" => "Creator(s) (Authors)",
"dc:date" => "Date",
"dc:description" => "Description (Caption)",
"dc:format" => "MIME Data Format",
"dc:identifier" => "Unique Resource Identifer",
"dc:language" => "Language(s)",
"dc:publisher" => "Publisher(s)",
"dc:relation" => "Relations to other documents",
"dc:rights" => "Rights Statement",
"dc:source" => "Source (from which this Resource is derived)",
"dc:subject" => "Subject and Keywords",
"dc:title" => "Title",
"dc:type" => "Resource Type",

"xmp:Advisory" => "Externally Editied Properties",
"xmp:BaseURL" => "Base URL for relative URL's",
"xmp:CreateDate" => "Original Creation Date",
"xmp:CreatorTool" => "Creator Tool",
"xmp:Identifier" => "Identifier(s)",
"xmp:MetadataDate" => "Metadata Last Modify Date",
"xmp:ModifyDate" => "Resource Last Modify Date",
"xmp:Nickname" => "Nickname",
"xmp:Thumbnails" => "Thumbnails",

"xmpidq:Scheme" => "Identification Scheme",

// These are not in spec but Photoshop CS seems to use them
"xap:Advisory" => "Externally Editied Properties",
"xap:BaseURL" => "Base URL for relative URL's",
"xap:CreateDate" => "Original Creation Date",
"xap:CreatorTool" => "Creator Tool",
"xap:Identifier" => "Identifier(s)",
"xap:MetadataDate" => "Metadata Last Modify Date",
"xap:ModifyDate" => "Resource Last Modify Date",
"xap:Nickname" => "Nickname",
"xap:Thumbnails" => "Thumbnails",
"xapidq:Scheme" => "Identification Scheme",


"xapRights:Certificate" => "Certificate",
"xapRights:Copyright" => "Copyright",
"xapRights:Marked" => "Marked",
"xapRights:Owner" => "Owner",
"xapRights:UsageTerms" => "Legal Terms of Usage",
"xapRights:WebStatement" => "Web Page describing rights statement (Owner URL)",

"xapMM:ContainedResources" => "Contained Resources",
"xapMM:ContributorResources" => "Contributor Resources",
"xapMM:DerivedFrom" => "Derived From",
"xapMM:DocumentID" => "Document ID",
"xapMM:History" => "History",
"xapMM:LastURL" => "Last Written URL",
"xapMM:ManagedFrom" => "Managed From",
"xapMM:Manager" => "Asset Management System",
"xapMM:ManageTo" => "Manage To",
"xapMM:xmpMM:ManageUI" => "Managed Resource URI",
"xapMM:ManagerVariant" => "Particular Variant of Asset Management System",
"xapMM:RenditionClass" => "Rendition Class",
"xapMM:RenditionParams" => "Rendition Parameters",
"xapMM:RenditionOf" => "Rendition Of",
"xapMM:SaveID" => "Save ID",
"xapMM:VersionID" => "Version ID",
"xapMM:Versions" => "Versions",

"xapBJ:JobRef" => "Job Reference",

"xmpTPg:MaxPageSize" => "Largest Page Size",
"xmpTPg:NPages" => "Number of pages",

"pdf:Keywords" => "Keywords",
"pdf:PDFVersion" => "PDF file version",
"pdf:Producer" => "PDF Creation Tool",

"photoshop:AuthorsPosition" => "Authors Position",
"photoshop:CaptionWriter" => "Caption Writer",
"photoshop:Category" => "Category",
"photoshop:City" => "City",
"photoshop:Country" => "Country",
"photoshop:Credit" => "Credit",
"photoshop:DateCreated" => "Creation Date",
"photoshop:Headline" => "Headline",
"photoshop:History" => "History",                       // Not in XMP spec
"photoshop:Instructions" => "Instructions",
"photoshop:Source" => "Source",
"photoshop:State" => "State",
"photoshop:SupplementalCategories" => "Supplemental Categories",
"photoshop:TransmissionReference" => "Technical (Transmission) Reference",
"photoshop:Urgency" => "Urgency",


"tiff:ImageWidth" => "Image Width",
"tiff:ImageLength" => "Image Length",
"tiff:BitsPerSample" => "Bits Per Sample",
"tiff:Compression" => "Compression",
"tiff:PhotometricInterpretation" => "Photometric Interpretation",
"tiff:Orientation" => "Orientation",
"tiff:SamplesPerPixel" => "Samples Per Pixel",
"tiff:PlanarConfiguration" => "Planar Configuration",
"tiff:YCbCrSubSampling" => "YCbCr Sub-Sampling",
"tiff:YCbCrPositioning" => "YCbCr Positioning",
"tiff:XResolution" => "X Resolution",
"tiff:YResolution" => "Y Resolution",
"tiff:ResolutionUnit" => "Resolution Unit",
"tiff:TransferFunction" => "Transfer Function",
"tiff:WhitePoint" => "White Point",
"tiff:PrimaryChromaticities" => "Primary Chromaticities",
"tiff:YCbCrCoefficients" => "YCbCr Coefficients",
"tiff:ReferenceBlackWhite" => "Black & White Reference",
"tiff:DateTime" => "Date & Time",
"tiff:ImageDescription" => "Image Description",
"tiff:Make" => "Make",
"tiff:Model" => "Model",
"tiff:Software" => "Software",
"tiff:Artist" => "Artist",
"tiff:Copyright" => "Copyright",


"exif:ExifVersion" => "Exif Version",
"exif:FlashpixVersion" => "Flash pix Version",
"exif:ColorSpace" => "Color Space",
"exif:ComponentsConfiguration" => "Components Configuration",
"exif:CompressedBitsPerPixel" => "Compressed Bits Per Pixel",
"exif:PixelXDimension" => "Pixel X Dimension",
"exif:PixelYDimension" => "Pixel Y Dimension",
"exif:MakerNote" => "Maker Note",
"exif:UserComment" => "User Comment",
"exif:RelatedSoundFile" => "Related Sound File",
"exif:DateTimeOriginal" => "Date & Time of Original",
"exif:DateTimeDigitized" => "Date & Time Digitized",
"exif:ExposureTime" => "Exposure Time",
"exif:FNumber" => "F Number",
"exif:ExposureProgram" => "Exposure Program",
"exif:SpectralSensitivity" => "Spectral Sensitivity",
"exif:ISOSpeedRatings" => "ISO Speed Ratings",
"exif:OECF" => "Opto-Electronic Conversion Function",
"exif:ShutterSpeedValue" => "Shutter Speed Value",
"exif:ApertureValue" => "Aperture Value",
"exif:BrightnessValue" => "Brightness Value",
"exif:ExposureBiasValue" => "Exposure Bias Value",
"exif:MaxApertureValue" => "Max Aperture Value",
"exif:SubjectDistance" => "Subject Distance",
"exif:MeteringMode" => "Metering Mode",
"exif:LightSource" => "Light Source",
"exif:Flash" => "Flash",
"exif:FocalLength" => "Focal Length",
"exif:SubjectArea" => "Subject Area",
"exif:FlashEnergy" => "Flash Energy",
"exif:SpatialFrequencyResponse" => "Spatial Frequency Response",
"exif:FocalPlaneXResolution" => "Focal Plane X Resolution",
"exif:FocalPlaneYResolution" => "Focal Plane Y Resolution",
"exif:FocalPlaneResolutionUnit" => "Focal Plane Resolution Unit",
"exif:SubjectLocation" => "Subject Location",
"exif:SensingMethod" => "Sensing Method",
"exif:FileSource" => "File Source",
"exif:SceneType" => "Scene Type",
"exif:CFAPattern" => "Colour Filter Array Pattern",
"exif:CustomRendered" => "Custom Rendered",
"exif:ExposureMode" => "Exposure Mode",
"exif:WhiteBalance" => "White Balance",
"exif:DigitalZoomRatio" => "Digital Zoom Ratio",
"exif:FocalLengthIn35mmFilm" => "Focal Length In 35mm Film",
"exif:SceneCaptureType" => "Scene Capture Type",
"exif:GainControl" => "Gain Control",
"exif:Contrast" => "Contrast",
"exif:Saturation" => "Saturation",
"exif:Sharpness" => "Sharpness",
"exif:DeviceSettingDescription" => "Device Setting Description",
"exif:SubjectDistanceRange" => "Subject Distance Range",
"exif:ImageUniqueID" => "Image Unique ID",
"exif:GPSVersionID" => "GPS Version ID",
"exif:GPSLatitude" => "GPS Latitude",
"exif:GPSLongitude" => "GPS Longitude",
"exif:GPSAltitudeRef" => "GPS Altitude Reference",
"exif:GPSAltitude" => "GPS Altitude",
"exif:GPSTimeStamp" => "GPS Time Stamp",
"exif:GPSSatellites" => "GPS Satellites",
"exif:GPSStatus" => "GPS Status",
"exif:GPSMeasureMode" => "GPS Measure Mode",
"exif:GPSDOP" => "GPS Degree Of Precision",
"exif:GPSSpeedRef" => "GPS Speed Reference",
"exif:GPSSpeed" => "GPS Speed",
"exif:GPSTrackRef" => "GPS Track Reference",
"exif:GPSTrack" => "GPS Track",
"exif:GPSImgDirectionRef" => "GPS Image Direction Reference",
"exif:GPSImgDirection" => "GPS Image Direction",
"exif:GPSMapDatum" => "GPS Map Datum",
"exif:GPSDestLatitude" => "GPS Destination Latitude",
"exif:GPSDestLongitude" => "GPS Destnation Longitude",
"exif:GPSDestBearingRef" => "GPS Destination Bearing Reference",
"exif:GPSDestBearing" => "GPS Destination Bearing",
"exif:GPSDestDistanceRef" => "GPS Destination Distance Reference",
"exif:GPSDestDistance" => "GPS Destination Distance",
"exif:GPSProcessingMethod" => "GPS Processing Method",
"exif:GPSAreaInformation" => "GPS Area Information",
"exif:GPSDifferential" => "GPS Differential",

"stDim:w" => "Width",
"stDim:h" => "Height",
"stDim:unit" => "Units",

"xapGImg:height" => "Height",
"xapGImg:width" => "Width",
"xapGImg:format" => "Format",
"xapGImg:image" => "Image",

"stEvt:action" => "Action",
"stEvt:instanceID" => "Instance ID",
"stEvt:parameters" => "Parameters",
"stEvt:softwareAgent" => "Software Agent",
"stEvt:when" => "When",

"stRef:instanceID" => "Instance ID",
"stRef:documentID" => "Document ID",
"stRef:versionID" => "Version ID",
"stRef:renditionClass" => "Rendition Class",
"stRef:renditionParams" => "Rendition Parameters",
"stRef:manager" => "Asset Management System",
"stRef:managerVariant" => "Particular Variant of Asset Management System",
"stRef:manageTo" => "Manage To",
"stRef:manageUI" => "Managed Resource URI",

"stVer:comments" => "",
"stVer:event" => "",
"stVer:modifyDate" => "",
"stVer:modifier" => "",
"stVer:version" => "",



"stJob:name" => "Job Name",
"stJob:id" => "Unique Job ID",
"stJob:url" => "URL for External Job Management File",

// Exif Flash
"exif:Fired" => "Fired",
"exif:Return" => "Return",
"exif:Mode" => "Mode",
"exif:Function" => "Function",
"exif:RedEyeMode" => "Red Eye Mode",

// Exif OECF/SFR
"exif:Columns" => "Columns",
"exif:Rows" => "Rows",
"exif:Names" => "Names",
"exif:Values" => "Values",

// Exif CFAPattern
"exif:Columns" => "Columns",
"exif:Rows" => "Rows",
"exif:Values" => "Values",


// Exif DeviceSettings
"exif:Columns" => "Columns",
"exif:Rows" => "Rows",
"exif:Settings" => "Settings",



);

function read_xml_array_from_text( $xmltext )
{
        // Check if there actually is any text to parse
        if ( trim( $xmltext ) == "" )
        {
                return FALSE;
        }

        // Create an instance of a xml parser to parse the XML text
        $xml_parser = xml_parser_create( "UTF-8" );


        // Change: Fixed problem that caused the whitespace (especially newlines) to be destroyed when converting xml text to an xml array, as of revision 1.10

        // We would like to remove unneccessary white space, but this will also
        // remove things like newlines (&#xA;) in the XML values, so white space
        // will have to be removed later
        if ( xml_parser_set_option($xml_parser,XML_OPTION_SKIP_WHITE,0) == FALSE )
        {
                // Error setting case folding - destroy the parser and return
                xml_parser_free($xml_parser);
                return FALSE;
        }

        // to use XML code correctly we have to turn case folding
        // (uppercasing) off. XML is case sensitive and upper
        // casing is in reality XML standards violation
        if ( xml_parser_set_option($xml_parser,XML_OPTION_CASE_FOLDING,0) == FALSE )
        {
                // Error setting case folding - destroy the parser and return
                xml_parser_free($xml_parser);
                return FALSE;
        }

        // Parse the XML text into a array structure
        if ( xml_parse_into_struct($xml_parser, $xmltext, $vals, $index) == 0 )
        {
                // Error Parsing XML - destroy the parser and return
                xml_parser_free($xml_parser);
                return FALSE;
        }

        // Destroy the xml parser
        xml_parser_free($xml_parser);


        // Change: Fixed problem that caused the whitespace (especially newlines) to be destroyed when converting xml text to an xml array, as of revision 1.10

        // Since the xml was processed with whitespace enabled, it will have many values which are
        // only whitespace. These need to be removed to make a sensible array.

        $newvals = array( );

        // Cycle through each of the items
        foreach( $vals as $valno => $val )
        {
                // If the item has a whitespace only value, remove it
                if ( ( array_key_exists( 'value', $val ) ) && (trim( $val[ 'value' ] ) == "" ) )
                {
                        unset( $val[ 'value' ] );
                }
                // If the item has a value (which will be non blank now) or is of type other than cdata, add it to the new array
                if ( ( $val[ 'type' ] != 'cdata' ) || ( array_key_exists( 'value', $val ) ) )
                {
                        $newvals[] = $val;
                }

        }

        // The xml_parse_into_struct function returns a flat version
        // of the XML data, where each tag has a level number attached.
        // This is very difficult to work with, so it needs to be
        // converted to a tree structure before being returned
        return xml_get_children($newvals, $i);

}

/******************************************************************************
* End of Function:     read_xml_array_from_text
******************************************************************************/





/******************************************************************************
*
* Function:     write_xml_array_to_text
*
* Description:  Takes a tree structure array (in the same format as returned
*               by read_xml_array_from_text, and constructs a string containing
*               the equivalent XML. This function is recursive, and produces
*               XML which has correct indents.
*               Note: All text information contained in the tree structure
*                     can be either 7-bit ASCII or encoded as Unicode UTF-8,
*                     since UTF-8 passes 7-bit ASCII text unchanged.
*
* Parameters:   xmlarray - the tree structure array containing the information to
*                          be converted to XML
*               indentlevel - the indent level of the top level tags (usually zero)
*
* Returns:      output - the string containing the equivalent XML
*               FALSE - if an error occured
*
******************************************************************************/

function write_xml_array_to_text( $xmlarray, $indentlevel )
{
        // Create a string to receive the XML
        $output_xml_text = "";


        // Cycle through each xml element at this level
        foreach ($xmlarray as $xml_elem)
        {

                // Add the indent, then the cleaned tag name to the output
                $output_xml_text .= str_repeat ( " ", $indentlevel ) . "<" . xml_UTF8_clean( $xml_elem['tag'] );

                // Check if there are any attributes for this tag
                if (array_key_exists('attributes',$xml_elem))
                {
                        // There are attributes
                        // Cycle through each attribute for this tag
                        foreach ($xml_elem['attributes'] as  $xml_attr_name => $xml_attr_val)
                        {
                                // Add the cleaned attribute name, and cleaned attribute value to the output
                                $output_xml_text .= " ". xml_UTF8_clean( $xml_attr_name ) ." ='" .  xml_UTF8_clean( $xml_attr_val ) ."'";
                        }
                }

                // Add the 'greater-than' to close this tag to the output
                $output_xml_text .= ">";

                // Check if this element has any text inside it.
                if (array_key_exists('value',$xml_elem) )
                {
                        // There is text for this element - clean it and add it to the output
                        $output_xml_text .=  xml_UTF8_clean( $xml_elem['value'] );
                }

                // Check if there are any lower levels contained by this element
                if (array_key_exists('children',$xml_elem) )
                {
                        // There are sub-elements for this element

                        // Add a newline to the output, so the sub-elements start on a fresh line
                        $output_xml_text .= "\n";

                        // Recursively call this function to output the sub-elements, and add the result to the output
                        $output_xml_text .= write_xml_array_to_text( $xml_elem['children'], $indentlevel + 1 );

                        // Add an indent to the output for the closing tag, since we are on a new line due to the sub-elements
                        $output_xml_text .= str_repeat ( " ", $indentlevel );
                }

                // Add the cleaned closing tag to the output
                $output_xml_text .= "</" .xml_UTF8_clean($xml_elem['tag']) . ">\n";
        }

        // Return the XML text
        return $output_xml_text;
}

/******************************************************************************
* End of Function:     write_xml_array_to_text
******************************************************************************/























/******************************************************************************
*
*         INTERNAL FUNCTIONS
*
******************************************************************************/






/******************************************************************************
*
* Internal Function:     xml_get_children
*
* Description:  Used by the read_xml_array_from_text function.
*               This function recursively converts the values retrieved from
*               the xml_parse_into_struct function into a tree structure array,
*               which is much more useful and easier to use.
*
* Parameters:   input_xml_array - the flat array of XML elements retrieved
*                                 from xml_parse_into_struct
*               $item_num - the number of the element at which the conversion
*                           should start (usually zero when called from another
*                           function, this is used for recursion)
*
* Returns:      children - the tree structure array containing XML elements
*               FALSE - if an error occured
*
******************************************************************************/

function xml_get_children( &$input_xml_array, &$item_num )
{

        // Make an array to receive the output XML tree structure
        $children = array();


        // Cycle through all the elements of the input XML array
        while ( $item_num < count( $input_xml_array ) )
        {
                // Retrieve the current array element
                $v = &$input_xml_array[ $item_num++ ];

                // Check what type of XML array element this is, and process accordingly

                switch ( $v['type'] )
                {
                        case 'cdata':     // This is a non parsed Character Data tag
                        case 'complete':  // This is a pair of XML matching tags possibly with text (but no tags) inside
                                $children[] = xml_get_child( $v );
                                break;

                        case 'open':      // This is a single opening tag
                                // Recursively get the children for this opening tag
                                $children[] = xml_get_child( $v, xml_get_children( $input_xml_array, $item_num ) );
                                break;    // This is a single opening tag

                        case 'close':     // This is a single closing tag
                                break 2;  // leave "while" loop (and the function)
                }
        }

        // Return the results
        return $children;
}

/******************************************************************************
* End of Function:     xml_get_children
******************************************************************************/


/******************************************************************************
*
* Internal Function:     xml_get_child
*
* Description:  Used by the xml_get_children function.
*               Takes an element from an array provided by xml_parse_into_struct
*               and returns an element for a tree structure array
*
* Parameters:   input_xml_item - the item from the array provided by xml_parse_into_struct
*               children - an array of sub-elements to be added to the tree
*                          structure array. Null or missing value indicate no
*                          sub-elements are to be added.
*
* Returns:      child - the element for a tree structure array
*               FALSE - if an error occured
*
******************************************************************************/

function xml_get_child( &$input_xml_item, $children = NULL )
{
        // Create an array to receive the child structure
        $child = array();

        // If the input item has the 'tag' element set, copy it to the child
        if ( isset( $input_xml_item['tag'] ) )
        {
                $child['tag'] = $input_xml_item['tag'] ;
        }

        // If the input item has the 'value' element set, copy it to the child
        if ( isset( $input_xml_item['value'] ) )
        {
                $child['value'] = $input_xml_item['value'] ;
        }

        // If the input item has the 'attributes' element set, copy it to the child
        if ( isset( $input_xml_item['attributes'] ) )
        {
                $child['attributes'] = $input_xml_item['attributes'];
        }

        // If children have been specified, add them to the child
        if ( is_array( $children ) )
        {
                $child['children'] = $children;
        }

        // Return the child structure
        return $child;
}

function UTF8_fix( $utf8_text )
{
        // Initialise the current position in the string
        $pos = 0;

        // Create a string to accept the well formed output
        $output = "" ;

        // Cycle through each group of bytes, ensuring the coding is correct
        while ( $pos < strlen( $utf8_text ) )
        {
                // Retreive the current numerical character value
                $chval = ord($utf8_text{$pos});

                // Check what the first character is - it will tell us how many bytes the
                // Unicode value covers

                if ( ( $chval >= 0x00 ) && ( $chval <= 0x7F ) )
                {
                        // 1 Byte UTF-8 Unicode (7-Bit ASCII) Character
                        $bytes = 1;
                }
                else if ( ( $chval >= 0xC0 ) && ( $chval <= 0xDF ) )
                {
                        // 2 Byte UTF-8 Unicode Character
                        $bytes = 2;
                }
                else if ( ( $chval >= 0xE0 ) && ( $chval <= 0xEF ) )
                {
                        // 3 Byte UTF-8 Unicode Character
                        $bytes = 3;
                }
                else if ( ( $chval >= 0xF0 ) && ( $chval <= 0xF7 ) )
                {
                        // 4 Byte UTF-8 Unicode Character
                        $bytes = 4;
                }
                else if ( ( $chval >= 0xF8 ) && ( $chval <= 0xFB ) )
                {
                        // 5 Byte UTF-8 Unicode Character
                        $bytes = 5;
                }
                else if ( ( $chval >= 0xFC ) && ( $chval <= 0xFD ) )
                {
                        // 6 Byte UTF-8 Unicode Character
                        $bytes = 6;
                }
                else
                {
                        // Invalid Code - skip character and do nothing
                        $bytes = 0;
                        $pos++;
                }


                // check that there is enough data remaining to read
                if (($pos + $bytes - 1) < strlen( $utf8_text ) )
                {
                        // Cycle through the number of bytes specified,
                        // copying them to the output string
                        while ( $bytes > 0 )
                        {
                                $output .= $utf8_text{$pos};
                                $pos++;
                                $bytes--;
                        }
                }
                else
                {
                        break;
                }
        }

        // Return the result
        return $output;
}

/******************************************************************************
* End of Function:     UTF8_fix
******************************************************************************/









/******************************************************************************
*
* Function:     UTF16_fix
*
* Description:  Checks a string for badly formed Unicode UTF-16 coding and
*               returns the same string containing only the parts which
*               were properly formed UTF-16 data.
*
* Parameters:   utf16_text - a string with possibly badly formed UTF-16 data
*               MSB_first - True will cause processing as Big Endian UTF-16 (Motorola, MSB first)
*                           False will cause processing as Little Endian UTF-16 (Intel, LSB first)
*
* Returns:      output - the well formed UTF-16 version of the string
*
******************************************************************************/

function UTF16_fix( $utf16_text, $MSB_first )
{
        // Initialise the current position in the string
        $pos = 0;

        // Create a string to accept the well formed output
        $output = "" ;

        // Cycle through each group of bytes, ensuring the coding is correct
        while ( $pos < strlen( $utf16_text ) )
        {
                // Retreive the current numerical character value
                $chval1 = ord($utf16_text{$pos});

                // Skip over character just read
                $pos++;

                // Check if there is another character available
                if ( $pos  < strlen( $utf16_text ) )
                {
                        // Another character is available - get it for the second half of the UTF-16 value
                        $chval2 = ord( $utf16_text{$pos} );
                }
                else
                {
                        // Error - no second byte to this UTF-16 value - end processing
                        continue 1;
                }

                // Skip over character just read
                $pos++;

                // Calculate the 16 bit unicode value
                if ( $MSB_first )
                {
                        // Big Endian
                        $UTF16_val = $chval1 * 0x100 + $chval2;
                }
                else
                {
                        // Little Endian
                        $UTF16_val = $chval2 * 0x100 + $chval1;
                }



                if ( ( ( $UTF16_val >= 0x0000 ) && ( $UTF16_val <= 0xD7FF ) ) ||
                     ( ( $UTF16_val >= 0xE000 ) && ( $UTF16_val <= 0xFFFF ) ) )
                {
                        // Normal Character (Non Surrogate pair)
                        // Add it to the output
                        $output .= chr( $chval1 ) . chr ( $chval2 );
                }
                else if ( ( $UTF16_val >= 0xD800 ) && ( $UTF16_val <= 0xDBFF ) )
                {
                        // High surrogate of a surrogate pair
                        // Now we need to read the low surrogate
                        // Check if there is another 2 characters available
                        if ( ( $pos + 3 ) < strlen( $utf16_text ) )
                        {
                                // Another 2 characters are available - get them
                                $chval3 = ord( $utf16_text{$pos} );
                                $chval4 = ord( $utf16_text{$pos+1} );

                                // Calculate the second 16 bit unicode value
                                if ( $MSB_first )
                                {
                                        // Big Endian
                                        $UTF16_val2 = $chval3 * 0x100 + $chval4;
                                }
                                else
                                {
                                        // Little Endian
                                        $UTF16_val2 = $chval4 * 0x100 + $chval3;
                                }

                                // Check that this is a low surrogate
                                if ( ( $UTF16_val2 >= 0xDC00 ) && ( $UTF16_val2 <= 0xDFFF ) )
                                {
                                        // Low surrogate found following high surrogate
                                        // Add both to the output
                                        $output .= chr( $chval1 ) . chr ( $chval2 ) . chr( $chval3 ) . chr ( $chval4 );

                                        // Skip over the low surrogate
                                        $pos += 2;
                                }
                                else
                                {
                                        // Low surrogate not found after high surrogate
                                        // Don't add either to the output
                                        // Only the High surrogate is skipped and processing continues after it
                                }

                        }
                        else
                        {
                                // Error - not enough data for low surrogate - end processing
                                continue 1;
                        }

                }
                else
                {
                        // Low surrogate of a surrogate pair
                        // This should not happen - it means this is a lone low surrogate
                        // Dont add it to the output
                }

        }

        // Return the result
        return $output;
}

/******************************************************************************
* End of Function:     UTF16_fix
******************************************************************************/





/******************************************************************************
*
* Function:     UTF8_to_unicode_array
*
* Description:  Converts a string encoded with Unicode UTF-8, to an array of
*               numbers which represent unicode character numbers
*
* Parameters:   utf8_text - a string containing the UTF-8 data
*
* Returns:      output - the array containing the unicode character numbers
*
******************************************************************************/

function UTF8_to_unicode_array( $utf8_text )
{
        // Create an array to receive the unicode character numbers output
        $output = array( );

        // Cycle through the characters in the UTF-8 string
        for ( $pos = 0; $pos < strlen( $utf8_text ); $pos++ )
        {
                // Retreive the current numerical character value
                $chval = ord($utf8_text{$pos});

                // Check what the first character is - it will tell us how many bytes the
                // Unicode value covers

                if ( ( $chval >= 0x00 ) && ( $chval <= 0x7F ) )
                {
                        // 1 Byte UTF-8 Unicode (7-Bit ASCII) Character
                        $bytes = 1;
                        $outputval = $chval;    // Since 7-bit ASCII is unaffected, the output equals the input
                }
                else if ( ( $chval >= 0xC0 ) && ( $chval <= 0xDF ) )
                {
                        // 2 Byte UTF-8 Unicode
                        $bytes = 2;
                        $outputval = $chval & 0x1F;     // The first byte is bitwise ANDed with 0x1F to remove the leading 110b
                }
                else if ( ( $chval >= 0xE0 ) && ( $chval <= 0xEF ) )
                {
                        // 3 Byte UTF-8 Unicode
                        $bytes = 3;
                        $outputval = $chval & 0x0F;     // The first byte is bitwise ANDed with 0x0F to remove the leading 1110b
                }
                else if ( ( $chval >= 0xF0 ) && ( $chval <= 0xF7 ) )
                {
                        // 4 Byte UTF-8 Unicode
                        $bytes = 4;
                        $outputval = $chval & 0x07;     // The first byte is bitwise ANDed with 0x07 to remove the leading 11110b
                }
                else if ( ( $chval >= 0xF8 ) && ( $chval <= 0xFB ) )
                {
                        // 5 Byte UTF-8 Unicode
                        $bytes = 5;
                        $outputval = $chval & 0x03;     // The first byte is bitwise ANDed with 0x03 to remove the leading 111110b
                }
                else if ( ( $chval >= 0xFC ) && ( $chval <= 0xFD ) )
                {
                        // 6 Byte UTF-8 Unicode
                        $bytes = 6;
                        $outputval = $chval & 0x01;     // The first byte is bitwise ANDed with 0x01 to remove the leading 1111110b
                }
                else
                {
                        // Invalid Code - do nothing
                        $bytes = 0;
                }

                // Check if the byte was valid
                if ( $bytes !== 0 )
                {
                        // The byte was valid

                        // Check if there is enough data left in the UTF-8 string to allow the
                        // retrieval of the remainder of this unicode character
                        if ( $pos + $bytes - 1 < strlen( $utf8_text ) )
                        {
                                // The UTF-8 string is long enough

                                // Cycle through the number of bytes required,
                                // minus the first one which has already been done
                                while ( $bytes > 1 )
                                {
                                        $pos++;
                                        $bytes--;

                                        // Each remaining byte is coded with 6 bits of data and 10b on the high
                                        // order bits. Hence we need to shift left by 6 bits (0x40) then add the
                                        // current characer after it has been bitwise ANDed with 0x3F to remove the
                                        // highest two bits.
                                        $outputval = $outputval*0x40 + ( (ord($utf8_text{$pos})) & 0x3F );
                                }

                                // Add the calculated Unicode number to the output array
                                $output[] = $outputval;
                        }
                }

        }

        // Return the resulting array
        return $output;
}

/******************************************************************************
* End of Function:     UTF8_to_unicode_array
******************************************************************************/





/******************************************************************************
*
* Function:     UTF16_to_unicode_array
*
* Description:  Converts a string encoded with Unicode UTF-16, to an array of
*               numbers which represent unicode character numbers
*
* Parameters:   utf16_text - a string containing the UTF-16 data
*               MSB_first - True will cause processing as Big Endian UTF-16 (Motorola, MSB first)
*                           False will cause processing as Little Endian UTF-16 (Intel, LSB first)
*
* Returns:      output - the array containing the unicode character numbers
*
******************************************************************************/

function UTF16_to_unicode_array( $utf16_text, $MSB_first )
{
        // Create an array to receive the unicode character numbers output
        $output = array( );


        // Initialise the current position in the string
        $pos = 0;

        // Cycle through each group of bytes, ensuring the coding is correct
        while ( $pos < strlen( $utf16_text ) )
        {
                // Retreive the current numerical character value
                $chval1 = ord($utf16_text{$pos});

                // Skip over character just read
                $pos++;

                // Check if there is another character available
                if ( $pos  < strlen( $utf16_text ) )
                {
                        // Another character is available - get it for the second half of the UTF-16 value
                        $chval2 = ord( $utf16_text{$pos} );
                }
                else
                {
                        // Error - no second byte to this UTF-16 value - end processing
                        continue 1;
                }

                // Skip over character just read
                $pos++;

                // Calculate the 16 bit unicode value
                if ( $MSB_first )
                {
                        // Big Endian
                        $UTF16_val = $chval1 * 0x100 + $chval2;
                }
                else
                {
                        // Little Endian
                        $UTF16_val = $chval2 * 0x100 + $chval1;
                }


                if ( ( ( $UTF16_val >= 0x0000 ) && ( $UTF16_val <= 0xD7FF ) ) ||
                     ( ( $UTF16_val >= 0xE000 ) && ( $UTF16_val <= 0xFFFF ) ) )
                {
                        // Normal Character (Non Surrogate pair)
                        // Add it to the output
                        $output[] = $UTF16_val;
                }
                else if ( ( $UTF16_val >= 0xD800 ) && ( $UTF16_val <= 0xDBFF ) )
                {
                        // High surrogate of a surrogate pair
                        // Now we need to read the low surrogate
                        // Check if there is another 2 characters available
                        if ( ( $pos + 3 ) < strlen( $utf16_text ) )
                        {
                                // Another 2 characters are available - get them
                                $chval3 = ord( $utf16_text{$pos} );
                                $chval4 = ord( $utf16_text{$pos+1} );

                                // Calculate the second 16 bit unicode value
                                if ( $MSB_first )
                                {
                                        // Big Endian
                                        $UTF16_val2 = $chval3 * 0x100 + $chval4;
                                }
                                else
                                {
                                        // Little Endian
                                        $UTF16_val2 = $chval4 * 0x100 + $chval3;
                                }

                                // Check that this is a low surrogate
                                if ( ( $UTF16_val2 >= 0xDC00 ) && ( $UTF16_val2 <= 0xDFFF ) )
                                {
                                        // Low surrogate found following high surrogate
                                        // Add both to the output
                                        $output[] = 0x10000 + ( ( $UTF16_val - 0xD800 ) * 0x400 ) + ( $UTF16_val2 - 0xDC00 );

                                        // Skip over the low surrogate
                                        $pos += 2;
                                }
                                else
                                {
                                        // Low surrogate not found after high surrogate
                                        // Don't add either to the output
                                        // The high surrogate is skipped and processing continued
                                }

                        }
                        else
                        {
                                // Error - not enough data for low surrogate - end processing
                                continue 1;
                        }

                }
                else
                {
                        // Low surrogate of a surrogate pair
                        // This should not happen - it means this is a lone low surrogate
                        // Don't add it to the output
                }

        }

        // Return the result
        return $output;


}

/******************************************************************************
* End of Function:     UTF16_to_unicode_array
******************************************************************************/







/******************************************************************************
*
* Function:     unicode_array_to_UTF8
*
* Description:  Converts an array of unicode character numbers to a string
*               encoded by UTF-8
*
* Parameters:   unicode_array - the array containing unicode character numbers
*
* Returns:      output - the UTF-8 encoded string representing the data
*
******************************************************************************/

function unicode_array_to_UTF8( $unicode_array )
{

        // Create a string to receive the UTF-8 output
        $output = "";

        // Cycle through each Unicode character number
        foreach( $unicode_array as $unicode_char )
        {
                // Check which range the current unicode character lies in
                if ( ( $unicode_char >= 0x00 ) && ( $unicode_char <= 0x7F ) )
                {
                        // 1 Byte UTF-8 Unicode (7-Bit ASCII) Character

                        $output .= chr($unicode_char);          // Output is equal to input for 7-bit ASCII
                }
                else if ( ( $unicode_char >= 0x80 ) && ( $unicode_char <= 0x7FF ) )
                {
                        // 2 Byte UTF-8 Unicode - binary encode data as : 110xxxxx 10xxxxxx

                        $output .= chr(0xC0 + ($unicode_char/0x40));
                        $output .= chr(0x80 + ($unicode_char & 0x3F));
                }
                else if ( ( $unicode_char >= 0x800 ) && ( $unicode_char <= 0xFFFF ) )
                {
                        // 3 Byte UTF-8 Unicode - binary encode data as : 1110xxxx 10xxxxxx 10xxxxxx

                        $output .= chr(0xE0 + ($unicode_char/0x1000));
                        $output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
                        $output .= chr(0x80 + ($unicode_char & 0x3F));
                }
                else if ( ( $unicode_char >= 0x10000 ) && ( $unicode_char <= 0x1FFFFF ) )
                {
                        // 4 Byte UTF-8 Unicode - binary encode data as : 11110xxx 10xxxxxx 10xxxxxx 10xxxxxx

                        $output .= chr(0xF0 + ($unicode_char/0x40000));
                        $output .= chr(0x80 + (($unicode_char/0x1000) & 0x3F));
                        $output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
                        $output .= chr(0x80 + ($unicode_char & 0x3F));
                }
                else if ( ( $unicode_char >= 0x200000 ) && ( $unicode_char <= 0x3FFFFFF ) )
                {
                        // 5 Byte UTF-8 Unicode - binary encode data as : 111110xx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx

                        $output .= chr(0xF8 + ($unicode_char/0x1000000));
                        $output .= chr(0x80 + (($unicode_char/0x40000) & 0x3F));
                        $output .= chr(0x80 + (($unicode_char/0x1000) & 0x3F));
                        $output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
                        $output .= chr(0x80 + ($unicode_char & 0x3F));
                }
                else if ( ( $unicode_char >= 0x4000000 ) && ( $unicode_char <= 0x7FFFFFFF ) )
                {
                        // 6 Byte UTF-8 Unicode - binary encode data as : 1111110x 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx 10xxxxxx

                        $output .= chr(0xFC + ($unicode_char/0x40000000));
                        $output .= chr(0x80 + (($unicode_char/0x1000000) & 0x3F));
                        $output .= chr(0x80 + (($unicode_char/0x40000) & 0x3F));
                        $output .= chr(0x80 + (($unicode_char/0x1000) & 0x3F));
                        $output .= chr(0x80 + (($unicode_char/0x40) & 0x3F));
                        $output .= chr(0x80 + ($unicode_char & 0x3F));
                }
                else
                {
                        // Invalid Code - do nothing
                }

        }

        // Return resulting UTF-8 String
        return $output;
}

/******************************************************************************
* End of Function:     unicode_array_to_UTF8
******************************************************************************/









/******************************************************************************
*
* Function:     unicode_array_to_UTF16
*
* Description:  Converts an array of unicode character numbers to a string
*               encoded by UTF-16
*
* Parameters:   unicode_array - the array containing unicode character numbers
*               MSB_first - True will cause processing as Big Endian UTF-16 (Motorola, MSB first)
*                           False will cause processing as Little Endian UTF-16 (Intel, LSB first)
*
* Returns:      output - the UTF-16 encoded string representing the data
*
******************************************************************************/

function unicode_array_to_UTF16( $unicode_array, $MSB_first )
{

        // Create a string to receive the UTF-16 output
        $output = "";

        // Cycle through each Unicode character number
        foreach( $unicode_array as $unicode_char )
        {
                // Check which range the current unicode character lies in
                if ( ( ( $unicode_char >= 0x0000 ) && ( $unicode_char <= 0xD7FF ) ) ||
                     ( ( $unicode_char >= 0xE000 ) && ( $unicode_char <= 0xFFFF ) ) )
                {
                        // Normal 16 Bit Character  (Not a Surrogate Pair)

                        // Check what byte order should be used
                        if ( $MSB_first )
                        {
                                // Big Endian
                                $output .= chr( $unicode_char / 0x100 ) . chr( $unicode_char % 0x100 ) ;
                        }
                        else
                        {
                                // Little Endian
                                $output .= chr( $unicode_char % 0x100 ) . chr( $unicode_char / 0x100 ) ;
                        }

                }
                else if ( ( $unicode_char >= 0x10000 ) && ( $unicode_char <= 0x10FFFF ) )
                {
                        // Surrogate Pair required

                        // Calculate Surrogates
                        $High_Surrogate = ( ( $unicode_char - 0x10000 ) / 0x400 ) + 0xD800;
                        $Low_Surrogate = ( ( $unicode_char - 0x10000 ) % 0x400 ) + 0xDC00;

                        // Check what byte order should be used
                        if ( $MSB_first )
                        {
                                // Big Endian
                                $output .= chr( $High_Surrogate / 0x100 ) . chr( $High_Surrogate % 0x100 );
                                $output .= chr( $Low_Surrogate / 0x100 ) . chr( $Low_Surrogate % 0x100 );
                        }
                        else
                        {
                                // Little Endian
                                $output .= chr( $High_Surrogate % 0x100 ) . chr( $High_Surrogate / 0x100 );
                                $output .= chr( $Low_Surrogate % 0x100 ) . chr( $Low_Surrogate / 0x100 );
                        }
                }
                else
                {
                        // Invalid UTF-16 codepoint
                        // Unicode value should never be between 0xD800 and 0xDFFF
                        // Do not output this point - there is no way to encode it in UTF-16
                }

        }

        // Return resulting UTF-16 String
        return $output;
}

/******************************************************************************
* End of Function:     unicode_array_to_UTF16
******************************************************************************/





/******************************************************************************
*
* Function:     xml_UTF8_clean
*
* Description:  XML has specific requirements about the characters that are
*               allowed, and characters that must be escaped.
*               This function ensures that all characters in the given string
*               are valid, and that characters such as Quotes, Greater than,
*               Less than and Ampersand are properly escaped. Newlines and Tabs
*               are also escaped.
*               Note - Do not use this on constructed XML which includes tags,
*                      as it will escape the tags. It is designed to be used
*                      on the tag and attribute names, attribute values, and text.
*
* Parameters:   utf8_text - a string containing the UTF-8 data
*
* Returns:      output - the array containing the unicode character numbers
*
******************************************************************************/

function xml_UTF8_clean( $UTF8_text )
{
        // Ensure that the Unicode UTF8 encoding is valid.

        $UTF8_text = UTF8_fix( $UTF8_text );


        // XML only allows characters in the following unicode ranges
        // #x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF]
        // Hence we need to delete any characters that dont fit this

        // Convert the UTF-8 string to an array of unicode character numbers
        $unicode_array = UTF8_to_unicode_array( $UTF8_text );

        // Create a new array to receive the valid unicode character numbers
        $new_unicode_array = array( );

        // Cycle through the unicode character numbers
        foreach( $unicode_array as  $unichar )
        {
                // Check if the unicode character number is valid for XML
                if ( ( $unichar == 0x09 ) ||
                     ( $unichar == 0x0A ) ||
                     ( $unichar == 0x0D ) ||
                     ( ( $unichar >= 0x20 ) && ( $unichar <= 0xD7FF ) ) ||
                     ( ( $unichar >= 0xE000 ) && ( $unichar <= 0xFFFD ) ) ||
                     ( ( $unichar >= 0x10000 ) && ( $unichar <= 0x10FFFF ) ) )
                {
                       // Unicode character is valid for XML - add it to the valid characters array
                       $new_unicode_array[] = $unichar;
                }

        }

        // Convert the array of valid unicode character numbers back to UTF-8 encoded text
        $UTF8_text = unicode_array_to_UTF8( $new_unicode_array );

        // Escape any special HTML characters present
        $UTF8_text =  htmlspecialchars ( $UTF8_text, ENT_QUOTES );

        // Escape CR, LF and TAB characters, so that they are kept and not treated as expendable white space
        $trans = array( "\x09" => "&#x09;", "\x0A" => "&#x0A;", "\x0D" => "&#x0D;" );
        $UTF8_text = strtr( $UTF8_text, $trans );

        // Return the resulting XML valid string
        return $UTF8_text;
}

/******************************************************************************
* End of Function:     xml_UTF8_clean
******************************************************************************/









/******************************************************************************
*
* Function:     xml_UTF16_clean
*
* Description:  XML has specific requirements about the characters that are
*               allowed, and characters that must be escaped.
*               This function ensures that all characters in the given string
*               are valid, and that characters such as Quotes, Greater than,
*               Less than and Ampersand are properly escaped. Newlines and Tabs
*               are also escaped.
*               Note - Do not use this on constructed XML which includes tags,
*                      as it will escape the tags. It is designed to be used
*                      on the tag and attribute names, attribute values, and text.
*
* Parameters:   utf16_text - a string containing the UTF-16 data
*               MSB_first - True will cause processing as Big Endian UTF-16 (Motorola, MSB first)
*                           False will cause processing as Little Endian UTF-16 (Intel, LSB first)
*
* Returns:      output - the array containing the unicode character numbers
*
******************************************************************************/

function xml_UTF16_clean( $UTF16_text, $MSB_first )
{
        // Ensure that the Unicode UTF16 encoding is valid.

        $UTF16_text = UTF16_fix( $UTF16_text, $MSB_first );


        // XML only allows characters in the following unicode ranges
        // #x9 | #xA | #xD | [#x20-#xD7FF] | [#xE000-#xFFFD] | [#x10000-#x10FFFF]
        // Hence we need to delete any characters that dont fit this

        // Convert the UTF-16 string to an array of unicode character numbers
        $unicode_array = UTF16_to_unicode_array( $UTF16_text, $MSB_first );

        // Create a new array to receive the valid unicode character numbers
        $new_unicode_array = array( );

        // Cycle through the unicode character numbers
        foreach( $unicode_array as  $unichar )
        {
                // Check if the unicode character number is valid for XML
                if ( ( $unichar == 0x09 ) ||
                     ( $unichar == 0x0A ) ||
                     ( $unichar == 0x0D ) ||
                     ( ( $unichar >= 0x20 ) && ( $unichar <= 0xD7FF ) ) ||
                     ( ( $unichar >= 0xE000 ) && ( $unichar <= 0xFFFD ) ) ||
                     ( ( $unichar >= 0x10000 ) && ( $unichar <= 0x10FFFF ) ) )
                {
                       // Unicode character is valid for XML - add it to the valid characters array
                       $new_unicode_array[] = $unichar;
                }

        }

        // Convert the array of valid unicode character numbers back to UTF-16 encoded text
        $UTF16_text = unicode_array_to_UTF16( $new_unicode_array, $MSB_first );

        // Escape any special HTML characters present
        $UTF16_text =  htmlspecialchars ( $UTF16_text, ENT_QUOTES );

        // Escape CR, LF and TAB characters, so that they are kept and not treated as expendable white space
        $trans = array( "\x09" => "&#x09;", "\x0A" => "&#x0A;", "\x0D" => "&#x0D;" );
        $UTF16_text = strtr( $UTF16_text, $trans );

        // Return the resulting XML valid string
        return $UTF16_text;
}

/******************************************************************************
* End of Function:     xml_UTF16_clean
******************************************************************************/






/******************************************************************************
*
* Function:     HTML_UTF8_Escape
*
* Description:  A HTML page can display UTF-8 data properly if it has a
*               META http-equiv="Content-Type" tag with the content attribute
*               including the value: "charset=utf-8".
*               Otherwise the ISO-8859-1 character set is usually assumed, and
*               Unicode values above 0x7F must be escaped.
*               This function takes a UTF-8 encoded string and escapes the
*               characters above 0x7F as well as reserved HTML characters such
*               as Quotes, Greater than, Less than and Ampersand.
*
* Parameters:   utf8_text - a string containing the UTF-8 data
*
* Returns:      htmloutput - a string containing the HTML equivalent
*
******************************************************************************/

function HTML_UTF8_Escape( $UTF8_text )
{

        // Ensure that the Unicode UTF8 encoding is valid.
        $UTF8_text = UTF8_fix( $UTF8_text );

        // Change: changed to use smart_htmlspecialchars, so that characters which were already escaped would remain intact, as of revision 1.10
        // Escape any special HTML characters present
        $UTF8_text =  smart_htmlspecialchars( $UTF8_text, ENT_QUOTES );

        // Convert the UTF-8 string to an array of unicode character numbers
        $unicode_array = UTF8_to_unicode_array( $UTF8_text );

        // Create a string to receive the escaped HTML
        $htmloutput = "";

        // Cycle through the unicode character numbers
        foreach( $unicode_array as  $unichar )
        {
                // Check if the character needs to be escaped
                if ( ( $unichar >= 0x00 ) && ( $unichar <= 0x7F ) )
                {
                        // Character is less than 0x7F - add it to the html as is
                        $htmloutput .= chr( $unichar );
                }
                else
                {
                        // Character is greater than 0x7F - escape it and add it to the html
                        $htmloutput .= "&#x" . dechex($unichar) . ";";
                }
        }

        // Return the resulting escaped HTML
        return $htmloutput;
}

/******************************************************************************
* End of Function:     HTML_UTF8_Escape
******************************************************************************/



/******************************************************************************
*
* Function:     HTML_UTF8_UnEscape
*
* Description:  Converts HTML which contains escaped decimal or hex characters
*               into UTF-8 text
*
* Parameters:   HTML_text - a string containing the HTML text to convert
*
* Returns:      utfoutput - a string containing the UTF-8 equivalent
*
******************************************************************************/

function HTML_UTF8_UnEscape( $HTML_text )
{
        preg_match_all( "/\&\#(\d+);/", $HTML_text, $matches);
        preg_match_all( "/\&\#[x|X]([A|B|C|D|E|F|a|b|c|d|e|f|0-9]+);/", $HTML_text, $hexmatches);
        foreach( $hexmatches[1] as $index => $match )
        {
                $matches[0][] = $hexmatches[0][$index];
                $matches[1][] = hexdec( $match );
        }

        for ( $i = 0; $i < count( $matches[ 0 ] ); $i++ )
        {
                $trans = array( $matches[0][$i] => unicode_array_to_UTF8( array( $matches[1][$i] ) ) );

                $HTML_text = strtr( $HTML_text , $trans );
        }
        return $HTML_text;
}

/******************************************************************************
* End of Function:     HTML_UTF8_UnEscape
******************************************************************************/






/******************************************************************************
*
* Function:     HTML_UTF16_Escape
*
* Description:  A HTML page can display UTF-16 data properly if it has a
*               META http-equiv="Content-Type" tag with the content attribute
*               including the value: "charset=utf-16".
*               Otherwise the ISO-8859-1 character set is usually assumed, and
*               Unicode values above 0x7F must be escaped.
*               This function takes a UTF-16 encoded string and escapes the
*               characters above 0x7F as well as reserved HTML characters such
*               as Quotes, Greater than, Less than and Ampersand.
*
* Parameters:   utf16_text - a string containing the UTF-16 data
*               MSB_first - True will cause processing as Big Endian UTF-16 (Motorola, MSB first)
*                           False will cause processing as Little Endian UTF-16 (Intel, LSB first)
*
* Returns:      htmloutput - a string containing the HTML equivalent
*
******************************************************************************/

function HTML_UTF16_Escape( $UTF16_text, $MSB_first )
{

        // Ensure that the Unicode UTF16 encoding is valid.
        $UTF16_text = UTF16_fix( $UTF16_text, $MSB_first );

        // Change: changed to use smart_htmlspecialchars, so that characters which were already escaped would remain intact, as of revision 1.10
        // Escape any special HTML characters present
        $UTF16_text =  smart_htmlspecialchars( $UTF16_text );

        // Convert the UTF-16 string to an array of unicode character numbers
        $unicode_array = UTF16_to_unicode_array( $UTF16_text, $MSB_first );

        // Create a string to receive the escaped HTML
        $htmloutput = "";

        // Cycle through the unicode character numbers
        foreach( $unicode_array as  $unichar )
        {
                // Check if the character needs to be escaped
                if ( ( $unichar >= 0x00 ) && ( $unichar <= 0x7F ) )
                {
                        // Character is less than 0x7F - add it to the html as is
                        $htmloutput .= chr( $unichar );
                }
                else
                {
                        // Character is greater than 0x7F - escape it and add it to the html
                        $htmloutput .= "&#x" . dechex($unichar) . ";";
                }
        }

        // Return the resulting escaped HTML
        return $htmloutput;
}

/******************************************************************************
* End of Function:     HTML_UTF16_Escape
******************************************************************************/


/******************************************************************************
*
* Function:     HTML_UTF16_UnEscape
*
* Description:  Converts HTML which contains escaped decimal or hex characters
*               into UTF-16 text
*
* Parameters:   HTML_text - a string containing the HTML text to be converted
*               MSB_first - True will cause processing as Big Endian UTF-16 (Motorola, MSB first)
*                           False will cause processing as Little Endian UTF-16 (Intel, LSB first)
*
* Returns:      utfoutput - a string containing the UTF-16 equivalent
*
******************************************************************************/

function HTML_UTF16_UnEscape( $HTML_text, $MSB_first )
{
        $utf8_text = HTML_UTF8_UnEscape( $HTML_text );

        return unicode_array_to_UTF16( UTF8_to_unicode_array( $utf8_text ), $MSB_first );
}

/******************************************************************************
* End of Function:     HTML_UTF16_UnEscape
******************************************************************************/




/******************************************************************************
*
* Function:     smart_HTML_Entities
*
* Description:  Performs the same function as HTML_Entities, but leaves entities
*               that are already escaped intact.
*
* Parameters:   HTML_text - a string containing the HTML text to be escaped
*
* Returns:      HTML_text_out - a string containing the escaped HTML text
*
******************************************************************************/

function smart_HTML_Entities( $HTML_text )
{
        // Get a table containing the HTML entities translations
        $translation_table = get_html_translation_table( HTML_ENTITIES );

        // Change the ampersand to translate to itself, to avoid getting &amp;
        $translation_table[ chr(38) ] = '&';

        // Perform replacements
        // Regular expression says: find an ampersand, check the text after it,
        // if the text after it is not one of the following, then replace the ampersand
        // with &amp;
        // a) any combination of up to 4 letters (upper or lower case) with at least 2 or 3 non whitespace characters, then a semicolon
        // b) a hash symbol, then between 2 and 7 digits
        // c) a hash symbol, an 'x' character, then between 2 and 7 digits
        // d) a hash symbol, an 'X' character, then between 2 and 7 digits
        return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,7}|#x[0-9]{2,7}|#X[0-9]{2,7};)/","&amp;" , strtr( $HTML_text, $translation_table ) );
}

/******************************************************************************
* End of Function:     smart_HTML_Entities
******************************************************************************/



/******************************************************************************
*
* Function:     smart_htmlspecialchars
*
* Description:  Performs the same function as htmlspecialchars, but leaves characters
*               that are already escaped intact.
*
* Parameters:   HTML_text - a string containing the HTML text to be escaped
*
* Returns:      HTML_text_out - a string containing the escaped HTML text
*
******************************************************************************/

function smart_htmlspecialchars( $HTML_text )
{
        // Get a table containing the HTML special characters translations
        $translation_table=get_html_translation_table (HTML_SPECIALCHARS);

        // Change the ampersand to translate to itself, to avoid getting &amp;
        $translation_table[ chr(38) ] = '&';

        // Perform replacements
        // Regular expression says: find an ampersand, check the text after it,
        // if the text after it is not one of the following, then replace the ampersand
        // with &amp;
        // a) any combination of up to 4 letters (upper or lower case) with at least 2 or 3 non whitespace characters, then a semicolon
        // b) a hash symbol, then between 2 and 7 digits
        // c) a hash symbol, an 'x' character, then between 2 and 7 digits
        // d) a hash symbol, an 'X' character, then between 2 and 7 digits
        return preg_replace( "/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,7}|#x[0-9]{2,7}|#X[0-9]{2,7};)/","&amp;" , strtr( $HTML_text, $translation_table ) );
}
$GLOBALS['Toolkit_Version'] = "1.12";

function Decode_PIM( $tag, $Tag_Definitions_Name )
{

        // Create a new EXIF tag for the output
        $newtag = $tag;

        // Check that this tag is for Print Image Matching Info
        if ( $tag['Type'] == "PIM" )
        {

                // Check that the data starts with PrintIM
                if ( substr( $tag['Data'], 0, 8 ) == "PrintIM\x00" )
                {

                        // Find the end of the version string
                        if ( ( $ver_pos = strpos ( $tag['Data'], "\0", 8 ) ) == -1 )
                        {
                                // couldn't find the start of the version string
                                return $newtag;
                        }
                        
                        // Create an array to receive the Data
                        $newtag['Data'] = array( );

                        // Extract the PrintIM version
                        $newtag['Data']['Version'] = substr( $tag['Data'], 8, $ver_pos - 8 );
                        // Skip the position over the version
                        $count_pos =  $ver_pos+2;
                        
                        // Extract the count of tags - 2 bytes
                        $PI_tag_count = get_IFD_Data_Type( substr($tag['Data'], $count_pos, 2) , 3, $tag['Byte Align'] );

                        // Panasonic have put an extra Null after the Version, which
                        // causes the tag count to be wrong -
                        // check if it is zero - i.e. possibly wrong
                        if ( ( $PI_tag_count == 0 ) )
                        {
                                // Tag count is zero - try moving the position by one,
                                // then re-extracting the count
                                $count_pos++;
                                $PI_tag_count = get_IFD_Data_Type( substr($tag['Data'], $count_pos, 2) , 3, $tag['Byte Align'] );
                        }

                        // Extract the data part of the PrintIM block
                        $data_part = substr($tag['Data'], $count_pos+2);

                        // Cycle through each tag
                        for ( $a = 0; $a < $PI_tag_count; $a++ )
                        {
                                // Read the tag number - 2 bytes
                                $PI_tag = get_IFD_Data_Type( substr($data_part, $a*6, 2) , 3, $tag['Byte Align'] );
                                
                                // Read the tag data - 4 bytes
                                $newtag['Data'][ ] = array( 'Tag Number' => $PI_tag, 'Data' => substr($data_part, $a*6+2, 4) , 'Decoded' => False );
                        }
                }
                
        }

        // Return the updated tag
        return $newtag;
        
}

/******************************************************************************
* End of Function:     Decode_PIM
******************************************************************************/




/******************************************************************************
*
* Function:     Encode_PIM
*
* Description:  Encodes the contents of a EXIF tag containing Print Image
*               Matching information, and returns the contents as a packed binary string
*
* Parameters:   tag - An EXIF tag containing Print Image Matching information
*                     as from get_EXIF_JPEG
*               Byte_Align - the Byte alignment to use - "MM" or "II"
*
* Returns:      packed_data - The packed binary string representing the PIM data
*
******************************************************************************/

function Encode_PIM( $tag, $Byte_Align)
{

        // Create a string to receive the packed data
        $packed_data = "";

        // Check that this tag is for Print Image Matching Info
        if ( $tag['Type'] == "PIM" )
        {
                // Check that the tag has been decoded - otherwise we don't need to do anything
                if ( ( is_array( $tag['Data'] ) ) &&
                     ( count ( $tag['Data'] ) > 0 ) )
                {
                        // Add the header to the packed data
                        $packed_data .= "PrintIM\x00";
                        
                        // Add the version to the packed data
                        $packed_data .= $tag['Data']['Version'] . "\x00";

                        // Create a string to receive the tag data
                        $tag_data_str = "";
                        
                        // Cycle through each tag
                        $tag_count = 0;
                        foreach( $tag['Data'] as $key => $curr_tag )
                        {
                                // Make sure this is a tag and not supplementary info
                                if ( is_numeric( $key ) )
                                {
                                        // Count how many tags are created
                                        $tag_count++;

                                        // Add the tag number to the packed tag data
                                        $tag_data_str .= put_IFD_Data_Type( $curr_tag['Tag Number'], 3, $Byte_Align );

                                        // Add the tag data to the packed tag data
                                        $tag_data_str .= $curr_tag['Data'];
                                }
                        }
                        
                        // Add the tag count to the packed data
                        $packed_data .= put_IFD_Data_Type( $tag_count, 3, $Byte_Align );
                        
                        // Add the packed tag data to the packed data
                        $packed_data .= $tag_data_str;
                }
        }
                        
        // Return the resulting packed data
        return $packed_data;

}

/******************************************************************************
* End of Function:     Encode_PIM
******************************************************************************/










/******************************************************************************
*
* Function:     get_PIM_Text_Value
*
* Description:  Interprets the contents of a EXIF tag containing Print Image
*               Matching information, and returns content as as a text string
*
* Parameters:   tag - An EXIF tag containing Print Image Matching information
*                     as from get_EXIF_JPEG
*               Tag_Definitions_Name - The name of the Tag Definitions group
*                                      within the global array IFD_Tag_Definitions
*
* Returns:      output_str - The text string representing the PIM info
*
******************************************************************************/

function get_PIM_Text_Value( $Tag, $Tag_Definitions_Name )
{

        // Create a string to receive the output
        $output_str = "";
        
        // Check if the PIM tag has been decoded
        if ( ( is_array( $Tag['Data'] ) ) &&
             ( count ( $Tag['Data'] ) > 0 ) )
        {
                // The tag has been decoded

                // Add the Version to the output
                $output_str = "Version: " . $Tag['Data']['Version'] . "\n";
                
                // Check if the user wants to hide unknown tags
                if ( $GLOBALS['HIDE_UNKNOWN_TAGS'] == FALSE )
                {
                        // The user wants to see unknown tags
                        // Cycle through each tag
                        foreach ( $Tag['Data'] as $PIM_tag_Key => $PIM_tag )
                        {
                                // Check that the tag is not the version array element
                                if ( $PIM_tag_Key !== 'Version' )
                                {
                                        // Add the tag to the output
                                        $output_str .= "Unknown Tag " . $PIM_tag['Tag Number'] . ": (" . strlen( $PIM_tag['Data'] ) . " bytes of data)\n";
                                }
                        }
                }
        }
        
        // Return the output text
        return $output_str;
}

function get_jpeg_App12_Pic_Info( $jpeg_header_data )
{
        // Flag that an APP12 segment has not been found yet
        $App12_PI_Location = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // Check if we have found an APP12 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP12" ) == 0 )
                {
                        // Found an APP12 segment
                        // Check if the APP12 has one of the correct labels (headers)
                        // for a picture info segment
                        if ( ( strncmp ( $jpeg_header_data[$i]['SegData'], "[picture info]", 14) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "\x0a\x09\x09\x09\x09[picture info]", 19) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "SEIKO EPSON CORP.  \00", 20) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "Agfa Gevaert   \x00", 16) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "SanyoElectricDSC\x00", 17) == 0 ) ||
                             ( strncmp ( substr($jpeg_header_data[$i]['SegData'],1,3), "\x00\x00\x00", 3) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "Type=", 5) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "OLYMPUS OPTICAL CO.,LTD.", 24) == 0 )  )
                        {
                                // A Picture Info segment was found, mark this position
                                $App12_PI_Location = $i;
                        }
                }
        }

        // Check if a Picture Info Segment was found
        if ( $App12_PI_Location != -1 )
        {
                // A picture Info Segment was found - Process it

                // Determine the length of the header if there is one
                $head_length = 0;

                if ( strncmp ( $jpeg_header_data[$App12_PI_Location]['SegData'], "App12 Gevaert   \x00", 16) == 0 )
                {
                        $head_length = 16;
                }
                else if ( strncmp ( $jpeg_header_data[$App12_PI_Location]['SegData'], "OLYMPUS OPTICAL CO.,LTD.", 24) == 0 )
                {
                        $head_length = 25;
                }
                else if ( strncmp ( $jpeg_header_data[$App12_PI_Location]['SegData'], "SEIKO EPSON CORP.  \00", 20) == 0 )
                {
                        $head_length = 20;
                }
                else if ( strncmp ( $jpeg_header_data[$App12_PI_Location]['SegData'], "\x0a\x09\x09\x09\x09[picture info]", 19) == 0 )
                {
                        $head_length = 5;
                }
                else if ( strncmp ( substr($jpeg_header_data[$App12_PI_Location]['SegData'],1,3), "\x00\x00\x00", 3) == 0 ) // HP
                {
                        $head_length = 0;
                }
                else if ( strncmp ( $jpeg_header_data[$App12_PI_Location]['SegData'], "SanyoElectricDSC\x00", 17) == 0 )
                {
                        $head_length = 17;
                }
                else
                {
                        $head_length = 0;
                }

                // Extract the header and the Picture Info Text from the APP12 segment
                $App12_PI_Head = substr( $jpeg_header_data[$App12_PI_Location]['SegData'], 0, $head_length );
                $App12_PI_Text = substr( $jpeg_header_data[$App12_PI_Location]['SegData'], $head_length );

                
                // Return the text which was extracted

                if ( ($pos = strpos ( $App12_PI_Text, "[end]" ) ) !== FALSE )
                {
                        return array( "Header" => $App12_PI_Head, "Picture Info" => substr( $App12_PI_Text, 0, $pos + 5 ) );
                }
                else
                {
                        return array( "Header" => $App12_PI_Head, "Picture Info" => $App12_PI_Text );
                }
        }

        // No Picture Info Segment Found - Return False
        return array( FALSE, FALSE );
}

/******************************************************************************
* End of Function:     get_jpeg_header_data
******************************************************************************/





/******************************************************************************
*
* Function:     put_jpeg_App12_Pic_Info
*
* Description:  Writes Picture Info text into an App12 JPEG segment. Uses information
*               supplied by the get_jpeg_header_data function. If no App12 exists
*               already a new one is created, otherwise it replaces the old one
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*               new_Pic_Info_Text - The Picture Info Text, including any header
*                                   that is required
*
* Returns:      jpeg_header_data - the JPEG header array with the new Picture
*                                  info segment inserted
*               FALSE - if an error occured
*
******************************************************************************/

function put_jpeg_App12_Pic_Info( $jpeg_header_data, $new_Pic_Info_Text )
{

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // Check if we have found an APP12 header,
                if ( strcmp ( $jpeg_header_data[$i][SegName], "APP12" ) == 0 )
                {
                        // Found an APP12 segment
                        // Check if the APP12 has one of the correct labels (headers)
                        // for a picture info segment
                        if ( ( strncmp ( $jpeg_header_data[$i]['SegData'], "[picture info]", 14) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "\x0a\x09\x09\x09\x09[picture info]", 19) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "SEIKO EPSON CORP.  \x00", 20) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "Agfa Gevaert   \x00", 16) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "SanyoElectricDSC\x00", 17) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "Type=", 5) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "OLYMPUS OPTICAL CO.,LTD.", 24) == 0 )  )
                        {
                                // Found a preexisting Picture Info segment - Replace it with the new one and return.
                                $jpeg_header_data[$i][SegData] = $new_Pic_Info_Text;
                                return $jpeg_header_data;
                        }
                }
        }

        // No preexisting Picture Info segment found, insert a new one at the start of the header data.

        // Determine highest position of an APP segment at or below APP12, so we can put the
        // new APP12 at this position
        

        $highest_APP = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // Check if we have found an APP segment at or below APP12,
                if ( ( $jpeg_header_data[$i]['SegType'] >= 0xE0 ) && ( $jpeg_header_data[$i]['SegType'] <= 0xEC ) )
                {
                        // Found an APP segment at or below APP12
                        $highest_APP = $i;
                }
        }

        // Insert the new Picture Info segment
        array_splice($jpeg_header_data, $highest_APP + 1 , 0, array( array(     "SegType" => 0xEC,
                                                                                "SegName" => "APP12",
                                                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xEC ],
                                                                                "SegData" => $new_Pic_Info_Text ) ) );

        return $jpeg_header_data;


}

/******************************************************************************
* End of Function:     put_jpeg_header_data
******************************************************************************/



/******************************************************************************
*
* Function:     Interpret_App12_Pic_Info_to_HTML
*
* Description:  Generates html showing the contents of any JPEG App12 Picture
*               Info segment
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      output - the HTML
*
******************************************************************************/

function Interpret_App12_Pic_Info_to_HTML( $jpeg_header_data )
{
        // Create a string to receive the output
        $output = "";

        // read the App12 Picture Info segment
        $PI = get_jpeg_App12_Pic_Info( $jpeg_header_data );

        // Check if the Picture Info segment was valid
        if ( $PI !== array(FALSE, FALSE) )
        {
                // Picture Info exists - add it to the output
                $output .= "<h2 class=\"Picture_Info_Main_Heading\">Picture Info Text</h2>\n";
                $output .= "<p><span class=\"Picture_Info_Caption_Text\">Header: </span><span class=\"Picture_Info_Value_Text\">" . HTML_UTF8_Escape( $PI['Header'] ) . "</span></p>\n";
                $output .= "<p class=\"Picture_Info_Caption_Text\">Picture Info Text:</p><pre class=\"Picture_Info_Value_Text\">" . HTML_UTF8_Escape( $PI['Picture Info'] ) . "</pre>\n";
        }

        // Return the result
        return $output;
}

function get_Photoshop_IRB( $jpeg_header_data )
{
        // Photoshop Image Resource blocks can span several JPEG APP13 segments, so we need to join them up if there are more than one
        $joined_IRB = "";


        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP13 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP13" ) == 0 )
                {
                        // And if it has the photoshop label,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "Photoshop 3.0\x00", 14) == 0 )
                        {
                                // join it to the other previous IRB data
                                $joined_IRB .= substr ( $jpeg_header_data[$i]['SegData'], 14 );
                        }
                }
        }

        // If there was some Photoshop IRB information found,
        if ( $joined_IRB != "" )
        {
                // Found a Photoshop Image Resource Block - extract it.
                // Change: Moved code into unpack_Photoshop_IRB_Data to allow TIFF reading as of 1.11
                return unpack_Photoshop_IRB_Data( $joined_IRB );

        }
        else
        {
                // No Photoshop IRB found
                return FALSE;
        }

}

/******************************************************************************
* End of Function:     get_Photoshop_IRB
******************************************************************************/










/******************************************************************************
*
* Function:     put_Photoshop_IRB
*
* Description:  Adds or modifies the Photoshop Information Resource Block (IRB)
*               information from an App13 JPEG segment. If a Photoshop IRB already
*               exists, it is replaced, otherwise a new one is inserted, using the
*               supplied data. Uses information supplied by the get_jpeg_header_data
*               function
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*               new_IRB_data - an array of the data to be stored in the Photoshop
*                              IRB segment. Should be in the same format as received
*                              from get_Photoshop_IRB
*
* Returns:      jpeg_header_data - the JPEG header data array with the
*                                  Photoshop IRB added.
*               FALSE - if an error occured
*
******************************************************************************/

function put_Photoshop_IRB( $jpeg_header_data, $new_IRB_data )
{
        // Delete all existing Photoshop IRB blocks - the new one will replace them

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ) ; $i++ )
        {
                // If we find an APP13 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP13" ) == 0 )
                {
                        // And if it has the photoshop label,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "Photoshop 3.0\x00", 14) == 0 )
                        {
                                // Delete the block information - it needs to be rebuilt
                                array_splice( $jpeg_header_data, $i, 1 );
                        }
                }
        }


        // Now we have deleted the pre-existing blocks


        // Retrieve the Packed Photoshop IRB Data
        // Change: Moved code into pack_Photoshop_IRB_Data to allow TIFF writing as of 1.11
        $packed_IRB_data = pack_Photoshop_IRB_Data( $new_IRB_data );

        // Change : This section changed to fix incorrect positioning of IRB segment, as of revision 1.10
        //          when there are no APP segments present

        //Cycle through the header segments in reverse order (to find where to put the APP13 block - after any APP0 to APP12 blocks)
        $i = count( $jpeg_header_data ) - 1;
        while (( $i >= 0 ) && ( ( $jpeg_header_data[$i]['SegType'] > 0xED ) || ( $jpeg_header_data[$i]['SegType'] < 0xE0 ) ) )
        {
                $i--;
        }



        // Cycle through the packed output data until it's size is less than 32000 bytes, outputting each 32000 byte block to an APP13 segment
        while ( strlen( $packed_IRB_data ) > 32000 )
        {
                // Change: Fixed put_Photoshop_IRB to output "Photoshop 3.0\x00" string with every APP13 segment, not just the first one, as of 1.03

                // Write a 32000 byte APP13 segment
                array_splice($jpeg_header_data, $i +1  , 0, array(  "SegType" => 0xED,
                                                                "SegName" => "APP13",
                                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xED ],
                                                                "SegData" => "Photoshop 3.0\x00" . substr( $packed_IRB_data,0,32000) ) );

                // Delete the 32000 bytes from the packed output data, that were just output
                $packed_IRB_data = substr_replace($packed_IRB_data, '', 0, 32000);
                $i++;
        }

        // Write the last block of packed output data to an APP13 segment - Note array_splice doesn't work with multidimensional arrays, hence inserting a blank string
        array_splice($jpeg_header_data, $i + 1 , 0, "" );
        $jpeg_header_data[$i + 1] =  array( "SegType" => 0xED,
                                        "SegName" => "APP13",
                                        "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xED ],
                                        "SegData" => "Photoshop 3.0\x00" . $packed_IRB_data );

        return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_Photoshop_IRB
******************************************************************************/








/******************************************************************************
*
* Function:     get_Photoshop_IPTC
*
* Description:  Retrieves IPTC-NAA IIM information from within a Photoshop
*               IRB (if it is present) and returns it in an array. Uses
*               information supplied by the get_jpeg_header_data function
*
* Parameters:   Photoshop_IRB_data - an array of Photoshop IRB records, as
*                                    returned from get_Photoshop_IRB
*
* Returns:      IPTC_Data_Out - The array of IPTC-NAA IIM records
*               FALSE - if an IPTC-NAA IIM record could not be found, or if
*                       an error occured
*
******************************************************************************/

function get_Photoshop_IPTC( $Photoshop_IRB_data )
{

        // Change: Initialise array correctly, as of revision 1.10
        $IPTC_Data_Out = array();

        //Cycle through the Photoshop 8BIM records looking for the IPTC-NAA record
        for( $i = 0; $i < count( $Photoshop_IRB_data ); $i++ )
        {
                // Check if each record is a IPTC record (which has id 0x0404)
                if ( $Photoshop_IRB_data[$i]['ResID']  == 0x0404 )
                {
                        // We've found an IPTC block - Decode it
                        $IPTC_Data_Out = get_IPTC( $Photoshop_IRB_data[$i]['ResData'] );
                }
        }

        // If there was no records put into the output array,
        if ( count( $IPTC_Data_Out ) == 0 )
        {
                // Then return false
                return FALSE;
        }
        else
        {
                // Otherwise return the array
                return $IPTC_Data_Out;
        }

}
/******************************************************************************
* End of Function:     get_Photoshop_IPTC
******************************************************************************/






/******************************************************************************
*
* Function:     put_Photoshop_IPTC
*
* Description:  Inserts a new IPTC-NAA IIM resource into a Photoshop
*               IRB, or replaces an the existing resource if one is present.
*               Uses information supplied by the get_Photoshop_IRB function
*
* Parameters:   Photoshop_IRB_data - an array of Photoshop IRB records, as
*                                    returned from get_Photoshop_IRB, into
*                                    which the IPTC-NAA IIM record will be inserted
*               new_IPTC_block - an array of IPTC-NAA records in the same format
*                                as those returned by get_Photoshop_IPTC
*
* Returns:      Photoshop_IRB_data - The Photoshop IRB array with the
*                                     IPTC-NAA IIM resource inserted
*
******************************************************************************/

function put_Photoshop_IPTC( $Photoshop_IRB_data, $new_IPTC_block )
{
        $iptc_block_pos = -1;

        //Cycle through the 8BIM records looking for the IPTC-NAA record
        for( $i = 0; $i < count( $Photoshop_IRB_data ); $i++ )
        {
                // Check if each record is a IPTC record (which has id 0x0404)
                if ( $Photoshop_IRB_data[$i]['ResID']  == 0x0404 )
                {
                        // We've found an IPTC block - save the position
                        $iptc_block_pos = $i;
                }
        }

        // If no IPTC block was found, create a new one
        if ( $iptc_block_pos == -1 )
        {
                // New block position will be at the end of the array
                $iptc_block_pos = count( $Photoshop_IRB_data );
        }


        // Write the new IRB resource to the Photoshop IRB array with no data
        $Photoshop_IRB_data[$iptc_block_pos] = array(   "ResID" =>   0x0404,
                                                        "ResName" => $GLOBALS['Photoshop_ID_Names'][ 0x0404 ],
                                                        "ResDesc" => $GLOBALS[ "Photoshop_ID_Descriptions" ][ 0x0404 ],
                                                        "ResEmbeddedName" => "\x00\x00",
                                                        "ResData" => put_IPTC( $new_IPTC_block ) );


        // Return the modified IRB
        return $Photoshop_IRB_data;
}

/******************************************************************************
* End of Function:     put_Photoshop_IPTC
******************************************************************************/








/******************************************************************************
*
* Function:     Interpret_IRB_to_HTML
*
* Description:  Generates html showing the information contained in a Photoshop
*               IRB data array, as retrieved with get_Photoshop_IRB, including
*               any IPTC-NAA IIM records found.
*
*               Please note that the following resource numbers are not currently
*               decoded: ( Many of these do not apply to JPEG images)
*               0x03E9, 0x03EE, 0x03EF, 0x03F0, 0x03F1, 0x03F2, 0x03F6, 0x03F9,
*               0x03FA, 0x03FB, 0x03FD, 0x03FE, 0x0400, 0x0401, 0x0402, 0x0405,
*               0x040E, 0x040F, 0x0410, 0x0412, 0x0413, 0x0415, 0x0416, 0x0417,
*               0x041B, 0x041C, 0x041D, 0x0BB7
*
*               ( Also these Obsolete resource numbers)
*               0x03E8, 0x03EB, 0x03FC, 0x03FF, 0x0403
*
*
* Parameters:   IRB_array - a Photoshop IRB data array as from get_Photoshop_IRB
*               filename - the name of the JPEG file being processed ( used
*                          by the script which displays the Photoshop thumbnail)
*
*
* Returns:      output_str - the HTML string
*
******************************************************************************/

function Interpret_IRB_to_HTML( $IRB_array, $filename )
{
        // Create a string to receive the HTML
        $output_str = "";

        // Check if the Photoshop IRB array is valid
        if ( $IRB_array !== FALSE )
        {

                // Create another string to receive secondary HTML to be appended at the end
                $secondary_output_str = "";

                // Add the Heading to the HTML
                $output_str .= "<h2 class=\"Photoshop_Main_Heading\">Contains Photoshop Information Resource Block (IRB)</h2>";

                // Add Table to the HTML
                $output_str .= "<table class=\"Photoshop_Table\" border=1>\n";

                // Cycle through each of the Photoshop IRB records, creating HTML for each
                foreach( $IRB_array as $IRB_Resource )
                {
                        // Check if the entry is a known Photoshop IRB resource

                        // Get the Name of the Resource
                        if ( array_key_exists( $IRB_Resource['ResID'], $GLOBALS[ "Photoshop_ID_Names" ] ) )
                        {
                                $Resource_Name = $GLOBALS['Photoshop_ID_Names'][ $IRB_Resource['ResID'] ];
                        }
                        else
                        {
                                // Change: Added check for $GLOBALS['HIDE_UNKNOWN_TAGS'] to allow hiding of unknown resources as of 1.11
                                if ( $GLOBALS['HIDE_UNKNOWN_TAGS'] == TRUE )
                                {
                                        continue;
                                }
                                else
                                {
                                        // Unknown Resource - Make appropriate name
                                        $Resource_Name = "Unknown Resource (". $IRB_Resource['ResID'] .")";
                                }
                        }

                        // Add HTML for the resource as appropriate
                        switch ( $IRB_Resource['ResID'] )
                        {

                                case 0x0404 : // IPTC-NAA IIM Record
                                        $secondary_output_str .= Interpret_IPTC_to_HTML( get_IPTC( $IRB_Resource['ResData'] ) );
                                        break;

                                case 0x040B : // URL
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><a href=\"" . $IRB_Resource['ResData'] . "\">" . htmlentities( $IRB_Resource['ResData'] ) ."</a></td></tr>\n";
                                        break;

                                case 0x040A : // Copyright Marked
                                        if ( hexdec( bin2hex( $IRB_Resource['ResData'] ) ) == 1 )
                                        {
                                                $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>Image is Copyrighted Material</pre></td></tr>\n";
                                        }
                                        else
                                        {
                                                $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>Image is Not Copyrighted Material</pre></td></tr>\n";
                                        }
                                        break;

                                case 0x040D : // Global Lighting Angle
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>Global lighting angle for effects layer = " . hexdec( bin2hex( $IRB_Resource['ResData'] ) ) . " degrees</pre></td></tr>\n";
                                        break;

                                case 0x0419 : // Global Altitude
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>Global Altitude = " . hexdec( bin2hex( $IRB_Resource['ResData'] ) ) . "</pre></td></tr>\n";
                                        break;

                                case 0x0421 : // Version Info
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= "Version = " . hexdec( bin2hex( substr( $IRB_Resource['ResData'], 0, 4 ) ) ) . "\n";
                                        $output_str .= "Has Real Merged Data = " . ord( $IRB_Resource['ResData']{4} ) . "\n";
                                        $writer_size = hexdec( bin2hex( substr( $IRB_Resource['ResData'], 5, 4 ) ) ) * 2;

                                        $output_str .= "Writer Name = " . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], 9, $writer_size ), TRUE ) . "\n";
                                        $reader_size = hexdec( bin2hex( substr( $IRB_Resource['ResData'], 9 + $writer_size , 4 ) ) ) * 2;
                                        $output_str .= "Reader Name = " . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], 13 + $writer_size, $reader_size ), TRUE ) . "\n";
                                        $output_str .= "File Version = " . hexdec( bin2hex( substr( $IRB_Resource['ResData'], 13 + $writer_size + $reader_size, 4 ) ) ) . "\n";
                                        $output_str .=  "</pre></td></tr>\n";
                                        break;

                                case 0x0411 : // ICC Untagged
                                        if ( $IRB_Resource['ResData'] == "\x01" )
                                        {
                                                $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>Intentionally untagged - any assumed ICC profile handling disabled</pre></td></tr>\n";
                                        }
                                        else
                                        {
                                                $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>Unknown value (0x" .bin2hex( $IRB_Resource['ResData'] ). ")</pre></td></tr>\n";
                                        }
                                        break;

                                case 0x041A : // Slices
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\">";

                                        // Unpack the first 24 bytes
                                        $Slices_Info = unpack("NVersion/NBound_top/NBound_left/NBound_bottom/NBound_right/NStringlen", $IRB_Resource['ResData'] );
                                        $output_str .= "Version = " . $Slices_Info['Version'] . "<br>\n";
                                        $output_str .= "Bounding Rectangle =  Top:" . $Slices_Info['Bound_top'] . ", Left:" . $Slices_Info['Bound_left'] . ", Bottom:" . $Slices_Info['Bound_bottom'] . ", Right:" . $Slices_Info['Bound_right'] . " (Pixels)<br>\n";
                                        $Slicepos = 24;

                                        // Extract a Unicode String
                                        $output_str .= "Text = '" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], 24, $Slices_Info['Stringlen']*2), TRUE ) . "'<br>\n";
                                        $Slicepos += $Slices_Info['Stringlen'] * 2;

                                        // Unpack the number of Slices
                                        $Num_Slices = hexdec( bin2hex( substr( $IRB_Resource['ResData'], $Slicepos, 4 ) ) );
                                        $output_str .= "Number of Slices = " . $Num_Slices . "\n";
                                        $Slicepos += 4;

                                        // Cycle through the slices
                                        for( $i = 1; $i <= $Num_Slices; $i++ )
                                        {
                                                $output_str .= "<br><br>Slice $i:<br>\n";

                                                // Unpack the first 16 bytes of the slice
                                                $SliceA = unpack("NID/NGroupID/NOrigin/NStringlen", substr($IRB_Resource['ResData'], $Slicepos ) );
                                                $Slicepos += 16;
                                                $output_str .= "ID = " . $SliceA['ID'] . "<br>\n";
                                                $output_str .= "Group ID = " . $SliceA['GroupID'] . "<br>\n";
                                                $output_str .= "Origin = " . $SliceA['Origin'] . "<br>\n";

                                                // Extract a Unicode String
                                                $output_str .= "Text = '" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], $Slicepos, $SliceA['Stringlen']*2), TRUE ) . "'<br>\n";
                                                $Slicepos += $SliceA['Stringlen'] * 2;

                                                // Unpack the next 24 bytes of the slice
                                                $SliceB = unpack("NType/NLeftPos/NTopPos/NRightPos/NBottomPos/NURLlen", substr($IRB_Resource['ResData'], $Slicepos )  );
                                                $Slicepos += 24;
                                                $output_str .= "Type = " . $SliceB['Type'] . "<br>\n";
                                                $output_str .= "Position =  Top:" . $SliceB['TopPos'] . ", Left:" . $SliceB['LeftPos'] . ", Bottom:" . $SliceB['BottomPos'] . ", Right:" . $SliceB['RightPos'] . " (Pixels)<br>\n";

                                                // Extract a Unicode String
                                                $output_str .= "URL = <a href='" . substr( $IRB_Resource['ResData'], $Slicepos, $SliceB['URLlen']*2) . "'>" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], $Slicepos, $SliceB['URLlen']*2), TRUE ) . "</a><br>\n";
                                                $Slicepos += $SliceB['URLlen'] * 2;

                                                // Unpack the length of a Unicode String
                                                $Targetlen = hexdec( bin2hex( substr( $IRB_Resource['ResData'], $Slicepos, 4 ) ) );
                                                $Slicepos += 4;
                                                // Extract a Unicode String
                                                $output_str .= "Target = '" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], $Slicepos, $Targetlen*2), TRUE ) . "'<br>\n";
                                                $Slicepos += $Targetlen * 2;

                                                // Unpack the length of a Unicode String
                                                $Messagelen = hexdec( bin2hex( substr( $IRB_Resource['ResData'], $Slicepos, 4 ) ) );
                                                $Slicepos += 4;
                                                // Extract a Unicode String
                                                $output_str .= "Message = '" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], $Slicepos, $Messagelen*2), TRUE ) . "'<br>\n";
                                                $Slicepos += $Messagelen * 2;

                                                // Unpack the length of a Unicode String
                                                $AltTaglen = hexdec( bin2hex( substr( $IRB_Resource['ResData'], $Slicepos, 4 ) ) );
                                                $Slicepos += 4;
                                                // Extract a Unicode String
                                                $output_str .= "Alt Tag = '" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], $Slicepos, $AltTaglen*2), TRUE ) . "'<br>\n";
                                                $Slicepos += $AltTaglen * 2;

                                                // Unpack the HTML flag
                                                if ( ord( $IRB_Resource['ResData']{ $Slicepos } ) === 0x01 )
                                                {
                                                        $output_str .= "Cell Text is HTML<br>\n";
                                                }
                                                else
                                                {
                                                        $output_str .= "Cell Text is NOT HTML<br>\n";
                                                }
                                                $Slicepos++;

                                                // Unpack the length of a Unicode String
                                                $CellTextlen = hexdec( bin2hex( substr( $IRB_Resource['ResData'], $Slicepos, 4 ) ) );
                                                $Slicepos += 4;
                                                // Extract a Unicode String
                                                $output_str .= "Cell Text = '" . HTML_UTF16_Escape( substr( $IRB_Resource['ResData'], $Slicepos, $CellTextlen*2), TRUE ) . "'<br>\n";
                                                $Slicepos += $CellTextlen * 2;


                                                // Unpack the last 12 bytes of the slice
                                                $SliceC = unpack("NAlignH/NAlignV/CAlpha/CRed/CGreen/CBlue", substr($IRB_Resource['ResData'], $Slicepos )  );
                                                $Slicepos += 12;
                                                $output_str .= "Alignment =  Horizontal:" . $SliceC['AlignH'] . ", Vertical:" . $SliceC['AlignV'] . "<br>\n";
                                                $output_str .= "Alpha Colour = " . $SliceC['Alpha'] . "<br>\n";
                                                $output_str .= "Red = " . $SliceC['Red'] . "<br>\n";
                                                $output_str .= "Green = " . $SliceC['Green'] . "<br>\n";
                                                $output_str .= "Blue = " . $SliceC['Blue'] . "\n";
                                        }

                                        $output_str .= "</td></tr>\n";

                                        break;


                                case 0x0408 : // Grid and Guides information
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\">";

                                        // Unpack the Grids info
                                        $Grid_Info = unpack("NVersion/NGridCycleH/NGridCycleV/NGuideCount", $IRB_Resource['ResData'] );
                                        $output_str .= "Version = " . $Grid_Info['Version'] . "<br>\n";
                                        $output_str .= "Grid Cycle = " . $Grid_Info['GridCycleH']/32 . " Pixel(s)  x  " . $Grid_Info['GridCycleV']/32 . " Pixel(s)<br>\n";
                                        $output_str .= "Number of Guides = " . $Grid_Info['GuideCount'] . "\n";

                                        // Cycle through the Guides
                                        for( $i = 0; $i < $Grid_Info['GuideCount']; $i++ )
                                        {
                                                // Unpack the info for this guide
                                                $Guide_Info = unpack("NLocation/CDirection", substr($IRB_Resource['ResData'],16+$i*5,5) );
                                                $output_str .= "<br>Guide $i : Location = " . $Guide_Info['Location']/32 . " Pixel(s) from edge";
                                                if ( $Guide_Info['Direction'] === 0 )
                                                {
                                                        $output_str .= ", Vertical\n";
                                                }
                                                else
                                                {
                                                        $output_str .= ", Horizontal\n";
                                                }
                                        }
                                        break;
                                        $output_str .= "</td></tr>\n";

                                case 0x0406 : // JPEG Quality
                                        $Qual_Info = unpack("nQuality/nFormat/nScans/Cconst", $IRB_Resource['ResData'] );
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\">";
                                        switch ( $Qual_Info['Quality'] )
                                        {
                                                case 0xFFFD:
                                                        $output_str .= "Quality 1 (Low)<br>\n";
                                                        break;
                                                case 0xFFFE:
                                                        $output_str .= "Quality 2 (Low)<br>\n";
                                                        break;
                                                case 0xFFFF:
                                                        $output_str .= "Quality 3 (Low)<br>\n";
                                                        break;
                                                case 0x0000:
                                                        $output_str .= "Quality 4 (Low)<br>\n";
                                                        break;
                                                case 0x0001:
                                                        $output_str .= "Quality 5 (Medium)<br>\n";
                                                        break;
                                                case 0x0002:
                                                        $output_str .= "Quality 6 (Medium)<br>\n";
                                                        break;
                                                case 0x0003:
                                                        $output_str .= "Quality 7 (Medium)<br>\n";
                                                        break;
                                                case 0x0004:
                                                        $output_str .= "Quality 8 (High)<br>\n";
                                                        break;
                                                case 0x0005:
                                                        $output_str .= "Quality 9 (High)<br>\n";
                                                        break;
                                                case 0x0006:
                                                        $output_str .= "Quality 10 (Maximum)<br>\n";
                                                        break;
                                                case 0x0007:
                                                        $output_str .= "Quality 11 (Maximum)<br>\n";
                                                        break;
                                                case 0x0008:
                                                        $output_str .= "Quality 12 (Maximum)<br>\n";
                                                        break;
                                                default:
                                                        $output_str .= "Unknown Quality (" . $Qual_Info['Quality'] . ")<br>\n";
                                                        break;
                                        }

                                        switch ( $Qual_Info['Format'] )
                                        {
                                                case 0x0000:
                                                        $output_str .= "Standard Format\n";
                                                        break;
                                                case 0x0001:
                                                        $output_str .= "Optimised Format\n";
                                                        break;
                                                case 0x0101:
                                                        $output_str .= "Progressive Format<br>\n";
                                                        break;
                                                default:
                                                        $output_str .= "Unknown Format (" . $Qual_Info['Format'] .")\n";
                                                        break;
                                        }
                                        if ( $Qual_Info['Format'] == 0x0101 )
                                        {
                                                switch ( $Qual_Info['Scans'] )
                                                {
                                                        case 0x0001:
                                                                $output_str .= "3 Scans\n";
                                                                break;
                                                        case 0x0002:
                                                                $output_str .= "4 Scans\n";
                                                                break;
                                                        case 0x0003:
                                                                $output_str .= "5 Scans\n";
                                                                break;
                                                        default:
                                                                $output_str .= "Unknown number of scans (" . $Qual_Info['Scans'] .")\n";
                                                                break;
                                                }
                                        }
                                        $output_str .= "</td></tr>\n";
                                        break;

                                case 0x0409 : // Thumbnail Resource
                                case 0x040C : // Thumbnail Resource
                                        $thumb_data = unpack("NFormat/NWidth/NHeight/NWidthBytes/NSize/NCompressedSize/nBitsPixel/nPlanes", $IRB_Resource['ResData'] );
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= "Format = " . (( $thumb_data['Format'] == 1 ) ? "JPEG RGB\n" :  "Raw RGB\n");
                                        $output_str .= "Width = " . $thumb_data['Width'] . "\n";
                                        $output_str .= "Height = " . $thumb_data['Height'] . "\n";
                                        $output_str .= "Padded Row Bytes = " . $thumb_data['WidthBytes'] . " bytes\n";
                                        $output_str .= "Total Size = " . $thumb_data['Size'] . " bytes\n";
                                        $output_str .= "Compressed Size = " . $thumb_data['CompressedSize'] . " bytes\n";
                                        $output_str .= "Bits per Pixel = " . $thumb_data['BitsPixel'] . " bits\n";
                                        $output_str .= "Number of planes = " . $thumb_data['Planes'] . " bytes\n";

                                        // Change: as of version 1.11 - Changed to make thumbnail link portable across directories
                                        // Build the path of the thumbnail script and its filename parameter to put in a url
                                        $link_str = get_relative_path( dirname(__FILE__) . "/get_ps_thumb.php" , getcwd ( ) );
                                        $link_str .= "?filename=";
                                        $link_str .= get_relative_path( $filename, dirname(__FILE__) );

                                        // Add thumbnail link to html
                                        $output_str .= "Thumbnail Data:</pre><a class=\"Photoshop_Thumbnail_Link\" href=\"$link_str\"><img class=\"Photoshop_Thumbnail_Link\" src=\"$link_str\"></a>\n";

                                        $output_str .=  "</td></tr>\n";
                                        break;

                                case 0x0414 : // Document Specific ID's
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>" . hexdec( bin2hex( $IRB_Resource['ResData'] ) ) . "</pre></td></tr>\n";
                                        break;

                                case 0x041E : // URL List
                                        $URL_count = hexdec( bin2hex( substr( $IRB_Resource['ResData'], 0, 4 ) ) );
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\">\n";
                                        $output_str .= "$URL_count URL's in list<br>\n";
                                        $urlstr = substr( $IRB_Resource['ResData'], 4 );
                                        // TODO: Check if URL List in Photoshop IRB works
                                        for( $i = 0; $i < $URL_count; $i++ )
                                        {
                                                $url_data = unpack( "NLong/NID/NURLSize", $urlstr );
                                                $output_str .= "URL $i info: long = " . $url_data['Long'] .", ";
                                                $output_str .= "ID = " . $url_data['ID'] . ", ";
                                                $urlstr = substr( $urlstr, 12 );
                                                $url = substr( $urlstr, 0, $url_data['URLSize'] );
                                                $output_str .= "URL = <a href=\"" . xml_UTF16_clean( $url, TRUE ) . "\">" . HTML_UTF16_Escape( $url, TRUE ) . "</a><br>\n";
                                        }
                                        $output_str .= "</td></tr>\n";
                                        break;
                                case 0x03F4 : // Grayscale and multichannel halftoning information.
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= Interpret_Halftone( $IRB_Resource['ResData'] );
                                        $output_str .= "</pre></td></tr>\n";
                                        break;
                                case 0x03F5 : // Color halftoning information
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= "Cyan Halftoning Info:\n" . Interpret_Halftone( substr( $IRB_Resource['ResData'], 0, 18 ) ) . "\n\n";
                                        $output_str .= "Magenta Halftoning Info:\n" . Interpret_Halftone( substr( $IRB_Resource['ResData'], 18, 18 ) ) . "\n\n";
                                        $output_str .= "Yellow Halftoning Info:\n" . Interpret_Halftone( substr( $IRB_Resource['ResData'], 36, 18 ) ) . "\n";
                                        $output_str .= "Black Halftoning Info:\n" . Interpret_Halftone( substr( $IRB_Resource['ResData'], 54, 18 ) ) . "\n";
                                        $output_str .= "</pre></td></tr>\n";
                                        break;

                                case 0x03F7 : // Grayscale and multichannel transfer function.
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= Interpret_Transfer_Function( substr( $IRB_Resource['ResData'], 0, 28 ) ) ;
                                        $output_str .= "</pre></td></tr>\n";
                                        break;

                                case 0x03F8 : // Color transfer functions
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= "Red Transfer Function:   \n" . Interpret_Transfer_Function( substr( $IRB_Resource['ResData'], 0, 28 ) ) . "\n\n";
                                        $output_str .= "Green Transfer Function: \n" . Interpret_Transfer_Function( substr( $IRB_Resource['ResData'], 28, 28 ) ) . "\n\n";
                                        $output_str .= "Blue Transfer Function:  \n" . Interpret_Transfer_Function( substr( $IRB_Resource['ResData'], 56, 28 ) ) . "\n";
                                        $output_str .= "</pre></td></tr>\n";
                                        break;

                                case 0x03F3 : // Print Flags
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        if ( $IRB_Resource['ResData']{0} == "\x01" )
                                        {
                                                $output_str .= "Labels Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Labels Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{1} == "\x01" )
                                        {
                                                $output_str .= "Crop Marks Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Crop Marks Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{2} == "\x01" )
                                        {
                                                $output_str .= "Color Bars Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Color Bars Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{3} == "\x01" )
                                        {
                                                $output_str .= "Registration Marks Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Registration Marks Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{4} == "\x01" )
                                        {
                                                $output_str .= "Negative Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Negative Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{5} == "\x01" )
                                        {
                                                $output_str .= "Flip Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Flip Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{6} == "\x01" )
                                        {
                                                $output_str .= "Interpolate Selected\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Interpolate Not Selected\n";
                                        }
                                        if ( $IRB_Resource['ResData']{7} == "\x01" )
                                        {
                                                $output_str .= "Caption Selected";
                                        }
                                        else
                                        {
                                                $output_str .= "Caption Not Selected";
                                        }
                                        $output_str .= "</pre></td></tr>\n";
                                        break;

                                case 0x2710 : // Print Flags Information
                                        $PrintFlags = unpack( "nVersion/CCentCrop/Cjunk/NBleedWidth/nBleedWidthScale", $IRB_Resource['ResData'] );
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= "Version = " . $PrintFlags['Version'] . "\n";
                                        $output_str .= "Centre Crop Marks = " . $PrintFlags['CentCrop'] . "\n";
                                        $output_str .= "Bleed Width = " . $PrintFlags['BleedWidth'] . "\n";
                                        $output_str .= "Bleed Width Scale = " . $PrintFlags['BleedWidthScale'];
                                        $output_str .= "</pre></td></tr>\n";
                                        break;

                                case 0x03ED : // Resolution Info
                                        $ResInfo = unpack( "nhRes_int/nhResdec/nhResUnit/nwidthUnit/nvRes_int/nvResdec/nvResUnit/nheightUnit", $IRB_Resource['ResData'] );
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\"><pre>\n";
                                        $output_str .= "Horizontal Resolution = " . ($ResInfo['hRes_int'] + $ResInfo['hResdec']/65536) . " pixels per Inch\n";
                                        $output_str .= "Vertical Resolution = " . ($ResInfo['vRes_int'] + $ResInfo['vResdec']/65536) . " pixels per Inch\n";
                                        if ( $ResInfo['hResUnit'] == 1 )
                                        {
                                                $output_str .= "Display units for Horizontal Resolution = Pixels per Inch\n";
                                        }
                                        elseif ( $ResInfo['hResUnit'] == 2 )
                                        {
                                                $output_str .= "Display units for Horizontal Resolution = Pixels per Centimetre\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Display units for Horizontal Resolution = Unknown Value (". $ResInfo['hResUnit'] .")\n";
                                        }

                                        if ( $ResInfo['vResUnit'] == 1 )
                                        {
                                                $output_str .= "Display units for Vertical Resolution = Pixels per Inch\n";
                                        }
                                        elseif ( $ResInfo['vResUnit'] == 2 )
                                        {
                                                $output_str .= "Display units for Vertical Resolution = Pixels per Centimetre\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Display units for Vertical Resolution = Unknown Value (". $ResInfo['vResUnit'] .")\n";
                                        }

                                        if ( $ResInfo['widthUnit'] == 1 )
                                        {
                                                $output_str .= "Display units for Image Width = Inches\n";
                                        }
                                        elseif ( $ResInfo['widthUnit'] == 2 )
                                        {
                                                $output_str .= "Display units for Image Width = Centimetres\n";
                                        }
                                        elseif ( $ResInfo['widthUnit'] == 3 )
                                        {
                                                $output_str .= "Display units for Image Width = Points\n";
                                        }
                                        elseif ( $ResInfo['widthUnit'] == 4 )
                                        {
                                                $output_str .= "Display units for Image Width = Picas\n";
                                        }
                                        elseif ( $ResInfo['widthUnit'] == 5 )
                                        {
                                                $output_str .= "Display units for Image Width = Columns\n";
                                        }
                                        else
                                        {
                                                $output_str .= "Display units for Image Width = Unknown Value (". $ResInfo['widthUnit'] .")\n";
                                        }

                                        if ( $ResInfo['heightUnit'] == 1 )
                                        {
                                                $output_str .= "Display units for Image Height = Inches";
                                        }
                                        elseif ( $ResInfo['heightUnit'] == 2 )
                                        {
                                                $output_str .= "Display units for Image Height = Centimetres";
                                        }
                                        elseif ( $ResInfo['heightUnit'] == 3 )
                                        {
                                                $output_str .= "Display units for Image Height = Points";
                                        }
                                        elseif ( $ResInfo['heightUnit'] == 4 )
                                        {
                                                $output_str .= "Display units for Image Height = Picas";
                                        }
                                        elseif ( $ResInfo['heightUnit'] == 5 )
                                        {
                                                $output_str .= "Display units for Image Height = Columns";
                                        }
                                        else
                                        {
                                                $output_str .= "Display units for Image Height = Unknown Value (". $ResInfo['heightUnit'] .")";
                                        }
                                        $output_str .= "</pre></td></tr>\n";
                                        break;

                                default : // All other records
                                        $output_str .= "<tr class=\"Photoshop_Table_Row\"><td class=\"Photoshop_Caption_Cell\">$Resource_Name</td><td class=\"Photoshop_Value_Cell\">RESOURCE DECODING NOT IMPLEMENTED YET<BR>" . strlen( $IRB_Resource['ResData'] ) . " bytes</td></tr>\n";

                        }

                }

                // Add the table end to the HTML
                $output_str .= "</table>\n";

                // Add any secondary output to the HTML
                $output_str .= $secondary_output_str;

        }

        // Return the HTML
        return $output_str;
}

/******************************************************************************
* End of Function:     Interpret_IRB_to_HTML
******************************************************************************/






/******************************************************************************
*
*         INTERNAL FUNCTIONS
*
******************************************************************************/







/******************************************************************************
*
* Function:     unpack_Photoshop_IRB_Data
*
* Description:  Extracts Photoshop Information Resource Block (IRB) information
*               from a binary string containing the IRB, as read from a file
*
* Parameters:   IRB_Data - The binary string containing the IRB
*
* Returns:      IRBdata - The array of Photoshop IRB records
*
******************************************************************************/

function unpack_Photoshop_IRB_Data( $IRB_Data )
{
        $pos = 0;

        // Cycle through the IRB and extract its records - Records are started with 8BIM, so cycle until no more instances of 8BIM can be found
        while ( ( $pos < strlen( $IRB_Data ) ) && ( ($pos = strpos( $IRB_Data, "8BIM", $pos) ) !== FALSE ) )
        {
                // Skip the position over the 8BIM characters
                $pos += 4;

                // Next two characters are the record ID - denoting what type of record it is.
                $ID = ord( $IRB_Data{ $pos } ) * 256 + ord( $IRB_Data{ $pos +1 } );

                // Skip the positionover the two record ID characters
                $pos += 2;

                // Next comes a Record Name - usually not used, but it should be a null terminated string, padded with 0x00 to be an even length
                $namestartpos = $pos;

                // Change: Fixed processing of embedded resource names, as of revision 1.10

                // NOTE: Photoshop does not process resource names according to the standard :
                // "Adobe Photoshop 6.0 File Formats Specification, Version 6.0, Release 2, November 2000"
                //
                // The resource name is actually formatted as follows:
                // One byte name length, followed by the null terminated ascii name string.
                // The field is then padded with a Null character if required, to ensure that the
                // total length of the name length and name is even.

                // Name - process it
                // Get the length
                $namelen = ord ( $IRB_Data{ $namestartpos } );

                // Total length of name and length info must be even, hence name length must be odd
                // Check if the name length is even,
                if ( $namelen % 2 == 0 )
                {
                        // add one to length to make it odd
                        $namelen ++;
                }
                // Extract the name
                $resembeddedname = trim( substr ( $IRB_Data, $namestartpos+1,  $namelen) );
                $pos += $namelen + 1;


                // Next is a four byte size field indicating the size in bytes of the record's data  - MSB first
                $datasize =     ord( $IRB_Data{ $pos } ) * 16777216 + ord( $IRB_Data{ $pos + 1 } ) * 65536 +
                                ord( $IRB_Data{ $pos + 2 } ) * 256 + ord( $IRB_Data{ $pos + 3 } );
                $pos += 4;

                // The record is stored padded with 0x00 characters to make the size even, so we need to calculate the stored size
                $storedsize =  $datasize + ($datasize % 2);

                $resdata = substr ( $IRB_Data, $pos, $datasize );

                // Get the description for this resource
                // Check if this is a Path information Resource, since they have a range of ID's
                if ( ( $ID >= 0x07D0 ) && ( $ID <= 0x0BB6 ) )
                {
                        $ResDesc = "ID Info : Path Information (saved paths).";
                }
                else
                {
                        if ( array_key_exists( $ID, $GLOBALS[ "Photoshop_ID_Descriptions" ] ) )
                        {
                                $ResDesc = $GLOBALS[ "Photoshop_ID_Descriptions" ][ $ID ];
                        }
                        else
                        {
                                $ResDesc = "";
                        }
                }

                // Get the Name of the Resource
                if ( array_key_exists( $ID, $GLOBALS[ "Photoshop_ID_Names" ] ) )
                {
                        $ResName = $GLOBALS['Photoshop_ID_Names'][ $ID ];
                }
                else
                {
                        $ResName = "";
                }


                // Store the Resource in the array to be returned

                $IRB_Array[] = array(     "ResID" => $ID,
                                        "ResName" => $ResName,
                                        "ResDesc" => $ResDesc,
                                        "ResEmbeddedName" => $resembeddedname,
                                        "ResData" => $resdata );

                // Jump over the data to the next record
                $pos += $storedsize;
        }

        // Return the array created
        return $IRB_Array;
}

/******************************************************************************
* End of Function:     unpack_Photoshop_IRB_Data
******************************************************************************/











/******************************************************************************
*
* Function:     pack_Photoshop_IRB_Data
*
* Description:  Packs a Photoshop Information Resource Block (IRB) array into it's
*               binary form, which can be written to a file
*
* Parameters:   IRB_data - an Photoshop IRB array to be converted. Should be in
*                          the same format as received from get_Photoshop_IRB
*
* Returns:      packed_IRB_data - the binary string of packed IRB data
*
******************************************************************************/

function pack_Photoshop_IRB_Data( $IRB_data )
{
        $packed_IRB_data = "";

        // Cycle through each resource in the IRB,
        foreach ($IRB_data as $resource)
        {

                // Change: Fix to avoid creating blank resources, as of revision 1.10

                // Check if there is actually any data for this resource
                if( strlen( $resource['ResData'] ) == 0 )
                {
                        // No data for resource - skip it
                        continue;
                }

                // Append the 8BIM tag, and resource ID to the packed output data
                $packed_IRB_data .= pack("a4n", "8BIM", $resource['ResID'] );


                // Change: Fixed processing of embedded resource names, as of revision 1.10

                // NOTE: Photoshop does not process resource names according to the standard :
                // "Adobe Photoshop 6.0 File Formats Specification, Version 6.0, Release 2, November 2000"
                //
                // The resource name is actually formatted as follows:
                // One byte name length, followed by the null terminated ascii name string.
                // The field is then padded with a Null character if required, to ensure that the
                // total length of the name length and name is even.

                // Append Name Size
                $packed_IRB_data .= pack( "c", strlen(trim($resource['ResEmbeddedName'])));

                // Append the Resource Name to the packed output data
                $packed_IRB_data .= trim($resource['ResEmbeddedName']);

                // If the resource name is even length, then with the addition of
                // the size it becomes odd and needs to be padded to an even number
                if ( strlen( trim($resource['ResEmbeddedName']) ) % 2 == 0 )
                {
                        // then it needs to be evened up by appending another null
                        $packed_IRB_data .= "\x00";
                }

                // Append the resource data size to the packed output data
                $packed_IRB_data .= pack("N", strlen( $resource['ResData'] ) );

                // Append the resource data to the packed output data
                $packed_IRB_data .= $resource['ResData'];

                // If the resource data is odd length,
                if ( strlen( $resource['ResData'] ) % 2 == 1 )
                {
                        // then it needs to be evened up by appending another null
                        $packed_IRB_data .= "\x00";
                }
        }

        // Return the packed data string
        return $packed_IRB_data;
}

/******************************************************************************
* End of Function:     pack_Photoshop_IRB_Data
******************************************************************************/








/******************************************************************************
*
* Internal Function:     Interpret_Transfer_Function
*
* Description:  Used by Interpret_IRB_to_HTML to interpret Color transfer functions
*               for Photoshop IRB resource 0x03F8. Converts the transfer function
*               information to a human readable version.
*
* Parameters:   Transfer_Function_Binary - a 28 byte Ink curves structure string
*
* Returns:      output_str - the text string containing the transfer function
*                            information
*
******************************************************************************/

function Interpret_Transfer_Function( $Transfer_Function_Binary )
{
        // Unpack the Transfer function information
        $Trans_vals = unpack ( "n13Curve/nOverride",  $Transfer_Function_Binary );

        $output_str = "Transfer Function Points: ";

        // Cycle through each of the Transfer function array values
        foreach ( $Trans_vals as $Key => $val )
        {
                // Check if the value should be negative
                if ($val > 32768 )
                {
                        // Value should be negative - make it so
                        $val = $val - 65536;
                }
                // Check that the Override item is not getting in this list, and
                // that the value is not -1, which means ignored
                if ( ( $Key != "Override" ) && ( $val != -1 ) )
                {
                        // This is a valid transfer function point, output it
                        $output_str .= $val/10 . "%, ";
                }
        }

        // Output the override info
        if ( $Trans_vals['Override'] == 0 )
        {
                $output_str .= "\nOverride: Let printer supply curve";
        }
        else
        {
                $output_str .= "\nOverride: Override printer’s default transfer curve";
        }

        // Return the result
        return $output_str;
}

/******************************************************************************
* End of Function:     Interpret_Transfer_Function
******************************************************************************/





/******************************************************************************
*
* Internal Function:     Interpret_Halftone
*
* Description:  Used by Interpret_IRB_to_HTML to interpret Color halftoning information
*               for Photoshop IRB resource 0x03F5. Converts the halftoning info
*               to a human readable version.
*
* Parameters:   Transfer_Function_Binary - a 18 byte Halftone screen parameter
&                                          structure string
*
* Returns:      output_str - the text string containing the transfer function
*                            information
*
******************************************************************************/

function Interpret_Halftone( $Halftone_Binary )
{
        // Create a string to receive the output
        $output_str = "";

        // Unpack the binary data into an array
        $HalftoneInfo = unpack( "nFreqVal_int/nFreqVal_dec/nFreqScale/nAngle_int/nAngle_dec/nShapeCode/NMisc/CAccurate/CDefault", $Halftone_Binary );

        // Interpret Ink Screen Frequency
        $output_str .= "Ink Screen Frequency = " . ($HalftoneInfo['FreqVal_int'] + $HalftoneInfo['FreqVal_dec']/65536) . " lines per Inch\n";
        if ( $HalftoneInfo['FreqScale'] == 1 )
        {
                $output_str .= "Display units for Ink Screen Frequency = Inches\n";
        }
        else
        {
                $output_str .= "Display units for Ink Screen Frequency = Centimetres\n";
        }

        // Interpret Angle for screen
        $output_str .= "Angle for screen = " . ($HalftoneInfo['Angle_int'] + $HalftoneInfo['Angle_dec']/65536) . " degrees\n";

        // Interpret Shape of Halftone Dots
        if ($HalftoneInfo['ShapeCode'] > 32768 )
        {
                $HalftoneInfo['ShapeCode'] = $HalftoneInfo['ShapeCode'] - 65536;
        }
        if ( $HalftoneInfo['ShapeCode'] == 0 )
        {
                $output_str .= "Shape of Halftone Dots = Round\n";
        }
        elseif ( $HalftoneInfo['ShapeCode'] == 1 )
        {
                $output_str .= "Shape of Halftone Dots = Ellipse\n";
        }
        elseif ( $HalftoneInfo['ShapeCode'] == 2 )
        {
                $output_str .= "Shape of Halftone Dots = Line\n";
        }
        elseif ( $HalftoneInfo['ShapeCode'] == 3 )
        {
                $output_str .= "Shape of Halftone Dots = Square\n";
        }
        elseif ( $HalftoneInfo['ShapeCode'] == 4 )
        {
                $output_str .= "Shape of Halftone Dots = Cross\n";
        }
        elseif ( $HalftoneInfo['ShapeCode'] == 6 )
        {
                $output_str .= "Shape of Halftone Dots = Diamond\n";
        }
        else
        {
                $output_str .= "Shape of Halftone Dots = Unknown shape (" . $HalftoneInfo['ShapeCode'] . ")\n";
        }

        // Interpret Accurate Screens
        if ( $HalftoneInfo['Accurate'] == 1 )
        {
                $output_str .= "Use Accurate Screens Selected\n";
        }
        else
        {
                $output_str .= "Use Other (not Accurate) Screens Selected\n";
        }

        // Interpret Printer Default Screens
        if ( $HalftoneInfo['Default'] == 1 )
        {
                $output_str .= "Use printer’s default screens\n";
        }
        else
        {
                $output_str .= "Use Other (not Printer Default) Screens Selected\n";
        }

        // Return Text
        return $output_str;

}

/******************************************************************************
* End of Global Variable:     Interpret_Halftone
******************************************************************************/












/******************************************************************************
* Global Variable:      Photoshop_ID_Names
*
* Contents:     The Names of the Photoshop IRB resources, indexed by their
*               resource number
*
******************************************************************************/

$GLOBALS[ "Photoshop_ID_Names" ] = array(
0x03E8 => "Number of channels, rows, columns, depth, and mode. (Obsolete)",
0x03E9 => "Macintosh print manager info ",
0x03EB => "Indexed color table (Obsolete)",
0x03ED => "Resolution Info",
0x03EE => "Alpha Channel Names",
0x03EF => "Display Info",
0x03F0 => "Caption String",
0x03F1 => "Border information",
0x03F2 => "Background color",
0x03F3 => "Print flags",
0x03F4 => "Grayscale and multichannel halftoning information",
0x03F5 => "Color halftoning information",
0x03F6 => "Duotone halftoning information",
0x03F7 => "Grayscale and multichannel transfer function",
0x03F8 => "Color transfer functions",
0x03F9 => "Duotone transfer functions",
0x03FA => "Duotone image information",
0x03FB => "Black and white values",
0x03FC => "Obsolete Resource.",
0x03FD => "EPS options",
0x03FE => "Quick Mask information",
0x03FF => "Obsolete Resource",
0x0400 => "Layer state information",
0x0401 => "Working path (not saved)",
0x0402 => "Layers group information",
0x0403 => "Obsolete Resource",
0x0404 => "IPTC-NAA record",
0x0405 => "Raw Format Image mode",
0x0406 => "JPEG quality",
0x0408 => "Grid and guides information",
0x0409 => "Thumbnail resource",
0x040A => "Copyright flag",
0x040B => "URL",
0x040C => "Thumbnail resource",
0x040D => "Global Angle",
0x040E => "Color samplers resource",
0x040F => "ICC Profile",
0x0410 => "Watermark",
0x0411 => "ICC Untagged",
0x0412 => "Effects visible",
0x0413 => "Spot Halftone",
0x0414 => "Document Specific IDs",
0x0415 => "Unicode Alpha Names",
0x0416 => "Indexed Color Table Count",
0x0417 => "Tansparent Index. Index of transparent color, if any.",
0x0419 => "Global Altitude",
0x041A => "Slices",
0x041B => "Workflow URL",
0x041C => "Jump To XPEP",
0x041D => "Alpha Identifiers",
0x041E => "URL List",
0x0421 => "Version Info",
0x0BB7 => "Name of clipping path.",
0x2710 => "Print flags information"
);

/******************************************************************************
* End of Global Variable:     Photoshop_ID_Names
******************************************************************************/





/******************************************************************************
* Global Variable:      Photoshop_ID_Descriptions
*
* Contents:     The Descriptions of the Photoshop IRB resources, indexed by their
*               resource number
*
******************************************************************************/

$GLOBALS[ "Photoshop_ID_Descriptions" ] = array(
0x03E8 => "Obsolete—Photoshop 2.0 only. number of channels, rows, columns, depth, and mode.",
0x03E9 => "Optional. Macintosh print manager print info record.",
0x03EB => "Obsolete—Photoshop 2.0 only. Contains the indexed color table.",
0x03ED => "ResolutionInfo structure. See Appendix A in Photoshop SDK Guide.pdf",
0x03EE => "Names of the alpha channels as a series of Pascal strings.",
0x03EF => "DisplayInfo structure. See Appendix A in Photoshop SDK Guide.pdf",
0x03F0 => "Optional. The caption as a Pascal string.",
0x03F1 => "Border information. border width, border units",
0x03F2 => "Background color.",
0x03F3 => "Print flags. labels, crop marks, color bars, registration marks, negative, flip, interpolate, caption.",
0x03F4 => "Grayscale and multichannel halftoning information.",
0x03F5 => "Color halftoning information.",
0x03F6 => "Duotone halftoning information.",
0x03F7 => "Grayscale and multichannel transfer function.",
0x03F8 => "Color transfer functions.",
0x03F9 => "Duotone transfer functions.",
0x03FA => "Duotone image information.",
0x03FB => "Effective black and white values for the dot range.",
0x03FC => "Obsolete Resource.",
0x03FD => "EPS options.",
0x03FE => "Quick Mask information. Quick Mask channel ID, Mask initially empty.",
0x03FF => "Obsolete Resource.",
0x0400 => "Layer state information. Index of target layer.",
0x0401 => "Working path (not saved).",
0x0402 => "Layers group information. Group ID for the dragging groups. Layers in a group have the same group ID.",
0x0403 => "Obsolete Resource.",
0x0404 => "IPTC-NAA record. This contains the File Info... information. See the IIMV4.pdf document.",
0x0405 => "Image mode for raw format files.",
0x0406 => "JPEG quality. Private.",
0x0408 => "Grid and guides information.",
0x0409 => "Thumbnail resource.",
0x040A => "Copyright flag. Boolean indicating whether image is copyrighted. Can be set via Property suite or by user in File Info...",
0x040B => "URL. Handle of a text string with uniform resource locator. Can be set via Property suite or by user in File Info...",
0x040C => "Thumbnail resource.",
0x040D => "Global Angle. Global lighting angle for effects layer.",
0x040E => "Color samplers resource.",
0x040F => "ICC Profile. The raw bytes of an ICC format profile, see the ICC34.pdf and ICC34.h files from the Internation Color Consortium.",
0x0410 => "Watermark.",
0x0411 => "ICC Untagged. Disables any assumed profile handling when opening the file. 1 = intentionally untagged.",
0x0412 => "Effects visible. Show/hide all the effects layer.",
0x0413 => "Spot Halftone. Version, length, variable length data.",
0x0414 => "Document specific IDs for layer identification",
0x0415 => "Unicode Alpha Names. Length and the string",
0x0416 => "Indexed Color Table Count. Number of colors in table that are actually defined",
0x0417 => "Transparent Index. Index of transparent color, if any.",
0x0419 => "Global Altitude.",
0x041A => "Slices.",
0x041B => "Workflow URL. Length, string.",
0x041C => "Jump To XPEP. Major version, Minor version, Count. Table which can include: Dirty flag, Mod date.",
0x041D => "Alpha Identifiers.",
0x041E => "URL List. Count of URLs, IDs, and strings",
0x0421 => "Version Info. Version, HasRealMergedData, string of writer name, string of reader name, file version.",
0x0BB7 => "Name of clipping path.",
0x2710 => "Print flags information. Version, Center crop marks, Bleed width value, Bleed width scale."
);

$GLOBALS[ "Software Name" ] = "(PHP JPEG Metadata Toolkit v" . $GLOBALS['Toolkit_Version'] . ")";          // Change:  Changed version numbers to reference Toolkit_Version.php - as of version 1.11

/******************************************************************************
* End of Global Variable:     Software Name
******************************************************************************/






/******************************************************************************
*
* Function:     get_photoshop_file_info
*
* Description:  Retrieves Photoshop 'File Info' metadata in the same way that Photoshop
*               does. The results are returned in an array as below:
*
*               $file_info_array = array(
*                       "title"                  => "",
*                       "author"                 => "",
*                       "authorsposition"        => "",      // Note: Not used in Photoshop 7 or higher
*                       "caption"                => "",
*                       "captionwriter"          => "",
*                       "jobname"                => "",      // Note: Not used in Photoshop CS
*                       "copyrightstatus"        => "",
*                       "copyrightnotice"        => "",
*                       "ownerurl"               => "",
*                       "keywords"               => array( 0 => "", 1 => "", ... ),
*                       "category"               => "",     // Note: Max 3 characters
*                       "supplementalcategories" => array( 0 => "", 1 => "", ... ),
*                       "date"                   => "",     // Note: DATE MUST BE IN YYYY-MM-DD format
*                       "city"                   => "",
*                       "state"                  => "",
*                       "country"                => "",
*                       "credit"                 => "",
*                       "source"                 => "",
*                       "headline"               => "",
*                       "instructions"           => "",
*                       "transmissionreference"  => "",
*                       "urgency"                => "" );
*
* Parameters:   Exif_array - an array containing the EXIF information to be
*                            searched, as retrieved by get_EXIF_JPEG. (saves having to parse the EXIF again)
*               XMP_array - an array containing the XMP information to be
*                           searched, as retrieved by read_XMP_array_from_text. (saves having to parse the XMP again)
*               IRB_array - an array containing the Photoshop IRB information
*                           to be searched, as retrieved by get_Photoshop_IRB. (saves having to parse the IRB again)
*
* Returns:      outputarray - an array as above, containing the Photoshop File Info data
*
******************************************************************************/

function get_photoshop_file_info( $Exif_array, $XMP_array, $IRB_array )
{

        // Create a blank array to receive the output
        $outputarray = array(
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


        /***************************************/

        // XMP Processing


        // Retrieve the dublin core section from the XMP header

        // Extract the Dublin Core section from the XMP
        $dublincore_block = find_XMP_block( $XMP_array, "dc" );

        // Check that the Dublin Core section exists
        if ( $dublincore_block != FALSE )
        {
                // Dublin Core Description Field contains caption
                // Extract Description
                $Item = find_XMP_item( $dublincore_block, "dc:description" );

                // Check if Description Tag existed
                if ( $Item != FALSE )
                {
                        // Ensure that the Description value exists and save it.
                        if  ( ( array_key_exists( 'children', $Item ) ) &&
                              ( $Item['children'][0]['tag'] == "rdf:Alt" ) && array_key_exists( 'children', $Item['children'][0]) &&
                              ( array_key_exists( 'value', $Item['children'][0]['children'][0] ) ) )
                        {
                                $outputarray = add_to_field( $outputarray, 'caption' , HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ), "\n" );
                        }
                }

                /***************************************/

                // Dublin Core Creator Field contains author
                // Extract Description
                $Item = find_XMP_item( $dublincore_block, "dc:creator" );

                // Check if Creator Tag existed
                if ( $Item != FALSE )
                {
                        // Ensure that the Creator value exists and save it.
                        if  ( ( array_key_exists( 'children', $Item ) ) &&
                              ( $Item['children'][0]['tag'] =="rdf:Seq" ) &&array_key_exists( 'children', $Item['children'][0]) &&
                              ( array_key_exists( 'value', $Item['children'][0]['children'][0] ) ) )
                        {
                                $outputarray = add_to_field( $outputarray, 'author' , HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ), "\n" );
                        }
                }

                /***************************************/

                // Dublin Core Title Field contains title
                // Extract Title
                $Item = find_XMP_item( $dublincore_block, "dc:title" );

                // Check if Title Tag existed
                if ( $Item != FALSE )
                {
                        // Ensure that the Title value exists and save it.
                        if  ( ( array_key_exists( 'children', $Item ) ) &&
                              ( $Item['children'][0]['tag'] =="rdf:Alt" ) &&array_key_exists( 'children', $Item['children'][0]) &&
                              ( array_key_exists( 'value', $Item['children'][0]['children'][0] ) ) )
                        {

                                $outputarray = add_to_field( $outputarray, 'title' , HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ), "," );
                        }
                }

                /***************************************/

                // Dublin Core Rights Field contains copyrightnotice
                // Extract Rights
                $Item = find_XMP_item( $dublincore_block, "dc:rights" );

                // Check if Rights Tag existed
                if ( $Item != FALSE )
                {
                        // Ensure that the Rights value exists and save it.
                        if  ( ( array_key_exists( 'children', $Item ) ) &&
                              ( $Item['children'][0]['tag'] =="rdf:Alt" ) &&array_key_exists( 'children', $Item['children'][0]) &&
                              ( array_key_exists( 'value', $Item['children'][0]['children'][0] ) ) )
                        {

                                $outputarray = add_to_field( $outputarray, 'copyrightnotice' , HTML_UTF8_Escape( $Item['children'][0]['children'][0]['value'] ), "," );
                        }
                }

                /***************************************/

                // Dublin Core Subject Field contains keywords
                // Extract Subject
                $Item = find_XMP_item( $dublincore_block, "dc:subject" );

                // Check if Subject Tag existed
                if ( $Item != FALSE )
                {
                        // Ensure that the Subject values exist
                        if  ( ( array_key_exists( 'children', $Item ) ) && ( $Item['children'][0]['tag'] =="rdf:Bag" ) )
                        {

                                if(array_key_exists( 'children', $Item['children'][0])){
                                         // Cycle through each Subject value and save them
                                foreach ( $Item['children'][0]['children'] as $keywords )
                                {

                                        if ( ! in_array ( HTML_UTF8_Escape( $keywords['value'] ), $outputarray['keywords']))
                                        {
                                                if  ( array_key_exists( 'value', $keywords ) )
                                                {
                                                        $outputarray['keywords'][] = HTML_UTF8_Escape( $keywords['value'] );
                                                }
                                        }
                                }
                                }
                               
                        }
                }



        }

        /***************************************/

        // Find the Photoshop Information within the XMP block
        $photoshop_block = find_XMP_block( $XMP_array, "photoshop" );

        // Check that the Photoshop Information exists
        if ( $photoshop_block != FALSE )
        {
                // The Photoshop CaptionWriter tag contains captionwriter - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:CaptionWriter" );

                // Check that the CaptionWriter Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'captionwriter' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Headline tag contains headline - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Headline" );

                // Check that the Headline Field exists and save the value
                if ( ( $Item != FALSE )  && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'headline' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Instructions tag contains instructions - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Instructions" );

                // Check that the Instructions Field exists and save the value
                if ( ( $Item != FALSE )  && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'instructions' , HTML_UTF8_Escape( $Item['value'] ), "\n" );
                }

                /***************************************/

                // The Photoshop AuthorsPosition tag contains authorsposition - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:AuthorsPosition" );

                // Check that the AuthorsPosition Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'authorsposition' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Credit tag contains credit - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Credit" );

                // Check that the Credit Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'credit' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Source tag contains source - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Source" );

                // Check that the Credit Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'source' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop City tag contains city - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:City" );

                // Check that the City Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'city' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop State tag contains state - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:State" );

                // Check that the State Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'state' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Country tag contains country - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Country" );

                // Check that the Country Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'country' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop TransmissionReference tag contains transmissionreference - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:TransmissionReference" );

                // Check that the TransmissionReference Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'transmissionreference' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Category tag contains category - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Category" );

                // Check that the TransmissionReference Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'category' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop DateCreated tag contains date - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:DateCreated" );

                // Check that the DateCreated Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'date' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop Urgency tag contains urgency - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:Urgency" );

                // Check that the Urgency Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'urgency' , HTML_UTF8_Escape( $Item['value'] ), "," );
                }

                /***************************************/

                // The Photoshop SupplementalCategories tag contains supplementalcategories - Find it
                $Item = find_XMP_item( $photoshop_block, "photoshop:SupplementalCategories" );

                // Check that the SupplementalCategories Field exists
                if ( $Item != FALSE )
                {
                        // Check that the values exist
                        if  ( ( array_key_exists( 'children', $Item ) ) && ( $Item['children'][0]['tag'] =="rdf:Bag" ) )
                        {
                                // Cycle through the values and save them
                                foreach ( $Item['children'][0]['children'] as $sup_category )
                                {
                                        if ( ( array_key_exists( 'value', $sup_category ) ) &&
                                             ( ! in_array ( HTML_UTF8_Escape( $sup_category['value'] ), $outputarray['supplementalcategories'])) )
                                        {
                                                if ( array_key_exists( 'value', $sup_category ) )
                                                {
                                                        $outputarray['supplementalcategories'][] = HTML_UTF8_Escape( $sup_category['value'] );
                                                }
                                        }
                                }
                        }
                }

        }

        /***************************************/

        // Find the Job Reference Information within the XMP block
        $job_block = find_XMP_block( $XMP_array, "xapBJ" );

        // Check that the Job Reference Information exists
        if ( $job_block != FALSE )
        {
                // The JobRef Field contains jobname - Find it
                $Item = find_XMP_item( $job_block, "xapBJ:JobRef" );

                // Check that the JobRef Field exists
                if ( $Item != FALSE )
                {
                        // Check that the value exists and save it
                        if ( ( array_key_exists( 'children', $Item ) ) &&
                             ( $Item['children'][0]['tag'] =="rdf:Bag" ) &&
                             ( array_key_exists( 'children', $Item['children'][0] ) ) &&
                             ( $Item['children'][0]['children'][0]['tag'] =="rdf:li" ) &&
                             ( array_key_exists( 'children', $Item['children'][0]['children'][0] ) ) &&
                             ( $Item['children'][0]['children'][0]['children'][0]['tag'] =="stJob:name" ) &&
                             ( array_key_exists( 'value', $Item['children'][0]['children'][0]['children'][0] ) ) )
                        {
                                $outputarray = add_to_field( $outputarray, 'jobname' , HTML_UTF8_Escape( $Item['children'][0]['children'][0]['children'][0]['value'] ), "," );
                        }
                }
        }


        /***************************************/

        // Find the Rights Information within the XMP block
        $rights_block = find_XMP_block( $XMP_array, "xapRights" );

        // Check that the Rights Information exists
        if ( $rights_block != FALSE )
        {
                // The WebStatement Field contains ownerurl - Find it
                $Item = find_XMP_item( $rights_block, "xapRights:WebStatement" );

                // Check that the WebStatement Field exists and save the value
                if ( ( $Item != FALSE )  && ( array_key_exists( 'value', $Item ) ) )
                {
                        $outputarray = add_to_field( $outputarray, 'ownerurl' , HTML_UTF8_Escape( $Item['value'] ), "\n" );
                }

                /***************************************/

                // The Marked Field contains copyrightstatus - Find it
                $Item = find_XMP_item( $rights_block, "xapRights:Marked" );

                // Check that the Marked Field exists and save the value
                if ( ( $Item != FALSE ) && ( array_key_exists( 'value', $Item ) ) )
                {
                        if ( $Item['value'] == "True" )
                        {
                                $outputarray = add_to_field( $outputarray, 'copyrightstatus' , "Copyrighted Work", "," );
                        }
                        else
                        {
                                $outputarray = add_to_field( $outputarray, 'copyrightstatus' , "Public Domain", "," );
                        }
                }

        }





        /***************************************/

        // Photoshop IRB Processing

        // Check that the Photoshop IRB exists
        if ( $IRB_array != FALSE )
        {
                // Create a translation table to convert carriage returns to linefeeds
                $irbtrans = array("\x0d" => "\x0a");

                // The Photoshop IRB Copyright flag (0x040A) contains copyrightstatus - find it
                $IRB_copyright_flag = find_Photoshop_IRB_Resource( $IRB_array, 0x040A );

                // Check if the Copyright flag Field exists, and save the value
                if( $IRB_copyright_flag != FALSE )
                {
                        // Check the value of the copyright flag
                        if ( hexdec( bin2hex( $IRB_copyright_flag['ResData'] ) ) == 1 )
                        {
                                // Save the value
                                $outputarray = add_to_field( $outputarray, 'copyrightstatus' , "Copyrighted Work", "," );
                        }
                        else
                        {
                                // Do nothing - copyrightstatus will be set to unmarked if still blank at end
                        }
                }

                /***************************************/

                // The Photoshop IRB URL (0x040B) contains ownerurl - find it
                $IRB_url = find_Photoshop_IRB_Resource( $IRB_array, 0x040B );

                // Check if the URL Field exists and save the value
                if( $IRB_url != FALSE )
                {
                        $outputarray = add_to_field( $outputarray, 'ownerurl' , strtr( $IRB_url['ResData'], $irbtrans ), "\n" );
                }

                /***************************************/

                // Extract any IPTC block from the Photoshop IRB information
                $IPTC_array = get_Photoshop_IPTC( $IRB_array );

                // Check if the IPTC block exits
                if ( ( $IPTC_array != FALSE ) && ( count( $IPTC_array ) != 0 ) )
                {
                        // The IPTC Caption/Abstract Field contains caption - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:120" );

                        // Check if the Caption/Abstract Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'caption' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Caption Writer/Editor Field contains captionwriter - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:122" );

                        // Check if the Caption Writer/Editor Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'captionwriter' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Headline Field contains headline - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:105" );

                        // Check if the Headline Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'headline' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Special Instructions Field contains instructions - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:40" );

                        // Check if the Special Instructions Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'instructions' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC By-Line Field contains author - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:80" );

                        // Check if the By-Line Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'author' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC By-Line Title Field contains authorsposition - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:85" );

                        // Check if the By-Line Title Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'authorsposition' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Credit Field contains credit - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:110" );

                        // Check if the Credit Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'credit' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Source Field contains source - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:115" );

                        // Check if the Source Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'source' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Object Name Field contains title - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:05" );

                        // Check if the Object Name Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'title' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Date Created Field contains date - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:55" );

                        // Check if the Date Created Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $date_array = unpack( "a4Year/a2Month/A2Day", $record['RecData'] );
                                $tmpdate = $date_array['Year'] . "-" . $date_array['Month'] . "-" . $date_array['Day'];
                                $outputarray = add_to_field( $outputarray, 'date' , strtr( $tmpdate, $irbtrans ), "," );

                        }

                        /***************************************/

                        // The IPTC City Field contains city - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:90" );

                        // Check if the City Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'city' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Province/State Field contains state - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:95" );

                        // Check if the Province/State Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'state' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Country/Primary Location Name Field contains country - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:101" );

                        // Check if the Country/Primary Location Name Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'country' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Original Transmission Reference Field contains transmissionreference - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:103" );

                        // Check if the Original Transmission Reference Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'transmissionreference' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                        /***************************************/

                        // The IPTC Category Field contains category - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:15" );

                        // Check if the Category Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'category' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }


                        /***************************************/

                        // Cycle through the IPTC records looking for Supplemental Category records
                        foreach ($IPTC_array as $record)
                        {
                                // Check if a Supplemental Category record has been found
                                if ( $record['IPTC_Type'] == "2:20" )
                                {
                                        // A Supplemental Category record has been found, save it's value if the value doesn't already exist
                                        if ( ! in_array ( $record['RecData'], $outputarray['supplementalcategories']))
                                        {
                                                $outputarray['supplementalcategories'][] = strtr( $record['RecData'], array("\x0a" => "", "\x0d" => "&#xA;") ) ;
                                        }
                                }
                        }


                        /***************************************/

                        // The IPTC Urgency Field contains urgency - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:10" );

                        // Check if the Urgency Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'urgency' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }



                        /***************************************/

                        // Cycle through the IPTC records looking for Keywords records
                        foreach ($IPTC_array as $record)
                        {
                                // Check if a Keywords record has been found
                                if ( $record['IPTC_Type'] == "2:25" )
                                {
                                        // A Keywords record has been found, save it's value if the value doesn't already exist
                                        if ( ! in_array ( $record['RecData'], $outputarray['keywords']))
                                        {
                                                $outputarray['keywords'][] = strtr( $record['RecData'], array("\x0a" => "", "\x0d" => "&#xA;") ) ;
                                        }
                                }
                        }


                        /***************************************/

                        // The IPTC Copyright Notice Field contains copyrightnotice - find it
                        $record = find_IPTC_Resource( $IPTC_array, "2:116" );

                        // Check if the Copyright Field exists and save the value
                        if ( $record != FALSE  )
                        {
                                $outputarray = add_to_field( $outputarray, 'copyrightnotice' , strtr( $record['RecData'], $irbtrans ), "\n" );
                        }

                }
        }




        /***************************************/

        // EXIF Processing


        // Retreive Information from the EXIF data if it exists

        if ( ( $Exif_array != FALSE ) || ( count( $Exif_array ) == 0 ) )
        {
                // Check the Image Description Tag - it can contain the caption
                if ( array_key_exists( 270, $Exif_array[0] ) )
                {
                        $outputarray = add_to_field( $outputarray, 'caption' , $Exif_array[0][270]['Data'][0], "\n" );
                }

                /***************************************/

                // Check the Copyright Information Tag - it contains the copyrightnotice
                if ( array_key_exists( 33432, $Exif_array[0] ) )
                {
                        $outputarray = add_to_field( $outputarray, 'copyrightnotice' , HTML_UTF8_UnEscape( $Exif_array[0][33432]['Data'][0] ), "\n" );
                }

                /***************************************/

                // Check the Artist Name Tag - it contains the author
                if ( array_key_exists( 315, $Exif_array[0] ) )
                {
                        $outputarray = add_to_field( $outputarray, 'author' , HTML_UTF8_UnEscape( $Exif_array[0][315]['Data'][0] ), "\n" );
                }

        }


        /***************************/

        // FINISHED RETRIEVING INFORMATION

        // Perform final processing


        // Check if any urgency information was found
        if ( $outputarray["urgency"] == "" )
        {
                // No urgency information was found - set it to default (None)
                $outputarray["urgency"] = "none";
        }

        // Check if any copyrightstatus information was found
        if ( $outputarray["copyrightstatus"] == "" )
        {
                // No copyrightstatus information was found - set it to default (Unmarked)
                $outputarray["copyrightstatus"] = "unmarked";
        }

        // Return the resulting Photoshop File Info Array
        return $outputarray;

}

/******************************************************************************
* End of Function:     get_photoshop_file_info
******************************************************************************/






/******************************************************************************
*
* Function:     put_photoshop_file_info
*
* Description:  Stores Photoshop 'File Info' metadata in the same way that Photoshop
*               does. The 'File Info' metadata must be in an array similar to that
*               returned by get_photoshop_file_info, as follows:
*
*               $file_info_array = array(
*                       "title"                  => "",
*                       "author"                 => "",
*                       "authorsposition"        => "",      // Note: Not used in Photoshop 7 or higher
*                       "caption"                => "",
*                       "captionwriter"          => "",
*                       "jobname"                => "",      // Note: Not used in Photoshop CS
*                       "copyrightstatus"        => "",
*                       "copyrightnotice"        => "",
*                       "ownerurl"               => "",
*                       "keywords"               => array( 0 => "", 1 => "", ... ),
*                       "category"               => "",     // Note: Max 3 characters
*                        "supplementalcategories" => array( 0 => "", 1 => "", ... ),
*                       "date"                   => "",     // Note: DATE MUST BE IN YYYY-MM-DD format
*                       "city"                   => "",
*                       "state"                  => "",
*                       "country"                => "",
*                       "credit"                 => "",
*                       "source"                 => "",
*                       "headline"               => "",
*                       "instructions"           => "",
*                       "transmissionreference"  => "",
*                       "urgency"                => "" );
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data. This contains the
*                                  header information which is to be updated.
*               new_ps_file_info_array - An array as above, which contains the
*                                        'File Info' metadata information to be
*                                        written.
*               Old_Exif_array - an array containing the EXIF information to be
*                                updated, as retrieved by get_EXIF_JPEG. (saves having to parse the EXIF again)
*               Old_XMP_array - an array containing the XMP information to be
*                               updated, as retrieved by read_XMP_array_from_text. (saves having to parse the XMP again)
*               Old_IRB_array - an array containing the Photoshop IRB information
*                                to be updated, as retrieved by get_Photoshop_IRB. (saves having to parse the IRB again)
*
* Returns:      jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data, containing the
*                                  Photshop 'File Info' metadata. This can then
*                                  be written to a file using put_jpeg_header_data.
*
******************************************************************************/

function put_photoshop_file_info( $jpeg_header_data, $new_ps_file_info_array, $Old_Exif_array, $Old_XMP_array, $Old_IRB_array )
{
        /*******************************************/
        // PREPROCESSING

        // Check that the date is in the correct format (YYYY-MM-DD)

        // Explode the date into pieces using the - symbol
        $date_pieces = explode( "-", $new_ps_file_info_array[ 'date' ] );

        // If there are not 3 pieces to the date, it is invalid
        if ( count( $date_pieces ) != 3 )
        {
                // INVALID DATE
                echo "Invalid Date - must be YYYY-MM-DD format<br>";
                return FALSE;
        }

        // Cycle through each piece of the date
        foreach( $date_pieces as $piece )
        {
                // If the piece is not numeric, then the date is invalid.
                if ( ! is_numeric( $piece ) )
                {
                        // INVALID DATE
                        echo "Invalid Date - must be YYYY-MM-DD format<br>";
                        return FALSE;
                }
        }

        // Make a unix timestamp at midnight on the date specified
        $date_stamp = mktime( 0,0,0, $date_pieces[1], $date_pieces[2], $date_pieces[0] );




        // Create a translation table to remove carriage return characters
        $trans = array( "\x0d" => "" );

        // Cycle through each of the File Info elements
        foreach( $new_ps_file_info_array as $valkey => $val )
        {
                // If the element is 'Keywords' or 'Supplemental Categories', then
                // it is an array, and needs to be treated as one
                if ( ( $valkey != 'supplementalcategories' ) && ( $valkey != 'keywords' ) )
                {
                        // Not Keywords or Supplemental Categories
                        // Convert escaped HTML characters to UTF8 and remove carriage returns
                        $new_ps_file_info_array[ $valkey ] = strtr( HTML_UTF8_UnEscape( $val ), $trans );
                }
                else
                {
                        // Either Keywords or Supplemental Categories
                        // Cycle through the array,
                        foreach( $val as $subvalkey => $subval )
                        {
                                // Convert escaped HTML characters to UTF8 and remove carriage returns
                                $new_ps_file_info_array[ $valkey ][ $subvalkey ] = strtr( HTML_UTF8_UnEscape( $subval ), $trans );
                        }
                }
        }





        /*******************************************/

        // EXIF Processing


        // Check if the EXIF array exists
        if( $Old_Exif_array == FALSE )
        {
                // EXIF Array doesn't exist - create a new one
                $new_Exif_array = array (       'Byte_Align' => "MM",
                                                'Makernote_Tag' => false,
                                                'Tags Name' => "TIFF",
                                                 0 => array( "Tags Name" => "TIFF" ) );
        }
        else
        {
                // EXIF Array Does Exist - use it
                $new_Exif_array = $Old_Exif_array;
        }



        // Update the EXIF Image Description Tag with the new value
        $new_Exif_array[0][270] = array (       "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 270 ]['Name'],
                                                "Tag Number" => 270,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 270 ]['Type'],
                                                //"Data"       => array( HTML_UTF8_Escape( $new_ps_file_info_array[ 'caption' ]) ));
                                                "Data"       => array( ( $new_ps_file_info_array[ 'caption' ]) ));

        // Update the EXIF Artist Name Tag with the new value
        $new_Exif_array[0][315] = array (       "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 315 ]['Name'],
                                                "Tag Number" => 315,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 315 ]['Type'],
                                                //"Data"       => array( HTML_UTF8_Escape( $new_ps_file_info_array[ 'author' ] ) ) );
                                                "Data"       => array( ( $new_ps_file_info_array[ 'author' ] ) ) );

        // Update the EXIF Copyright Information Tag with the new value
        $new_Exif_array[0][33432] = array (     "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 33432 ]['Name'],
                                                "Tag Number" => 33432,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 33432 ]['Type'],
                                                "Data"       => array( HTML_UTF8_Escape( $new_ps_file_info_array[ 'copyrightnotice' ]) ) );


        // Photoshop checks if the "Date and Time of Original" and "Date and Time when Digitized" tags exist
        // If they don't exist, it means that the EXIF date may be wiped out if it is changed, so Photoshop
        // copies the EXIF date to these two tags

        if ( ( array_key_exists( 306, $new_Exif_array[0] ) )&&
             ( array_key_exists( 34665, $new_Exif_array[0] ) ) &&
             ( array_key_exists( 0, $new_Exif_array[0][34665] ) ) )
        {
                // Replace "Date and Time of Original" if it doesn't exist
                if ( ! array_key_exists( 36867, $new_Exif_array[0][34665][0] ) )
                {
                        $new_Exif_array[0][34665][0][36867] = array (       "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['EXIF'][ 36867 ]['Name'],
                                                "Tag Number" => 36867,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['EXIF'][ 36867 ]['Type'],
                                                "Data"       => $new_Exif_array[0][306]['Data'] );
                }

                // Replace "Date and Time when Digitized" if it doesn't exist
                if ( ! array_key_exists( 36868, $new_Exif_array[0][34665][0] ) )
                {
                        $new_Exif_array[0][34665][0][36868] = array (       "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['EXIF'][ 36868 ]['Name'],
                                                "Tag Number" => 36868,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['EXIF'][ 36868 ]['Type'],
                                                "Data"       => $new_Exif_array[0][306]['Data'] );
                }
        }


        // Photoshop changes the EXIF date Tag (306) to the current date, not the date that was entered in File Info
        $exif_date = date ( "Y:m:d H:i:s" );

        // Update the EXIF Date and Time Tag with the new value
        $new_Exif_array[0][306] = array (       "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 306 ]['Name'],
                                                "Tag Number" => 306,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 306 ]['Type'],
                                                "Data"       => array( $exif_date ) );



        // Photoshop replaces the EXIF Software or Firmware Tag with "Adobe Photoshop ..."
        // This toolkit instead preserves existing value and appends the toolkit name to the end of it

        // Check if the EXIF Software or Firmware Tag exists
        if ( array_key_exists( 305, $new_Exif_array[0] ) )
        {
                // An existing EXIF Software or Firmware Tag was found
                // Check if the existing Software or Firmware Tag already contains the Toolkit's name
                if ( stristr ( $new_Exif_array[0][305]['Data'][0], $GLOBALS[ "Software Name" ]) == FALSE )
                {
                        // Toolkit Name string not found in the existing Software/Firmware string - append it.
                        $firmware_str = $new_Exif_array[0][305]['Data'][0] . " " . $GLOBALS[ "Software Name" ];
                }
                else
                {
                        // Toolkit name already exists in Software/Firmware string - don't put another copy in the string
                        $firmware_str = $new_Exif_array[0][305]['Data'][0];
                }
        }
        else
        {
                // No Software/Firmware string exists - create one
                $firmware_str = $GLOBALS[ "Software Name" ];
        }

        // Update the EXIF Software/Firmware Tag with the new value
        $new_Exif_array[0][305] = array(        "Tag Name"   => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 305 ]['Name'],
                                                "Tag Number" => 305,
                                                "Data Type"  => 2,
                                                "Type"       => $GLOBALS[ "IFD_Tag_Definitions" ]['TIFF'][ 305 ]['Type'],
                                                "Data"       => array( HTML_UTF8_Escape( $firmware_str ) ) );





        /*******************************************/

        // Photoshop IRB Processing


        // Check if there is an existing Photoshop IRB array
        if ($Old_IRB_array == FALSE )
        {
                // No existing IRB array - create one
                $new_IRB_array = array();
        }
        else
        {
                // There is an existing Photoshop IRB array - use it
                $new_IRB_array = $Old_IRB_array;
        }

        // Remove any existing Copyright Flag, URL, or IPTC resources - these will be re-written
        foreach( $new_IRB_array as  $resno => $res )
        {
                if ( ( $res[ 'ResID' ] == 0x040A ) ||
                     ( $res[ 'ResID' ] == 0x040B ) ||
                     ( $res[ 'ResID' ] == 0x0404 ) )
                {
                        array_splice( $new_IRB_array, $resno, 1 );
                }
        }


        // Add a new Copyright Flag resource
        if ( $new_ps_file_info_array[ 'copyrightstatus' ] == "Copyrighted Work" )
        {
                $PS_copyright_flag = "\x01"; // Copyrighted
        }
        else
        {
                $PS_copyright_flag = "\x00"; // Public domain or Unmarked
        }
        $new_IRB_array[] = array(       'ResID' => 0x040A,
                                        'ResName' => $GLOBALS[ "Photoshop_ID_Names" ][0x040A],
                                        'ResDesc' => $GLOBALS[ "Photoshop_ID_Descriptions" ][0x040A],
                                        'ResEmbeddedName' => "",
                                        'ResData' => $PS_copyright_flag );



        // Add a new URL resource
        $new_IRB_array[] = array(       'ResID' => 0x040B,
                                        'ResName' => $GLOBALS[ "Photoshop_ID_Names" ][0x040B],
                                        'ResDesc' => $GLOBALS[ "Photoshop_ID_Descriptions" ][0x040B],
                                        'ResEmbeddedName' => "",
                                        'ResData' => $new_ps_file_info_array[ 'ownerurl' ] );



        // Create IPTC resource

        // IPTC requires date to be in the following format YYYYMMDD
        $iptc_date = date( "Ymd", $date_stamp );

        // Create the new IPTC array
        $new_IPTC_array = array (
                                  0 =>
                                  array (
                                    'IPTC_Type' => '2:00',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:00'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:00'],
                                    'RecData' => "\x00\x02",
                                  ),
                                  // 1 =>
                                  // array (
                                  //   'IPTC_Type' => '2:120',
                                  //   'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:120'],
                                  //   'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:120'],
                                  //   'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'caption' ] ), 0 , 2000 ),
                                  // ),
                                  2 =>
                                  array (
                                    'IPTC_Type' => '2:122',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:122'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:122'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'captionwriter' ] ), 0 , 32 ),
                                  ),
                                  3 =>
                                  array (
                                    'IPTC_Type' => '2:105',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:105'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:105'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'headline' ] ), 0 , 256 ),
                                  ),
                                  4 =>
                                  array (
                                    'IPTC_Type' => '2:40',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:40'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:40'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'instructions' ] ), 0, 256 ),
                                  ),
                                  // 5 =>
                                  // array (
                                  //   'IPTC_Type' => '2:80',
                                  //   'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:80'],
                                  //   'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:80'],
                                  //   'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'author' ] ), 0, 32 ),
                                  // ),
                                  6 =>
                                  array (
                                    'IPTC_Type' => '2:85',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:85'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:85'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'authorsposition' ] ), 0, 32 ),
                                  ),
                                  7 =>
                                  array (
                                    'IPTC_Type' => '2:110',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:110'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:110'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'credit' ] ), 0, 32 ),
                                  ),
                                  8 =>
                                  array (
                                    'IPTC_Type' => '2:115',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:115'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:115'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'source' ] ), 0, 32 ),
                                  ),
                                  9 =>
                                  array (
                                    'IPTC_Type' => '2:05',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:05'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:05'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'title' ] ), 0, 64 ),
                                  ),
                                  10 =>
                                  array (
                                    'IPTC_Type' => '2:55',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:55'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:55'],
                                    'RecData' => "$iptc_date",
                                  ),
                                  11 =>
                                  array (
                                    'IPTC_Type' => '2:90',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:90'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:90'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'city' ] ), 0, 32 ),
                                  ),
                                  12 =>
                                  array (
                                    'IPTC_Type' => '2:95',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:95'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:95'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'state' ] ), 0, 32 ),
                                  ),
                                  13 =>
                                  array (
                                    'IPTC_Type' => '2:101',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:101'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:101'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'country' ] ), 0, 64 ),
                                  ),
                                  14 =>
                                  array (
                                    'IPTC_Type' => '2:103',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:103'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:103'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'transmissionreference' ] ), 0, 32 ),
                                  ),
                                  15 =>
                                  array (
                                    'IPTC_Type' => '2:15',
                                    'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:15'],
                                    'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:15'],
                                    'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'category' ] ), 0, 3 ),
                                  ),
                                  // 21 =>
                                  // array (
                                  //   'IPTC_Type' => '2:116',
                                  //   'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:10'],
                                  //   'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:10'],
                                  //   'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'copyrightnotice' ] ), 0, 128 ),
                                  // ),
                                );

        // Check the value of urgency is valid
        if ( ( $new_ps_file_info_array[ 'urgency' ] > 0 ) && ( $new_ps_file_info_array[ 'urgency' ] < 9 ) )
        {
                // Add the Urgency item to the IPTC array
                $new_IPTC_array[] = array (
                                                'IPTC_Type' => '2:10',
                                                'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:10'],
                                                'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:10'],
                                                'RecData' => substr( HTML_UTF8_Escape( $new_ps_file_info_array[ 'urgency' ] ), 0, 1 ),
                                          );
        }

        // Cycle through the Supplemental Categories,
        foreach( $new_ps_file_info_array[ 'supplementalcategories' ] as $supcat )
        {
                // Add this Supplemental Category to the IPTC array
                $new_IPTC_array[] = array (
                                            'IPTC_Type' => '2:20',
                                            'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:20'],
                                            'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:20'],
                                            'RecData' => HTML_UTF8_Escape( $supcat ),
                                          );
        }


        // Cycle through the Keywords,
        foreach( $new_ps_file_info_array[ 'keywords' ] as $keyword )
        {
                // Add this Keyword to the IPTC array
                $new_IPTC_array[] = array (
                                            'IPTC_Type' => '2:25',
                                            'RecName' => $GLOBALS[ "IPTC_Entry_Names" ]['2:25'],
                                            'RecDesc' => $GLOBALS[ "IPTC_Entry_Descriptions" ]['2:25'],
                                            'RecData' => $keyword,
                                          );
        }


        /***********************************/

        // XMP Processing

        // Check if XMP existed previously
        if ($Old_XMP_array == FALSE )
        {
                // XMP didn't exist - create a new one based on a blank structure
                $new_XMP_array = XMP_Check( $GLOBALS[ 'Blank XMP Structure' ], array( ) );
        }
        else
        {
                // XMP does exist
                // Some old XMP processors used x:xapmeta, check for this
                if ( $Old_XMP_array[0]['tag'] == 'x:xapmeta' )
                {
                        // x:xapmeta found - change it to x:xmpmeta
                        $Old_XMP_array[0]['tag'] = 'x:xmpmeta';
                }

                // Ensure that the existing XMP has all required fields, and add any that are missing
                $new_XMP_array = XMP_Check( $GLOBALS[ 'Blank XMP Structure' ], $Old_XMP_array );
        }


        // Process the XMP Photoshop block

        // Find the Photoshop Information within the XMP block
        $photoshop_block = find_XMP_block( $new_XMP_array, "photoshop" );

        // The Photoshop CaptionWriter tag contains captionwriter - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:CaptionWriter" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'captionwriter' ];

        // The Photoshop Category tag contains category - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Category" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'category' ];

        // The Photoshop DateCreated tag contains date - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:DateCreated" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'date' ];

        // The Photoshop City tag contains city - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:City" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'city' ];

        // The Photoshop State tag contains state - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:State" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'state' ];

        // The Photoshop Country tag contains country - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Country" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'country' ];

        // The Photoshop AuthorsPosition tag contains authorsposition - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:AuthorsPosition" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'authorsposition' ];

        // The Photoshop Credit tag contains credit - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Credit" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'credit' ];

        // The Photoshop Source tag contains source - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Source" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'source' ];

        // The Photoshop Headline tag contains headline - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Headline" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'headline' ];

        // The Photoshop Instructions tag contains instructions - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Instructions" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'instructions' ];

        // The Photoshop TransmissionReference tag contains transmissionreference - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:TransmissionReference" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'transmissionreference' ];

        // The Photoshop Urgency tag contains urgency - Find it and Update the value
        $Item = find_XMP_item( $photoshop_block, "photoshop:Urgency" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'urgency' ];

        // The Photoshop SupplementalCategories tag contains supplementalcategories - Find it
        $Item = find_XMP_item( $photoshop_block, "photoshop:SupplementalCategories" );

        // Create an array to receive the XML list items for the Supplemental Categories
        $new_supcat_array = array( );

        // Cycle through the Supplemental Categories
        foreach ( $new_ps_file_info_array[ 'supplementalcategories' ] as $sup_category )
        {
                // Add a new list item for this Supplemental Category
                $new_supcat_array[] = array( 'tag' => 'rdf:li', 'value' => $sup_category );
        }

        // Add the array of Supplemental Category List Items to the Photoshop SupplementalCategories tag
        $Item[ 'children' ][ 0 ][ 'children' ] = $new_supcat_array;



        // Process the XMP XAP block

        // Find the XAP Information within the XMP block
        $XAP_block = find_XMP_block( $new_XMP_array, "xap" );

        // The XAP CreateDate tag contains date XMP was first created - Find it and Update the value
        $Item = find_XMP_item( $XAP_block, "xap:CreateDate" );

        // Check if the CreateDate is blank
        if ( $Item[ 'value' ] == "" )
        {
                // CreateDate is blank - we must have just added it - set it to the current date
                $Item[ 'value' ] = date( "Y-m-d\TH:i:s" );
                $Item[ 'value' ] .= get_Local_Timezone_Offset( );
        }


        // The XAP ModifyDate tag contains last resource change date  - Find it and Update the value to the current date
        $Item = find_XMP_item( $XAP_block, "xap:ModifyDate" );
        $Item[ 'value' ] = date( "Y-m-d\TH:i:s" );
        $Item[ 'value' ] .= get_Local_Timezone_Offset( );

        // The XAP ModifyDate tag contains last XMP change date  - Find it and Update the value to the current date
        $Item = find_XMP_item( $XAP_block, "xap:MetadataDate" );
        $Item[ 'value' ] = date( "Y-m-d\TH:i:s" );
        $Item[ 'value' ] .= get_Local_Timezone_Offset( );



        // The XAP CreatorTool tag contains name of the software editor  - Find it
        $Item = find_XMP_item( $XAP_block, "xap:CreatorTool" );

        // Photoshop replaces the CreatorTool with "Adobe Photoshop ..."
        // This toolkit instead preserves existing value and appends the toolkit name to the end of it

        // Check if a CreatorTool already exists
        if ( $Item[ 'value' ] != "" )
        {
                // An existing CreatorTool was found
                // Check if the existing CreatorTool already contains the Toolkit's name
                if ( stristr ( $Item[ 'value' ], $GLOBALS[ "Software Name" ]) == FALSE )
                {
                        // Toolkit Name string not found in the existing CreatorTool string - append it.
                        $Item[ 'value' ] = $Item[ 'value' ] . " " . $GLOBALS[ "Software Name" ];
                }
                else
                {
                        // Toolkit name already exists in CreatorTool string - leave as is
                }
        }
        else
        {
                // No CreatorTool string exists - create one
                $Item[ 'value' ] = $GLOBALS[ "Software Name" ];
        }
        $Item = find_XMP_item( $XAP_block, "xap:Rating" );
        $Item[ 'value' ] ="5";


        // Process the XMP Basic Job Information block

        // Find the XAP Basic Job Information within the XMP block
        $XAPBJ_block = find_XMP_block( $new_XMP_array, "xapBJ" );

        // The XAP Basic Job JobRef tag contains urgency - Find it and Update the value
        $Item = find_XMP_item( $XAPBJ_block, "xapBJ:JobRef" );
        $Item[ 'children' ][ 0 ][ 'children' ] =
                array( array (  'tag'        => 'rdf:li',
                                'attributes' => array ( 'rdf:parseType' => 'Resource' ),
                                'children'   => array ( 0 => array (    'tag'   => 'stJob:name',
                                                                        'value' => $new_ps_file_info_array[ 'jobname' ] ),
                                                      ),
                             ),
                     );




        // Process the XMP XAP Rights Information block

        // Find the XAP Rights Information within the XMP block
        $XAPRights_block = find_XMP_block( $new_XMP_array, "xapRights" );



        // The XAP Rights Marked tag should only be present if copyrightstatus is 'Copyrighted Work' or 'Public Domain'
        // If copyrightstatus 'Unmarked' or anything else, the XAP Rights Marked tag should be missing


        // Remove any existing XAP Rights Marked tags - they will be replaced
        foreach( $XAPRights_block as  $tagno => $tag )
        {
                if ( $tag[ 'tag' ] == "xapRights:Marked" )
                {
                        array_splice( $XAPRights_block, $tagno, 1 );
                }
        }

        // Check the value of the copyrightstatus flag
        if ( $new_ps_file_info_array[ 'copyrightstatus' ] == "Copyrighted Work" )
        {
                // Copyrighted - add the tag
                $XAPRights_block[] = array ( 'tag' => 'xapRights:Marked', 'value' => 'True' );
        }
        else if ( $new_ps_file_info_array[ 'copyrightstatus' ] == "Public Domain" )
        {
                // Public domain - add the tag
                $XAPRights_block[] = array ( 'tag' => 'xapRights:Marked', 'value' => 'False' );
        }
        else
        {
                // Unmarked or Other - Do nothing - don't add a Marked tag
        }


        // The XAP Rights WebStatement tag contains ownerurl - Find it and Update the value
        $Item = find_XMP_item( $XAPRights_block, "xapRights:WebStatement" );
        $Item[ 'value' ] = $new_ps_file_info_array[ 'ownerurl' ];




        // Process the XMP Dublin Core block

        // Find the Dublin Core Information within the XMP block
        $DC_block = find_XMP_block( $new_XMP_array, "dc" );


        // The Dublin Core description tag contains caption - Find it and Update the value
        $Item = find_XMP_item( $DC_block, "dc:description" );
        $Item[ 'children' ][ 0 ][ 'children' ] = array( array(  'tag'   => "rdf:li",
                                                                'value' => $new_ps_file_info_array[ 'caption' ],
                                                                'attributes' => array( 'xml:lang' => "x-default" ) ) );


        // The Dublin Core title tag contains title - Find it and Update the value
        $Item = find_XMP_item( $DC_block, "dc:title" );
        $Item[ 'children' ][ 0 ][ 'children' ] = array( array(  'tag'   => "rdf:li",
                                                                'value' => $new_ps_file_info_array[ 'title' ],
                                                                'attributes' => array( 'xml:lang' => "x-default" ) ) );


        // The Dublin Core rights tag contains copyrightnotice - Find it and Update the value
        $Item = find_XMP_item( $DC_block, "dc:rights" );
        $Item[ 'children' ][ 0 ][ 'children' ] = array( array(  'tag'   => "rdf:li",
                                                                'value' =>  $new_ps_file_info_array[ 'copyrightnotice' ],
                                                                'attributes' => array( 'xml:lang' => "x-default" ) ) );

        // The Dublin Core creator tag contains author - Find it and Update the value
        $Item = find_XMP_item( $DC_block, "dc:creator" );
        $Item[ 'children' ][ 0 ][ 'children' ] = array( array(  'tag'   => "rdf:li",
                                                                'value' => $new_ps_file_info_array[ 'author' ]) );

        // The Dublin Core subject tag contains keywords - Find it
        $Item = find_XMP_item( $DC_block, "dc:subject" );

        // Create an array to receive the Keywords List Items
        $new_keywords_array = array( );

        // Cycle through each keyword
        foreach( $new_ps_file_info_array[ 'keywords' ] as $keyword )
        {
                // Add a List item for this keyword
                $new_keywords_array[] = array(  'tag'   => "rdf:li", 'value' => $keyword );
        }
        // Add the Keywords List Items array to the Dublin Core subject tag
        $Item[ 'children' ][ 0 ][ 'children' ] = $new_keywords_array;



        /***************************************/

        // FINISHED UPDATING VALUES

        // Insert the new IPTC array into the Photoshop IRB array
        $new_IRB_array = put_Photoshop_IPTC( $new_IRB_array, $new_IPTC_array );

        // Write the EXIF array to the JPEG header
        $jpeg_header_data = put_EXIF_JPEG( $new_Exif_array, $jpeg_header_data );

        // Convert the XMP array to XMP text
        $xmp_text = write_XMP_array_to_text( $new_XMP_array );

        // Write the XMP text to the JPEG Header
        $jpeg_header_data = put_XMP_text( $jpeg_header_data, $xmp_text );

        // Write the Photoshop IRB array to the JPEG header
        $jpeg_header_data = put_Photoshop_IRB( $jpeg_header_data, $new_IRB_array );

        return $jpeg_header_data;

}

/******************************************************************************
* End of Function:     put_photoshop_file_info
******************************************************************************/



































/******************************************************************************
*
*         INTERNAL FUNCTIONS
*
******************************************************************************/


































/******************************************************************************
*
* Function:     get_Local_Timezone_Offset
*
* Description:  Returns a string indicating the time difference between the local
*               timezone and GMT in hours and minutes, e.g.  +10:00 or -06:30
*
* Parameters:   None
*
* Returns:      $tz_str - a string containing the timezone offset
*
******************************************************************************/

function get_Local_Timezone_Offset( )
{
        // Retrieve the Timezone offset in seconds
        $tz_seconds = date( "Z" );

        // Check if the offset is less than zero
        if ( $tz_seconds < 0 )
        {
                // Offset is less than zero - add a Minus sign to the output
                $tz_str = "-";
        }
        else
        {
                // Offset is greater than or equal to zero - add a Plus sign to the output
                $tz_str = "+";
        }

        // Add the absolute offset to the output, formatted as HH:MM
        $tz_str .= gmdate( "H:i", abs($tz_seconds) );

        // Return the result
        return $tz_str;
}

/******************************************************************************
* End of Function:     get_Local_Timezone_Offset
******************************************************************************/



/******************************************************************************
*
* Function:     XMP_Check
*
* Description:  Checks a given XMP array against a reference array, and adds any
*               missing blocks and tags
*
*               NOTE: This is a recursive function
*
* Parameters:   reference_array - The standard XMP array which contains all required tags
*               check_array - The XMP array to check
*
* Returns:      output - a string containing the timezone offset
*
******************************************************************************/

function XMP_Check( $reference_array, $check_array)
{
        // Cycle through each of the elements of the reference XMP array
        foreach( $reference_array as $valkey => $val )
        {

                // Search for the current reference tag within the XMP array to be checked
                $tagpos = find_XMP_Tag( $check_array,  $val );

                // Check if the tag was found
                if ( $tagpos === FALSE )
                {
                        // Tag not found - Add tag to array being checked
                        $tagpos = count( $check_array );
                        $check_array[ $tagpos ] = $val;
                }

                // Check if the reference tag has children
                if ( array_key_exists( 'children', $val ) )
                {
                        // Reference tag has children - these need to be checked too

                        // Determine if the array being checked has children for this tag
                        if ( ! array_key_exists( 'children', $check_array[ $tagpos ] ) )
                        {
                                // Array being checked has no children - add a blank children array
                                $check_array[ $tagpos ][ 'children' ] = array( );
                        }

                        // Recurse, checking the children tags against the reference children
                        $check_array[ $tagpos ][ 'children' ] = XMP_Check( $val[ 'children' ] , $check_array[ $tagpos ][ 'children' ] );
                }
                else
                {
                        // No children - don't need to check anything else
                }
        }

        // Return the checked XMP array
        return $check_array;
}


/******************************************************************************
* End of Function:     XMP_Check
******************************************************************************/




/******************************************************************************
*
* Function:     find_XMP_Tag
*
* Description:  Searches one level of an XMP array for a specific tag, and
*               returns the tag position. Does not descend the XMP tree.
*
* Parameters:   XMP_array - The XMP array which should be searched
*               tag - The XMP tag to search for (in same format as would be found in XMP array)
*
* Returns:      output - a string containing the timezone offset
*
******************************************************************************/

function find_XMP_Tag( $XMP_array, $tag )
{
        $namespacestr = "";

        // Some tags have a namespace attribute which defines them (i.e. rdf:Description tags)

        // Check if the tag being searched for has attributs
        if ( array_key_exists( 'attributes', $tag ) )
        {
                // Tag has attributes - cycle through them
                foreach( $tag['attributes'] as $key => $val )
                {
                        // Check if the current attribute is the namespace attribute - i.e. starts with xmlns:
                        if ( strcasecmp( substr($key,0,6), "xmlns:" ) == 0 )
                        {
                                // Found a namespace attribute - save it for later.
                                $namespacestr = $key;
                        }
                }
        }



        // Cycle through the elements of the XMP array to be searched.
        foreach( $XMP_array as $valkey => $val )
        {

                // Check if the current element is a rdf:Description tag
                if ( strcasecmp ( $tag[ 'tag' ], 'rdf:Description' ) == 0 )
                {
                        // Current element is a rdf:Description tag
                        // Check if the namespace attribute is the same as in the tag that is being searched for
                        if ( array_key_exists( $namespacestr, $val['attributes'] ) )
                        {
                                // Namespace is the same - this is the correct tag - return it's position
                                return $valkey;
                        }
                }
                // Otherwise check if the current element has the same name as the tag in question
                else if ( strcasecmp ( $val[ 'tag' ], $tag[ 'tag' ] ) == 0 )
                {
                        // Tags have same name - this is the correct tag - return it's position
                        return $valkey;
                }
        }

        // Cycled through all tags without finding the correct one - return error value
        return FALSE;
}

/******************************************************************************
* End of Function:     find_XMP_Tag
******************************************************************************/




/******************************************************************************
*
* Function:     create_GUID
*
* Description:  Creates a Globally Unique IDentifier, in the format that is used
*               by XMP (and Windows). This value is not guaranteed to be 100% unique,
*               but it is ridiculously unlikely that two identical values will be produced
*
* Parameters:   none
*
* Returns:      output - a string containing the timezone offset
*
******************************************************************************/

function create_GUID( )
{
        // Create a md5 sum of a random number - this is a 32 character hex string
        $raw_GUID = md5( uniqid( getmypid() . rand( ) . (double)microtime()*1000000, TRUE ) );

        // Format the string into 8-4-4-4-12 (numbers are the number of characters in each block)
        return  substr($raw_GUID,0,8) . "-" . substr($raw_GUID,8,4) . "-" . substr($raw_GUID,12,4) . "-" . substr($raw_GUID,16,4) . "-" . substr($raw_GUID,20,12);
}

/******************************************************************************
* End of Function:     create_GUID
******************************************************************************/





/******************************************************************************
*
* Function:     add_to_field
*
* Description:  Adds a value to a particular field in a Photoshop File Info array,
*               first checking whether the value is already there. If the value is
*               already in the array, it is not changed, otherwise the value is appended
*               to whatever is already in that field of the array
*
* Parameters:   field_array - The Photoshop File Info array to receive the new value
*               field - The File Info field which the value is for
*               value - The value to be written into the File Info
*               separator - The string to place between values when having to append the value
*
* Returns:      output - the Photoshop File Info array with the value added
*
******************************************************************************/

function add_to_field( $field_array, $field, $value, $separator )
{
        // Check if the value is blank
        if ( $value == "" )
        {
                // Value is blank - return File Info array unchanged
                return $field_array;
        }

        // Check if the value can be found anywhere within the existing value for this field
        if ( stristr ( $field_array[ $field ], $value ) == FALSE)
        {
                // Value could not be found
                // Check if the existing value for the field is blank
                if ( $field_array[$field] != "" )
                {
                        // Existing value for field is not blank - append a separator
                        $field_array[$field] .= $separator;
                }
                // Append the value to the field
                $field_array[$field] .= $value;
        }

        // Return the File Info Array
        return $field_array;
}

/******************************************************************************
* End of Function:     add_to_field
******************************************************************************/



/******************************************************************************
*
* Function:     find_IPTC_Resource
*
* Description:  Searches an IPTC array for a particular record, and returns it if found
*
* Parameters:   IPTC_array - The IPTC array to search
*               record_type - The IPTC record number to search for (e.g.  2:151 )
*
* Returns:      output - the contents of the record if found
*               FALSE - otherwise
*
******************************************************************************/

function find_IPTC_Resource( $IPTC_array, $record_type )
{
        // Cycle through the ITPC records
        foreach ($IPTC_array as $record)
        {
                // Check the IPTC type against the required type
                if ( $record['IPTC_Type'] == $record_type )
                {
                        // IPTC type matches - return this record
                        return $record;
                }
        }

        // No matching record found - return error code
        return FALSE;
}

/******************************************************************************
* End of Function:     find_IPTC_Resource
******************************************************************************/




/******************************************************************************
*
* Function:     find_Photoshop_IRB_Resource
*
* Description:  Searches a Photoshop IRB array for a particular resource, and returns it if found
*
* Parameters:   IRB_array - The IRB array to search
*               resource_ID - The IRB resource number to search for (e.g.  0x03F9 )
*
* Returns:      output - the contents of the resource if found
*               FALSE - otherwise
*
******************************************************************************/

function find_Photoshop_IRB_Resource( $IRB_array, $resource_ID )
{
        // Cycle through the IRB resources
        foreach( $IRB_array as $IRB_Resource )
        {
                // Check the IRB resource ID against the required ID
                if ( $resource_ID == $IRB_Resource['ResID'] )
                {
                        // Resource ID matches - return this resource
                        return $IRB_Resource;
                }
        }

        // No matching resource found - return error code
        return FALSE;
}

/******************************************************************************
* End of Function:     find_Photoshop_IRB_Resource
******************************************************************************/








/******************************************************************************
*
* Function:     find_XMP_item
*
* Description:  Searches a one level of a XMP array for a particular item by name, and returns it if found.
*               Does not descend through the XMP array
*
* Parameters:   Item_Array - The XMP array to search
*               item_name - The name of the tag to serch for (e.g.  photoshop:CaptionWriter )
*
* Returns:      output - the contents of the tag if found
*               FALSE - otherwise
*
******************************************************************************/

function  find_XMP_item( & $Item_Array, $item_name )
{
        // Cycle through the top level of the XMP array
        foreach( $Item_Array as $Item_Key => $Item )
        {
                // Check this tag name against the required tag name
                if( $Item['tag'] == $item_name )
                {
                        // The tag names match - return the item
                        return $Item_Array[ $Item_Key ];
                }
        }

        // No matching tag found - return error code
        return FALSE;
}

/******************************************************************************
* End of Function:     find_XMP_item
******************************************************************************/





/******************************************************************************
*
* Function:     find_XMP_block
*
* Description:  Searches a for a particular rdf:Description block within a XMP array, and returns its children if found.
*
* Parameters:   XMP_array - The XMP array to search as returned by read_XMP_array_from_text
*               block_name - The namespace of the XMP block to be found (e.g.  photoshop or xapRights )
*
* Returns:      output - the children of the tag if found
*               FALSE - otherwise
*
******************************************************************************/

function find_XMP_block( & $XMP_array, $block_name )
{
        // Check that the rdf:RDF section can be found (which contains the rdf:Description tags
        if ( ( $XMP_array !== FALSE ) &&
             ( ( $XMP_array[0]['tag'] ==  "x:xapmeta" ) ||
               ( $XMP_array[0]['tag'] ==  "x:xmpmeta" ) ) &&
             ( $XMP_array[0]['children'][0]['tag'] ==  "rdf:RDF" ) )
        {
                // Found rdf:RDF
                // Make it's children easily accessible
                $RDF_Contents = $XMP_array[0]['children'][0]['children'];

                // Cycle through the children (rdf:Description tags)
                foreach ($RDF_Contents as $RDF_Key => $RDF_Item)
                {
                        // Check if this is a rdf:description tag that has children
                        if ( ( $RDF_Item['tag'] == "rdf:Description" ) &&
                             ( array_key_exists( 'children', $RDF_Item ) ) )
                        {
                                // RDF Description tag has children,
                                // Cycle through it's attributes
                                foreach( $RDF_Item['attributes'] as $key => $val )
                                {
                                        // Check if this attribute matches the namespace block name required
                                        if ( $key == "xmlns:$block_name" )
                                        {
                                                // Namespace matches required block name - return it's children
                                                return  $XMP_array[0]['children'][0]['children'][ $RDF_Key ]['children'];
                                        }
                                }
                        }
                }
        }

        // No matching rdf:Description block found
        return FALSE;
}

/******************************************************************************
* End of Function:     find_XMP_block
******************************************************************************/









/******************************************************************************
* Global Variable:      Blank XMP Structure
*
* Contents:     A template XMP array which can be used to create a new XMP segment
*
******************************************************************************/

// Create a GUID to be used in this template array
$new_GUID = create_GUID( );

$GLOBALS[ 'Blank XMP Structure' ] =
array (
  0 =>
  array (
    'tag' => 'x:xmpmeta',
    'attributes' =>
    array (
      'xmlns:x' => 'adobe:ns:meta/',
      'x:xmptk' => 'XMP toolkit 3.0-28, framework 1.6',
    ),
    'children' =>
    array (
      0 =>
      array (
        'tag' => 'rdf:RDF',
        'attributes' =>
        array (
          'xmlns:rdf' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
          'xmlns:iX' => 'http://ns.adobe.com/iX/1.0/',
        ),
        'children' =>
        array (
          1 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:pdf' => 'http://ns.adobe.com/pdf/1.3/',
            ),
          ),
          2 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:photoshop' => 'http://ns.adobe.com/photoshop/1.0/',
            ),
            'children' =>
            array (
              0 =>
              array (
                'tag' => 'photoshop:CaptionWriter',
                'value' => '',
              ),
              1 =>
              array (
                'tag' => 'photoshop:Category',
                'value' => '',
              ),
              2 =>
              array (
                'tag' => 'photoshop:DateCreated',
                'value' => '',
              ),
              3 =>
              array (
                'tag' => 'photoshop:City',
                'value' => '',
              ),
              4 =>
              array (
                'tag' => 'photoshop:State',
                'value' => '',
              ),
              5 =>
              array (
                'tag' => 'photoshop:Country',
                'value' => '',
              ),
              6 =>
              array (
                'tag' => 'photoshop:Credit',
                'value' => '',
              ),
              7 =>
              array (
                'tag' => 'photoshop:Source',
                'value' => '',
              ),
              8 =>
              array (
                'tag' => 'photoshop:Headline',
                'value' => '',
              ),
              9 =>
              array (
                'tag' => 'photoshop:Instructions',
                'value' => '',
              ),
              10 =>
              array (
                'tag' => 'photoshop:TransmissionReference',
                'value' => '',
              ),
              11 =>
              array (
                'tag' => 'photoshop:Urgency',
                'value' => '',
              ),
              12 =>
              array (
                'tag' => 'photoshop:SupplementalCategories',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Bag',
                  ),
                ),
              ),
              13 =>
              array (
                'tag' => 'photoshop:AuthorsPosition',
                'value' => '',
              ),
            ),
          ),
          4 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:xap' => 'http://ns.adobe.com/xap/1.0/',
            ),
            'children' =>
            array (
              0 =>
              array (
                'tag' => 'xap:CreateDate',
                'value' => '',
              ),
              1 =>
              array (
                'tag' => 'xap:ModifyDate',
                'value' => '',
              ),
              2 =>
              array (
                'tag' => 'xap:MetadataDate',
                'value' => '',
              ),
              3 =>
              array (
                'tag' => 'xap:CreatorTool',
                'value' => '',
              ),
            4 =>
              array (
                'tag' => 'xap:Rating',
                'value' => '',
              ),
            ),
          ),
          5 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'about' => "uuid:$new_GUID",
              'xmlns:stJob' => 'http://ns.adobe.com/xap/1.0/sType/Job#',
              'xmlns:xapBJ' => 'http://ns.adobe.com/xap/1.0/bj/',
            ),
            'children' =>
            array (
              0 =>
              array (
                'tag' => 'xapBJ:JobRef',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Bag',
                    'children' =>
                    array (
                    ),
                  ),
                ),
              ),
            ),
          ),
          6 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:xapRights' => 'http://ns.adobe.com/xap/1.0/rights/',
            ),
            'children' =>
            array (
              1 =>
              array (
                'tag' => 'xapRights:WebStatement',
                'value' => '',
              ),
            ),
          ),
          7 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:dc' => 'http://purl.org/dc/elements/1.1/',
            ),
            'children' =>
            array (
              0 =>
              array (
                'tag' => 'dc:format',
                'value' => 'image/jpeg',
              ),
              1 =>
              array (
                'tag' => 'dc:title',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Alt',
                  ),
                ),
              ),
              2 =>
              array (
                'tag' => 'dc:description',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Alt',
                  ),
                ),
              ),
              3 =>
              array (
                'tag' => 'dc:rights',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Alt',
                  ),
                ),
              ),
              4 =>
              array (
                'tag' => 'dc:creator',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Seq',
                  ),
                ),
              ),
              5 =>
              array (
                'tag' => 'dc:subject',
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'rdf:Bag',
                  ),
                ),
              ),
            ),
          ),

/*          0 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:exif' => 'http://ns.adobe.com/exif/1.0/',
            ),
            'children' =>
            array (

//EXIF DATA GOES HERE - Not Implemented yet
            ),
          ),
*/
/*
          2 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:tiff' => 'http://ns.adobe.com/tiff/1.0/',
            ),
            'children' =>
            array (
// TIFF DATA GOES HERE - Not Implemented yet
              0 =>
              array (
                'tag' => 'tiff:Make',
                'value' => 'NIKON CORPORATION',
              ),
            ),
          ),
*/
/*
          3 =>
          array (
            'tag' => 'rdf:Description',
            'attributes' =>
            array (
              'rdf:about' => "uuid:$new_GUID",
              'xmlns:stRef' => 'http://ns.adobe.com/xap/1.0/sType/ResourceRef#',
              'xmlns:xapMM' => 'http://ns.adobe.com/xap/1.0/mm/',
            ),
            'children' =>
            array (
// XAPMM DATA GOES HERE - Not Implemented yet
              0 =>
              array (
                'tag' => 'xapMM:DocumentID',
                'value' => 'adobe:docid:photoshop:dceba4c2-e699-11d8-94b2-b6ec48319f2d',
              ),
              1 =>
              array (
                'tag' => 'xapMM:DerivedFrom',
                'attributes' =>
                array (
                  'rdf:parseType' => 'Resource',
                ),
                'children' =>
                array (
                  0 =>
                  array (
                    'tag' => 'stRef:documentID',
                    'value' => 'adobe:docid:photoshop:5144475b-e698-11d8-94b2-b6ec48319f2d',
                  ),
                  1 =>
                  array (
                    'tag' => 'stRef:instanceID',
                    'value' => "uuid:$new_GUID",
                  ),
                ),
              ),
            ),
          ),
*/

        ),
      ),
    ),
  ),
);

function get_jpeg_header_data( $filename )
{

        // prevent refresh from aborting file operations and hosing file
        ignore_user_abort(true);


        // Attempt to open the jpeg file - the at symbol supresses the error message about
        // not being able to open files. The file_exists would have been used, but it
        // does not work with files fetched over http or ftp.
        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $filename</p>\n";
                return FALSE;
        }


        // Read the first two characters
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the first two characters are 0xFF 0xDA  (SOI - Start of image)
        if ( $data != "\xFF\xD8" )
        {
                // No SOI (FF D8) at start of file - This probably isn't a JPEG file - close file and return;
                echo "<p>This probably is not a JPEG file</p>\n";
                fclose($filehnd);
                return FALSE;
        }


        // Read the third character
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the third character is 0xFF (Start of first segment header)
        if ( $data{0} != "\xFF" )
        {
                // NO FF found - close file and return - JPEG is probably corrupted
                fclose($filehnd);
                return FALSE;
        }

        // Flag that we havent yet hit the compressed image data
        $hit_compressed_image_data = FALSE;


        // Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
        //                                       2) we have hit the compressed image data (no more headers are allowed after data)
        //                                       3) or end of file is hit

        while ( ( $data{1} != "\xD9" ) && (! $hit_compressed_image_data) && ( ! feof( $filehnd ) ))
        {
                // Found a segment to look at.
                // Check that the segment marker is not a Restart marker - restart markers don't have size or data after them
                if (  ( ord($data{1}) < 0xD0 ) || ( ord($data{1}) > 0xD7 ) )
                {
                        // Segment isn't a Restart marker
                        // Read the next two bytes (size)
                        $sizestr = network_safe_fread( $filehnd, 2 );

                        // convert the size bytes to an integer
                        $decodedsize = unpack ("nsize", $sizestr);

                        // Save the start position of the data
                        $segdatastart = ftell( $filehnd );

                        // Read the segment data with length indicated by the previously read size
                        $segdata = network_safe_fread( $filehnd, $decodedsize['size'] - 2 );


                        // Store the segment information in the output array
                        $headerdata[] = array(  "SegType" => ord($data{1}),
                                                "SegName" => $GLOBALS[ "JPEG_Segment_Names" ][ ord($data{1}) ],
                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ ord($data{1}) ],
                                                "SegDataStart" => $segdatastart,
                                                "SegData" => $segdata );
                }

                // If this is a SOS (Start Of Scan) segment, then there is no more header data - the compressed image data follows
                if ( $data{1} == "\xDA" )
                {
                        // Flag that we have hit the compressed image data - exit loop as no more headers available.
                        $hit_compressed_image_data = TRUE;
                }
                else
                {
                        // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
                        $data = network_safe_fread( $filehnd, 2 );

                        // Check that the first byte of the two is 0xFF as it should be for a marker
                        if ( $data{0} != "\xFF" )
                        {
                                // NO FF found - close file and return - JPEG is probably corrupted
                                fclose($filehnd);
                                return FALSE;
                        }
                }
        }

        // Close File
        fclose($filehnd);
        // Alow the user to abort from now on
        ignore_user_abort(false);

        // Return the header data retrieved
        return $headerdata;
}


/******************************************************************************
* End of Function:     get_jpeg_header_data
******************************************************************************/




/******************************************************************************
*
* Function:     put_jpeg_header_data
*
* Description:  Writes JPEG header data into a JPEG file. Takes an array in the
*               same format as from get_jpeg_header_data, and combines it with
*               the image data of an existing JPEG file, to create a new JPEG file
*               WARNING: As this function will replace all JPEG headers,
*                        including SOF etc, it is best to read the jpeg headers
*                        from a file, alter them, then put them back on the same
*                        file. If a SOF segment wer to be transfered from one
*                        file to another, the image could become unreadable unless
*                        the images were idenical size and configuration
*
*
* Parameters:   old_filename - the JPEG file from which the image data will be retrieved
*               new_filename - the name of the new JPEG to create (can be same as old_filename)
*               jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*
* Returns:      TRUE - on Success
*               FALSE - on Failure
*
******************************************************************************/

function put_jpeg_header_data( $old_filename, $new_filename, $jpeg_header_data )
{

        // Change: added check to ensure data exists, as of revision 1.10
        // Check if the data to be written exists
        if ( $jpeg_header_data == FALSE )
        {
                // Data to be written not valid - abort
                return FALSE;
        }

        // extract the compressed image data from the old file
        $compressed_image_data = get_jpeg_image_data( $old_filename );

        // Check if the extraction worked
        if ( ( $compressed_image_data === FALSE ) || ( $compressed_image_data === NULL ) )
        {
                // Couldn't get image data from old file
                return FALSE;
        }


        // Cycle through new headers
        foreach ($jpeg_header_data as $segno => $segment)
        {
                // Check that this header is smaller than the maximum size
                if ( strlen($segment['SegData']) > 0xfffd )
                {
                        // Could't open the file - exit
                        echo "<p>A Header is too large to fit in JPEG segment</p>\n";
                        return FALSE;
                }
        }

        ignore_user_abort(true);    ## prevent refresh from aborting file operations and hosing file


        // Attempt to open the new jpeg file
        $newfilehnd = @fopen($new_filename, 'wb');
        // Check if the file opened successfully
        if ( ! $newfilehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $new_filename</p>\n";
                return FALSE;
        }

        // Write SOI
        fwrite( $newfilehnd, "\xFF\xD8" );

        // Cycle through new headers, writing them to the new file
        foreach ($jpeg_header_data as $segno => $segment)
        {

                // Write segment marker
                fwrite( $newfilehnd, sprintf( "\xFF%c", $segment['SegType'] ) );

                // Write segment size
                fwrite( $newfilehnd, pack( "n", strlen($segment['SegData']) + 2 ) );

                // Write segment data
                fwrite( $newfilehnd, $segment['SegData'] );
        }

        // Write the compressed image data
        fwrite( $newfilehnd, $compressed_image_data );

        // Write EOI
        fwrite( $newfilehnd, "\xFF\xD9" );

        // Close File
        fclose($newfilehnd);

        // Alow the user to abort from now on
        ignore_user_abort(false);


        return TRUE;

}

/******************************************************************************
* End of Function:     put_jpeg_header_data
******************************************************************************/



/******************************************************************************
*
* Function:     get_jpeg_Comment
*
* Description:  Retreives the contents of the JPEG Comment (COM = 0xFFFE) segment if one
*               exists
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      string - Contents of the Comment segement
*               FALSE - if the comment segment couldnt be found
*
******************************************************************************/

function get_jpeg_Comment( $jpeg_header_data )
{
        //Cycle through the header segments until COM is found or we run out of segments
        $i = 0;
        while ( ( $i < count( $jpeg_header_data) )  && ( $jpeg_header_data[$i]['SegName'] != "COM" ) )
        {
                $i++;
        }

        // Check if a COM segment has been found
        if (  $i < count( $jpeg_header_data) )
        {
                // A COM segment was found, return it's contents
                return $jpeg_header_data[$i]['SegData'];
        }
        else
        {
                // No COM segment found
                return FALSE;
        }
}

/******************************************************************************
* End of Function:     get_jpeg_Comment
******************************************************************************/


/******************************************************************************
*
* Function:     put_jpeg_Comment
*
* Description:  Creates a new JPEG Comment segment from a string, and inserts
*               this segment into the supplied JPEG header array
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data, into which the
*                                  new Comment segment will be put
*               $new_Comment - a string containing the new Comment
*
* Returns:      jpeg_header_data - the JPEG header data array with the new
*                                  JPEG Comment segment added
*
******************************************************************************/

function put_jpeg_Comment( $jpeg_header_data, $new_Comment )
{
        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an COM header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "COM" ) == 0 )
                {
                        // Found a preexisting Comment block - Replace it with the new one and return.
                        $jpeg_header_data[$i]['SegData'] = $new_Comment;
                        return $jpeg_header_data;
                }
        }



        // No preexisting Comment block found, find where to put it by searching for the highest app segment
        $i = 0;
        while ( ( $i < count( $jpeg_header_data ) ) && ( $jpeg_header_data[$i]["SegType"] >= 0xE0 ) )
        {
                $i++;
        }


        // insert a Comment segment new at the position found of the header data.
        array_splice($jpeg_header_data, $i , 0, array( array(   "SegType" => 0xFE,
                                                                "SegName" => $GLOBALS[ "JPEG_Segment_Names" ][ 0xFE ],
                                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xFE ],
                                                                "SegData" => $new_Comment ) ) );
        return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_jpeg_Comment
******************************************************************************/




/******************************************************************************
*
* Function:     Interpret_Comment_to_HTML
*
* Description:  Generates html showing the contents of any JPEG Comment segment
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      output - the HTML
*
******************************************************************************/

function Interpret_Comment_to_HTML( $jpeg_header_data )
{
        // Create a string to receive the output
        $output = "";

        // read the comment segment
        $comment = get_jpeg_Comment( $jpeg_header_data );

        // Check if the comment segment was valid
        if ( $comment !== FALSE )
        {
                // Comment exists - add it to the output
                $output .= "<h2 class=\"JPEG_Comment_Main_Heading\">JPEG Comment</h2>\n";
                $output .= "<p class=\"JPEG_Comment_Text\">$comment</p>\n";
        }

        // Return the result
        return $output;
}

/******************************************************************************
* End of Function:     Interpret_Comment_to_HTML
******************************************************************************/




/******************************************************************************
*
* Function:     get_jpeg_intrinsic_values
*
* Description:  Retreives information about the intrinsic characteristics of the
*               jpeg image, such as Bits per Component, Height and Width.
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      array - An array containing the intrinsic JPEG values
*               FALSE - if the comment segment couldnt be found
*
******************************************************************************/

function get_jpeg_intrinsic_values( $jpeg_header_data )
{
        // Create a blank array for the output
        $Outputarray = array( );

        //Cycle through the header segments until Start Of Frame (SOF) is found or we run out of segments
        $i = 0;
        while ( ( $i < count( $jpeg_header_data) )  && ( substr( $jpeg_header_data[$i]['SegName'], 0, 3 ) != "SOF" ) )
        {
                $i++;
        }

        // Check if a SOF segment has been found
        if ( substr( $jpeg_header_data[$i]['SegName'], 0, 3 ) == "SOF" )
        {
                // SOF segment was found, extract the information

                $data = $jpeg_header_data[$i]['SegData'];

                // First byte is Bits per component
                $Outputarray['Bits per Component'] = ord( $data{0} );

                // Second and third bytes are Image Height
                $Outputarray['Image Height'] = ord( $data{ 1 } ) * 256 + ord( $data{ 2 } );

                // Forth and fifth bytes are Image Width
                $Outputarray['Image Width'] = ord( $data{ 3 } ) * 256 + ord( $data{ 4 } );

                // Sixth byte is number of components
                $numcomponents = ord( $data{ 5 } );

                // Following this is a table containing information about the components
                for( $i = 0; $i < $numcomponents; $i++ )
                {
                        $Outputarray['Components'][] = array (  'Component Identifier' => ord( $data{ 6 + $i * 3 } ),
                                                                'Horizontal Sampling Factor' => ( ord( $data{ 7 + $i * 3 } ) & 0xF0 ) / 16,
                                                                'Vertical Sampling Factor' => ( ord( $data{ 7 + $i * 3 } ) & 0x0F ),
                                                                'Quantization table destination selector' => ord( $data{ 8 + $i * 3 } ) );
                }
        }
        else
        {
                // Couldn't find Start Of Frame segment, hence can't retrieve info
                return FALSE;
        }

        return $Outputarray;
}


/******************************************************************************
* End of Function:     get_jpeg_intrinsic_values
******************************************************************************/





/******************************************************************************
*
* Function:     Interpret_intrinsic_values_to_HTML
*
* Description:  Generates html showing some of the intrinsic JPEG values which
*               were retrieved with the get_jpeg_intrinsic_values function
*
* Parameters:   values - the JPEG intrinsic values, as read from get_jpeg_intrinsic_values
*
* Returns:      OutputStr - A string containing the HTML
*
******************************************************************************/

function Interpret_intrinsic_values_to_HTML( $values )
{
        // Check values are valid
        if ( $values != FALSE )
        {
                // Write Heading
                $OutputStr = "<h2 class=\"JPEG_Intrinsic_Main_Heading\">Intrinsic JPEG Information</h2>\n";

                // Create Table
                $OutputStr .= "<table class=\"JPEG_Intrinsic_Table\" border=1>\n";

                // Put image height and width into table
                $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Image Height</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . $values['Image Height'] . " pixels</td></tr>\n";
                $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Image Width</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . $values['Image Width'] . " pixels</td></tr>\n";

                // Put colour depth into table
                if ( count( $values['Components'] ) == 1 )
                {
                        $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Colour Depth</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . $values['Bits per Component'] . " bit Monochrome</td></tr>\n";
                }
                else
                {
                        $OutputStr .= "<tr class=\"JPEG_Intrinsic_Table_Row\"><td class=\"JPEG_Intrinsic_Caption_Cell\">Colour Depth</td><td class=\"JPEG_Intrinsic_Value_Cell\">" . ($values['Bits per Component'] * count( $values['Components'] ) ) . " bit</td></tr>\n";
                }

                // Close Table
                $OutputStr .= "</table>\n";

                // Return html
                return $OutputStr;
        }
}

/******************************************************************************
* End of Function:     Interpret_intrinsic_values_to_HTML
******************************************************************************/







/******************************************************************************
*
* Function:     get_jpeg_image_data
*
* Description:  Retrieves the compressed image data part of the JPEG file
*
* Parameters:   filename - the filename of the JPEG file to read
*
* Returns:      compressed_data - A string containing the compressed data
*               FALSE - if retrieval failed
*
******************************************************************************/

function get_jpeg_image_data( $filename )
{

        // prevent refresh from aborting file operations and hosing file
        ignore_user_abort(true);

        // Attempt to open the jpeg file
        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                return FALSE;
        }


        // Read the first two characters
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the first two characters are 0xFF 0xDA  (SOI - Start of image)
        if ( $data != "\xFF\xD8" )
        {
                // No SOI (FF D8) at start of file - close file and return;
                fclose($filehnd);
                return FALSE;
        }



        // Read the third character
        $data = network_safe_fread( $filehnd, 2 );

        // Check that the third character is 0xFF (Start of first segment header)
        if ( $data{0} != "\xFF" )
        {
                // NO FF found - close file and return
                fclose($filehnd);
                return;
        }

        // Flag that we havent yet hit the compressed image data
        $hit_compressed_image_data = FALSE;


        // Cycle through the file until, one of: 1) an EOI (End of image) marker is hit,
        //                                       2) we have hit the compressed image data (no more headers are allowed after data)
        //                                       3) or end of file is hit

        while ( ( $data{1} != "\xD9" ) && (! $hit_compressed_image_data) && ( ! feof( $filehnd ) ))
        {
                // Found a segment to look at.
                // Check that the segment marker is not a Restart marker - restart markers don't have size or data after them
                if (  ( ord($data{1}) < 0xD0 ) || ( ord($data{1}) > 0xD7 ) )
                {
                        // Segment isn't a Restart marker
                        // Read the next two bytes (size)
                        $sizestr = network_safe_fread( $filehnd, 2 );

                        // convert the size bytes to an integer
                        $decodedsize = unpack ("nsize", $sizestr);

                         // Read the segment data with length indicated by the previously read size
                        $segdata = network_safe_fread( $filehnd, $decodedsize['size'] - 2 );
                }

                // If this is a SOS (Start Of Scan) segment, then there is no more header data - the compressed image data follows
                if ( $data{1} == "\xDA" )
                {
                        // Flag that we have hit the compressed image data - exit loop after reading the data
                        $hit_compressed_image_data = TRUE;

                        // read the rest of the file in
                        // Can't use the filesize function to work out
                        // how much to read, as it won't work for files being read by http or ftp
                        // So instead read 1Mb at a time till EOF

                        $compressed_data = "";
                        do
                        {
                                $compressed_data .= network_safe_fread( $filehnd, 1048576 );
                        } while( ! feof( $filehnd ) );

                        // Strip off EOI and anything after
                        $EOI_pos = strpos( $compressed_data, "\xFF\xD9" );
                        $compressed_data = substr( $compressed_data, 0, $EOI_pos );
                }
                else
                {
                        // Not an SOS - Read the next two bytes - should be the segment marker for the next segment
                        $data = network_safe_fread( $filehnd, 2 );

                        // Check that the first byte of the two is 0xFF as it should be for a marker
                        if ( $data{0} != "\xFF" )
                        {
                                // Problem - NO FF foundclose file and return";
                                fclose($filehnd);
                                return;
                        }
                }
        }

        // Close File
        fclose($filehnd);

        // Alow the user to abort from now on
        ignore_user_abort(false);


        // Return the compressed data if it was found
        if ( $hit_compressed_image_data )
        {
                return $compressed_data;
        }
        else
        {
                return FALSE;
        }
}


/******************************************************************************
* End of Function:     get_jpeg_image_data
******************************************************************************/







/******************************************************************************
*
* Function:     Generate_JPEG_APP_Segment_HTML
*
* Description:  Generates html showing information about the Application (APP)
*               segments which are present in the JPEG file
*
* Parameters:   jpeg_header_data - the JPEG header data, as retrieved
*                                  from the get_jpeg_header_data function
*
* Returns:      output - A string containing the HTML
*
******************************************************************************/

function Generate_JPEG_APP_Segment_HTML( $jpeg_header_data )
{
        if ( $jpeg_header_data == FALSE )
        {
                return "";
        }


        // Write Heading
        $output = "<h2 class=\"JPEG_APP_Segments_Main_Heading\">Application Metadata Segments</h2>\n";

        // Create table
        $output .= "<table class=\"JPEG_APP_Segments_Table\" border=1>\n";


        // Cycle through each segment in the array

        foreach( $jpeg_header_data as $jpeg_header )
        {

                // Check if the segment is a APP segment

                if ( ( $jpeg_header['SegType'] >= 0xE0 ) && ( $jpeg_header['SegType'] <= 0xEF ) )
                {
                        // This is an APP segment

                        // Read APP Segment Name - a Null terminated string at the start of the segment
                        $seg_name = strtok($jpeg_header['SegData'], "\x00");

                        // Some Segment names are either too long or not meaningfull, so
                        // we should clean them up

                        if ( $seg_name == "http://ns.adobe.com/xap/1.0/" )
                        {
                                $seg_name = "XAP/RDF (\"http://ns.adobe.com/xap/1.0/\")";
                        }
                        elseif ( $seg_name == "Photoshop 3.0" )
                        {
                                $seg_name = "Photoshop IRB (\"Photoshop 3.0\")";
                        }
                        elseif ( ( strncmp ( $seg_name, "[picture info]", 14) == 0 ) ||
                                 ( strncmp ( $seg_name, "\x0a\x09\x09\x09\x09[picture info]", 19) == 0 ) )
                        {
                                $seg_name = "[picture info]";
                        }
                        elseif (  strncmp ( $seg_name, "Type=", 5) == 0 )
                        {
                                $seg_name = "Epson Info";
                        }
                        elseif ( ( strncmp ( $seg_name, "HHHHHHHHHHHHHHH", 15) == 0 ) ||
                                 ( strncmp ( $seg_name, "@s33", 5) == 0 ) )
                        {
                                $seg_name = "HP segment full of \"HHHHH\"";
                        }


                        // Clean the segment name so it doesn't cause problems with HTML
                        $seg_name = htmlentities( $seg_name );

                        // Output a Table row containing this APP segment
                        $output .= "<tr class=\"JPEG_APP_Segments_Table_Row\"><td class=\"JPEG_APP_Segments_Caption_Cell\">$seg_name</td><td class=\"JPEG_APP_Segments_Type_Cell\">" . $jpeg_header['SegName'] . "</td><td  class=\"JPEG_APP_Segments_Size_Cell\" align=\"right\">" . strlen( $jpeg_header['SegData']). " bytes</td></tr>\n";
                }
        }

        // Close the table
        $output .= "</table>\n";

        // Return the HTML
        return $output;
}


/******************************************************************************
* End of Function:     Generate_JPEG_APP_Segment_HTML
******************************************************************************/




/******************************************************************************
*
* Function:     network_safe_fread
*
* Description:  Retrieves data from a file. This function is required since
*               the fread function will not always return the requested number
*               of characters when reading from a network stream or pipe
*
* Parameters:   file_handle - the handle of a file to read from
*               length - the number of bytes requested
*
* Returns:      data - the data read from the file. may be less than the number
*                      requested if EOF was hit
*
******************************************************************************/

function network_safe_fread( $file_handle, $length )
{
        // Create blank string to receive data
        $data = "";

        // Keep reading data from the file until either EOF occurs or we have
        // retrieved the requested number of bytes

        while ( ( !feof( $file_handle ) ) && ( strlen($data) < $length ) )
        {
                $data .= fread( $file_handle, $length-strlen($data) );
        }

        // return the data read
        return $data;
}

/******************************************************************************
* End of Function:     network_safe_fread
******************************************************************************/




/******************************************************************************
* Global Variable:      JPEG_Segment_Names
*
* Contents:     The names of the JPEG segment markers, indexed by their marker number
*
******************************************************************************/

$GLOBALS[ "JPEG_Segment_Names" ] = array(

0xC0 =>  "SOF0",  0xC1 =>  "SOF1",  0xC2 =>  "SOF2",  0xC3 =>  "SOF4",
0xC5 =>  "SOF5",  0xC6 =>  "SOF6",  0xC7 =>  "SOF7",  0xC8 =>  "JPG",
0xC9 =>  "SOF9",  0xCA =>  "SOF10", 0xCB =>  "SOF11", 0xCD =>  "SOF13",
0xCE =>  "SOF14", 0xCF =>  "SOF15",
0xC4 =>  "DHT",   0xCC =>  "DAC",

0xD0 =>  "RST0",  0xD1 =>  "RST1",  0xD2 =>  "RST2",  0xD3 =>  "RST3",
0xD4 =>  "RST4",  0xD5 =>  "RST5",  0xD6 =>  "RST6",  0xD7 =>  "RST7",

0xD8 =>  "SOI",   0xD9 =>  "EOI",   0xDA =>  "SOS",   0xDB =>  "DQT",
0xDC =>  "DNL",   0xDD =>  "DRI",   0xDE =>  "DHP",   0xDF =>  "EXP",

0xE0 =>  "APP0",  0xE1 =>  "APP1",  0xE2 =>  "APP2",  0xE3 =>  "APP3",
0xE4 =>  "APP4",  0xE5 =>  "APP5",  0xE6 =>  "APP6",  0xE7 =>  "APP7",
0xE8 =>  "APP8",  0xE9 =>  "APP9",  0xEA =>  "APP10", 0xEB =>  "APP11",
0xEC =>  "APP12", 0xED =>  "APP13", 0xEE =>  "APP14", 0xEF =>  "APP15",


0xF0 =>  "JPG0",  0xF1 =>  "JPG1",  0xF2 =>  "JPG2",  0xF3 =>  "JPG3",
0xF4 =>  "JPG4",  0xF5 =>  "JPG5",  0xF6 =>  "JPG6",  0xF7 =>  "JPG7",
0xF8 =>  "JPG8",  0xF9 =>  "JPG9",  0xFA =>  "JPG10", 0xFB =>  "JPG11",
0xFC =>  "JPG12", 0xFD =>  "JPG13",

0xFE =>  "COM",   0x01 =>  "TEM",   0x02 =>  "RES",

);

/******************************************************************************
* End of Global Variable:     JPEG_Segment_Names
******************************************************************************/


/******************************************************************************
* Global Variable:      JPEG_Segment_Descriptions
*
* Contents:     The descriptions of the JPEG segment markers, indexed by their marker number
*
******************************************************************************/

$GLOBALS[ "JPEG_Segment_Descriptions" ] = array(

/* JIF Marker byte pairs in JPEG Interchange Format sequence */
0xC0 => "Start Of Frame (SOF) Huffman  - Baseline DCT",
0xC1 =>  "Start Of Frame (SOF) Huffman  - Extended sequential DCT",
0xC2 =>  "Start Of Frame Huffman  - Progressive DCT (SOF2)",
0xC3 =>  "Start Of Frame Huffman  - Spatial (sequential) lossless (SOF3)",
0xC5 =>  "Start Of Frame Huffman  - Differential sequential DCT (SOF5)",
0xC6 =>  "Start Of Frame Huffman  - Differential progressive DCT (SOF6)",
0xC7 =>  "Start Of Frame Huffman  - Differential spatial (SOF7)",
0xC8 =>  "Start Of Frame Arithmetic - Reserved for JPEG extensions (JPG)",
0xC9 =>  "Start Of Frame Arithmetic - Extended sequential DCT (SOF9)",
0xCA =>  "Start Of Frame Arithmetic - Progressive DCT (SOF10)",
0xCB =>  "Start Of Frame Arithmetic - Spatial (sequential) lossless (SOF11)",
0xCD =>  "Start Of Frame Arithmetic - Differential sequential DCT (SOF13)",
0xCE =>  "Start Of Frame Arithmetic - Differential progressive DCT (SOF14)",
0xCF =>  "Start Of Frame Arithmetic - Differential spatial (SOF15)",
0xC4 =>  "Define Huffman Table(s) (DHT)",
0xCC =>  "Define Arithmetic coding conditioning(s) (DAC)",

0xD0 =>  "Restart with modulo 8 count 0 (RST0)",
0xD1 =>  "Restart with modulo 8 count 1 (RST1)",
0xD2 =>  "Restart with modulo 8 count 2 (RST2)",
0xD3 =>  "Restart with modulo 8 count 3 (RST3)",
0xD4 =>  "Restart with modulo 8 count 4 (RST4)",
0xD5 =>  "Restart with modulo 8 count 5 (RST5)",
0xD6 =>  "Restart with modulo 8 count 6 (RST6)",
0xD7 =>  "Restart with modulo 8 count 7 (RST7)",

0xD8 =>  "Start of Image (SOI)",
0xD9 =>  "End of Image (EOI)",
0xDA =>  "Start of Scan (SOS)",
0xDB =>  "Define quantization Table(s) (DQT)",
0xDC =>  "Define Number of Lines (DNL)",
0xDD =>  "Define Restart Interval (DRI)",
0xDE =>  "Define Hierarchical progression (DHP)",
0xDF =>  "Expand Reference Component(s) (EXP)",

0xE0 =>  "Application Field 0 (APP0) - usually JFIF or JFXX",
0xE1 =>  "Application Field 1 (APP1) - usually EXIF or XMP/RDF",
0xE2 =>  "Application Field 2 (APP2) - usually Flashpix",
0xE3 =>  "Application Field 3 (APP3)",
0xE4 =>  "Application Field 4 (APP4)",
0xE5 =>  "Application Field 5 (APP5)",
0xE6 =>  "Application Field 6 (APP6)",
0xE7 =>  "Application Field 7 (APP7)",

0xE8 =>  "Application Field 8 (APP8)",
0xE9 =>  "Application Field 9 (APP9)",
0xEA =>  "Application Field 10 (APP10)",
0xEB =>  "Application Field 11 (APP11)",
0xEC =>  "Application Field 12 (APP12) - usually [picture info]",
0xED =>  "Application Field 13 (APP13) - usually photoshop IRB / IPTC",
0xEE =>  "Application Field 14 (APP14)",
0xEF =>  "Application Field 15 (APP15)",


0xF0 =>  "Reserved for JPEG extensions (JPG0)",
0xF1 =>  "Reserved for JPEG extensions (JPG1)",
0xF2 =>  "Reserved for JPEG extensions (JPG2)",
0xF3 =>  "Reserved for JPEG extensions (JPG3)",
0xF4 =>  "Reserved for JPEG extensions (JPG4)",
0xF5 =>  "Reserved for JPEG extensions (JPG5)",
0xF6 =>  "Reserved for JPEG extensions (JPG6)",
0xF7 =>  "Reserved for JPEG extensions (JPG7)",
0xF8 =>  "Reserved for JPEG extensions (JPG8)",
0xF9 =>  "Reserved for JPEG extensions (JPG9)",
0xFA =>  "Reserved for JPEG extensions (JPG10)",
0xFB =>  "Reserved for JPEG extensions (JPG11)",
0xFC =>  "Reserved for JPEG extensions (JPG12)",
0xFD =>  "Reserved for JPEG extensions (JPG13)",

0xFE =>  "Comment (COM)",
0x01 =>  "For temp private use arith code (TEM)",
0x02 =>  "Reserved (RES)",

);

function get_JFIF( $jpeg_header_data )
{
        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP0 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP0" ) == 0 )
                {
                        // And if it has the JFIF label,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "JFIF\x00", 5) == 0 )
                        {
                                // Found a JPEG File Interchange Format (JFIF) Block

                                // unpack the JFIF data from the incoming string
                                // First is the JFIF label string
                                // Then a two byte version number
                                // Then a byte, units identifier, ( 0 = aspect ration, 1 = dpi, 2 = dpcm)
                                // Then a two byte int X-Axis pixel Density (resolution)
                                // Then a two byte int Y-Axis pixel Density (resolution)
                                // Then a byte X-Axis JFIF thumbnail size
                                // Then a byte Y-Axis JFIF thumbnail size
                                // Then the uncompressed RGB JFIF thumbnail data

                                $JFIF_data = unpack( 'a5JFIF/C2Version/CUnits/nXDensity/nYDensity/CThumbX/CThumbY/a*ThumbData', $jpeg_header_data[$i]['SegData'] );

                                return $JFIF_data;
                        }
                }
        }
        return FALSE;
}

/******************************************************************************
* End of Function:     get_JFIF
******************************************************************************/




/******************************************************************************
*
* Function:     put_JFIF
*
* Description:  Creates a new JFIF segment from an array of JFIF data in the
*               same format as would be retrieved from get_JFIF, and inserts
*               this segment into the supplied JPEG header array
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data, into which the
*                                  new JFIF segment will be put
*               new_JFIF_array - a JFIF information array in the same format as
*                                from get_JFIF, to create the new segment
*
* Returns:      jpeg_header_data - the JPEG header data array with the new
*                                  JFIF segment added
*
******************************************************************************/

function put_JFIF( $jpeg_header_data, $new_JFIF_array )
{
        // pack the JFIF data into its proper format for a JPEG file
        $packed_data = pack( 'a5CCCnnCCa*',"JFIF\x00", $new_JFIF_array['Version1'], $new_JFIF_array['Version2'], $new_JFIF_array['Units'], $new_JFIF_array['XDensity'], $new_JFIF_array['YDensity'], $new_JFIF_array['ThumbX'], $new_JFIF_array['ThumbY'], $new_JFIF_array['ThumbData'] );

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP0 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP0" ) == 0 )
                {
                        // And if it has the JFIF label,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "JFIF\x00", 5) == 0 )
                        {
                                // Found a preexisting JFIF block - Replace it with the new one and return.
                                $jpeg_header_data[$i]['SegData'] = $packed_data;
                                return $jpeg_header_data;
                        }
                }
        }

        // No preexisting JFIF block found, insert a new one at the start of the header data.
        array_splice($jpeg_header_data, 0 , 0, array( array(   "SegType" => 0xE0,
                                                                "SegName" => "APP0",
                                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE0 ],
                                                                "SegData" => $packed_data ) ) );
        return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_JFIF
******************************************************************************/








/******************************************************************************
*
* Function:     Interpret_JFIF_to_HTML
*
* Description:  Generates html showing the JFIF information contained in
*               a JFIF data array, as retrieved with get_JFIF
*
* Parameters:   JFIF_array - a JFIF data array, as from get_JFIF
*               filename - the name of the JPEG file being processed ( used
*                          by the script which displays the JFIF thumbnail)
*
*
* Returns:      output - the HTML string
*
******************************************************************************/

function Interpret_JFIF_to_HTML( $JFIF_array, $filename )
{
        $output = "";
        if ( $JFIF_array !== FALSE )
        {
                $output .= "<H2 class=\"JFIF_Main_Heading\">Contains JPEG File Interchange Format (JFIF) Information</H2>\n";
                $output .= "\n<table class=\"JFIF_Table\" border=1>\n";
                $output .= "<tr class=\"JFIF_Table_Row\"><td class=\"JFIF_Caption_Cell\">JFIF version: </td><td class=\"JFIF_Value_Cell\">". sprintf( "%d.%02d", $JFIF_array['Version1'], $JFIF_array['Version2'] ) . "</td></tr>\n";
                if ( $JFIF_array['Units'] == 0 )
                {
                        $output .= "<tr class=\"JFIF_Table_Row\"><td class=\"JFIF_Caption_Cell\">Pixel Aspect Ratio: </td><td class=\"JFIF_Value_Cell\">" . $JFIF_array['XDensity'] ." x " . $JFIF_array['YDensity'] . "</td></tr>\n";
                }
                elseif ( $JFIF_array['Units'] == 1 )
                {
                        $output .= "<tr class=\"JFIF_Table_Row\"><td class=\"JFIF_Caption_Cell\">Resolution: </td><td class=\"JFIF_Value_Cell\">" . $JFIF_array['XDensity'] ." x " . $JFIF_array['YDensity'] . " pixels per inch</td></tr>\n";
                }
                elseif ( $JFIF_array['Units'] == 2 )
                {
                        $output .= "<tr class=\"JFIF_Table_Row\"><td class=\"JFIF_Caption_Cell\">Resolution: </td><td class=\"JFIF_Value_Cell\">" . $JFIF_array['XDensity'] ." x " . $JFIF_array['YDensity'] . " pixels per cm</td></tr>\n";
                }

                $output .= "<tr class=\"JFIF_Table_Row\"><td class=\"JFIF_Caption_Cell\">JFIF (uncompressed) thumbnail: </td><td class=\"JFIF_Value_Cell\">";
                if ( ( $JFIF_array['ThumbX'] != 0 ) && ( $JFIF_array['ThumbY'] != 0 ) )
                {
                        $output .= $JFIF_array['ThumbX'] ." x " . $JFIF_array['ThumbY'] . " pixels, Thumbnail Display Not Yet Implemented</td></tr>\n";
                        // TODO Implement JFIF Thumbnail display
                }
                else
                {
                        $output .= "None</td></tr>\n";
                }

                $output .= "</table><br>\n";
        }

        return $output;

}


/******************************************************************************
* End of Function:     Interpret_JFIF_to_HTML
******************************************************************************/










/******************************************************************************
*
* Function:     get_JFXX
*
* Description:  Retrieves information from a JPEG File Interchange Format Extension (JFXX)
*               segment and returns it in an array. Uses information supplied by
*               the get_jpeg_header_data function
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data
*
* Returns:      JFXX_data - an array of JFXX data
*               FALSE - if a JFXX segment could not be found
*
******************************************************************************/

function get_JFXX( $jpeg_header_data )
{
        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP0 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP0" ) == 0 )
                {
                        // And if it has the JFIF label,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "JFXX\x00", 5) == 0 )
                        {
                                // Found a JPEG File Interchange Format Extension (JFXX) Block

                                // unpack the JFXX data from the incoming string
                                // First is the 5 byte JFXX label string
                                // Then a 1 byte Extension code, indicating Thumbnail Format
                                // Then the thumbnail data

                                $JFXX_data = unpack( 'a5JFXX/CExtension_Code/a*ThumbData', $jpeg_header_data[$i]['SegData'] );
                                return $JFXX_data;
                        }
                }
        }
        return FALSE;
}

/******************************************************************************
* End of Function:     get_JFXX
******************************************************************************/




/******************************************************************************
*
* Function:     put_JFXX
*
* Description:  Creates a new JFXX segment from an array of JFXX data in the
*               same format as would be retrieved from get_JFXX, and inserts
*               this segment into the supplied JPEG header array
*
* Parameters:   jpeg_header_data - a JPEG header data array in the same format
*                                  as from get_jpeg_header_data, into which the
*                                  new JFXX segment will be put
*               new_JFXX_array - a JFXX information array in the same format as
*                                from get_JFXX, to create the new segment
*
* Returns:      jpeg_header_data - the JPEG header data array with the new
*                                  JFXX segment added
*
******************************************************************************/

function put_JFXX( $jpeg_header_data, $new_JFXX_array )
{
        // pack the JFXX data into its proper format for a JPEG file
        $packed_data = pack( 'a5Ca*',"JFXX\x00", $new_JFXX_array['Extension_Code'], $new_JFXX_array['ThumbData'] );

        $JFIF_pos = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP0 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP0" ) == 0 )
                {
                        // And if it has the JFXX label,
                        if( strncmp ( $jpeg_header_data[$i]['SegData'], "JFXX\x00", 5) == 0 )
                        {
                                // Found a preexisting JFXX block - Replace it with the new one and return.
                                $jpeg_header_data[$i]['SegData'] = $packed_data;
                                return $jpeg_header_data;
                        }

                        // if it has the JFIF label,
                        if( strncmp ( $jpeg_header_data[$i][SegData], "JFIF\x00", 5) == 0 )
                        {
                                // Found a preexisting JFIF block - Mark it in case we need to insert the JFXX after it
                                $JFIF_pos = $i;
                        }
                }
        }


        // No preexisting JFXX block found

        // Check if a JFIF segment was found,
        if ( $JFIF_pos !== -1 )
        {
                // A pre-existing JFIF segment was found,
                // insert the new JFXX segment after it.
                array_splice($jpeg_header_data, $JFIF_pos +1 , 0, array ( array(        "SegType" => 0xE0,
                                                                                        "SegName" => "APP0",
                                                                                        "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE0 ],
                                                                                        "SegData" => $packed_data ) ) );

        }
        else
        {
                // No pre-existing JFIF segment was found,
                // insert a new JFIF and the new JFXX segment at the start of the array.

                // Insert new JFXX segment
                array_splice($jpeg_header_data, 0 , 0, array( array(   "SegType" => 0xE0,
                                                                        "SegName" => "APP0",
                                                                        "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE0 ],
                                                                        "SegData" => $packed_data ) ) );

                // Create a new JFIF to be inserted at the start of
                // the array, with generic values
                $packed_data = pack( 'a5CCCnnCCa*',"JFIF\x00", 1, 2, 1, 72, 72, 0, 0, "" );

                array_splice($jpeg_header_data, 0 , 0, array( array(   "SegType" => 0xE0,
                                                                        "SegName" => "APP0",
                                                                        "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE0 ],
                                                                        "SegData" => $packed_data ) ) );
        }


        return $jpeg_header_data;
}

/******************************************************************************
* End of Function:     put_JFIF
******************************************************************************/



/******************************************************************************
*
* Function:     Interpret_JFXX_to_HTML
*
* Description:  Generates html showing the JFXX thumbnail contained in
*               a JFXX data array, as retrieved with get_JFXX
*
* Parameters:   JFXX_array - a JFXX information array in the same format as
*                            from get_JFXX, to create the new segment
*               filename - the name of the JPEG file being processed ( used
*                          by the script which displays the JFXX thumbnail)
*
* Returns:      output - the Html string
*
******************************************************************************/

function Interpret_JFXX_to_HTML( $JFXX_array, $filename )
{
        $output = "";
        if ( $JFXX_array !== FALSE )
        {
                $output .= "<H2 class=\"JFXX_Main_Heading\">Contains JPEG File Interchange Extension Format  (JFXX) Thumbnail</H2>\n";
                switch ( $JFXX_array['Extension_Code'] )
                {
                        case 0x10 :     $output .= "<p class=\"JFXX_Text\">JFXX Thumbnail is JPEG Encoded</p>\n";

                                        // Change: as of version 1.11 - Changed to make thumbnail link portable across directories
                                        // Build the path of the thumbnail script and its filename parameter to put in a url
                                        $link_str = get_relative_path( dirname(__FILE__) . "/get_JFXX_thumb.php" , getcwd ( ) );
                                        $link_str .= "?filename=";
                                        $link_str .= get_relative_path( $filename, dirname(__FILE__) );

                                        // Add thumbnail link to html
                                        $output .= "<a class=\"JFXX_Thumbnail_Link\" href=\"$link_str\"><img  class=\"JFXX_Thumbnail\" src=\"$link_str\"></a>\n";
                                        break;
                        case 0x11 :     $output .= "<p class=\"JFXX_Text\">JFXX Thumbnail is Encoded 1 byte/pixel</p>\n";
                                        $output .= "<p class=\"JFXX_Text\">Thumbnail Display Not Implemented Yet</p>\n";
                                        break;
                        case 0x13 :     $output .= "<p class=\"JFXX_Text\">JFXX Thumbnail is Encoded 3 bytes/pixel</p>\n";
                                        $output .= "<p class=\"JFXX_Text\">Thumbnail Display Not Implemented Yet</p>\n";
                                        break;
                        default :       $output .= "<p class=\"JFXX_Text\">JFXX Thumbnail is Encoded with Unknown format</p>\n";
                                        break;

                        // TODO: Implement JFXX one and three bytes per pixel thumbnail decoding
                }

        }

        return $output;

}


function get_IPTC( $Data_Str )
{

        // Initialise the start position
        $pos = 0;
        // Create the array to receive the data
        $OutputArray = array( );

        // Cycle through the IPTC records, decoding and storing them
        while( $pos < strlen($Data_Str) )
        {
                // TODO - Extended Dataset record not supported

                // Check if there is sufficient data for reading the record
                if ( strlen( substr($Data_Str,$pos) ) < 5 )
                {
                        // Not enough data left for a record - Probably corrupt data - ERROR
                        // Change: changed to return partial data as of revision 1.01
                        return $OutputArray;
                }

                // Unpack data from IPTC record:
                // First byte - IPTC Tag Marker - always 28
                // Second byte - IPTC Record Number
                // Third byte - IPTC Dataset Number
                // Fourth and fifth bytes - two byte size value
                $iptc_raw = unpack( "CIPTC_Tag_Marker/CIPTC_Record_No/CIPTC_Dataset_No/nIPTC_Size", substr($Data_Str,$pos) );

                // Skip position over the unpacked data
                $pos += 5;

                // Construct the IPTC type string eg 2:105
                $iptctype = sprintf( "%01d:%02d", $iptc_raw['IPTC_Record_No'], $iptc_raw['IPTC_Dataset_No']);

                // Check if there is sufficient data for reading the record contents
                if ( strlen( substr( $Data_Str, $pos, $iptc_raw['IPTC_Size'] ) ) !== $iptc_raw['IPTC_Size'] )
                {
                        // Not enough data left for the record content - Probably corrupt data - ERROR
                        // Change: changed to return partial data as of revision 1.01
                        return $OutputArray;
                }

                // Add the IPTC record to the output array
                $OutputArray[] = array( "IPTC_Type" => $iptctype ,
                                        "RecName" => $GLOBALS[ "IPTC_Entry_Names" ][ $iptctype ],
                                        "RecDesc" => $GLOBALS[ "IPTC_Entry_Descriptions" ][ $iptctype ],
                                        "RecData" => substr( $Data_Str, $pos, $iptc_raw['IPTC_Size'] ) );

                // Skip over the IPTC record data
                $pos += $iptc_raw['IPTC_Size'];
        }
        return $OutputArray;

}


/******************************************************************************
* End of Function:     get_IPTC
******************************************************************************/




/******************************************************************************
*
* Function:     put_IPTC
*
* Description:  Encodes an array of IPTC-NAA records into a string encoded
*               as IPTC-NAA IIM. (The reverse of get_IPTC)
*
* Parameters:   new_IPTC_block - the IPTC-NAA array to be encoded. Should be
*                                the same format as that received from get_IPTC
*
* Returns:      iptc_packed_data - IPTC-NAA IIM encoded string
*
******************************************************************************/


function put_IPTC( $new_IPTC_block )
{
        // Check if the incoming IPTC block is valid
        if ( $new_IPTC_block == FALSE )
        {
                // Invalid IPTC block - abort
                return FALSE;
        }
        // Initialise the packed output data string
        $iptc_packed_data = "";

        // Cycle through each record in the new IPTC block
        foreach ($new_IPTC_block as $record)
        {
                // Extract the Record Number and Dataset Number from the IPTC_Type field
                list($IPTC_Record, $IPTC_Dataset) = sscanf( $record['IPTC_Type'], "%d:%d");

                // Write the IPTC-NAA IIM Tag Marker, Record Number, Dataset Number and Data Size to the packed output data string
                $iptc_packed_data .= pack( "CCCn", 28, $IPTC_Record, $IPTC_Dataset, strlen($record['RecData']) );

                // Write the IPTC-NAA IIM Data to the packed output data string
                $iptc_packed_data .= $record['RecData'];
        }

        // Return the IPTC-NAA IIM data
        return $iptc_packed_data;
}

/******************************************************************************
* End of Function:     put_IPTC
******************************************************************************/



/******************************************************************************
*
* Function:     Interpret_IPTC_to_HTML
*
* Description:  Generates html detailing the contents a IPTC-NAA IIM array
*               which was retrieved with the get_IPTC function
*
* Parameters:   IPTC_info - the IPTC-NAA IIM array,as read from get_IPTC
*
* Returns:      OutputStr - A string containing the HTML
*
******************************************************************************/

function Interpret_IPTC_to_HTML( $IPTC_info )
{
        // Create a string to receive the HTML
        $output_str ="";

        // Check if the IPTC
        if ( $IPTC_info !== FALSE )
        {


                // Add Heading to HTML
                $output_str .= "<h3 class=\"IPTC_Main_Heading\">IPTC-NAA Record</h3>\n";

                // Add Table to HTML
                $output_str .= "\n<table class=\"IPTC_Table\" border=1>\n";

                // Cycle through each of the IPTC-NAA IIM records
                foreach( $IPTC_info as $IPTC_Record )
                {
                        // Check if the record is a known IPTC field
                        $Record_Name = $IPTC_Record['RecName'];
                        if ( $Record_Name == "" )
                        {
                                // Record is an unknown field - add message to HTML
                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">Unknown IPTC field '". htmlentities( $IPTC_Record['IPTC_Type'] ). "' :</td><td class=\"IPTC_Value_Cell\">" . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                        }
                        else
                        {
                                // Record is a recognised IPTC field - Process it accordingly

                                switch ( $IPTC_Record['IPTC_Type'] )
                                {
                                        case "1:00":    // Envelope Record:Model Version
                                        case "1:22":    // Envelope Record:File Format Version
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">" . hexdec( bin2hex( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                break;

                                        case "1:90":    // Envelope Record:Coded Character Set
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Decoding not yet implemented<br>\n (Hex Data: " . bin2hex( $IPTC_Record['RecData'] )  .")</td></tr>\n";
                                                break;
                                                // TODO: Implement decoding of IPTC record 1:90

                                        case "1:20":    // Envelope Record:File Format

                                                $formatno = hexdec( bin2hex( $IPTC_Record['RecData'] ) );

                                                // Lookup file format from lookup-table
                                                if ( array_key_exists( $formatno, $GLOBALS[ "IPTC_File Formats" ] ) )
                                                {
                                                        // Entry was found in lookup table - add it to HTML
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">File Format</td><td class=\"IPTC_Value_Cell\">". $GLOBALS[ "IPTC_File Formats" ][$formatno] . "</td></tr>\n";
                                                }
                                                else
                                                {
                                                        // No matching entry was found in lookup table - add message to html
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">File Format</td><td class=\"IPTC_Value_Cell\">Unknown File Format ($formatno)</td></tr>\n";
                                                }
                                                break;


                                        case "2:00":    // Application Record:Record Version
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">IPTC Version</td><td class=\"IPTC_Value_Cell\">" . hexdec( bin2hex( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                break;

                                        case "2:42":    // Application Record: Action Advised

                                                // Looup Action
                                                if ( $IPTC_Record['RecData'] == "01" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Kill</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "02" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Replace</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "03" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Append</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "04" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Reference</td></tr>\n";
                                                }
                                                else
                                                {
                                                        // Unknown Action
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Unknown : " . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                }
                                                break;

                                        case "2:08":    // Application Record:Editorial Update
                                                if ( $IPTC_Record['RecData'] == "01" )
                                                {
                                                        // Additional Language
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Additional language</td></tr>\n";
                                                }
                                                else
                                                {
                                                        // Unknown Value
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Unknown : " . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                }
                                                break;

                                        case "2:30":    // Application Record:Release Date
                                        case "2:37":    // Application Record:Expiration Date
                                        case "2:47":    // Application Record:Reference Date
                                        case "2:55":    // Application Record:Date Created
                                        case "2:62":    // Application Record:Digital Creation Date
                                        case "1:70":    // Envelope Record:Date Sent
                                                $date_array = unpack( "a4Year/a2Month/A2Day", $IPTC_Record['RecData'] );
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">" . nl2br( HTML_UTF8_Escape( $date_array['Day'] . "/" . $date_array['Month'] . "/" . $date_array['Year'] ) ) ."</td></tr>\n";
                                                break;

                                        case "2:35":    // Application Record:Release Time
                                        case "2:38":    // Application Record:Expiration Time
                                        case "2:60":    // Application Record:Time Created
                                        case "2:63":    // Application Record:Digital Creation Time
                                        case "1:80":    // Envelope Record:Time Sent
                                                $time_array = unpack( "a2Hour/a2Minute/A2Second/APlusMinus/A4Timezone", $IPTC_Record['RecData'] );
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">" . nl2br( HTML_UTF8_Escape( $time_array['Hour'] . ":" . $time_array['Minute'] . ":" . $time_array['Second'] . " ". $time_array['PlusMinus'] . $time_array['Timezone'] ) ) ."</td></tr>\n";
                                                break;

                                        case "2:75":    // Application Record:Object Cycle
                                                // Lookup Value
                                                if ( $IPTC_Record['RecData'] == "a" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Morning</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "p" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Evening</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "b" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Both Morning and Evening</td></tr>\n";
                                                }
                                                else
                                                {
                                                        // Unknown Value
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Unknown : " . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                }
                                                break;

                                        case "2:125":   // Application Record:Rasterised Caption
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">460x128 pixel black and white caption image</td></tr>\n";
                                                break;
                                                // TODO: Display Rasterised Caption for IPTC record 2:125

                                        case "2:130":   // Application Record:Image Type
                                                // Lookup Number of Components
                                                if ( $IPTC_Record['RecData']{0} == "0" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">No Objectdata";
                                                }
                                                elseif ( $IPTC_Record['RecData']{0} == "9" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Supplemental objects related to other objectdata";
                                                }
                                                else
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Number of Colour Components : " . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData']{0} ) );
                                                }

                                                // Lookup current objectdata colour
                                                if ( $GLOBALS['ImageType_Names'][ $IPTC_Record['RecData']{1} ] == "" )
                                                {
                                                        $output_str .= ", Unknown : " . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData']{1} ) );
                                                }
                                                else
                                                {
                                                        $output_str .= ", " . nl2br( HTML_UTF8_Escape( $GLOBALS['ImageType_Names'][ $IPTC_Record['RecData']{1} ] ) );
                                                }
                                                $output_str .= "</td></tr>\n";
                                                break;

                                        case "2:131":   // Application Record:Image Orientation
                                                // Lookup value
                                                if ( $IPTC_Record['RecData'] == "L" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Landscape</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "P" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Portrait</td></tr>\n";
                                                }
                                                elseif ( $IPTC_Record['RecData'] == "S" )
                                                {
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Square</td></tr>\n";
                                                }
                                                else
                                                {
                                                        // Unknown Orientation Value
                                                        $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">Unknown : " . nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                }
                                                break;

                                        default:        // All other records
                                                $output_str .= "<tr class=\"IPTC_Table_Row\"><td class=\"IPTC_Caption_Cell\">$Record_Name</td><td class=\"IPTC_Value_Cell\">" .nl2br( HTML_UTF8_Escape( $IPTC_Record['RecData'] ) ) ."</td></tr>\n";
                                                break;
                                }
                        }
                }

                // Add Table End to HTML
                $output_str .= "</table><br>\n";
        }

        // Return HTML
        return $output_str;
}


/******************************************************************************
* End of Function:     Interpret_IPTC_to_HTML
******************************************************************************/



/******************************************************************************
* Global Variable:      IPTC_Entry_Names
*
* Contents:     The names of the IPTC-NAA IIM fields
*
******************************************************************************/

$GLOBALS[ "IPTC_Entry_Names" ] = array(
// Envelope Record
"1:00" => "Model Version",
"1:05" => "Destination",
"1:20" => "File Format",
"1:22" => "File Format Version",
"1:30" => "Service Identifier",
"1:40" => "Envelope Number",
"1:50" => "Product ID",
"1:60" => "Envelope Priority",
"1:70" => "Date Sent",
"1:80" => "Time Sent",
"1:90" => "Coded Character Set",
"1:100" => "UNO (Unique Name of Object)",
"1:120" => "ARM Identifier",
"1:122" => "ARM Version",

// Application Record
"2:00" => "Record Version",
"2:03" => "Object Type Reference",
"2:05" => "Object Name (Title)",
"2:07" => "Edit Status",
"2:08" => "Editorial Update",
"2:10" => "Urgency",
"2:12" => "Subject Reference",
"2:15" => "Category",
"2:20" => "Supplemental Category",
"2:22" => "Fixture Identifier",
"2:25" => "Keywords",
"2:26" => "Content Location Code",
"2:27" => "Content Location Name",
"2:30" => "Release Date",
"2:35" => "Release Time",
"2:37" => "Expiration Date",
"2:35" => "Expiration Time",
"2:40" => "Special Instructions",
"2:42" => "Action Advised",
"2:45" => "Reference Service",
"2:47" => "Reference Date",
"2:50" => "Reference Number",
"2:55" => "Date Created",
"2:60" => "Time Created",
"2:62" => "Digital Creation Date",
"2:63" => "Digital Creation Time",
"2:65" => "Originating Program",
"2:70" => "Program Version",
"2:75" => "Object Cycle",
"2:80" => "By-Line (Author)",
"2:85" => "By-Line Title (Author Position) [Not used in Photoshop 7]",
"2:90" => "City",
"2:92" => "Sub-Location",
"2:95" => "Province/State",
"2:100" => "Country/Primary Location Code",
"2:101" => "Country/Primary Location Name",
"2:103" => "Original Transmission Reference",
"2:105" => "Headline",
"2:110" => "Credit",
"2:115" => "Source",
"2:116" => "Copyright Notice",
"2:118" => "Contact",
"2:120" => "Caption/Abstract",
"2:122" => "Caption Writer/Editor",
"2:125" => "Rasterized Caption",
"2:130" => "Image Type",
"2:131" => "Image Orientation",
"2:135" => "Language Identifier",
"2:150" => "Audio Type",
"2:151" => "Audio Sampling Rate",
"2:152" => "Audio Sampling Resolution",
"2:153" => "Audio Duration",
"2:154" => "Audio Outcue",
"2:200" => "ObjectData Preview File Format",
"2:201" => "ObjectData Preview File Format Version",
"2:202" => "ObjectData Preview Data",

// Pre-ObjectData Descriptor Record
"7:10"  => "Size Mode",
"7:20"  => "Max Subfile Size",
"7:90"  => "ObjectData Size Announced",
"7:95"  => "Maximum ObjectData Size",

// ObjectData Record
"8:10"  => "Subfile",

// Post ObjectData Descriptor Record
"9:10"  => "Confirmed ObjectData Size"

);

/******************************************************************************
* End of Global Variable:     IPTC_Entry_Names
******************************************************************************/





/******************************************************************************
* Global Variable:      IPTC_Entry_Descriptions
*
* Contents:     The Descriptions of the IPTC-NAA IIM fields
*
******************************************************************************/

$GLOBALS[ "IPTC_Entry_Descriptions" ] = array(
// Envelope Record
"1:00" => "2 byte binary version number",
"1:05" => "Max 1024 characters of Destination",
"1:20" => "2 byte binary file format number, see IPTC-NAA V4 Appendix A",
"1:22" => "Binary version number of file format",
"1:30" => "Max 10 characters of Service Identifier",
"1:40" => "8 Character Envelope Number",
"1:50" => "Product ID - Max 32 characters",
"1:60" => "Envelope Priority - 1 numeric characters",
"1:70" => "Date Sent - 8 numeric characters CCYYMMDD",
"1:80" => "Time Sent - 11 characters HHMMSS±HHMM",
"1:90" => "Coded Character Set - Max 32 characters",
"1:100" => "UNO (Unique Name of Object) - 14 to 80 characters",
"1:120" => "ARM Identifier - 2 byte binary number",
"1:122" => "ARM Version - 2 byte binary number",

// Application Record
"2:00" => "Record Version - 2 byte binary number",
"2:03" => "Object Type Reference -  3 plus 0 to 64 Characters",
"2:05" => "Object Name (Title) - Max 64 characters",
"2:07" => "Edit Status - Max 64 characters",
"2:08" => "Editorial Update - 2 numeric characters",
"2:10" => "Urgency - 1 numeric character",
"2:12" => "Subject Reference - 13 to 236 characters",
"2:15" => "Category - Max 3 characters",
"2:20" => "Supplemental Category - Max 32 characters",
"2:22" => "Fixture Identifier - Max 32 characters",
"2:25" => "Keywords - Max 64 characters",
"2:26" => "Content Location Code - 3 characters",
"2:27" => "Content Location Name - Max 64 characters",
"2:30" => "Release Date - 8 numeric characters CCYYMMDD",
"2:35" => "Release Time - 11 characters HHMMSS±HHMM",
"2:37" => "Expiration Date - 8 numeric characters CCYYMMDD",
"2:35" => "Expiration Time - 11 characters HHMMSS±HHMM",
"2:40" => "Special Instructions - Max 256 Characters",
"2:42" => "Action Advised - 2 numeric characters",
"2:45" => "Reference Service - Max 10 characters",
"2:47" => "Reference Date - 8 numeric characters CCYYMMDD",
"2:50" => "Reference Number - 8 characters",
"2:55" => "Date Created - 8 numeric characters CCYYMMDD",
"2:60" => "Time Created - 11 characters HHMMSS±HHMM",
"2:62" => "Digital Creation Date - 8 numeric characters CCYYMMDD",
"2:63" => "Digital Creation Time - 11 characters HHMMSS±HHMM",
"2:65" => "Originating Program - Max 32 characters",
"2:70" => "Program Version - Max 10 characters",
"2:75" => "Object Cycle - 1 character",
"2:80" => "By-Line (Author) - Max 32 Characters",
"2:85" => "By-Line Title (Author Position) - Max 32 characters",
"2:90" => "City - Max 32 Characters",
"2:92" => "Sub-Location - Max 32 characters",
"2:95" => "Province/State - Max 32 Characters",
"2:100" => "Country/Primary Location Code - 3 alphabetic characters",
"2:101" => "Country/Primary Location Name - Max 64 characters",
"2:103" => "Original Transmission Reference - Max 32 characters",
"2:105" => "Headline - Max 256 Characters",
"2:110" => "Credit - Max 32 Characters",
"2:115" => "Source - Max 32 Characters",
"2:116" => "Copyright Notice - Max 128 Characters",
"2:118" => "Contact - Max 128 characters",
"2:120" => "Caption/Abstract - Max 2000 Characters",
"2:122" => "Caption Writer/Editor - Max 32 Characters",
"2:125" => "Rasterized Caption - 7360 bytes, 1 bit per pixel, 460x128pixel image",
"2:130" => "Image Type - 2 characters",
"2:131" => "Image Orientation - 1 alphabetic character",
"2:135" => "Language Identifier - 2 or 3 aphabetic characters",
"2:150" => "Audio Type - 2 characters",
"2:151" => "Audio Sampling Rate - 6 numeric characters",
"2:152" => "Audio Sampling Resolution - 2 numeric characters",
"2:153" => "Audio Duration - 6 numeric characters",
"2:154" => "Audio Outcue - Max 64 characters",
"2:200" => "ObjectData Preview File Format - 2 byte binary number",
"2:201" => "ObjectData Preview File Format Version - 2 byte binary number",
"2:202" => "ObjectData Preview Data - Max 256000 binary bytes",

// Pre-ObjectData Descriptor Record
"7:10"  => "Size Mode - 1 numeric character",
"7:20"  => "Max Subfile Size",
"7:90"  => "ObjectData Size Announced",
"7:95"  => "Maximum ObjectData Size",

// ObjectData Record
"8:10"  => "Subfile",

// Post ObjectData Descriptor Record
"9:10"  => "Confirmed ObjectData Size"

);

/******************************************************************************
* End of Global Variable:     IPTC_Entry_Descriptions
******************************************************************************/




/******************************************************************************
* Global Variable:      IPTC_File Formats
*
* Contents:     The names of the IPTC-NAA IIM File Formats for field 1:20
*
******************************************************************************/

$GLOBALS[ "IPTC_File Formats" ] = array(
00 => "No ObjectData",
01 => "IPTC-NAA Digital Newsphoto Parameter Record",
02 => "IPTC7901 Recommended Message Format",
03 => "Tagged Image File Format (Adobe/Aldus Image data)",
04 => "Illustrator (Adobe Graphics data)",
05 => "AppleSingle (Apple Computer Inc)",
06 => "NAA 89-3 (ANPA 1312)",
07 => "MacBinary II",
08 => "IPTC Unstructured Character Oriented File Format (UCOFF)",
09 => "United Press International ANPA 1312 variant",
10 => "United Press International Down-Load Message",
11 => "JPEG File Interchange (JFIF)",
12 => "Photo-CD Image-Pac (Eastman Kodak)",
13 => "Microsoft Bit Mapped Graphics File [*.BMP]",
14 => "Digital Audio File [*.WAV] (Microsoft & Creative Labs)",
15 => "Audio plus Moving Video [*.AVI] (Microsoft)",
16 => "PC DOS/Windows Executable Files [*.COM][*.EXE]",
17 => "Compressed Binary File [*.ZIP] (PKWare Inc)",
18 => "Audio Interchange File Format AIFF (Apple Computer Inc)",
19 => "RIFF Wave (Microsoft Corporation)",
20 => "Freehand (Macromedia/Aldus)",
21 => "Hypertext Markup Language - HTML (The Internet Society)",
22 => "MPEG 2 Audio Layer 2 (Musicom), ISO/IEC",
23 => "MPEG 2 Audio Layer 3, ISO/IEC",
24 => "Portable Document File (*.PDF) Adobe",
25 => "News Industry Text Format (NITF)",
26 => "Tape Archive (*.TAR)",
27 => "Tidningarnas Telegrambyrå NITF version (TTNITF DTD)",
28 => "Ritzaus Bureau NITF version (RBNITF DTD)",
29 => "Corel Draw [*.CDR]"
);


/******************************************************************************
* End of Global Variable:     IPTC_File Formats
******************************************************************************/

/******************************************************************************
* Global Variable:      ImageType_Names
*
* Contents:     The names of the colour components for IPTC-NAA IIM field 2:130
*
******************************************************************************/

$GLOBALS['ImageType_Names'] = array(    "M" => "Monochrome",
                                        "Y" => "Yellow Component",
                                        "M" => "Magenta Component",
                                        "C" => "Cyan Component",
                                        "K" => "Black Component",
                                        "R" => "Red Component",
                                        "G" => "Green Component",
                                        "B" => "Blue Component",
                                        "T" => "Text Only",
                                        "F" => "Full colour composite, frame sequential",
                                        "L" => "Full colour composite, line sequential",
                                        "P" => "Full colour composite, pixel sequential",
                                        "S" => "Full colour composite, special interleaving" );

$GLOBALS[ "IFD_Tag_Definitions" ] = array(


/*****************************************************************************/
/*                                                                           */
/* TIFF Tags                                                                 */
/*                                                                           */
/*****************************************************************************/


"TIFF" => array(


256 => array(   'Name'  => "Image Width",
                'Description' => "Width of image in pixels (number of columns)",
                'Type'  => "Numeric",
                'Units' => "pixels" ),

257 => array(   'Name'  =>  "Image Length",
                'Description' => "Height of image in pixels (number of rows)",
                'Type'  => "Numeric",
                'Units' => "pixels" ),

258 => array(   'Name'  => "Bits Per Sample",
                'Description' => "Number of bits recorded per sample (a sample is usually one colour (Red, Green or Blue) of one pixel)",
                'Type'  => "Numeric",
                'Units' => "bits ( for each colour component )" ),


259 => array(   'Name' => "Compression",
                'Description' => "Specifies what type of compression is used 1 = uncompressed, 6 = JPEG compression (thumbnails only), Other = reserved",
                'Type' => "Lookup",
                1 => "Uncompressed",
                5 => "LZW Compression",
                6 => "Thumbnail compressed with JPEG compression",
                7 => "JPEG Compression",
                8 => "ZIP Compression" ),                                // Change: Added TIFF compression types as of version 1.11

262 => array(   'Name' =>  "Photometric Interpretation",
                'Description' => "Specifies Pixel Composition - 0 or 1 = monochrome, 2 = RGB, 3 = Palatte Colour, 4 = Transparency Mask, 6 = YCbCr",
                'Type' => "Lookup",
                2 => "RGB (Red Green Blue)",
                6 => "YCbCr (Luminance, Chroma minus Blue, and Chroma minus Red)" ),

274 => array(   'Name' =>  "Orientation",
                'Description' => "Specifies the orientation of the image.\n
1 = Row 0 top, column 0 left\n
2 = Row 0 top, column 0 right\n
3 = Row 0 bottom, column 0 right\n
4 = Row 0 bottom, column 0 left\n
5 = Row 0 left, column 0 top\n
6 = Row 0 right, column 0 top\n
7 = Row 0 right, column 0 bottom\n
8 = Row 0 left, column 0 bottom",
                'Type' => "Lookup",
                1 => "No Rotation, No Flip \n(Row 0 is at the visual top of the image,\n and column 0 is the visual left-hand side)",
                2 => "No Rotation, Flipped Horizontally \n(Row 0 is at the visual top of the image,\n and column 0 is the visual right-hand side)",
                3 => "Rotated 180 degrees, No Flip \n(Row 0 is at the visual bottom of the image,\n and column 0 is the visual right-hand side)",
                4 => "No Rotation, Flipped Vertically \n(Row 0 is at the visual bottom of the image,\n and column 0 is the visual left-hand side)",
                5 => "Flipped Horizontally, Rotated 90 degrees counter clockwise \n(Row 0 is at the visual left-hand side of of the image,\n and column 0 is the visual top)",
                6 => "No Flip, Rotated 90 degrees clockwise \n(Row 0 is at the visual right-hand side of of the image,\n and column 0 is the visual top)",
                7 => "Flipped Horizontally, Rotated 90 degrees clockwise \n(Row 0 is at the visual right-hand side of of the image,\n and column 0 is the visual bottom)",
                8 => "No Flip, Rotated 90 degrees counter clockwise \n(Row 0 is at the visual left-hand side of of the image,\n and column 0 is the visual bottom)" ),
277 => array(   'Name' =>  "Samples Per Pixel",
                'Description' => "Number of recorded samples (colours) per pixel - usually 1 for B&W, grayscale, and palette-colour, usually 3 for RGB and YCbCr",
                'Type' => "Numeric",
                'Units' => "Components (colours)" ),

284 => array(   'Name' =>  "Planar Configuration",
                'Description' => "Specifies whether pixel components are recorded in chunky or planar format - 1 = Chunky, 2 = Planar",
                'Type' => "Lookup",
                1 => "Chunky Format",
                2 => "Planar Format" ),

530 => array(   'Name' =>  "YCbCr Sub-Sampling",
                'Description' => "Specifies ratio of chrominance to luminance components - [2, 1] = YCbCr4:2:2,  [2, 2] = YCbCr4:2:0",
                'Type' => "Special" ),


531 => array(   'Name' =>  "YCbCr Positioning",
                'Description' => "Specifies location of chrominance and luminance components - 1 = centered, 2 = co-sited",
                'Type' => "Lookup",
                1 => "Chrominance components Centred in relation to luminance components",
                2 => "Chrominance and luminance components Co-Sited" ),


282 => array(   'Name' =>  "X Resolution",
                'Description' => "Number of columns (pixels) per \'ResolutionUnit\'",
                'Type' => "Numeric",
                'Units'=> "pixels per 'Resolution Unit' " ),

283 => array(   'Name' =>  "Y Resolution",
                'Description' => "Number of rows (pixels) per \'ResolutionUnit\'",
                'Type' => "Numeric",
                'Units'=> "pixels per 'Resolution Unit' " ),

296 => array(   'Name' =>  "Resolution Unit",
                'Description' => "Units for measuring XResolution and YResolution - 1 = No units, 2 = Inches, 3 = Centimetres",
                'Type' => "Lookup",
                2 => "Inches",
                3 => "Centimetres" ),

273 => array(   'Name' =>  "Strip Offsets",
                'Type' => "Numeric",
                'Units'=> "bytes offset" ),

278 => array(   'Name' =>  "Rows Per Strip",
                'Type' => "Numeric",
                'Units'=> "rows" ),

279 => array(   'Name' => "Strip Byte Counts",
                'Type' => "Numeric",
                'Units'=> "bytes" ),

513 => array(   'Name' => "Exif Thumbnail (JPEG Interchange Format)",
                'Type' => "Special" ),

514 => array(   'Name' => "Exif Thumbnail Length (JPEG Interchange Format Length)",
                'Type' => "Numeric",
                'Units'=> "bytes" ),

301 => array(   'Name' => "Transfer Function",
                'Type' => "Numeric",
                'Units'=> "" ),

318 => array(   'Name' => "White Point Chromaticity",
                'Type' => "Numeric",
                'Units'=> "(x,y coordinates on a 1931 CIE xy chromaticity diagram)" ),

319 => array(   'Name' => "Primary Chromaticities",
                'Type' => "Numeric",
                'Units'=> "(Red x,y, Green x,y, Blue x,y coordinates on a 1931 CIE xy chromaticity diagram)" ),

529 => array(   'Name' => "YCbCr Coefficients",
                'Description' => "Transform Coefficients for transformation from RGB to YCbCr",
                'Type' => "Numeric",
                'Units'=> "(LumaRed, LumaGreen, LumaBlue [proportions of red, green, and blue in luminance])" ),

532 => array(   'Name' => "Reference Black point and White point",
                'Type' => "Numeric",
                'Units'=> "(R or Y White Headroom, R or Y Black Footroom, G or Cb White Headroom, G or Cb Black Footroom, B or Cr White Headroom, B or Cr Black Footroom)" ),

306 => array(   'Name' => "Date and Time",
                'Type' => "Numeric",
                'Units'=> " (Format: YYYY:MM:DD HH:mm:SS)" ),

270 => array(   'Name' => "Image Description",
                'Type' => "String" ),

271 => array(   'Name' => "Make (Manufacturer)",
                'Type' => "String" ),

272 => array(   'Name' => "Model",
                'Type' => "String" ),

305 => array(   'Name' => "Software or Firmware",
                'Type' => "String" ),

315 => array(   'Name' => "Artist Name",
                'Type' => "String" ),

700 => array(   'Name' => "Embedded XMP Block",        // Change: Added embedded XMP as of version 1.11
                'Type' => "XMP" ),

33432 => array( 'Name' => "Copyright Information",
                'Type' => "String" ),

34665 => array( 'Name' => "EXIF Image File Directory (IFD)",
                'Type' => "SubIFD",
                'Tags Name' => "EXIF" ),

33723 => array( 'Name' => "IPTC Records",
                'Type' => "IPTC" ),

34377 => array( 'Name' => "Embedded Photoshop IRB",    // Change: Added embedded IRB as of version 1.11
                'Type' => "IRB" ),

34853 => array( 'Name' => "GPS Info Image File Directory (IFD)",        // Change: Moved GPS IFD tag to correct location as of version 1.11
                'Type' => "SubIFD",
                'Tags Name' => "GPS" ),

50341 => array( 'Name' => "Print Image Matching Info",
                'Type' => "PIM" ),
40092 => array( 'Name' => "XP Comment",
                'Type' => "String" ),

),


/*****************************************************************************/
/*                                                                           */
/* EXIF Tags                                                                 */
/*                                                                           */
/*****************************************************************************/


'EXIF' => array (

// Exif IFD
36864 => array( 'Name' => "Exif Version",
                'Type' => "String" ),

40965 => array( 'Name' => "Interoperability Image File Directory (IFD)",
                'Type' => "SubIFD",
                'Tags Name' => "Interoperability" ),

// Change: removed GPS IFD tag from here as it was incorrect location - as of version 1.11

40960 => array( 'Name' => "FlashPix Version",
                'Type' => "String" ),

40961 => array( 'Name' => "Colour Space",
                'Type' => "Lookup",
                1 => "sRGB",
                0xFFFF => "Uncalibrated" ),

40962 => array( 'Name' => "Pixel X Dimension",
                'Type' => "Numeric",
                'Units'=> "pixels" ),

40963 => array( 'Name' => "Pixel Y Dimension",
                'Type' => "Numeric",
                'Units' => "pixels" ),

37121 => array( 'Name' => "Components Configuration",
                'Type' => "Special" ),

37122 => array( 'Name' => "Compressed Bits Per Pixel",
                'Type' => "Numeric",
                'Units' => "bits" ),

37500 => array( 'Name' => "Maker Note",
                'Type' => "Maker Note" ),

37510 => array( 'Name' => "User Comment",
                'Type' => "Character Coded String" ),




40964 => array( 'Name' => "Related Sound File",
                'Type' => "String" ),

36867 => array( 'Name' => "Date and Time of Original",
                'Type' => "String",
                'Units' => " (Format: YYYY:MM:DD HH:mm:SS)" ),

36868 => array( 'Name' => "Date and Time when Digitized",
                'Type' => "String",
                'Units' => " (Format: YYYY:MM:DD HH:mm:SS)" ),

37520 => array( 'Name' => "Sub Second Time",
                'Type' => "String" ),

37521 => array( 'Name' => "Sub Second Time of Original",
                'Type' => "String" ),

37522 => array( 'Name' => "Sub Second Time when Digitized",
                'Type' => "String" ),

33434 => array( 'Name' => "Exposure Time",
                'Type' => "Numeric",
                'Units' => "seconds" ),

37377 => array( 'Name' => "APEX Shutter Speed Value (Tv)",
                'Type' => "Numeric" ),

37378 => array( 'Name' => "APEX Aperture Value (Av)",
                'Type' => "Numeric" ),

37379 => array( 'Name' => "APEX Brightness Value (Bv)",
                'Type' => "Numeric" ),

37380 => array( 'Name' => "APEX Exposure Bias Value (Exposure Compensation)",
                'Type' => "Numeric",
                'Units' => "EV" ),

42240 => array( 'Name' => "Gamma Compensation for Playback",
                'Type' => "Numeric" ),


37381 => array( 'Name' => "APEX Maximum Aperture Value",
                'Type' => "Numeric" ),

37382 => array( 'Name' => "Subject Distance",
                'Type' => "Numeric",
                'Units' => "metres" ),

37383 => array( 'Name' => "Metering Mode",
                'Type' => "Lookup",
                0 => "Unknown",
                1 => "Average",
                2 => "Center Weighted Average",
                3 => "Spot",
                4 => "Multi Spot",
                5 => "Pattern",
                6 => "Partial",
                255 => "Other" ),

37384 => array( 'Name' => "Light Source",
                'Type' => "Lookup",
                0 => "Unknown",
                1 => "Daylight",
                2 => "Fluorescent",
                3 => "Tungsten (incandescent light)",
                4 => "Flash",
                9 => "Fine weather",
                10 => "Cloudy weather",
                11 => "Shade",
                12 => "Daylight fluorescent (D 5700 – 7100K)",
                13 => "Day white fluorescent (N 4600 – 5400K)",
                14 => "Cool white fluorescent (W 3900 – 4500K)",
                15 => "White fluorescent (WW 3200 – 3700K)",
                17 => "Standard light A",
                18 => "Standard light B",
                19 => "Standard light C",
                20 => "D55",
                21 => "D65",
                22 => "D75",
                23 => "D50",
                24 => "ISO studio tungsten",
                255 => "Other" ),

37385 => array( 'Name' => "Flash",
                'Type' => "Lookup",
                0  => "Flash did not fire",
                1  => "Flash fired",
                5  => "Strobe return light not detected",
                7  => "Strobe return light detected",
                9  => "Flash fired, compulsory flash mode",
                13 => "Flash fired, compulsory flash mode, return light not detected",
                15 => "Flash fired, compulsory flash mode, return light detected",
                16 => "Flash did not fire, compulsory flash suppression mode",
                24 => "Flash did not fire, auto mode",
                25 => "Flash fired, auto mode",
                29 => "Flash fired, auto mode, return light not detected",
                31 => "Flash fired, auto mode, return light detected",
                32 => "No flash function",
                65 => "Flash fired, red-eye reduction mode",
                69 => "Flash fired, red-eye reduction mode, return light not detected",
                71 => "Flash fired, red-eye reduction mode, return light detected",
                73 => "Flash fired, compulsory flash mode, red-eye reduction mode",
                77 => "Flash fired, compulsory flash mode, red-eye reduction mode, return light not detected",
                79 => "Flash fired, compulsory flash mode, red-eye reduction mode, return light detected",
                89 => "Flash fired, auto mode, red-eye reduction mode",
                93 => "Flash fired, auto mode, return light not detected, red-eye reduction mode",
                95 => "Flash fired, auto mode, return light detected, red-eye reduction mode" ),

37386 => array( 'Name' => "FocalLength",
                'Type' => "Numeric",
                'Units' => "mm" ),

37396 => array( 'Name' => "Subject Area",
                'Type' => "Numeric",
                'Units' => "( Two Values: x,y coordinates,  Three Values: x,y coordinates, diameter,  Four Values: center x,y coordinates, width, height)" ),

33437 => array( 'Name' => "Aperture F Number",
                'Type' => "Numeric" ),

34850 => array( 'Name' => "Exposure Program",
                'Type' => "Lookup",
                0 => "Not defined",
                1 => "Manual",
                2 => "Normal program",
                3 => "Aperture priority",
                4 => "Shutter priority",
                5 => "Creative program (biased toward depth of field)",
                6 => "Action program (biased toward fast shutter speed)",
                7 => "Portrait mode (for closeup photos with the background out of focus)",
                8 => "Landscape mode (for landscape photos with the background in focus)" ),

34852 => array( 'Name' => "Spectral Sensitivity",
                'Type' => "String" ),

34855 => array( 'Name' => "ISO Speed Ratings",
                'Type' => "Numeric" ),

34856 => array( 'Name' => "Opto-Electronic Conversion Function",
                'Type' => "Unknown" ),

41483 => array( 'Name' => "Flash Energy",
                'Type' => "Numeric",
                'Units' => "Beam Candle Power Seconds (BCPS)" ),

41484 => array( 'Name' => "Spatial Frequency Response",
                'Type' => "Unknown" ),

41486 => array( 'Name' => "Focal Plane X Resolution",
                'Type' => "Numeric",
                'Units' => "pixels per 'Focal Plane Resolution Unit'" ),

41487 => array( 'Name' => "Focal Plane Y Resolution",
                'Type' => "Numeric",
                'Units' => "pixels per 'Focal Plane Resolution Unit'" ),

41488 => array( 'Name' => "Focal Plane Resolution Unit",
                'Type' => "Lookup",
                2 => "Inches",
                3 => "Centimetres" ),

41492 => array( 'Name' => "Subject Location",
                'Type' => "Numeric",
                'Units' => "(x,y pixel coordinates of subject)" ),

41493 => array( 'Name' => "Exposure Index",
                'Type' => "Numeric" ),

41495 => array( 'Name' => "Sensing Method",
                'Type' => "Lookup",
                1 => "Not defined",
                2 => "One-chip colour area sensor",
                3 => "Two-chip colour area sensor",
                4 => "Three-chip colour area sensor",
                5 => "Colour sequential area sensor",
                7 => "Trilinear sensor",
                8 => "Colour sequential linear sensor" ),

41728 => array( 'Name' => "File Source",
                'Type' => "Lookup",
                3 => "Digital Still Camera" ),

41729 => array( 'Name' => "Scene Type",
                'Type' => "Lookup",
                1 => "A directly photographed image" ),

41730 => array( 'Name' => "Colour Filter Array Pattern",
                'Type' => "Special" ),

41985 => array( 'Name' => "Special Processing (Custom Rendered)",
                'Type' => "Lookup",
                0 => "Normal process",
                1 => "Custom process" ),

41986 => array( 'Name' => "Exposure Mode",
                'Type' => "Lookup",
                0 => "Auto exposure",
                1 => "Manual exposure",
                2 => "Auto bracket" ),

41987 => array( 'Name' => "White Balance",
                'Type' => "Lookup",
                0 => "Auto white balance",
                1 => "Manual white balance" ),

41988 => array( 'Name' => "Digital Zoom Ratio",
                'Type' => "Numeric",
                'Units' => " ( Zero = Digital Zoom Not Used )" ),

41989 => array( 'Name' => "Equivalent Focal Length In 35mm Film",
                'Type' => "Numeric",
                'Units' => "mm" ),

41990 => array( 'Name' => "Scene Capture Type",
                'Type' => "Lookup",
                0 => "Standard",
                1 => "Landscape",
                2 => "Portrait",
                3 => "Night scene" ),

41991 => array( 'Name' => "Gain Control",
                'Type' => "Lookup",
                0 => "None",
                1 => "Low gain up",
                2 => "High gain up",
                3 => "Low gain down",
                4 => "High gain down" ),

41992 => array( 'Name' => "Contrast",
                'Type' => "Lookup",
                0 => "Normal",
                1 => "Soft",
                2 => "Hard" ),

41993 => array( 'Name' => "Saturation",
                'Type' => "Lookup",
                0 => "Normal",
                1 => "Low saturation",
                2 => "High saturation" ),

41994 => array( 'Name' => "Sharpness",
                'Type' => "Lookup",
                0 => "Normal",
                1 => "Soft",
                2 => "Hard" ),

41995 => array( 'Name' => "Device Setting Description",
                'Type' => "Unknown" ),

41996 => array( 'Name' => "Subject Distance Range",
                'Type' => "Lookup",
                0 => "Unknown",
                1 => "Macro",
                2 => "Close view",
                3 => "Distant view" ),

42016 => array( 'Name' => "Image Unique ID",
                'Type' => "String" ),



//  11  => "ACDComment",
//  255 => "NewSubfileType"


),




/*****************************************************************************/
/*                                                                           */
/* Interoperability Tags                                                     */
/*                                                                           */
/*****************************************************************************/

"Interoperability" => array(

1 => array(     'Name' => "Interoperability Index",
                'Type' => "String" ),

2 => array(     'Name' => "Interoperability Version",
                'Type' => "String" ),

4096 => array(  'Name' => "Related Image File Format",
                'Type' => "String" ),

4097 => array(  'Name' => "Related Image File Width",
                'Type' => "Numeric",
                'Units' => "pixels" ),

4098 => array(  'Name' => "Related Image File Length",
                'Type' => "Numeric",
                'Units' => "pixels " )

),


/*****************************************************************************/
/*                                                                           */
/* GPS Tags                                                                  */
/*                                                                           */
/*****************************************************************************/

"GPS" => array(

0 => array(     'Name' => "GPS Tag Version",
                'Type' => "Numeric",
                'Units' => "(e.g.: 2.2.0.0 = Version 2.2 )" ),

1 => array(     'Name' => "North or South Latitude",
                'Type' => "String" ),

2 => array(     'Name' => "Latitude",
                'Type' => "Numeric",
                'Units' => "(Degrees Minutes Seconds North or South)" ),

3 => array(     'Name' => "East or West Longitude",
                'Type' => "String" ),

4 => array(     'Name' => "Longitude",
                'Type' => "Numeric",
                'Units' => "(Degrees Minutes Seconds East or West)" ),

5 => array(     'Name' => "Altitude Reference",
                'Type' => "Lookup",
                0 => "Sea Level",
                1 => "Sea level reference (negative value)" ),

6 => array(     'Name' => "Altitude",
                'Type' => "Numeric",
                'Units' => "Metres with respect to Altitude Reference" ),

7 => array(     'Name' => "GPS Time (atomic clock)",
                'Type' => "Numeric",
                'Units' => "(Hours Minutes Seconds)" ),

8 => array(     'Name' => "GPS Satellites used for Measurement",
                'Type' => "String" ),

9 => array(     'Name' => "GPS Receiver Status",
                'Type' => "Lookup",
                'A' => "Measurement in progress",          // Change: Fixed tag values as of version 1.11
                'V' => "Measurement Interoperability" ),

10 => array(    'Name' => "GPS Measurement Mode",
                'Type' => "Lookup",
                2 => "2-dimensional measurement",         // Change: Fixed tag values as of version 1.11
                3 => "3-dimensional measurement" ),

11 => array(    'Name' => "Measurement Precision",
                'Type' => "Numeric",
                'Units' => "(Data Degree of Precision, Horizontal for 2D, Position for 3D)" ),

12 => array(    'Name' => "Speed Unit",
                'Type' => "Lookup",
                'K' => "Kilometers per Hour",            // Change: Fixed tag values as of version 1.11
                'M' => "Miles per Hour",
                'N' => "Knots" ),

13 => array(    'Name' => "Speed of GPS receiver",
                'Type' => "Numeric",
                'Units' => "Speed Units" ),

14 => array(    'Name' => "Reference for direction of Movement",
                'Type' => "Lookup",                     // Change: Fixed tag values as of version 1.11
                'T' => "True North",
                'M' => "Magnetic North" ),

15 => array(    'Name' => "Direction of Movement",
                'Type' => "Numeric",
                'Units' => "Degrees relative to Movement Direction Reference" ),

16 => array(    'Name' => "Reference for Direction of Image",
                'Type' => "Lookup",
                'T' => "True North",                    // Change: Fixed tag values as of version 1.11
                'M' => "Magnetic North" ),

17 => array(    'Name' => "Direction of Image",
                'Type' => "Numeric",
                'Units' => "Degrees relative to Image Direction Reference" ),

18 => array(    'Name' => "Geodetic Survey Datum Used",
                'Type' => "String" ),

19 => array(    'Name' => "Destination - North or South Latitude",
                'Type' => "String" ),

20 => array(    'Name' => "Latitude of Destination",
                'Type' => "Numeric",
                'Units' => "(Degrees Minutes Seconds North or South)" ),

21 => array(    'Name' => "Destination - East or West Longitude",
                'Type' => "String" ),

22 => array(    'Name' => "Longitude of Destination",
                'Type' => "Numeric",
                'Units' => "(Degrees Minutes Seconds East or West)" ),

23 => array(    'Name' => "Reference for Bearing of Destination",
                'Type' => "Lookup",
                'T' => "True North",                    // Change: Fixed tag values as of version 1.11
                'M' => "Magnetic North" ),

24 => array(    'Name' => "Bearing of Destination",
                'Type' => "Numeric",
                'Units' => "Degrees relative to Destination Bearing Reference" ),

25 => array(    'Name' => "Units for Distance to Destination",
                'Type' => "Lookup",
                'K' => "Kilometres",                    // Change: Fixed tag values as of version 1.11
                'M' => "Miles",
                'N' => "Nautical Miles" ),

26 => array(    'Name' => "Distance to Destination",
                'Type' => "Numeric",
                'Units' => "Destination Distance Units" ),

27 => array(    'Name' => "Name of GPS Processing Method",
                'Type' => "Character Coded String" ),

28 => array(    'Name' => "Name of GPS Area",
                'Type' => "Character Coded String" ),

29 => array(    'Name' => "GPS Date",
                'Type' => "Numeric",
                'Units'=> " (Format: YYYY:MM:DD HH:mm:SS)" ),

30 => array(    'Name' => "GPS Differential Correction",
                'Type' => "Lookup",
                0 => "Measurement without differential correction",
                1 => "Differential correction applied" ),

),









/*****************************************************************************/
/*                                                                           */
/* META (App3) Tags                                                          */
/*                                                                           */
/*****************************************************************************/

"Meta" => array(


50000 => array( 'Name' => "CaptureDevice.FilmProductCode",
                'Type' => "Unknown" ),

50001 => array( 'Name' => "DigitalProcess.ImageSourceEK",
                'Type' => "Unknown" ),

50002 => array( 'Name' => "CaptureConditions.PAR",
                'Type' => "Unknown" ),

50003 => array( 'Name' => "CaptureDevice.CameraOwner.EK",
                'Type' => "Character Coded String" ),

50004 => array( 'Name' => "CaptureDevice.SerialNumber.Camera",
                'Type' => "Unknown" ),

50005 => array( 'Name' => "SceneContent.GroupCaption.UserSelectGroupTitle",
                'Type' => "Unknown" ),

50006 => array( 'Name' => "OutputOrder.Information.DealerIDNumber",
                'Type' => "Unknown" ),

50007 => array( 'Name' => "CaptureDevice.FID",
                'Type' => "Unknown" ),

50008 => array( 'Name' => "OutputOrder.Information.EnvelopeNumber",
                'Type' => "Unknown" ),

50009 => array( 'Name' => "OutputOrder.SimpleRenderInst.FrameNumber",
                'Type' => "Unknown" ),

50010 => array( 'Name' => "CaptureDevice.FilmCategory",
                'Type' => "Unknown" ),

50011 => array( 'Name' => "CaptureDevice.FilmGencode",
                'Type' => "Unknown" ),

50012 => array( 'Name' => "CaptureDevice.Scanner.ModelAndVersion",
                'Type' => "Unknown" ),

50013 => array( 'Name' => "CaptureDevice.FilmSize",
                'Type' => "Unknown" ),

50014 => array( 'Name' => "DigitalProcess.History.SBARGBShifts",
                'Type' => "Unknown" ),

50015 => array( 'Name' => "DigitalProcess.History.SBAInputImageColourspace",
                'Type' => "Unknown" ),

50016 => array( 'Name' => "DigitalProcess.History.SBAInputImageBitDepth",
                'Type' => "Unknown" ),

50017 => array( 'Name' => "DigitalProcess.History.SBAExposureRecord",
                'Type' => "Unknown" ),

50018 => array( 'Name' => "DigitalProcess.History.UserAdjSBARGBShifts",
                'Type' => "Unknown" ),

50019 => array( 'Name' => "DigitalProcess.ImageRotationStatus",
                'Type' => "Unknown" ),

50020 => array( 'Name' => "DigitalProcess.RollGuid.Elements",
                'Type' => "Unknown" ),

50021 => array( 'Name' => "ImageContainer.MetadataNumber",
                'Type' => "String" ),

50022 => array( 'Name' => "DigitalProcess.History.EditTagArray",
                'Type' => "Unknown" ),

50023 => array( 'Name' => "CaptureConditions.Magnification",
                'Type' => "Unknown" ),

50028 => array( 'Name' => "CaptureDevice.NativePhysicalXResolution",
                'Type' => "Unknown" ),

50029 => array( 'Name' => "CaptureDevice.NativePhysicalYResolution",
                'Type' => "Unknown" ),

50030 => array( 'Name' => "Kodak Special Effects IFD",
                'Type' => "SubIFD",
                'Tags Name' => "KodakSpecialEffects" ),

50031 => array( 'Name' => "Kodak Borders IFD",
                'Type' => "SubIFD",
                'Tags Name' => "KodakBorders" ),

50042 => array( 'Name' => "CaptureDevice.NativePhysicalResolutionUnit",
                'Type' => "Unknown" ),

50200 => array( 'Name' => "ImageContainer.SourceImageDirectory",
                'Type' => "Unknown" ),

50201 => array( 'Name' => "ImageContainer.SourceImageFileName",
                'Type' => "Unknown" ),

50202 => array( 'Name' => "ImageContainer.SourceImageVolumeName",
                'Type' => "Unknown" ),

50284 => array( 'Name' => "CaptureConditions.PrintQuantity",
                'Type' => "Unknown" ),

50286 => array( 'Name' => "DigitalProcess.ImagePrintStatus",
                'Type' => "Unknown" )

),



/*****************************************************************************/
/*                                                                           */
/* Kodak Special Effects IFD Tags                                            */
/*                                                                           */
/*****************************************************************************/

"KodakSpecialEffects" => array(

0 => array(     'Name' => "Digital Effects Version",
                'Type' => "Numeric" ),

1 => array(     'Name' => "Digital Effects Name",
                'Type' => "Character Coded String" ),

2 => array(     'Name' => "Digital Effects Type",
                'Type' => "Lookup",
                0 => "None Applied" )

),

/*****************************************************************************/
/*                                                                           */
/* Kodak Borders IFD Tags                                                    */
/*                                                                           */
/*****************************************************************************/

"KodakBorders" => array(

0 => array(     'Name' => "Borders Version",
                'Type' => "Numeric" ),

1 => array(     'Name' => "Border Name",
                'Type' => "Character Coded String" ),

2 => array(     'Name' => "Border ID",
                'Type' => "Numeric" ),

3 => array(     'Name' => "Border Location",
                'Type' => "Lookup" ),

4 => array(     'Name' => "Border Type",
                'Type' => "Lookup",
                0 => "None" ),

8 => array(     'Name' => "Watermark Type",
                'Type' => "Lookup",
                0 => "None" )

),

);

$GLOBALS['Makernote_Function_Array'] = array(   "Read_Makernote_Tag" => array( ),
                                                "get_Makernote_Text_Value" => array( ),
                                                "Interpret_Makernote_to_HTML" => array( ) );


// Include the Main TIFF and EXIF Tags array




/******************************************************************************
*
* Include the Makernote Scripts
*
******************************************************************************/

// Set the Makernotes Directory

$dir = dirname(__FILE__) . "/Makernotes/";      // Change: as of version 1.11 - to allow directory portability

// Open the directory
$dir_hnd = @opendir ( $dir );

// Cycle through each of the files in the Makernotes directory

while ( ( $file = readdir( $dir_hnd ) ) !== false )
{
        // Check if the current item is a file
        if ( is_file ( $dir . $file ) )
        {
                // Item is a file, break it into it's parts
                $path_parts = pathinfo( $dir . $file );

                // Check if the extension is php
                if ( $path_parts["extension"] == "php" )
                {
                        // This is a php script - include it
                        include_once ($dir . $file) ;
                }
        }
}
// close the directory
closedir( $dir_hnd );










/******************************************************************************
*
* Function:     Read_Makernote_Tag
*
* Description:  Attempts to decodes the Makernote tag supplied, returning the
*               new tag with the decoded information attached.
*
* Parameters:   Makernote_Tag - the element of an EXIF array containing the
*                               makernote, as returned from get_EXIF_JPEG
*               EXIF_Array - the entire EXIF array containing the
*                            makernote, as returned from get_EXIF_JPEG, in
*                            case more information is required for decoding
*               filehnd - an open file handle for the file containing the
*                         makernote - does not have to be positioned at the
*                         start of the makernote
*
*
* Returns:      Makernote_Tag - the Makernote_Tag from the parameters, but
*                               modified to contain the decoded information
*
******************************************************************************/

function Read_Makernote_Tag( $Makernote_Tag, $EXIF_Array, $filehnd )
{

        // Check if the Makernote is present but empty - this sometimes happens
        if ( ( strlen( $Makernote_Tag['Data'] ) === 0 ) ||
             ( $Makernote_Tag['Data'] === str_repeat ( "\x00", strlen( $Makernote_Tag['Data'] )) ) )
        {
                // Modify the makernote to display that it is empty
                $Makernote_Tag['Decoded Data'] = "Empty";
                $Makernote_Tag['Makernote Type'] = "Empty";
                $Makernote_Tag['Makernote Tags'] = "Empty";
                $Makernote_Tag['Decoded'] = TRUE;

                // Return the new makernote
                return $Makernote_Tag;
        }

        // Check if the Make Field exists in the TIFF IFD
        if ( array_key_exists ( 271, $EXIF_Array[0] ) )
        {
                // A Make tag exists in IFD0, collapse multiple strings (if any), and save result
                $Make_Field = implode ( "\n", $EXIF_Array[0][271]['Data']);
        }
        else
        {
                // No Make field found
                $Make_Field = "";
        }

        // Cycle through each of the "Read_Makernote_Tag" functions

        foreach( $GLOBALS['Makernote_Function_Array']['Read_Makernote_Tag'] as $func )
        {
                // Run the current function, and save the result
                $New_Makernote_Tag = $func( $Makernote_Tag, $EXIF_Array, $filehnd, $Make_Field );

                // Check if a valid result was returned
                if ( $New_Makernote_Tag !== FALSE )
                {
                        // A valid result was returned - stop cycling
                        break;
                }
        }

        // Check if a valid result was returned
        if ( $New_Makernote_Tag === false )
        {
                // A valid result was NOT returned - construct a makernote tag representing this
                $New_Makernote_Tag = $Makernote_Tag;
                $New_Makernote_Tag['Decoded'] = FALSE;
                $New_Makernote_Tag['Makernote Type'] = "Unknown Makernote";
        }

        // Return the new makernote tag
        return $New_Makernote_Tag;

}

/******************************************************************************
* End of Function:     Read_Makernote_Tag
******************************************************************************/









/******************************************************************************
*
* Function:     get_Makernote_Text_Value
*
* Description:  Attempts to provide a text value for any makernote tag marked
*               as type special. Returns false no handler could be found to
*               process the tag
*
* Parameters:   Exif_Tag - the element of an the Makernote array containing the
*                          tag in question, as returned from Read_Makernote_Tag
*               Tag_Definitions_Name - The name of the Tag Definitions group
*                                      within the global array IFD_Tag_Definitions
*
*
* Returns:      output - the text value for the tag
*               FALSE - If no handler could be found to process this tag, or if
*                       an error occured in decoding
*
******************************************************************************/

function get_Makernote_Text_Value( $Tag, $Tag_Definitions_Name )
{

        // Cycle through each of the "get_Makernote_Text_Value" functions

        foreach( $GLOBALS['Makernote_Function_Array']['get_Makernote_Text_Value'] as $func )
        {
                // Run the current function, and save the result
                $Text_Val = $func( $Tag, $Tag_Definitions_Name );

                // Check if a valid result was returned
                if ( $Text_Val !== FALSE )
                {
                        // valid result - return it
                        return $Text_Val;
                }
        }

        // No Special tag handler found for this tag - return false
        return FALSE;

}


/******************************************************************************
* End of Function:     get_Makernote_Text_Value
******************************************************************************/








/******************************************************************************
*
* Function:     Interpret_Makernote_to_HTML
*
* Description:  Attempts to interpret a makernote into html.
*
* Parameters:   Makernote_Tag - the element of an EXIF array containing the
*                               makernote, as returned from get_EXIF_JPEG
*               filename - the name of the JPEG file being processed ( used
*                          by scripts which display embedded thumbnails)
*
*
* Returns:      output - the html representing the makernote
*
******************************************************************************/

function Interpret_Makernote_to_HTML( $Makernote_tag, $filename )
{

        // Create a string to receive the HTML
        $output_str = "";

        // Check if the makernote tag is valid
        if ( $Makernote_tag === FALSE )
        {
                // No makernote info - return
                return $output_str;
        }


        // Check if the makernote has been marked as unknown
        if ( $Makernote_tag['Makernote Type'] == "Unknown Makernote" )
        {
                // Makernote is unknown - return message
                $output_str .= "<h4 class=\"EXIF_Makernote_Small_Heading\">Unknown Makernote Coding</h4>\n";
                return $output_str;
        }
        else
        {
                // Makernote is known - add a heading to the output
                $output_str .= "<p class=\"EXIF_Makernote_Text\">Makernote Coding: " . $Makernote_tag['Makernote Type'] . "</p>\n";
        }

        // Check if this is an empty makernote
        if ( $Makernote_tag['Makernote Type'] == "Empty" )
        {
                // It is empty - don't try to interpret
                return $output_str;
        }

        // Cycle through each of the "Interpret_Makernote_to_HTML" functions

        foreach( $GLOBALS['Makernote_Function_Array']['Interpret_Makernote_to_HTML'] as $func )
        {
                // Run the current function, and save the result
                $html_text = $func( $Makernote_tag, $filename );

                // Check if a valid result was returned
                if ( $html_text !== FALSE )
                {
                        // valid result - return it
                        return $output_str . $html_text;
                }
        }

        // No Interpreter function handled the makernote - return a message

        $output_str .= "<h4 class=\"EXIF_Makernote_Small_Heading\">Could not Decode Makernote, it may be corrupted or empty</h4>\n";

        return $output_str;


}


if ( !isset( $GLOBALS['HIDE_UNKNOWN_TAGS'] ) )     $GLOBALS['HIDE_UNKNOWN_TAGS']= FALSE;
if ( !isset( $GLOBALS['SHOW_BINARY_DATA_HEX'] ) )  $GLOBALS['SHOW_BINARY_DATA_HEX'] = FALSE;
if ( !isset( $GLOBALS['SHOW_BINARY_DATA_TEXT'] ) ) $GLOBALS['SHOW_BINARY_DATA_TEXT'] = FALSE;








/******************************************************************************
*
* Function:     get_EXIF_JPEG
*
* Description:  Retrieves information from a Exchangeable Image File Format (EXIF)
*               APP1 segment and returns it in an array.
*
* Parameters:   filename - the filename of the JPEG image to process
*
* Returns:      OutputArray - Array of EXIF records
*               FALSE - If an error occured in decoding
*
******************************************************************************/

function get_EXIF_JPEG( $filename )
{
        // Change: Added as of version 1.11
        // Check if a wrapper is being used - these are not currently supported (see notes at top of file)
        if ( ( stristr ( $filename, "http://" ) != FALSE ) || ( stristr ( $filename, "ftp://" ) != FALSE ) )
        {
                // A HTTP or FTP wrapper is being used - show a warning and abort
                echo "HTTP and FTP wrappers are currently not supported with EXIF - See EXIF functionality documentation - a local file must be specified<br>";
                echo "To work on an internet file, copy it locally to start with:<br><br>\n";
                echo "\$newfilename = tempnam ( \$dir, \"tmpexif\" );<br>\n";
                echo "copy ( \"http://whatever.com\", \$newfilename );<br><br>\n";
                return FALSE;
        }

        // get the JPEG headers
        $jpeg_header_data = get_jpeg_header_data( $filename );


        // Flag that an EXIF segment has not been found yet
        $EXIF_Location = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP1 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP1" ) == 0 )
                {
                        // And if it has the EXIF label,
                        if ( ( strncmp ( $jpeg_header_data[$i]['SegData'], "Exif\x00\x00", 6) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "Exif\x00\xFF", 6) == 0 ) )          // For some reason, some files have a faulty EXIF name which has a 0xFF in it
                        {
                                // Save the location of the EXIF segment
                                $EXIF_Location = $i;
                        }
                }

        }

        // Check if an EXIF segment was found
        if ( $EXIF_Location == -1 )
        {
                // Couldn't find any EXIF block to decode
                return FALSE;
        }

        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $filename</p>\n";
                return FALSE;
        }

        fseek( $filehnd, $jpeg_header_data[$EXIF_Location]['SegDataStart'] + 6  );

        // Decode the Exif segment into an array and return it
        $exif_data = process_TIFF_Header( $filehnd, "TIFF" );



        // Close File
        fclose($filehnd);
        return $exif_data;
}

/******************************************************************************
* End of Function:     get_EXIF_JPEG
******************************************************************************/



/******************************************************************************
*
* Function:     put_EXIF_JPEG
*
* Description:  Stores information into a Exchangeable Image File Format (EXIF)
*               APP1 segment from an EXIF array.
*
*               WARNING: Because the EXIF standard allows pointers to data
*               outside the APP1 segment, if there are any such pointers in
*               a makernote, this function will DAMAGE them since it will not
*               be aware that there is an external pointer. This will often
*               happen with Makernotes that include an embedded thumbnail.
*               This damage could be prevented where makernotes can be decoded,
*               but currently this is not implemented.
*
*
* Parameters:   exif_data - The array of EXIF data to insert into the JPEG header
*               jpeg_header_data - The JPEG header into which the EXIF data
*                                  should be stored, as from get_jpeg_header_data
*
* Returns:      jpeg_header_data - JPEG header array with the EXIF segment inserted
*               FALSE - If an error occured
*
******************************************************************************/

function put_EXIF_JPEG( $exif_data, $jpeg_header_data )
{
        // pack the EXIF data into its proper format for a JPEG file
        $packed_data = get_TIFF_Packed_Data( $exif_data );
        if ( $packed_data === FALSE )
        {
                return $jpeg_header_data;
        }

        $packed_data = "Exif\x00\x00$packed_data";

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP1 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP1" ) == 0 )
                {
                        // And if it has the EXIF label,
                        if ( ( strncmp ( $jpeg_header_data[$i]['SegData'], "Exif\x00\x00", 6) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "Exif\x00\xFF", 6) == 0 ) )          // For some reason, some files have a faulty EXIF name which has a 0xFF in it
                        {
                                // Found a preexisting EXIF block - Replace it with the new one and return.
                                $jpeg_header_data[$i]['SegData'] = $packed_data;
                                return $jpeg_header_data;
                        }
                }
        }

        // No preexisting segment segment found, insert a new one at the start of the header data.

        // Determine highest position of an APP segment at or below APP3, so we can put the
        // new APP3 at this position


        $highest_APP = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // Check if we have found an APP segment at or below APP3,
                if ( ( $jpeg_header_data[$i]['SegType'] >= 0xE0 ) && ( $jpeg_header_data[$i]['SegType'] <= 0xE3 ) )
                {
                        // Found an APP segment at or below APP12
                        $highest_APP = $i;
                }
        }

        // No preexisting EXIF block found, insert a new one at the start of the header data.
        array_splice($jpeg_header_data, $highest_APP + 1 , 0, array( array(   "SegType" => 0xE1,
                                                                              "SegName" => "APP1",
                                                                              "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE1 ],
                                                                              "SegData" => $packed_data ) ) );
        return $jpeg_header_data;

}

/******************************************************************************
* End of Function:     put_EXIF_JPEG
******************************************************************************/




/******************************************************************************
*
* Function:     get_Meta_JPEG
*
* Description:  Retrieves information from a Meta APP3 segment and returns it
*               in an array. Uses information supplied by the
*               get_jpeg_header_data function.
*               The Meta segment has the same format as an EXIF segment, but
*               uses different tags
*
* Parameters:   filename - the filename of the JPEG image to process
*
* Returns:      OutputArray - Array of Meta records
*               FALSE - If an error occured in decoding
*
******************************************************************************/

function get_Meta_JPEG( $filename )
{
        // Change: Added as of version 1.11
        // Check if a wrapper is being used - these are not currently supported (see notes at top of file)
        if ( ( stristr ( $filename, "http://" ) != FALSE ) || ( stristr ( $filename, "ftp://" ) != FALSE ) )
        {
                // A HTTP or FTP wrapper is being used - show a warning and abort
                echo "HTTP and FTP wrappers are currently not supported with Meta - See EXIF/Meta functionality documentation - a local file must be specified<br>";
                echo "To work on an internet file, copy it locally to start with:<br><br>\n";
                echo "\$newfilename = tempnam ( \$dir, \"tmpmeta\" );<br>\n";
                echo "copy ( \"http://whatever.com\", \$newfilename );<br><br>\n";
                return FALSE;
        }

        // get the JPEG headers
        $jpeg_header_data = get_jpeg_header_data( $filename );


        // Flag that an Meta segment has not been found yet
        $Meta_Location = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP3 header,
                if  ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP3" ) == 0 )
                {
                        // And if it has the Meta label,
                        if ( ( strncmp ( $jpeg_header_data[$i]['SegData'], "Meta\x00\x00", 6) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "META\x00\x00", 6) == 0 ) )
                        {
                                // Save the location of the Meta segment
                                $Meta_Location = $i;
                        }
                }
        }

        // Check if an EXIF segment was found
        if ( $Meta_Location == -1 )
        {
                // Couldn't find any Meta block to decode
                return FALSE;
        }


        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $filename</p>\n";
                return FALSE;
        }

        fseek( $filehnd, $jpeg_header_data[$Meta_Location]['SegDataStart'] + 6 );

        // Decode the Meta segment into an array and return it
        $meta = process_TIFF_Header( $filehnd, "Meta" );

         // Close File
        fclose($filehnd);

        return $meta;
}

/******************************************************************************
* End of Function:     get_Meta
******************************************************************************/







/******************************************************************************
*
* Function:     put_Meta_JPEG
*
* Description:  Stores information into a Meta APP3 segment from a Meta array.
*
*
*               WARNING: Because the Meta (EXIF) standard allows pointers to data
*               outside the APP1 segment, if there are any such pointers in
*               a makernote, this function will DAMAGE them since it will not
*               be aware that there is an external pointer. This will often
*               happen with Makernotes that include an embedded thumbnail.
*               This damage could be prevented where makernotes can be decoded,
*               but currently this is not implemented.
*
*
* Parameters:   meta_data - The array of Meta data to insert into the JPEG header
*               jpeg_header_data - The JPEG header into which the Meta data
*                                  should be stored, as from get_jpeg_header_data
*
* Returns:      jpeg_header_data - JPEG header array with the Meta segment inserted
*               FALSE - If an error occured
*
******************************************************************************/

function put_Meta_JPEG( $meta_data, $jpeg_header_data )
{
        // pack the Meta data into its proper format for a JPEG file
        $packed_data = get_TIFF_Packed_Data( $meta_data );
        if ( $packed_data === FALSE )
        {
                return $jpeg_header_data;
        }

        $packed_data = "Meta\x00\x00$packed_data";

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // If we find an APP1 header,
                if ( strcmp ( $jpeg_header_data[$i]['SegName'], "APP3" ) == 0 )
                {
                        // And if it has the Meta label,
                        if ( ( strncmp ( $jpeg_header_data[$i]['SegData'], "Meta\x00\x00", 6) == 0 ) ||
                             ( strncmp ( $jpeg_header_data[$i]['SegData'], "META\x00\x00", 6) == 0 ) )
                        {
                                // Found a preexisting Meta block - Replace it with the new one and return.
                                $jpeg_header_data[$i]['SegData'] = $packed_data;
                                return $jpeg_header_data;
                        }
                }
        }
        // No preexisting segment segment found, insert a new one at the start of the header data.

        // Determine highest position of an APP segment at or below APP3, so we can put the
        // new APP3 at this position


        $highest_APP = -1;

        //Cycle through the header segments
        for( $i = 0; $i < count( $jpeg_header_data ); $i++ )
        {
                // Check if we have found an APP segment at or below APP3,
                if ( ( $jpeg_header_data[$i]['SegType'] >= 0xE0 ) && ( $jpeg_header_data[$i]['SegType'] <= 0xE3 ) )
                {
                        // Found an APP segment at or below APP12
                        $highest_APP = $i;
                }
        }

        // No preexisting Meta block found, insert a new one at the start of the header data.
        array_splice($jpeg_header_data, $highest_APP + 1 , 0, array( array(     "SegType" => 0xE3,
                                                                                "SegName" => "APP3",
                                                                                "SegDesc" => $GLOBALS[ "JPEG_Segment_Descriptions" ][ 0xE1 ],
                                                                                "SegData" => $packed_data ) ) );
        return $jpeg_header_data;

}

/******************************************************************************
* End of Function:     put_Meta_JPEG
******************************************************************************/



/******************************************************************************
*
* Function:     get_EXIF_TIFF
*
* Description:  Retrieves information from a Exchangeable Image File Format (EXIF)
*               within a TIFF file and returns it in an array.
*
* Parameters:   filename - the filename of the TIFF image to process
*
* Returns:      OutputArray - Array of EXIF records
*               FALSE - If an error occured in decoding
*
******************************************************************************/

function get_EXIF_TIFF( $filename )
{
        // Change: Added as of version 1.11
        // Check if a wrapper is being used - these are not currently supported (see notes at top of file)
        if ( ( stristr ( $filename, "http://" ) != FALSE ) || ( stristr ( $filename, "ftp://" ) != FALSE ) )
        {
                // A HTTP or FTP wrapper is being used - show a warning and abort
                echo "HTTP and FTP wrappers are currently not supported with TIFF - See EXIF/TIFF functionality documentation - a local file must be specified<br>";
                echo "To work on an internet file, copy it locally to start with:<br><br>\n";
                echo "\$newfilename = tempnam ( \$dir, \"tmptiff\" );<br>\n";
                echo "copy ( \"http://whatever.com\", \$newfilename );<br><br>\n";
                return FALSE;
        }


        $filehnd = @fopen($filename, 'rb');

        // Check if the file opened successfully
        if ( ! $filehnd  )
        {
                // Could't open the file - exit
                echo "<p>Could not open file $filename</p>\n";
                return FALSE;
        }

        // Decode the Exif segment into an array and return it
        $exif_data = process_TIFF_Header( $filehnd, "TIFF" );

        // Close File
        fclose($filehnd);
        return $exif_data;
}

/******************************************************************************
* End of Function:     get_EXIF_TIFF
******************************************************************************/




/******************************************************************************
*
* Function:     Interpret_EXIF_to_HTML
*
* Description:  Generates html detailing the contents an APP1 EXIF array
*               which was retrieved with a get_EXIF_.... function.
*               Can also be used for APP3 Meta arrays.
*
* Parameters:   Exif_array - the EXIF array,as read from get_EXIF_....
*               filename - the name of the Image file being processed ( used
*                          by scripts which displays EXIF thumbnails)
*
* Returns:      output_str - A string containing the HTML
*
******************************************************************************/

function Interpret_EXIF_to_HTML( $Exif_array, $filename )
{
        // Create the string to receive the html output
        $output_str = "";

        // Check if the array to process is valid
        if ( $Exif_array === FALSE )
        {
                // Exif Array is not valid - abort processing
                return $output_str;
        }

        // Ouput the heading according to what type of tags were used in processing
        if ( $Exif_array[ 'Tags Name' ] == "TIFF" )
        {
                $output_str .= "<h2 class=\"EXIF_Main_Heading\">Contains Exchangeable Image File Format (EXIF) Information</h2>\n";
        }
        else if ( $Exif_array[ 'Tags Name' ] == "Meta" )
        {
                $output_str .= "<h2 class=\"EXIF_Main_Heading\">Contains META Information (APP3)</h2>\n";
        }
        else
        {
                $output_str .= "<h2 class=\"EXIF_Main_Heading\">Contains " . $Exif_array[ 'Tags Name' ] . " Information</h2>\n";
        }


        // Check that there are actually items to process in the array
        if ( count( $Exif_array ) < 1 )
        {
                // No items to process in array - abort processing
                return $output_str;
        }

        // Output secondary heading
        $output_str .= "<h3 class=\"EXIF_Secondary_Heading\">Main Image Information</h2>\n";

        // Interpret the zeroth IFD to html
        $output_str .= interpret_IFD( $Exif_array[0], $filename, $Exif_array['Byte_Align'] );

        // Check if there is a first IFD to process
        if ( array_key_exists( 1, $Exif_array ) )
        {
                // There is a first IFD for a thumbnail
                // Add a heading for it to the output
                $output_str .= "<h3 class=\"EXIF_Secondary_Heading\">Thumbnail Information</h2>\n";

                // Interpret the IFD to html and add it to the output
                $output_str .= interpret_IFD( $Exif_array[1], $filename, $Exif_array['Byte_Align'] );
        }

        // Cycle through any other IFD's
        $i = 2;
        while ( array_key_exists( $i, $Exif_array ) )
        {
                // Add a heading for the IFD
                $output_str .= "<h3  class=\"EXIF_Secondary_Heading\">Image File Directory (IFD) $i Information</h2>\n";

                // Interpret the IFD to html and add it to the output
                $output_str .= interpret_IFD( $Exif_array[$i], $filename, $Exif_array['Byte_Align'] );
                $i++;
        }

        // Return the resulting HTML
        return $output_str;
}

/******************************************************************************
* End of Function:     Interpret_EXIF_to_HTML
******************************************************************************/
















/******************************************************************************
*
*         INTERNAL FUNCTIONS
*
******************************************************************************/











/******************************************************************************
*
* Internal Function:     get_TIFF_Packed_Data
*
* Description:  Packs TIFF IFD data from EXIF or Meta into a form ready for
*               either a JPEG EXIF/Meta segment or a TIFF file
*               This function attempts to protect the contents of an EXIF makernote,
*               by ensuring that it remains in the same position relative to the
*               TIFF header
*
* Parameters:   tiff_data - the EXIF array,as read from get_EXIF_JPEG or get_Meta_JPEG
*
* Returns:      packed_data - A string containing packed segment
*
******************************************************************************/

function get_TIFF_Packed_Data( $tiff_data )
{
        // Check that the segment is valid
        if ( $tiff_data === FALSE )
        {
                return FALSE;
        }

        // Get the byte alignment
        $Byte_Align = $tiff_data['Byte_Align'];

        // Add the Byte Alignment to the Packed data
        $packed_data = $Byte_Align;

        // Add the TIFF ID to the Packed Data
        $packed_data .= put_IFD_Data_Type( 42, 3, $Byte_Align );

        // Create a string for the makernote
        $makernote = "";

        // Check if the makernote exists
        if ( $tiff_data[ 'Makernote_Tag' ] !== FALSE )
        {
                // A makernote exists - We need to ensure that it stays in the same position as it was
                // Put the Makernote before any of the IFD's by padding zeros to the correct offset
                $makernote .= str_repeat("\x00",( $tiff_data[ 'Makernote_Tag' ][ 'Offset' ] - 8 ) );
                $makernote .= $tiff_data[ 'Makernote_Tag' ]['Data'];
        }

        // Calculage where the zeroth ifd will be
        $ifd_offset = strlen( $makernote ) + 8;

        // Add the Zeroth IFD pointer to the packed data
        $packed_data .= put_IFD_Data_Type( $ifd_offset, 4, $Byte_Align );

        // Add the makernote to the packed data (if there was one)
        $packed_data .= $makernote;

        //Add the IFD's to the packed data
        $packed_data .= get_IFD_Array_Packed_Data( $tiff_data, $ifd_offset, $Byte_Align );

        // Return the result
        return $packed_data;
}

/******************************************************************************
* End of Function:     get_TIFF_Packed_Data
******************************************************************************/




/******************************************************************************
*
* Internal Function:     get_IFD_Array_Packed_Data
*
* Description:  Packs a chain of IFD's from EXIF or Meta segments into a form
*               ready for either a JPEG EXIF/Meta segment or a TIFF file
*
* Parameters:   ifd_data - the IFD chain array, as read from get_EXIF_JPEG or get_Meta_JPEG
*               Zero_IFD_offset - The offset to the first IFD from the start of the TIFF header
*               Byte_Align - the Byte alignment to use - "MM" or "II"
*
* Returns:      packed_data - A string containing packed IFD's
*
******************************************************************************/

function get_IFD_Array_Packed_Data( $ifd_data, $Zero_IFD_offset, $Byte_Align )
{
        // Create a string to receive the packed output
        $packed_data = "";

        // Count the IFDs
        $ifd_count = 0;
        foreach( $ifd_data as $key => $IFD )
        {
                // Make sure we only count the IFD's, not other information keys
                if ( is_numeric( $key ) )
                {
                        $ifd_count++;
                }
        }


        // Cycle through each IFD,
        for ( $ifdno = 0; $ifdno < $ifd_count; $ifdno++ )
        {
                // Check if this IFD is the last one
                if ( $ifdno == $ifd_count - 1 )
                {
                        // This IFD is the last one, get it's packed data
                        $packed_data .= get_IFD_Packed_Data( $ifd_data[ $ifdno ], $Zero_IFD_offset +strlen($packed_data), $Byte_Align, FALSE );
                }
                else
                {
                        // This IFD is NOT the last one, get it's packed data
                        $packed_data .= get_IFD_Packed_Data( $ifd_data[ $ifdno ], $Zero_IFD_offset +strlen($packed_data), $Byte_Align, TRUE );
                }

        }

        // Return the packed output
        return $packed_data;
}

/******************************************************************************
* End of Function:     get_IFD_Array_Packed_Data
******************************************************************************/



/******************************************************************************
*
* Internal Function:     get_IFD_Packed_Data
*
* Description:  Packs an IFD from EXIF or Meta segments into a form
*               ready for either a JPEG EXIF/Meta segment or a TIFF file
*
* Parameters:   ifd_data - the IFD chain array, as read from get_EXIF_JPEG or get_Meta_JPEG
*               IFD_offset - The offset to the IFD from the start of the TIFF header
*               Byte_Align - the Byte alignment to use - "MM" or "II"
*               Another_IFD - boolean - false if this is the last IFD in the chain
*                                     - true if it is not the last
*
* Returns:      packed_data - A string containing packed IFD's
*
******************************************************************************/

function get_IFD_Packed_Data( $ifd_data, $IFD_offset, $Byte_Align, $Another_IFD )
{

        $ifd_body_str = "";
        $ifd_data_str = "";

        $Tag_Definitions_Name = $ifd_data[ 'Tags Name' ];


        // Count the Tags in this IFD
        $tag_count = 0;
        foreach( $ifd_data as $key => $tag )
        {
                // Make sure we only count the Tags, not other information keys
                if ( is_numeric( $key ) )
                {
                        $tag_count++;
                }
        }

        // Add the Tag count to the packed data
        $packed_data = put_IFD_Data_Type( $tag_count, 3, $Byte_Align );

        // Calculate the total length of the IFD (without the offset data)
        $IFD_len = 2 + $tag_count * 12 + 4;


        // Cycle through each tag
        foreach( $ifd_data as $key => $tag )
        {
                // Make sure this is a tag, not another information key
                if ( is_numeric( $key ) )
                {

                        // Add the tag number to the packed data
                        $ifd_body_str .= put_IFD_Data_Type( $tag[ 'Tag Number' ], 3, $Byte_Align );

                        // Add the Data type to the packed data
                        $ifd_body_str .= put_IFD_Data_Type( $tag['Data Type'], 3, $Byte_Align );

                        // Check if this is a Print Image Matching entry
                        if ( $tag['Type'] == "PIM" )
                        {
                                // This is a Print Image Matching entry,
                                // encode it
                                $data = Encode_PIM( $tag, $Byte_Align );
                        }
                                // Check if this is a IPTC/NAA Record within the EXIF IFD
                        else if ( ( ( $Tag_Definitions_Name == "EXIF" ) || ( $Tag_Definitions_Name == "TIFF" ) ) &&
                                  ( $tag[ 'Tag Number' ] == 33723 ) )
                        {
                                // This is a IPTC/NAA Record, encode it
                                $data = put_IPTC( $tag['Data'] );
                        }
                                // Change: Check for embedded XMP as of version 1.11
                                // Check if this is a XMP Record within the EXIF IFD
                        else if ( ( ( $Tag_Definitions_Name == "EXIF" ) || ( $Tag_Definitions_Name == "TIFF" ) ) &&
                                  ( $tag[ 'Tag Number' ] == 700 ) )
                        {
                                // This is a XMP Record, encode it
                                $data = write_XMP_array_to_text( $tag['Data'] );
                        }
                                // Change: Check for embedded IRB as of version 1.11
                                // Check if this is a Photoshop IRB Record within the EXIF IFD
                        else if ( ( ( $Tag_Definitions_Name == "EXIF" ) || ( $Tag_Definitions_Name == "TIFF" ) ) &&
                                  ( $tag[ 'Tag Number' ] == 34377 ) )
                        {
                                // This is a Photoshop IRB Record, encode it
                                $data = pack_Photoshop_IRB_Data( $tag['Data'] );
                        }
                                // Exif Thumbnail Offset
                        else if ( ( $tag[ 'Tag Number' ] == 513 ) && ( $Tag_Definitions_Name == "TIFF" ) )
                        {
                                        // The Exif Thumbnail Offset is a pointer but of type Long, not Unknown
                                        // Hence we need to put the data into the packed string separately
                                        // Calculate the thumbnail offset
                                        $data_offset = $IFD_offset + $IFD_len + strlen($ifd_data_str);

                                        // Create the Offset for the IFD
                                        $data = put_IFD_Data_Type( $data_offset, 4, $Byte_Align );

                                        // Store the thumbnail
                                        $ifd_data_str .= $tag['Data'];
                        }
                                // Exif Thumbnail Length
                        else if ( ( $tag[ 'Tag Number' ] == 514 ) && ( $Tag_Definitions_Name == "TIFF" ) )
                        {
                                        // Encode the Thumbnail Length
                                        $data = put_IFD_Data_Type( strlen($ifd_data[513]['Data']), 4, $Byte_Align );
                        }
                                // Sub-IFD
                        else if ( $tag['Type'] == "SubIFD" )
                        {
                                        // This is a Sub-IFD
                                        // Calculate the offset to the start of the Sub-IFD
                                        $data_offset = $IFD_offset + $IFD_len + strlen($ifd_data_str);
                                        // Get the packed data for the IFD chain as the data for this tag
                                        $data = get_IFD_Array_Packed_Data( $tag['Data'], $data_offset, $Byte_Align );
                        }
                        else
                        {
                                // Not a special tag

                                // Create a string to receive the data
                                $data = "";

                                // Check if this is a type Unknown tag
                                if ( $tag['Data Type'] != 7 )
                                {
                                        // NOT type Unknown
                                        // Cycle through each data value and add it to the data string
                                        foreach( $tag[ 'Data' ] as $data_val )
                                        {
                                                $data .= put_IFD_Data_Type( $data_val, $tag['Data Type'], $Byte_Align );
                                        }
                                }
                                else
                                {
                                        // This is a type Unknown - just add the data as is to the data string
                                        $data .= $tag[ 'Data' ];
                                }
                        }

                        // Pad the data string out to at least 4 bytes
                        $data = str_pad ( $data, 4, "\x00" );


                        // Check if the data type is an ASCII String or type Unknown
                        if ( ( $tag['Data Type'] == 2 ) || ( $tag['Data Type'] == 7 ) )
                        {
                                // This is an ASCII String or type Unknown
                                // Add the Length of the string to the packed data as the Count
                                $ifd_body_str .= put_IFD_Data_Type( strlen($data), 4, $Byte_Align );
                        }
                        else
                        {
                                // Add the array count to the packed data as the Count
                                $ifd_body_str .= put_IFD_Data_Type( count($tag[ 'Data' ]), 4, $Byte_Align );
                        }


                        // Check if the data is over 4 bytes long
                        if ( strlen( $data ) > 4 )
                        {
                                // Data is longer than 4 bytes - it needs to be offset
                                // Check if this entry is the Maker Note
                                if ( ( $Tag_Definitions_Name == "EXIF" ) && ( $tag[ 'Tag Number' ] == 37500 ) )
                                {
                                        // This is the makernote - It will have already been stored
                                        // at its original offset to help preserve it
                                        // all we need to do is add the Offset to the IFD packed data
                                        $data_offset = $tag[ 'Offset' ];

                                        $ifd_body_str .= put_IFD_Data_Type( $data_offset, 4, $Byte_Align );
                                }
                                else
                                {
                                        // This is NOT the makernote
                                        // Calculate the data offset
                                        $data_offset = $IFD_offset + $IFD_len + strlen($ifd_data_str);

                                        // Add the offset to the IFD packed data
                                        $ifd_body_str .= put_IFD_Data_Type( $data_offset, 4, $Byte_Align );

                                        // Add the data to the offset packed data
                                        $ifd_data_str .= $data;
                                }
                        }
                        else
                        {
                                // Data is less than or equal to 4 bytes - Add it to the packed IFD data as is
                                $ifd_body_str .= $data;
                        }

                }
        }

        // Assemble the IFD body onto the packed data
        $packed_data .= $ifd_body_str;

        // Check if there is another IFD after this one
        if( $Another_IFD === TRUE )
        {
                // There is another IFD after this
                // Calculate the Next-IFD offset so that it goes immediately after this IFD
                $next_ifd_offset = $IFD_offset + $IFD_len + strlen($ifd_data_str);
        }
        else
        {
                // There is NO IFD after this - indicate with offset=0
                $next_ifd_offset = 0;
        }

        // Add the Next-IFD offset to the packed data
        $packed_data .= put_IFD_Data_Type( $next_ifd_offset, 4, $Byte_Align );

        // Add the offset data to the packed data
        $packed_data .= $ifd_data_str;

        // Return the resulting packed data
        return $packed_data;
}

/******************************************************************************
* End of Function:     get_IFD_Packed_Data
******************************************************************************/





/******************************************************************************
*
* Internal Function:     process_TIFF_Header
*
* Description:  Decodes the information stored in a TIFF header and it's
*               Image File Directories (IFD's). This information is returned
*               in an array
*
* Parameters:   filehnd - The handle of a open image file, positioned at the
*                          start of the TIFF header
*               Tag_Definitions_Name - The name of the Tag Definitions group
*                                      within the global array IFD_Tag_Definitions
*
*
* Returns:      OutputArray - Array of IFD records
*               FALSE - If an error occured in decoding
*
******************************************************************************/

function process_TIFF_Header( $filehnd, $Tag_Definitions_Name )
{


        // Save the file position where the TIFF header starts, as offsets are relative to this position
        $Tiff_start_pos = ftell( $filehnd );



        // Read the eight bytes of the TIFF header
        $DataStr = network_safe_fread( $filehnd, 8 );

        // Check that we did get all eight bytes
        if ( strlen( $DataStr ) != 8 )
        {
                return FALSE;   // Couldn't read the TIFF header properly
        }

        $pos = 0;
        // First two bytes indicate the byte alignment - should be 'II' or 'MM'
        // II = Intel (LSB first, MSB last - Little Endian)
        // MM = Motorola (MSB first, LSB last - Big Endian)
        $Byte_Align = substr( $DataStr, $pos, 2 );



        // Check the Byte Align Characters for validity
        if ( ( $Byte_Align != "II" ) && ( $Byte_Align != "MM" ) )
        {
                // Byte align field is invalid - we won't be able to decode file
                return FALSE;
        }

        // Skip over the Byte Align field which was just read
        $pos += 2;

        // Next two bytes are TIFF ID - should be value 42 with the appropriate byte alignment
        $TIFF_ID = substr( $DataStr, $pos, 2 );

        if ( get_IFD_Data_Type( $TIFF_ID, 3, $Byte_Align ) != 42 )
        {
                // TIFF header ID not found
                return FALSE;
        }

        // Skip over the TIFF ID field which was just read
        $pos += 2;


        // Next four bytes are the offset to the first IFD
        $offset_str = substr( $DataStr, $pos, 4 );
        $offset = get_IFD_Data_Type( $offset_str, 4, $Byte_Align );

        // Done reading TIFF Header


        // Move to first IFD

        if ( fseek( $filehnd, $Tiff_start_pos + $offset ) !== 0 )
        {
                // Error seeking to position of first IFD
                return FALSE;
        }



        // Flag that a makernote has not been found yet
        $GLOBALS[ "Maker_Note_Tag" ] = FALSE;

        // Read the IFD chain into an array
        $Output_Array = read_Multiple_IFDs( $filehnd, $Tiff_start_pos, $Byte_Align, $Tag_Definitions_Name );

        // Check if a makernote was found
        if ( $GLOBALS[ "Maker_Note_Tag" ] != FALSE )
        {
                // Makernote was found - Process it
                // The makernote needs to be processed after all other
                // tags as it may require some of the other tags in order
                // to be processed properly
                $GLOBALS[ "Maker_Note_Tag" ] = Read_Makernote_Tag( $GLOBALS[ "Maker_Note_Tag" ], $Output_Array, $filehnd );

        }

        $Output_Array[ 'Makernote_Tag' ] = $GLOBALS[ "Maker_Note_Tag" ];

        // Save the Name of the Tags used in the output array
        $Output_Array[ 'Tags Name' ] = $Tag_Definitions_Name;



        // Save the Byte alignment
        $Output_Array['Byte_Align'] = $Byte_Align;


        // Return the output array
        return $Output_Array ;
}

/******************************************************************************
* End of Function:     process_TIFF_Header
******************************************************************************/






/******************************************************************************
*
* Internal Function:     read_Multiple_IFDs
*
* Description:  Reads and interprets a chain of standard Image File Directories (IFD's),
*               and returns the entries in an array. This chain is made up from IFD's
*               which have a pointer to the next IFD. IFD's are read until the next
*               pointer indicates there are no more
*
* Parameters:   filehnd - a handle for the image file being read, positioned at the
*                         start of the IFD chain
*               Tiff_offset - The offset of the TIFF header from the start of the file
*               Byte_Align - either "MM" or "II" indicating Motorola or Intel Byte alignment
*               Tag_Definitions_Name - The name of the Tag Definitions group within the global array IFD_Tag_Definitions
*               local_offsets - True indicates that offset data should be interpreted as being relative to the start of the currrent entry
*                               False (normal) indicates offests are relative to start of Tiff header as per IFD standard
*               read_next_ptr - True (normal) indicates that a pointer to the next IFD should be read at the end of the IFD
*                               False indicates that no pointer follows the IFD
*
*
* Returns:      OutputArray - Array of IFD entries
*
******************************************************************************/

function read_Multiple_IFDs( $filehnd, $Tiff_offset, $Byte_Align, $Tag_Definitions_Name, $local_offsets = FALSE, $read_next_ptr = TRUE )
{
        // Start at the offset of the first IFD
        $Next_Offset = 0;

        do
        {
                // Read an IFD
                list($IFD_Array , $Next_Offset) = read_IFD_universal( $filehnd, $Tiff_offset, $Byte_Align, $Tag_Definitions_Name, $local_offsets, $read_next_ptr );

                // Move to the position of the next IFD
                if ( fseek( $filehnd, $Tiff_offset + $Next_Offset ) !== 0 )
                {
                        // Error seeking to position of next IFD
                        echo "<p>Error: Corrupted EXIF</p>\n";
                        return FALSE;
                }

                $Output_Array[] = $IFD_Array;


        } while ( $Next_Offset != 0 );      // Until the Next IFD Offset is zero


        // return resulting array

        return $Output_Array ;
}

/******************************************************************************
* End of Function:     read_Multiple_IFDs
******************************************************************************/







/******************************************************************************
*
* Internal Function:     read_IFD_universal
*
* Description:  Reads and interprets a standard or Non-standard Image File
*               Directory (IFD), and returns the entries in an array
*
* Parameters:   filehnd - a handle for the image file being read, positioned at the start
*                         of the IFD
*               Tiff_offset - The offset of the TIFF header from the start of the file
*               Byte_Align - either "MM" or "II" indicating Motorola or Intel Byte alignment
*               Tag_Definitions_Name - The name of the Tag Definitions group within the global array IFD_Tag_Definitions
*               local_offsets - True indicates that offset data should be interpreted as being relative to the start of the currrent entry
*                               False (normal) indicates offests are relative to start of Tiff header as per IFD standard
*               read_next_ptr - True (normal) indicates that a pointer to the next IFD should be read at the end of the IFD
*                               False indicates that no pointer follows the IFD
*
* Returns:      OutputArray - Array of IFD entries
*               Next_Offset - Offset to next IFD (zero = no next IFD)
*
******************************************************************************/

function read_IFD_universal( $filehnd, $Tiff_offset, $Byte_Align, $Tag_Definitions_Name, $local_offsets = FALSE, $read_next_ptr = TRUE )
{
        if ( ( $filehnd == NULL ) || ( feof( $filehnd ) ) )
        {
                return array (FALSE , 0);
        }

        // Record the Name of the Tag Group used for this IFD in the output array
        $OutputArray[ 'Tags Name' ] = $Tag_Definitions_Name;

        // Record the offset of the TIFF header in the output array
        $OutputArray[ 'Tiff Offset' ] = $Tiff_offset;

        // First 2 bytes of IFD are number of entries in the IFD
        $No_Entries_str = network_safe_fread( $filehnd, 2 );
        $No_Entries = get_IFD_Data_Type( $No_Entries_str, 3, $Byte_Align );


        // If the data is corrupt, the number of entries may be huge, which will cause errors
        // This is often caused by a lack of a Next-IFD pointer
        if ( $No_Entries> 10000 )
        {
                // Huge number of entries - abort
                echo "<p>Error: huge number of EXIF entries - EXIF is probably Corrupted</p>\n";

                return array ( FALSE , 0);
        }

        // If the data is corrupt or just stupid, the number of entries may zero,
        // Indicate this by returning false
        if ( $No_Entries === 0 )
        {
                // No entries - abort
                return array ( FALSE , 0);
        }

        // Save the file position where first IFD record starts as non-standard offsets
        // need to know this to calculate an absolute offset
        $IFD_first_rec_pos = ftell( $filehnd );


        // Read in the IFD structure
        $IFD_Data = network_safe_fread( $filehnd, 12 * $No_Entries );

        // Check if the entire IFD was able to be read
        if ( strlen( $IFD_Data ) != (12 * $No_Entries) )
        {
                // Couldn't read the IFD Data properly, Some Casio files have no Next IFD pointer, hence cause this error
                echo "<p>Error: EXIF Corrupted</p>\n";
                return array(FALSE, 0);
        }


        // Last 4 bytes of a standard IFD are the offset to the next IFD
        // Some NON-Standard IFD implementations do not have this, hence causing problems if it is read

        // If the Next IFD pointer has been requested to be read,
        if ( $read_next_ptr )
        {
                // Read the pointer to the next IFD

                $Next_Offset_str = network_safe_fread( $filehnd, 4 );
                $Next_Offset = get_IFD_Data_Type( $Next_Offset_str, 4, $Byte_Align );
        }
        else
        {
                // Otherwise set the pointer to zero ( no next IFD )
                $Next_Offset = 0;
        }



        // Initialise current position to the start
        $pos = 0;


        // Loop for reading IFD entries

        for ( $i = 0; $i < $No_Entries; $i++ )
        {
                // First 2 bytes of IFD entry are the tag number ( Unsigned Short )
                $Tag_No_str = substr( $IFD_Data, $pos, 2 );
                $Tag_No = get_IFD_Data_Type( $Tag_No_str, 3, $Byte_Align );
                $pos += 2;

                // Next 2 bytes of IFD entry are the data format ( Unsigned Short )
                $Data_Type_str = substr( $IFD_Data, $pos, 2 );
                $Data_Type = get_IFD_Data_Type( $Data_Type_str, 3, $Byte_Align );
                $pos += 2;

                // If Datatype is not between 1 and 12, then skip this entry, it is probably corrupted or custom
                if (( $Data_Type > 12 ) || ( $Data_Type < 1 ) )
                {
                        $pos += 8;
                        continue 1;  // Stop trying to process the tag any further and skip to the next one
                }

                // Next 4 bytes of IFD entry are the data count ( Unsigned Long )
                $Data_Count_str = substr( $IFD_Data, $pos, 4 );
                $Data_Count = get_IFD_Data_Type( $Data_Count_str, 4, $Byte_Align );
                $pos += 4;

                if ( $Data_Count > 100000 )
                {
                        echo "<p>Error: huge EXIF data count - EXIF is probably Corrupted</p>\n";

                        // Some Casio files have no Next IFD pointer, hence cause errors

                        return array ( FALSE , 0);
                }

                // Total Data size is the Data Count multiplied by the size of the Data Type
                $Total_Data_Size = $GLOBALS['IFD_Data_Sizes'][ $Data_Type ] * $Data_Count;

                $Data_Start_pos = -1;

                // If the total data size is larger than 4 bytes, then the data part is the offset to the real data
                if ( $Total_Data_Size > 4 )
                {
                        // Not enough room for data - offset provided instead
                        $Data_Offset_str = substr( $IFD_Data, $pos, 4 );
                        $Data_Start_pos = get_IFD_Data_Type( $Data_Offset_str, 4, $Byte_Align );


                        // In some NON-STANDARD makernotes, the offset is relative to the start of the current IFD entry
                        if ( $local_offsets )
                        {
                                // This is a NON-Standard IFD, seek relative to the start of the current tag
                                fseek( $filehnd, $IFD_first_rec_pos +  $pos - 8 + $Data_Start_pos );
                        }
                        else
                        {
                                // This is a normal IFD, seek relative to the start of the TIFF header
                                fseek( $filehnd, $Tiff_offset + $Data_Start_pos );
                        }

                        // Read the data block from the offset position
                        $DataStr = network_safe_fread( $filehnd, $Total_Data_Size );
                }
                else
                {
                        // The data block is less than 4 bytes, and is provided in the IFD entry, so read it
                        $DataStr = substr( $IFD_Data, $pos, $Total_Data_Size );
                }

                // Increment the position past the data
                $pos += 4;


                // Now create the entry for output array

                $Data_Array = array( );


                // Read the data items from the data block

                if ( ( $Data_Type != 2 ) && ( $Data_Type != 7 ) )
                {
                        // The data type is Numerical, Read the data items from the data block
                        for ( $j = 0; $j < $Data_Count; $j++ )
                        {
                                $Part_Data_Str = substr( $DataStr, $j * $GLOBALS['IFD_Data_Sizes'][ $Data_Type ], $GLOBALS['IFD_Data_Sizes'][ $Data_Type ] );
                                $Data_Array[] = get_IFD_Data_Type( $Part_Data_Str, $Data_Type, $Byte_Align );
                        }
                }
                elseif ( $Data_Type == 2 )
                {
                        // The data type is String(s)   (type 2)

                        // Strip the last terminating Null
                        $DataStr = substr( $DataStr, 0, strlen($DataStr)-1 );

                        // Split the data block into multiple strings whereever there is a Null
                        $Data_Array = explode( "\x00", $DataStr );
                }
                else
                {
                        // The data type is Unknown (type 7)
                        // Do nothing to data
                        $Data_Array = $DataStr;
                }


                // If this is a Sub-IFD entry,
                if ( ( array_key_exists( $Tag_No, $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name] ) ) &&
                     ( "SubIFD" == $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ]['Type'] ) )
                {
                        // This is a Sub-IFD entry, go and process the data forming Sub-IFD and use its output array as the new data for this entry
                        fseek( $filehnd, $Tiff_offset + $Data_Array[0] );
                        $Data_Array = read_Multiple_IFDs( $filehnd, $Tiff_offset, $Byte_Align, $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ]['Tags Name'] );
                }

                $desc = "";
                $units = "";

                // Check if this tag exists in the list of tag definitions,
                if ( array_key_exists ( $Tag_No, $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name]) )
                {

                        if ( array_key_exists ( 'Description', $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ] ) )
                        {
                                $desc = $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ]['Description'];
                        }

                        if ( array_key_exists ( 'Units', $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ] ) )
                        {
                                $units = $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ]['Units'];
                        }

                        // Tag exists in definitions, append details to output array
                        $OutputArray[ $Tag_No ] = array (       "Tag Number"      => $Tag_No,
                                                                "Tag Name"        => $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ]['Name'],
                                                                "Tag Description" => $desc,
                                                                "Data Type"       => $Data_Type,
                                                                "Type"            => $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag_No ]['Type'],
                                                                "Units"           => $units,
                                                                "Data"            => $Data_Array );

                }
                else
                {
                        // Tag doesnt exist in definitions, append unknown details to output array

                        $OutputArray[ $Tag_No ] = array (       "Tag Number"      => $Tag_No,
                                                                "Tag Name"        => "Unknown Tag #" . $Tag_No,
                                                                "Tag Description" => "",
                                                                "Data Type"       => $Data_Type,
                                                                "Type"            => "Unknown",
                                                                "Units"           => "",
                                                                "Data"            => $Data_Array );
                }



                // Some information of type "Unknown" (type 7) might require information about
                // how it's position and byte alignment in order to be decoded
                if ( $Data_Type == 7 )
                {
                        $OutputArray[ $Tag_No ]['Offset'] = $Data_Start_pos;
                        $OutputArray[ $Tag_No ]['Byte Align'] = $Byte_Align;
                }


                ////////////////////////////////////////////////////////////////////////
                // Special Data handling
                ////////////////////////////////////////////////////////////////////////


                // Check if this is a Print Image Matching entry
                if ( $OutputArray[ $Tag_No ]['Type'] == "PIM" )
                {
                        // This is a Print Image Matching entry, decode it.
                        $OutputArray[ $Tag_No ] = Decode_PIM( $OutputArray[ $Tag_No ], $Tag_Definitions_Name );
                }


                // Interpret the entry into a text string using a custom interpreter
                $text_val = get_Tag_Text_Value( $OutputArray[ $Tag_No ], $Tag_Definitions_Name );

                // Check if a text string was generated
                if ( $text_val !== FALSE )
                {
                        // A string was generated, append it to the output array entry
                        $OutputArray[ $Tag_No ]['Text Value'] = $text_val;
                        $OutputArray[ $Tag_No ]['Decoded'] = TRUE;
                }
                else
                {
                        // A string was NOT generated, append a generic string to the output array entry
                        $OutputArray[ $Tag_No ]['Text Value'] = get_IFD_value_as_text( $OutputArray[ $Tag_No ] )  . " " . $units;
                        $OutputArray[ $Tag_No ]['Decoded'] = FALSE;
                }




                // Check if this entry is the Maker Note
                if ( ( $Tag_Definitions_Name == "EXIF" ) && ( $Tag_No == 37500 ) )
                {

                        // Save some extra information which will allow Makernote Decoding with the output array entry
                        $OutputArray[ $Tag_No ]['Offset'] = $Data_Start_pos;
                        $OutputArray[ $Tag_No ][ 'Tiff Offset' ] = $Tiff_offset;
                        $OutputArray[ $Tag_No ]['ByteAlign'] = $Byte_Align;

                        // Save a pointer to this entry for Maker note processing later
                        $GLOBALS[ "Maker_Note_Tag" ] = & $OutputArray[ $Tag_No ];
                }


                // Check if this is a IPTC/NAA Record within the EXIF IFD
                if ( ( ( $Tag_Definitions_Name == "EXIF" ) || ( $Tag_Definitions_Name == "TIFF" ) ) &&
                     ( $Tag_No == 33723 ) )
                {
                        // This is a IPTC/NAA Record, interpret it and put result in the data for this entry
                        $OutputArray[ $Tag_No ]['Data'] = get_IPTC( $DataStr );
                        $OutputArray[ $Tag_No ]['Decoded'] = TRUE;
                }
                // Change: Check for embedded XMP as of version 1.11
                // Check if this is a XMP Record within the EXIF IFD
                if ( ( ( $Tag_Definitions_Name == "EXIF" ) || ( $Tag_Definitions_Name == "TIFF" ) ) &&
                     ( $Tag_No == 700 ) )
                {
                        // This is a XMP Record, interpret it and put result in the data for this entry
                        $OutputArray[ $Tag_No ]['Data'] =  read_XMP_array_from_text( $DataStr );
                        $OutputArray[ $Tag_No ]['Decoded'] = TRUE;
                }

                // Change: Check for embedded IRB as of version 1.11
                // Check if this is a Photoshop IRB Record within the EXIF IFD
                if ( ( ( $Tag_Definitions_Name == "EXIF" ) || ( $Tag_Definitions_Name == "TIFF" ) ) &&
                     ( $Tag_No == 34377 ) )
                {
                        // This is a Photoshop IRB Record, interpret it and put result in the data for this entry
                        $OutputArray[ $Tag_No ]['Data'] = unpack_Photoshop_IRB_Data( $DataStr );
                        $OutputArray[ $Tag_No ]['Decoded'] = TRUE;
                }

                // Exif Thumbnail
                // Check that both the thumbnail length and offset entries have been processed,
                // and that this is one of them
                if ( ( ( ( $Tag_No == 513 ) && ( array_key_exists( 514, $OutputArray ) ) ) ||
                       ( ( $Tag_No == 514 ) && ( array_key_exists( 513, $OutputArray ) ) ) )  &&
                     ( $Tag_Definitions_Name == "TIFF" ) )
                {
                        // Seek to the start of the thumbnail using the offset entry
                        fseek( $filehnd, $Tiff_offset + $OutputArray[513]['Data'][0] );

                        // Read the thumbnail data, and replace the offset data with the thumbnail
                        $OutputArray[513]['Data'] = network_safe_fread( $filehnd, $OutputArray[514]['Data'][0] );
                }


                // Casio Thumbnail
                // Check that both the thumbnail length and offset entries have been processed,
                // and that this is one of them
                if ( ( ( ( $Tag_No == 0x0004 ) && ( array_key_exists( 0x0003, $OutputArray ) ) ) ||
                       ( ( $Tag_No == 0x0003 ) && ( array_key_exists( 0x0004, $OutputArray ) ) ) )  &&
                     ( $Tag_Definitions_Name == "Casio Type 2" ) )
                {
                        // Seek to the start of the thumbnail using the offset entry
                        fseek( $filehnd, $Tiff_offset + $OutputArray[0x0004]['Data'][0] );

                        // Read the thumbnail data, and replace the offset data with the thumbnail
                        $OutputArray[0x0004]['Data'] = network_safe_fread( $filehnd, $OutputArray[0x0003]['Data'][0] );
                }

                // Minolta Thumbnail
                // Check that both the thumbnail length and offset entries have been processed,
                // and that this is one of them
                if ( ( ( ( $Tag_No == 0x0088 ) && ( array_key_exists( 0x0089, $OutputArray ) ) ) ||
                       ( ( $Tag_No == 0x0089 ) && ( array_key_exists( 0x0088, $OutputArray ) ) ) )  &&
                     ( $Tag_Definitions_Name == "Olympus" ) )
                {

                        // Seek to the start of the thumbnail using the offset entry
                        fseek( $filehnd, $Tiff_offset + $OutputArray[0x0088]['Data'][0] );

                        // Read the thumbnail data, and replace the offset data with the thumbnail
                        $OutputArray[0x0088]['Data'] = network_safe_fread( $filehnd, $OutputArray[0x0089]['Data'][0] );

                        // Sometimes the minolta thumbnail data is empty (or the offset is corrupt, which results in the same thing)

                        // Check if the thumbnail data exists
                        if ( $OutputArray[0x0088]['Data'] != "" )
                        {
                                // Thumbnail exists

                                // Minolta Thumbnails are missing their first 0xFF for some reason,
                                // which is replaced with some weird character, so fix this
                                $OutputArray[0x0088]['Data']{0} = "\xFF";
                        }
                        else
                        {
                                // Thumbnail doesnt exist - make it obvious
                                $OutputArray[0x0088]['Data'] = FALSE;
                        }
                }

        }







        // Return the array of IFD entries and the offset to the next IFD

        return array ($OutputArray , $Next_Offset);
}



/******************************************************************************
* End of Function:     read_IFD_universal
******************************************************************************/












/******************************************************************************
*
* Internal Function:     get_Tag_Text_Value
*
* Description:  Attempts to interpret an IFD entry into a text string using the
*               information in the IFD_Tag_Definitions global array.
*
* Parameters:   Tag - The IFD entry to process
*               Tag_Definitions_Name - The name of the tag definitions to use from within the IFD_Tag_Definitions global array
*
* Returns:      String - if the tag was successfully decoded into a text string
*               FALSE - if the tag could not be decoded using the information
*                       in the IFD_Tag_Definitions global array
*
******************************************************************************/

function get_Tag_Text_Value( $Tag, $Tag_Definitions_Name )
{
        // Check what format the entry is specified as

        if ( $Tag['Type'] == "String" )
        {
                // Format is Text String

                // If "Unknown" (type 7) data type,
                if ( $Tag['Data Type'] == 7 )
                {
                        // Return data as is.
                        return $Tag['Data'];
                }
                else
                {
                        // Otherwise return the default string value of the datatype
                        return get_IFD_value_as_text( $Tag );
                }
        }
        else if ( $Tag['Type'] == "Character Coded String" )
        {
                // Format is Character Coded String (First 8 characters indicate coding scheme)

                // Convert Data to a string
                if ( $Tag['Data Type'] == 7 )
                {
                        // If it is type "Unknown" (type 7) use data as is
                        $data =  $Tag['Data'];
                }
                else
                {
                        // Otherwise use the default string value of the datatype
                        $data = get_IFD_value_as_text( $Tag );
                }

                // Some implementations allow completely data with no Coding Scheme Name,
                // so we need to handle this to avoid errors
                if ( trim( $data ) == "" )
                {
                        return "";
                }

                // Extract the Coding Scheme Name from the first 8 characters
                $char_code = substr( $data, 0, 8 );

                // Extract the Data part from after the first 8 characters
                $characters = substr( $data, 8 );

                // Check coding scheme and interpret as neccessary

                if ( $char_code === "ASCII\x00\x00\x00" )
                {
                        // ASCII coding - return data as is.
                        return $characters;
                }
                elseif ( ( $char_code === "UNICODE\x00" ) ||
                         ( $char_code === "Unicode\x00" ) )             // Note lowercase is non standard
                {
                        // Unicode coding - interpret and return result.
                        return xml_UTF16_clean( $characters, TRUE );
                }
                else
                {
                        // Unknown coding - return string indicating this
                        return "Unsupported character coding : \"$char_code\"\n\"" . trim($characters) . "\"";
                }
                break;
        }
        else if ( $Tag['Type'] == "Numeric" )
        {
                // Format is numeric - return default text value with any required units text appended
                if ( array_key_exists ( 'Units', $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag["Tag Number"] ] ) )
                {
                        $units = $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag["Tag Number"] ]['Units'];
                }
                else
                {
                        $units = "";
                }
                return get_IFD_value_as_text( $Tag )  . " " . $units;
        }
        else if  ( $Tag['Type'] == "Lookup" )
        {
                // Format is a Lookup Table

                // Get a numeric value to use in lookup

                if ( is_array( $Tag['Data'] ) )
                {
                        // If data is an array, use first element
                        $first_val = $Tag['Data'][0];
                }
                else if ( is_string( $Tag['Data'] ) )
                {
                        // If data is a string, use the first character
                        $first_val = ord($Tag['Data']{0});
                }
                else
                {
                        // Otherwise use the data as is
                        $first_val = $Tag['Data'];
                }

                // Check if the data value exists in the lookup table for this IFD entry
                if ( array_key_exists( $first_val, $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag["Tag Number"] ] ) )
                {
                        // Data value exists in lookup table - return the matching string
                        return $GLOBALS[ "IFD_Tag_Definitions" ][$Tag_Definitions_Name][ $Tag["Tag Number"] ][ $first_val ];
                }
                else
                {
                        // Data value doesnt exist in lookup table - return explanation string
                        return "Unknown Reserved value $first_val ";
                }
        }
        else if  ( $Tag['Type'] == "Special" )
        {
                // Format is special - interpret to text with special handlers
                return get_Special_Tag_Text_Value( $Tag, $Tag_Definitions_Name );
        }
        else if  ( $Tag['Type'] == "PIM" )
        {
                // Format is Print Image Matching info - interpret with custom handler
                return get_PIM_Text_Value( $Tag, $Tag_Definitions_Name );
        }
        else if  ( $Tag['Type'] == "SubIFD" )
        {
                // Format is a Sub-IFD - this has no text value
                return "";
        }
        else
        {
                // Unknown Format - Couldn't interpret using the IFD_Tag_Definitions global array information
                return FALSE;
        }
}

/******************************************************************************
* End of Function:     get_Tag_Text_Value
******************************************************************************/






/******************************************************************************
*
* Internal Function:     get_Special_Tag_Text_Value
*
* Description:  Interprets an IFD entry marked as "Special" in the IFD_Tag_Definitions
*               global array into a text string using custom handlers
*
* Parameters:   Tag - The IFD entry to process
*               Tag_Definitions_Name - The name of the tag definitions to use from within the IFD_Tag_Definitions global array
*
* Returns:      String - if the tag was successfully decoded into a text string
*               FALSE - if the tag could not be decoded
*
******************************************************************************/

function get_Special_Tag_Text_Value( $Tag, $Tag_Definitions_Name )
{
        // Check what type of IFD is being decoded

        if ( $Tag_Definitions_Name == "TIFF" )
        {
                // This is a TIFF IFD (bottom level)

                // Check what tag number the IFD entry has.
                switch ( $Tag['Tag Number'] )
                {
                        case 530:  // YCbCr Sub Sampling Entry

                                // Data contains two numerical values

                                if ( ( $Tag['Data'][0] == 2 ) && ( $Tag['Data'][1] == 1 ) )
                                {
                                        // Values are 2,1 - hence YCbCr 4:2:2
                                        return "YCbCr 4:2:2 ratio of chrominance components to the luminance components";
                                }
                                elseif ( ( $Tag['Data'][0] == 2 ) && ( $Tag['Data'][1] == 2 ) )
                                {
                                        // Values are 2,2 - hence YCbCr 4:2:0
                                        return "YCbCr 4:2:0 ratio of chrominance components to the luminance components";
                                }
                                else
                                {
                                        // Other values are unknown
                                        return "Unknown Reserved value (" . $Tag['Data'][0] . ")";
                                }
                                break;

                        default:
                                return FALSE;
                }
        }
        else if ( $Tag_Definitions_Name == "EXIF" )
        {
                // This is an EXIF IFD

                // Check what tag number the IFD entry has.
                switch ( $Tag['Tag Number'] )
                {

                        case 37121: // Components configuration

                                // Data contains 4 numerical values indicating component type

                                $output_str = "";

                                // Cycle through each component
                                for ( $Num = 0; $Num < 4; $Num++ )
                                {
                                        // Construct first part of text string
                                        $output_str .= "Component " . ( $Num + 1 ) . ": ";

                                        // Construct second part of text string via
                                        // lookup using numerical value

                                        $value = ord( $Tag['Data']{$Num} );
                                        switch( $value )
                                        {
                                                case 0:
                                                        $output_str .= "Does not exist\n";
                                                        break;
                                                case 1:
                                                        $output_str .= "Y (Luminance)\n";
                                                        break;
                                                case 2:
                                                        $output_str .= "Cb (Chroma minus Blue)\n";
                                                        break;
                                                case 3:
                                                        $output_str .= "Cr (Chroma minus Red)\n";
                                                        break;
                                                case 4:
                                                        $output_str .= "Red\n";
                                                        break;
                                                case 5:
                                                        $output_str .= "Green\n";
                                                        break;
                                                case 6:
                                                        $output_str .= "Blue\n";
                                                        break;
                                                default:
                                                        $output_str .= "Unknown value $value\n";
                                        };
                                }

                                // Return the completed string

                                return $output_str;
                                break;



                        case 41730: // Colour Filter Array Pattern

                                // The first two characters are a SHORT for Horizontal repeat pixel unit -
                                $n_max = get_IFD_Data_Type( substr( $Tag['Data'], 0, 2 ), 3, $Tag['Byte Align'] );

                                // The next two characters are a SHORT for Vertical repeat pixel unit -
                                $m_max = get_IFD_Data_Type( substr( $Tag['Data'], 2, 2 ), 3, $Tag['Byte Align'] );


                                // At least one camera type appears to have byte reversed values for N_Max and M_Max
                                // Check if they need reversing
                                if ( $n_max > 256 )
                                {
                                        $n_max = $n_max/256 + 256*($n_max%256);
                                }

                                if ( $m_max > 256 )
                                {
                                        $m_max = $m_max/256 + 256*($m_max%256);
                                }


                                $output_str = "";


                                // Cycle through all the elements in the resulting 2 dimensional array,
                                for( $m = 1; $m <= $m_max; $m++ )
                                {
                                        for( $n = 1; $n <= $n_max; $n++ )
                                        {

                                                // Append text from a lookup table according to
                                                // the value read for this element

                                                switch ( ord($Tag['Data']{($n_max*($m-1)+$n+3)}) )
                                                {
                                                        case 0:
                                                                $output_str .= "RED     ";
                                                                break;
                                                        case 1:
                                                                $output_str .= "GREEN   ";
                                                                break;
                                                        case 2:
                                                                $output_str .= "BLUE    ";
                                                                break;
                                                        case 3:
                                                                $output_str .= "CYAN    ";
                                                                break;
                                                        case 4:
                                                                $output_str .= "MAGENTA ";
                                                                break;
                                                        case 5:
                                                                $output_str .= "YELLOW  ";
                                                                break;
                                                        case 6:
                                                                $output_str .= "WHITE   ";
                                                                break;
                                                        default:
                                                                $output_str .= "Unknown ";
                                                                break;
                                                };
                                        };
                                        $output_str .= "\n";
                                };

                                // Return the resulting string
                                return $output_str;
                                break;

                        default:
                                return FALSE;
                }
        }
        else
        {
                // Unknown IFD type, see if it is part of a makernote
                return get_Makernote_Text_Value( $Tag, $Tag_Definitions_Name );
        }


}

/******************************************************************************
* End of Function:     get_Tag_Text_Value
******************************************************************************/








/******************************************************************************
*
* Function:     interpret_IFD
*
* Description:  Generates html detailing the contents a single IFD.
*
* Parameters:   IFD_array - the array containing an IFD
*               filename - the name of the Image file being processed ( used
*                          by scripts which displays EXIF thumbnails)
*
* Returns:      output_str - A string containing the HTML
*
******************************************************************************/

function interpret_IFD( $IFD_array, $filename )
{
        // Create the output string with the table tag
        $output_str = "<table class=\"EXIF_Table\" border=1>\n";

        // Create an extra output string to receive any supplementary html
        // which cannot go inside the table
        $extra_IFD_str = "";

        // Check that the IFD array is valid
        if ( ( $IFD_array === FALSE ) || ( $IFD_array === NULL ) )
        {
                // the IFD array is NOT valid - exit
                return "";
        }

        // Check if this is an EXIF IFD and if there is a makernote present
        if ( ( $IFD_array['Tags Name'] === "EXIF" ) &&
             ( ! array_key_exists( 37500, $IFD_array ) ) )
        {

                // This is an EXIF IFD but NO makernote is present - Add a message to the output
                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">No Makernote Present</h3>";
        }

        // Cycle through each tag in the IFD

        foreach( $IFD_array as $Tag_ID => $Exif_Tag )
        {

                // Ignore the non numeric elements - they aren't tags
                if ( ! is_numeric ( $Tag_ID ) )
                {
                        // Skip Tags Name
                }
                        // Check if the Tag has been decoded successfully
                else if ( $Exif_Tag['Decoded'] == TRUE )
                {
                        // This tag has been successfully decoded

                        // Table cells won't get drawn with nothing in them -
                        // Ensure that at least a non breaking space exists in them

                        if ( trim($Exif_Tag['Text Value']) == "" )
                        {
                                $Exif_Tag['Text Value'] = "&nbsp;";
                        }

                        // Check if the tag is a sub-IFD
                        if ( $Exif_Tag['Type'] == "SubIFD" )
                        {
                                // This is a sub-IFD tag
                                // Add a sub-heading for the sub-IFD
                                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">" . $Exif_Tag['Tag Name'] . " contents</h3>";

                                // Cycle through each sub-IFD in the chain
                                foreach ( $Exif_Tag['Data'] as $subIFD )
                                {
                                        // Interpret this sub-IFD and add the html to the secondary output
                                        $extra_IFD_str .= interpret_IFD( $subIFD, $filename );
                                }
                        }
                                // Check if the tag is a makernote
                        else if ( $Exif_Tag['Type'] == "Maker Note" )
                        {
                                // This is a Makernote Tag
                                // Add a sub-heading for the Makernote
                                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">Maker Note Contents</h3>";

                                // Interpret the Makernote and add the html to the secondary output
                                $extra_IFD_str .= Interpret_Makernote_to_HTML( $Exif_Tag, $filename );
                        }
                                // Check if this is a IPTC/NAA Record within the EXIF IFD
                        else if ( $Exif_Tag['Type'] == "IPTC" )
                        {
                                // This is a IPTC/NAA Record, interpret it and output to the secondary html
                                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">Contains IPTC/NAA Embedded in EXIF</h3>";
                                $extra_IFD_str .=Interpret_IPTC_to_HTML( $Exif_Tag['Data'] );
                        }
                                // Change: Check for embedded XMP as of version 1.11
                                // Check if this is a XMP Record within the EXIF IFD
                        else if ( $Exif_Tag['Type'] == "XMP" )
                        {
                                // This is a XMP Record, interpret it and output to the secondary html
                                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">Contains XMP Embedded in EXIF</h3>";
                                $extra_IFD_str .= Interpret_XMP_to_HTML( $Exif_Tag['Data'] );
                        }
                                // Change: Check for embedded IRB as of version 1.11
                                // Check if this is a Photoshop IRB Record within the EXIF IFD
                        else if ( $Exif_Tag['Type'] == "IRB" )
                        {
                                // This is a Photoshop IRB Record, interpret it and output to the secondary html
                                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">Contains Photoshop IRB Embedded in EXIF</h3>";
                                $extra_IFD_str .= Interpret_IRB_to_HTML( $Exif_Tag['Data'], $filename );
                        }
                                // Check if the tag is Numeric
                        else if ( $Exif_Tag['Type'] == "Numeric" )
                        {
                                // Numeric Tag - Output text value as is.
                                $output_str .= "<tr class=\"EXIF_Table_Row\"><td class=\"EXIF_Caption_Cell\">" . $Exif_Tag['Tag Name'] . "</td><td class=\"EXIF_Value_Cell\">" . $Exif_Tag['Text Value'] . "</td></tr>\n";
                        }
                        else
                        {
                                // Other tag - Output text as preformatted
                                $output_str .= "<tr class=\"EXIF_Table_Row\"><td class=\"EXIF_Caption_Cell\">" . $Exif_Tag['Tag Name'] . "</td><td class=\"EXIF_Value_Cell\"><pre>" . trim( $Exif_Tag['Text Value']) . "</pre></td></tr>\n";
                        }

                }
                else
                {
                        // Tag has NOT been decoded successfully
                        // Hence it is either an unknown tag, or one which
                        // requires processing at the time of html construction

                        // Table cells won't get drawn with nothing in them -
                        // Ensure that at least a non breaking space exists in them

                        if ( trim($Exif_Tag['Text Value']) == "" )
                        {
                                $Exif_Tag['Text Value'] = "&nbsp;";
                        }

                        // Check if this tag is the first IFD Thumbnail
                        if ( ( $IFD_array['Tags Name'] == "TIFF" ) &&
                             ( $Tag_ID == 513 ) )
                        {
                                // This is the first IFD thumbnail - Add html to the output

                                // Change: as of version 1.11 - Changed to make thumbnail link portable across directories
                                // Build the path of the thumbnail script and its filename parameter to put in a url
                                $link_str = get_relative_path( dirname(__FILE__) . "/get_exif_thumb.php" , getcwd ( ) );
                                $link_str .= "?filename=";
                                $link_str .= get_relative_path( $filename, dirname(__FILE__) );

                                // Add thumbnail link to html
                                $output_str .= "<tr class=\"EXIF_Table_Row\"><td class=\"EXIF_Caption_Cell\">" . $Exif_Tag['Tag Name'] . "</td><td class=\"EXIF_Value_Cell\"><a class=\"EXIF_First_IFD_Thumb_Link\" href=\"$link_str\"><img class=\"EXIF_First_IFD_Thumb\" src=\"$link_str\"></a></td></tr>\n";
                        }
                                // Check if this is the Makernote
                        else if ( $Exif_Tag['Type'] == "Maker Note" )
                        {
                                // This is the makernote, but has not been decoded
                                // Add a message to the secondary output
                                $extra_IFD_str .= "<h3 class=\"EXIF_Secondary_Heading\">Makernote Coding Unknown</h3>\n";
                        }
                        else
                        {
                                // This is an Unknown Tag

                                // Check if the user wants to hide unknown tags
                                if ( $GLOBALS['HIDE_UNKNOWN_TAGS'] === FALSE )
                                {
                                        // User wants to display unknown tags

                                        // Check if the Data is an ascii string
                                        if ( $Exif_Tag['Data Type'] == 2 )
                                        {
                                                // This is a Ascii String field - add it preformatted to the output
                                                $output_str .= "<tr class=\"EXIF_Table_Row\"><td class=\"EXIF_Caption_Cell\">" . $Exif_Tag['Tag Name'] . "</td><td class=\"EXIF_Value_Cell\"><pre>" . trim( $Exif_Tag['Text Value'] ) . "</pre></td></tr>\n";
                                        }
                                        else
                                        {
                                                // Not an ASCII string - add it as is to the output
                                                $output_str .= "<tr class=\"EXIF_Table_Row\"><td class=\"EXIF_Caption_Cell\">" . $Exif_Tag['Tag Name'] . "</td><td class=\"EXIF_Value_Cell\">" . trim( $Exif_Tag['Text Value'] ) . "</td></tr>\n";
                                        }
                                }
                        }
                }
        }

        // Close the table in the output
        $output_str .= "</table>\n";

        // Add the secondary output at the end of the main output
        $output_str .= "$extra_IFD_str\n";

        // Return the resulting html
        return $output_str;
}

/******************************************************************************
* End of Function:     interpret_IFD
******************************************************************************/















/******************************************************************************
*
* Function:     get_IFD_Data_Type
*
* Description:  Decodes an IFD field value from a binary data string, using
*               information supplied about the data type and byte alignment of
*               the stored data.
*               This function should be used for all datatypes except ASCII strings
*
* Parameters:   input_data - a binary data string containing the IFD value,
*                            must be exact length of the value
*               data_type - a number representing the IFD datatype as per the
*                           TIFF 6.0 specification:
*                               1 = Unsigned 8-bit Byte
*                               2 = ASCII String
*                               3 = Unsigned 16-bit Short
*                               4 = Unsigned 32-bit Long
*                               5 = Unsigned 2x32-bit Rational
*                               6 = Signed 8-bit Byte
*                               7 = Undefined
*                               8 = Signed 16-bit Short
*                               9 = Signed 32-bit Long
*                               10 = Signed 2x32-bit Rational
*                               11 = 32-bit Float
*                               12 = 64-bit Double
*               Byte_Align - Indicates the byte alignment of the data.
*                            MM = Motorola, MSB first, Big Endian
*                            II = Intel, LSB first, Little Endian
*
* Returns:      output - the value of the data (string or numeric)
*
******************************************************************************/

function get_IFD_Data_Type( $input_data, $data_type, $Byte_Align )
{
        // Check if this is a Unsigned Byte, Unsigned Short or Unsigned Long
        if (( $data_type == 1 ) || ( $data_type == 3 ) || ( $data_type == 4 ))
        {
                // This is a Unsigned Byte, Unsigned Short or Unsigned Long

                // Check the byte alignment to see if the bytes need tp be reversed
                if ( $Byte_Align == "II" )
                {
                        // This is in Intel format, reverse it
                        $input_data = strrev ( $input_data );
                }

                // Convert the binary string to a number and return it
                return hexdec( bin2hex( $input_data ) );
        }
                // Check if this is a ASCII string type
        elseif ( $data_type == 2 )
        {
                // Null terminated ASCII string(s)
                // The input data may represent multiple strings, as the
                // 'count' field represents the total bytes, not the number of strings
                // Hence this should not be processed here, as it would have
                // to return multiple values instead of a single value

                echo "<p>Error - ASCII Strings should not be processed in get_IFD_Data_Type</p>\n";
                return "Error Should never get here"; //explode( "\x00", $input_data );
        }
                // Check if this is a Unsigned rational type
        elseif ( $data_type == 5 )
        {
                // This is a Unsigned rational type

                // Check the byte alignment to see if the bytes need to be reversed
                if ( $Byte_Align == "MM" )
                {
                        // Motorola MSB first byte aligment
                        // Unpack the Numerator and denominator and return them
                        return unpack( 'NNumerator/NDenominator', $input_data );
                }
                else
                {
                        // Intel LSB first byte aligment
                        // Unpack the Numerator and denominator and return them
                        return unpack( 'VNumerator/VDenominator', $input_data );
                }
        }
                // Check if this is a Signed Byte, Signed Short or Signed Long
        elseif ( ( $data_type == 6 ) || ( $data_type == 8 ) || ( $data_type == 9 ) )
        {
                // This is a Signed Byte, Signed Short or Signed Long

                // Check the byte alignment to see if the bytes need to be reversed
                if ( $Byte_Align == "II" )
                {
                        //Intel format, reverse the bytes
                        $input_data = strrev ( $input_data );
                }

                // Convert the binary string to an Unsigned number
                $value = hexdec( bin2hex( $input_data ) );

                // Convert to signed number

                // Check if it is a Byte above 128 (i.e. a negative number)
                if ( ( $data_type == 6 ) && ( $value > 128 ) )
                {
                        // number should be negative - make it negative
                        return  $value - 256;
                }

                // Check if it is a Short above 32767 (i.e. a negative number)
                if ( ( $data_type == 8 ) && ( $value > 32767 ) )
                {
                        // number should be negative - make it negative
                        return  $value - 65536;
                }

                // Check if it is a Long above 2147483648 (i.e. a negative number)
                if ( ( $data_type == 9 ) && ( $value > 2147483648 ) )
                {
                        // number should be negative - make it negative
                        return  $value - 4294967296;
                }

                // Return the signed number
                return $value;
        }
                // Check if this is Undefined type
        elseif ( $data_type == 7 )
        {
                // Custom Data - Do nothing
                return $input_data;
        }
                // Check if this is a Signed Rational type
        elseif ( $data_type == 10 )
        {
                // This is a Signed Rational type

                // Signed Long not available with endian in unpack , use unsigned and convert

                // Check the byte alignment to see if the bytes need to be reversed
                if ( $Byte_Align == "MM" )
                {
                        // Motorola MSB first byte aligment
                        // Unpack the Numerator and denominator
                        $value = unpack( 'NNumerator/NDenominator', $input_data );
                }
                else
                {
                        // Intel LSB first byte aligment
                        // Unpack the Numerator and denominator
                        $value = unpack( 'VNumerator/VDenominator', $input_data );
                }

                // Convert the numerator to a signed number
                // Check if it is above 2147483648 (i.e. a negative number)
                if ( $value['Numerator'] > 2147483648 )
                {
                        // number is negative
                        $value['Numerator'] -= 4294967296;
                }

                // Convert the denominator to a signed number
                // Check if it is above 2147483648 (i.e. a negative number)
                if ( $value['Denominator'] > 2147483648 )
                {
                        // number is negative
                        $value['Denominator'] -= 4294967296;
                }

                // Return the Signed Rational value
                return $value;
        }
                // Check if this is a Float type
        elseif ( $data_type == 11 )
        {
                // IEEE 754 Float
                // TODO - EXIF - IFD datatype Float not implemented yet
                return "FLOAT NOT IMPLEMENTED YET";
        }
                // Check if this is a Double type
        elseif ( $data_type == 12 )
        {
                // IEEE 754 Double
                // TODO - EXIF - IFD datatype Double not implemented yet
                return "DOUBLE NOT IMPLEMENTED YET";
        }
        else
        {
                // Error - Invalid Datatype
                return "Invalid Datatype $data_type";

        }

}

/******************************************************************************
* End of Function:     get_IFD_Data_Type
******************************************************************************/






/******************************************************************************
*
* Function:     put_IFD_Data_Type
*
* Description:  Encodes an IFD field from a value to a binary data string, using
*               information supplied about the data type and byte alignment of
*               the stored data.
*
* Parameters:   input_data - an IFD data value, numeric or string
*               data_type - a number representing the IFD datatype as per the
*                           TIFF 6.0 specification:
*                               1 = Unsigned 8-bit Byte
*                               2 = ASCII String
*                               3 = Unsigned 16-bit Short
*                               4 = Unsigned 32-bit Long
*                               5 = Unsigned 2x32-bit Rational
*                               6 = Signed 8-bit Byte
*                               7 = Undefined
*                               8 = Signed 16-bit Short
*                               9 = Signed 32-bit Long
*                               10 = Signed 2x32-bit Rational
*                               11 = 32-bit Float
*                               12 = 64-bit Double
*               Byte_Align - Indicates the byte alignment of the data.
*                            MM = Motorola, MSB first, Big Endian
*                            II = Intel, LSB first, Little Endian
*
* Returns:      output - the packed binary string of the data
*
******************************************************************************/

function put_IFD_Data_Type( $input_data, $data_type, $Byte_Align )
{
        // Process according to the datatype
        switch ( $data_type )
        {
                case 1: // Unsigned Byte - return character as is
                        return chr($input_data);
                        break;

                case 2: // ASCII String
                        // Return the string with terminating null
                        return $input_data . "\x00";
                        break;

                case 3: // Unsigned Short
                        // Check byte alignment
                        if ( $Byte_Align == "II" )
                        {
                                // Intel/Little Endian - pack the short and return
                                return pack( "v", $input_data );
                        }
                        else
                        {
                                // Motorola/Big Endian - pack the short and return
                                return pack( "n", $input_data );
                        }
                        break;

                case 4: // Unsigned Long
                        // Check byte alignment
                        if ( $Byte_Align == "II" )
                        {
                                // Intel/Little Endian - pack the long and return
                                return pack( "V", $input_data );
                        }
                        else
                        {
                                // Motorola/Big Endian - pack the long and return
                                return pack( "N", $input_data );
                        }
                        break;

                case 5: // Unsigned Rational
                        // Check byte alignment
                        if ( $Byte_Align == "II" )
                        {
                                // Intel/Little Endian - pack the two longs and return
                                return pack( "VV", $input_data['Numerator'], $input_data['Denominator'] );
                        }
                        else
                        {
                                // Motorola/Big Endian - pack the two longs and return
                                return pack( "NN", $input_data['Numerator'], $input_data['Denominator'] );
                        }
                        break;

                case 6: // Signed Byte
                        // Check if number is negative
                        if ( $input_data < 0 )
                        {
                                // Number is negative - return signed character
                                return chr( $input_data + 256 );
                        }
                        else
                        {
                                // Number is positive - return character
                                return chr( $input_data );
                        }
                        break;

                case 7: // Unknown - return as is
                        return $input_data;
                        break;

                case 8: // Signed Short
                        // Check if number is negative
                        if (  $input_data < 0 )
                        {
                                // Number is negative - make signed value
                                $input_data = $input_data + 65536;
                        }
                        // Check byte alignment
                        if ( $Byte_Align == "II" )
                        {
                                // Intel/Little Endian - pack the short and return
                                return pack( "v", $input_data );
                        }
                        else
                        {
                                // Motorola/Big Endian - pack the short and return
                                return pack( "n", $input_data );
                        }
                        break;

                case 9: // Signed Long
                        // Check if number is negative
                        if (  $input_data < 0 )
                        {
                                // Number is negative - make signed value
                                $input_data = $input_data + 4294967296;
                        }
                        // Check byte alignment
                        if ( $Byte_Align == "II" )
                        {
                                // Intel/Little Endian - pack the long and return
                                return pack( "v", $input_data );
                        }
                        else
                        {
                                // Motorola/Big Endian - pack the long and return
                                return pack( "n", $input_data );
                        }
                        break;

                case 10: // Signed Rational
                        // Check if numerator is negative
                        if (  $input_data['Numerator'] < 0 )
                        {
                                // Number is numerator - make signed value
                                $input_data['Numerator'] = $input_data['Numerator'] + 4294967296;
                        }
                        // Check if denominator is negative
                        if (  $input_data['Denominator'] < 0 )
                        {
                                // Number is denominator - make signed value
                                $input_data['Denominator'] = $input_data['Denominator'] + 4294967296;
                        }
                        // Check byte alignment
                        if ( $Byte_Align == "II" )
                        {
                                // Intel/Little Endian - pack the two longs and return
                                return pack( "VV", $input_data['Numerator'], $input_data['Denominator'] );
                        }
                        else
                        {
                                // Motorola/Big Endian - pack the two longs and return
                                return pack( "NN", $input_data['Numerator'], $input_data['Denominator'] );
                        }
                        break;

                case 11: // Float
                        // IEEE 754 Float
                        // TODO - EXIF - IFD datatype Float not implemented yet
                        return "FLOAT NOT IMPLEMENTED YET";
                        break;

                case 12: // Double
                        // IEEE 754 Double
                        // TODO - EXIF - IFD datatype Double not implemented yet
                        return "DOUBLE NOT IMPLEMENTED YET";
                        break;

                default:
                        // Error - Invalid Datatype
                        return "Invalid Datatype $data_type";
                        break;

        }

        // Shouldn't get here
        return FALSE;
}

/******************************************************************************
* End of Function:     put_IFD_Data_Type
******************************************************************************/





/******************************************************************************
*
* Function:     get_IFD_value_as_text
*
* Description:  Decodes an IFD field value from a binary data string, using
*               information supplied about the data type and byte alignment of
*               the stored data.
*               This function should be used for all datatypes except ASCII strings
*
* Parameters:   input_data - a binary data string containing the IFD value,
*                            must be exact length of the value
*               data_type - a number representing the IFD datatype as per the
*                           TIFF 6.0 specification:
*                               1 = Unsigned 8-bit Byte
*                               2 = ASCII String
*                               3 = Unsigned 16-bit Short
*                               4 = Unsigned 32-bit Long
*                               5 = Unsigned 2x32-bit Rational
*                               6 = Signed 8-bit Byte
*                               7 = Undefined
*                               8 = Signed 16-bit Short
*                               9 = Signed 32-bit Long
*                               10 = Signed 2x32-bit Rational
*                               11 = 32-bit Float
*                               12 = 64-bit Double
*               Byte_Align - Indicates the byte alignment of the data.
*                            MM = Motorola, MSB first, Big Endian
*                            II = Intel, LSB first, Little Endian
*
* Returns:      output - the value of the data (string or numeric)
*
******************************************************************************/

function get_IFD_value_as_text( $Exif_Tag )
{
        // Create a string to receive the output text
        $output_str = "";

        // Select Processing method according to the datatype
        switch  ($Exif_Tag['Data Type'])
        {
                case 1 : // Unsigned Byte
                case 3 : // Unsigned Short
                case 4 : // Unsigned Long
                case 6 : // Signed Byte
                case 8 : // Signed Short
                case 9 : // Signed Long

                        // Cycle through each of the values for this tag
                        foreach ( $Exif_Tag['Data'] as $val )
                        {
                                // Check that this isn't the first value,
                                if ( $output_str != "" )
                                {
                                        // This isn't the first value, Add a Comma and Newline to the output
                                        $output_str .= ",\n";
                                }
                                // Add the Value to the output
                                $output_str .= $val;
                        }
                        break;

                case 2 : // ASCII
                        // Append all the strings together, separated by Newlines
                        $output_str .= implode ( "\n", $Exif_Tag['Data']);
                        break;

                case 5 : // Unsigned Rational
                case 10: // Signed Rational

                        // Cycle through each of the values for this tag
                        foreach ( $Exif_Tag['Data'] as $val )
                        {
                                // Check that this isn't the first value,
                                if ( $output_str != "" )
                                {
                                        // This isn't the first value, Add a Comma and Newline to the output
                                        $output_str .= ",\n";
                                }

                                // Add the Full Value to the output
                                $output_str .= $val['Numerator'] ."/" . $val['Denominator'];

                                // Check if division by zero might be a problem
                                if ( $val['Denominator'] != 0 )
                                {
                                        // Denominator is not zero, Add the Decimal Value to the output text
                                        $output_str .= " (" . ($val['Numerator'] / $val['Denominator']) . ")";
                                }
                        }
                        break;

                case 11: // Float
                case 12: // Double
                        // TODO - EXIF - IFD datatype Double and Float not implemented yet
                        $output_str .= "Float and Double not implemented yet";
                        break;

                case 7 : // Undefined
                        // Unless the User has asked to see the raw binary data, this
                        // type should not be displayed

                        // Check if the user has requested to see the binary data in hex
                        if ( $GLOBALS['SHOW_BINARY_DATA_HEX'] == TRUE)
                        {
                                // User has requested to see the binary data in hex
                                // Add the value in hex
                                $output_str .= "( " . strlen( $Exif_Tag['Data'] ) . " bytes of binary data ): " . bin2hex( $Exif_Tag['Data'] )  ;
                        }
                                // Check if the user has requested to see the binary data as is
                        else if ( $GLOBALS['SHOW_BINARY_DATA_TEXT'] == TRUE)
                        {
                                // User has requested to see the binary data as is
                                // Add the value as is
                                $output_str .= "( " . strlen( $Exif_Tag['Data'] ) . " bytes of binary data ): " . $Exif_Tag['Data']  ;
                        }
                        else
                        {
                                // User has NOT requested to see binary data,
                                // Add a message indicating the number of bytes to the output
                                $output_str .= "( " . strlen( $Exif_Tag['Data'] ) . " bytes of binary data ) "  ;
                        }
                        break;

                default :
                        // Error - Unknown IFD datatype
                        $output_str .= "Error - Exif tag data type (" . $Exif_Tag['Data Type'] .") is invalid";
                        break;
        }

        // Return the resulting text string
        return $output_str;
}

/******************************************************************************
* End of Function:     get_IFD_value_as_text
******************************************************************************/




/******************************************************************************
* Global Variable:      IFD_Data_Sizes
*
* Contents:     The sizes (in bytes) of each EXIF IFD Datatype, indexed by
*               their datatype number
*
******************************************************************************/

$GLOBALS['IFD_Data_Sizes'] = array(     1 => 1,         // Unsigned Byte
                                        2 => 1,         // ASCII String
                                        3 => 2,         // Unsigned Short
                                        4 => 4,         // Unsigned Long
                                        5 => 8,         // Unsigned Rational
                                        6 => 1,         // Signed Byte
                                        7 => 1,         // Undefined
                                        8 => 2,         // Signed Short
                                        9 => 4,         // Signed Long
                                        10 => 8,        // Signed Rational
                                        11 => 4,        // Float
                                        12 => 8 );      // Double
function get_relative_path( $target, $fromdir )
{
        // Check that the fromdir has a trailing slash, otherwise realpath will
        // strip the last directory name off
        if ( ( $fromdir[ strlen( $fromdir ) - 1 ] != "\\" ) &&
             ( $fromdir[ strlen( $fromdir ) - 1 ] != "/" ) )
        {
                $fromdir .= "/";
        }

        // get a real directory name for each of the target and from directory
        $from = realpath( $fromdir );
        $target = realpath( $target );
        $to = dirname( $target  );

        // Can't get relative path with drive in path - remove it
        if ( ( $colonpos = strpos( $target, ":" ) ) != FALSE )
        {
                $target = substr( $target, $colonpos+1 );
        }
        if ( ( $colonpos = strpos( $from, ":" ) ) != FALSE )
        {
                $from = substr( $from, $colonpos+1 );
        }
        if ( ( $colonpos = strpos( $to, ":" ) ) != FALSE )
        {
                $to = substr( $to, $colonpos+1 );
        }


        $path = "../";
        $posval = 0;
        // Step through the paths until a difference is found (ignore slash, backslash differences
        // or the end of one is found
        while ( ( ( $from[$posval] == $to[$posval] ) ||
                  ( ( $from[$posval] == "\\" ) && ( $to[$posval] == "/" ) ) ||
                  ( ( $from[$posval] == "/" ) && ( $to[$posval] == "\\" ) ) ) &&
                ( $from[$posval] && $to[$posval] ) )
        {
                $posval++;
        }
        // Save the position of the first difference
        $diffpos = $posval;

        // Check if the directories are the same or
        // the if target is in a subdirectory of the fromdir
        if ( ( ! $from[$posval] ) &&
             ( $to[$posval] == "/" || $to[$posval] == "\\" || !$to[$posval] ) )
        {
                // target is in fromdir or a subdirectory
                // Build relative path starting with a ./
                return ( "./" . substr( $target, $posval+1, strlen( $target ) ) );
        }
        else
        {
                // target is outside the fromdir branch
                // find out how many "../"'s are necessary
                // Step through the fromdir path, checking for slashes
                // each slash encountered requires a "../"
                while ( $from[++$posval] )
                {
                        // Check for slash
                        if ( ( $from[$posval] == "/" ) || ( $from[$posval] == "\\" ) )
                        {
                                // Found a slash, add a "../"
                                $path .= "../";
                        }
                }

                // Search backwards to find where the first common directory
                // as some letters in the first different directory names
                // may have been the same
                $diffpos--;
                while ( ( $to[$diffpos] != "/" ) && ( $to[$diffpos] != "\\" ) && $to[$diffpos] )
                {
                        $diffpos--;
                }
                // Build relative path to return

                return ( $path . substr( $target, $diffpos+1, strlen( $target ) ) );
        }
}

 ?>