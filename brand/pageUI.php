<?

class PageUI {

    // class PageUI attributes    
    var $content = 'No Content';
    var $Google_conversion_content = '';
    var $change_folder = '';
    var $web_name = '';
    var $title = '';  //This is a boolean to determine if show title or not. This is the tile on the PageUI, not on the blue line on top of IE with <title>. 
    var $catID = 0;
    var $breadcrumb_type = 'none';
    var $cat_display = 'none';
    var $item_code = 0;
    var $print_this_PageUI_status = 0;
    var $html_body_tag_extra = '';
    var $delaer_locator_arr = array();
    var $guid = 0;
    var $noindex_flag = 0;  //1: Not display the top menu 0: Display the top menu
    var $mainDivID = '';
    var $meta_tags = '';
    var $pg_type = '';
    var $preload_imgs;

    function PageUIConstructor($pg_type) {
        $this->pg_type = $pg_type;
    }

    function Set_Google_conversion_contents($newVal) {
        $this->Google_conversion_content = $newVal;
    }

    function Get_Google_conversion_contents() {
        return $this->Google_conversion_content;
    }

    function set_delaer_locator_arr($newArr) {
        $this->delaer_locator_arr = $newArr;
    }

    function get_delaer_locator_arr() {      //DEALER LOCATOR ARR
        return $this->delaer_locator_arr;
    }

    function set_html_body_tag_extra($newVal) {     //EXTRA EVENTS FOR HTML BODY TAG
        $this->html_body_tag_extra = $newVal;
    }

    function get_html_body_tag_extra() {
        return $this->html_body_tag_extra;
    }

    function set_guid($newVal) {       //GUID
        $this->guid = $newVal;
    }

    function get_guid() {
        return $this->guid;
    }

    function SetMainDivID($newVal) {
        $this->mainDivID = $newVal;
    }

    function Set_noindex($newVal) {
        $this->noindex_flag = $newVal;
    }

    function Set_print_this_PageUI_status($newVal) {
        $this->print_this_PageUI_status = $newVal;
    }

    function SetcatID($newcontent) {
        $this->catID = $newcontent;
    }

    function Setbreadcrumb_type($newcontent) {
        $this->breadcrumb_type = $newcontent;
    }

    function SetContent($newcontent) {
        $this->content = $newcontent;
    }

    function set_preload_images($imgs){
        $this->preload_imgs = $imgs;
    }
    
    function SetLogo($newlogo) {
        $this->logo = $newlogo;
    }

    function Set_change_folder($new_change_folder) {
        $this->change_folder = $new_change_folder;
    }

    function SetTitle($newtitle) {
        $this->title = $newtitle;
    }

    function Setitem_code($newcontent) {
        $this->item_code = $newcontent;
    }

    function Set_meta_title($newcontent) {
        $this->meta_title = $newcontent;
    }

    function Set_meta_tag($newcontent) {
        $this->meta_tag = $newcontent;
    }

    function Set_meta_description($newcontent) {
        $this->meta_description = $newcontent;
    }

    function display_contact() {
        //echo 'CONTACT IS EMPTY';
    }

    function display_extra_information($item_code) {
        
    }

    function display_related_items($catID, $item_code, $file_name_thumb) {
        
    }

    function Display($command, $instruction, $var1, $var2, $var3, $var4, $var5, $var6) {

        $this->pageUI___HTMLStart();
        $this->pageUI___displayHead($var3);
        $this->HTML1(); //Opens BODY and DIV Container
        contents($command, $instruction, $var1, $var2, $var3, $var4, $var5, $var6);
        $this->HTML1_2(); //Close DIV Container and BODY
        $this->pageUI___HTMLEnd();
    }

    function pageUI___HTMLStart() {
        echo '<!DOCTYPE html>
<html>
';
    }

    function pageUI___HTMLStart_old() {
        //echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        /* echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
          <html xmlns="http://www.w3.org/1999/xhtml"> */
        echo '<!DOCTYPE html>
            <head>
            ';
    }

    function pageUI___HTMLEnd() {
        echo '</html>';
    }

    function display__brand_logo($brand) {
        
    }

    function display__brand_txt($brand) {
        
    }

    function Display_meta_tags() {   //<TITLE> and META TAGS
        $meta_tag = $this->meta_tags;
        $meta_tag .= '
            <meta charset="utf-8">

            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
            ';
        if ($this->noindex_flag == 1) {
            $meta_tag .= '<META name="robots" content="noindex,noarchive">';
        }
        if ($this->breadcrumb_type == 'Home')
            $meta_tag.= '<META name="robots" content="all, index, follow">';
        return $meta_tag;
    }

