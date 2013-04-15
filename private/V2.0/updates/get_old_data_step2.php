<?
    /*
    THIS FUNCTIONS ADD MORE INFORMATION TO THE BRAND AFTER ALL THE INFORMATION HAS BEEN UPLOADED FROM LESLIE, HIGHGLOW, ETC
    1-ADD THE BRAND TO OUR ADMIN USER
    2-ASSIGN ENGLIS AS THE BRAND'S DEFAULT LANGUAGE
    3-GENERATE IMAGE THUMBNAILS
     
    INSTRUCTIONS:
    1- RUN get_old_data.php FOLLOW THE INSTRUCTIONS ON THAT FILE!
    2- Define thumbnails sizes before calling update_brand_configuration. line 82 oln this page
      $HTTP_POST_VARS[img_xsmall] = 95;
      $HTTP_POST_VARS[img_small] = 196;
      $HTTP_POST_VARS[img_medium] = 270;
      $HTTP_POST_VARS[img_large] = 957;
      
      Default language: EN
      $HTTP_POST_VARS[language]="EN"; //Line 94
    3- RUN get_old_data_step2.php
    */
    

    define("VERSION", "V2.0");
    define("BRANDS_ROOT_FOLDER","../../../public/".VERSION."/brands/");
    require ('../../../includes/init.php');
    require ('../../../private/V2.0/updates/update_functions.php');
    define("BRANDS_ROOT_FOLDER","../../../public/".VERSION."/brands/");

    $format = '%H_%M_%S';
    $current_time = strftime($format);
    $st = "$current_time -- ".BRAND_NAME."\n\n";
    echo $st;

    echo '<br>Step 2 for '.BRAND_NAME;
    echo '<br>........... brand_master_id='.BRAND_MASTER_ID;
    echo '<br>........... parent_guid='.PARENT_GUID;
    
    $normal_file = "get_old_data/".BRAND_FOLDER.$current_time."_normal_".BRAND_NAME."_step2.txt";    
    $error_file = "get_old_data/".BRAND_FOLDER.$current_time."_error_".BRAND_NAME."_step2.txt";
    
    $fp_normal = fopen($normal_file, 'w');
    fwrite($fp_normal, $st);
    
    $fp_error = fopen($error_file, 'w');    
    fwrite($fp_error, $st);
    
    //$_SESSION[guid] = 2345;
    //recursive_advance(STARTING_CATID, $fp_normal, $fp_error, $fp_guid_cat, PARENT_GUID);
    
    connect_INT();

    echo '<br>***********************';
    echo '<br>** Brand access **';
    echo '<br>***********************';

    $access = 1;
    $result = query_general("update brand_access set brand_access='$access' where userID='".USERID."' and brand_master_id='".BRAND_MASTER_IDV."' ");
    if($result)  
    {
        $st="Brand access on ".BRAND_NAME." for userID=".USERID." granted.";
        fwrite($fp_normal, $st);
    }
    else
    {
        $st="Brand access on ".BRAND_NAME." for userID=".USERID." failed.";
        fwrite($fp_error, $st);
    }

    $st="Brand Configuration";
    fwrite($fp_normal, $st);
    
    //Enable languages for the brand
    $HTTP_POST_VARS[language_manager] = 1;
    $_SESSION[validated_userID] = USERID;

    echo '<br>***********************';
    echo '<br>** Images **';
    //3. Thumbsnails
    $HTTP_POST_VARS[img_xsmall] = 94;
//    $HTTP_POST_VARS[img_small] = 196;
//    $HTTP_POST_VARS[img_medium] = 270;
//    $HTTP_POST_VARS[img_large] = 957;

    echo '<br>***********************';
    //Generate the thumbnails
    update_brand_configuration(BRAND_MASTER_ID);

    $st="Languages";
    fwrite($fp_normal, $st);

    echo '<br>***********************';
    echo '<br>** Languages **';
    //1. Create the default language for the brand
    $error_msg = array();
    $normal_msg = array();
    $warning_msg = array();
    $HTTP_POST_VARS[language]="EN";
    $language = $HTTP_POST_VARS[language];
    $HTTP_POST_VARS[default_language]="EN";
    $HTTP_POST_VARS[confirmation]="CONFIRMED";

    $st =  'Add the language='.$language;
    echo '<br>'.$st;
    fwrite($fp_normal, $st);
    update_language_settings(BRAND_MASTER_ID, &$error_msg, &$normal_msg, &$warning_msg);
    echo '<br>Error:';print_r($error_msg);
    echo '<br>Normal:';print_r($normal_msg);
    echo '<br>Warning:';print_r($warning_msg);

    $error_msg = array();
    $normal_msg = array();
    $warning_msg = array();
    $st =  'Default language='.$HTTP_POST_VARS[default_language];
    echo '<br>'.$st;
    fwrite($fp_normal, $st);


    change_default_language(BRAND_MASTER_ID, &$error_msg, &$normal_msg, &$warning_msg);
    echo '<br>Error:';print_r($error_msg);
    echo '<br>Normal:';print_r($normal_msg);
    echo '<br>Warning:';print_r($warning_msg);

    //2. Copy the information from the original tables to the language tables
    $brnd_tbl_arr = get_brnd_tbl_name(BRAND_NAME);
    $db_tbl_name_base = get_base_brand_db_name_for_lang(BRAND_MASTER_ID, $language);
    $lang_brnd_tbl_arr = get_brnd_tbl_name($db_tbl_name_base);

    for ($i=0;$i<count($brnd_tbl_arr);$i++)
    {
    
          $query = "SHOW TABLES LIKE '$lang_brnd_tbl_arr[$i]'";
          $result = query_general($query);
          $num_rows = mysql_num_rows($result);
          
          if ($num_rows==1)
          {
               $query = "INSERT INTO $lang_brnd_tbl_arr[$i]
                         SELECT * FROM $brnd_tbl_arr[$i]";
               $result = query_general($query);
                $st =  "Information from $brnd_tbl_arr[$i] inserted on $lang_brnd_tbl_arr[$i]";
                fwrite($fp_normal, $st);
          }
    
    }

    echo '<br>***********************';

    fclose($fp_normal);
    fclose($fp_error);
    
    echo "<h1> DONE!</h1>";


?>