    function DisplayJavascripts($set_lang = NULL) {
        $search_address = URL . gen_page_link(2, '', 1050, "Product Search", SEARCH_GUID, $set_lang, $GLOBALS['PG_TYPES_URL']['1050']);
        
        $retailer_foler = '/brand/';
        
        $scripts = "
            <!--Scripts Here-->
        ";
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "{$retailer_foler}pg_type_scripts/".$this->pg_type.'.js')){
            $scripts .= '
            <script type="text/javascript" src="' . $retailer_foler . 'pg_type_scripts/'.$this->pg_type.'.js"></script>';
        }
        $scripts .= '
            <script type="text/javascript">
                function fsearch(vsearch)
                {
                    var vlocation = "' . $search_address . '/"+vsearch;
                    window.location = vlocation;
                }  
            </script>
        ';
        
        return $scripts;
    }

    function DisplayStyles() {
        
        $retailer_foler = '/brand/';
        
        $style = "
        
            <!--Fonts--->
            
            <!--/Fonts-->		

            ";
        if ($this->pg_type == 3) {
            $style .= "
            <!---home styles-->
            
            <!---/end-->
            ";
        }
        
        $style .= "
            
        ";
        
        return $style;
    }

    function display_navigation() {
        
    }

    function display_title() {  //This is a boolian to show the name on the PageUI, not <title> on the bar.
    }

    function display_shopping_cart() {
        
    }

    function display_menu() {
        
    }

    function display_live_chat() {
        
    }

    function display_bread_crumb($catID, $type) {   //$type defines the type of the PageUI, for example item.php, cat.php, home, contact, etc.                                     //we have 3 kind of $type: item, cat, and then everything else
    }

    function DisplayMenu() {
        if ($this->noindex_flag == 0) {
            $menu = '
    		
        ';
            return $menu;
        }
    }

    function HTML1() {

        echo '<body ', $this->get_html_body_tag_extra(), '>';
    }

    function HTML1_2() {
        echo '
</body>
';
    }

    function setMeta($meta) {
        $this->meta_tags = $meta;
    }

    function preload_images(){

    }
    
    function pageUI___displayHead($set_lang = NULL) {
        echo '<head>';
        echo
        $this->Display_meta_tags()
        . "\n" .
        $this->DisplayStyles()
        . "\n" .
        $this->DisplayJavascripts($set_lang)
        . "\n" . 
        (empty($this->preload_imgs) ? '' : $this->preload_images())
        . "
<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '".(defined("GOOGLE_ANALYTICS_TRACKING_NUMBER") ? GOOGLE_ANALYTICS_TRACKING_NUMBER : '')."']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</head>
";
    }

    function pageUI___displayFooter() {
        
    }

//END OF CLASS PageUI
}


function get_ishowcase_content_meta($pg_fields, $pg_info, $set_lang_flag, $set_lang, $newsID="",$tbl_arr,$search="")
{
   if($set_lang_flag==1) 
    {                   
          $table0=$tbl_arr[0];
          $table2=$tbl_arr[2];
    }
    else
    {
           $table0=$pg_info['tbl0'];
           $table2=$pg_info['tbl2'];
    } 

    if ($search && ($pg_fields['pg_type']==55)) 
    {
        $search_arr = explode("--",$search);
        $search = explode("|",$search_arr[0]);
        $collection_guid = $search[2];	
        $pg_fields_arr = query_get_rows_super_advanced($collection_guid, $table0, 'GUID');                
        $pg_fields =$pg_fields_arr[0];
    }

    $pg_fields['pg_name']=ucwords($pg_fields['pg_name']);
    $pg_name =$pg_fields['pg_name']; 
    $meta_title = $pg_fields['meta_title'];
    $meta_description = $pg_fields['meta_description'];
    $meta_keywords = $pg_fields['meta_keywords'];
    $meta =  "\n";

    $phone="";
    get_brand_name_new($pg_info['brand_master_id'], $brand_name);
    $real_brand_name = $brand_name['real_brand_name'];
    $brand_name = $brand_name['brand_name'];
    
    
    switch ($pg_fields['pg_type'])
    {
          //Linking pages
          case 1:
          case 4:
          case 9:
          case 8:
                if($meta_title=='')
                {
                    $meta_title = addslashes($real_brand_name).' | '.SHOWCASE_META_BRAND_NAME;
                }
                if ($meta_description=='')
                {
                    $meta_description=SHOWCASE_META_BRAND_NAME.', your authorized dealer of '.addslashes($real_brand_name).' '.$pg_name;
                }
                
                if ($meta_keywords=='')
                {
                    //<all parent and current collection names>
                    $collection_str = "";

                    $collection_arr = query_get_rows_super_advanced($pg_fields['guid'], $table0, 'NOPRODUCT');
                    for ($i=0;$i<count($collection_arr);$i++)
                    {
                        $collection = $collection_arr[$i];
                        $collection_str .= $collection['pg_name'].", ";
                        
                        $sub_collection_arr = query_get_rows_super_advanced($collection['guid'], $table0, 'NOPRODUCT');
                        for ($j=0;$j<count($sub_collection_arr);$j++)
                        {
                            $sub_collection = $sub_collection_arr[$j];
                            $collection_str .= $sub_collection['pg_name'].", ";
                        }
                    } 
                    $bread_crumb_arr = get_bread_crumb_array($pg_fields['bread_crumb'], $table0);
                    for ($j=0;$j<count($bread_crumb_arr);$j++)
                    {
                        $collection_str .= $bread_crumb_arr[$j][0].", ";
                    } 
                    
                }
                  
              break;
              
              
          case 2:
                
                $prod_det_fields = query_pg_fields($pg_fields['guid'], $pg_info['tbl1']);
                if($meta_title=='')
                {
                    $meta_title = addslashes($real_brand_name);
                    if (isset($prod_det_fields['model_number']))
                        $meta_title.=' | '.$prod_det_fields['model_number'];
                    $meta_title.=' | '.SHOWCASE_META_BRAND_NAME;
                }

                
                if ($meta_description=='') 
                {
                    $collection_arr = query_get_rows_super_advanced($pg_fields['parent'], $table0, 'GUID');
                    $collection_name = $collection_arr[0]['pg_name']; 

                    $meta_description = 'This '.$real_brand_name.' '.$prod_det_fields['model_number'].' is from '.$collection_name.' collection.';
                    
                   $spec_results = query_get_rows($pg_info["guid"], $table2);
                   $num_rows = mysql_num_rows($spec_results);
                    
                   if ($num_rows>0)
                   {
                          if ($num_rows>4)
                              $num_rows = 4;
                          for ($i=0;$i<$num_rows;$i++)
                          {
                              $spec = mysql_fetch_array($spec_results);
                              $meta_description .= " The ".$spec['spec_field'].' is '.$spec['spec_val'].".";
                          }
                   }
                }



                if ($meta_keywords=='')
                {
                    //<all parent collection names>
                    $collection_str = "";
                    $bread_crumb_arr = get_bread_crumb_array($pg_fields['bread_crumb'], $table0);
                    for ($j=0;$j<count($bread_crumb_arr);$j++)
                    {
                        if ($collection_str)
                            $collection_str.=", ";
                        $collection_str .= $bread_crumb_arr[$j][0];
                    } 
                    $alt_model_numbers_arr = get_alternative_model_numbers ($prod_det_fields['model_number']);
                    $alt_model_numbers_str = "";
                    
                    $meta_keywords = SHOWCASE_META_BRAND_NAME.", authorized retailer, ".addslashes($real_brand_name).", $collection_str";
                    if ($alt_model_numbers_arr)
                    {
                        $alt_model_numbers_str = implode(",",$alt_model_numbers_arr);
                        $meta_keywords.=",".$alt_model_numbers_str;  
                    }
                        
                }                
            break;
            
            
    }
    
    
    if($meta_title=='')
        $meta_title=$pg_name.' | '.SHOWCASE_META_BRAND_NAME;

    $meta .= "<title>$meta_title</title>";
    $meta .= "\n";
    $meta .= '<META name="title" content="'. $meta_title. '">';
    $meta .= "\n";
    
    
    if($meta_description=='')
        $meta_description=$pg_name.' | '.SHOWCASE_META_BRAND_NAME;
    $meta .='<META name="description" content="'. $meta_description. '">';
    $meta .= "\n";
    
    
    if($meta_keywords=='')
        $meta_keywords=$pg_name.' | '.SHOWCASE_META_BRAND_NAME;
    
    $meta .= '<META name="keywords" content="'. $meta_keywords. '">';
    $meta .= "\n";
    
    return $meta;
    
} 
 

?>
