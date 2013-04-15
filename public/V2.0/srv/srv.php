<?
    session_start();
    session_regenerate_id();
    require_once("$_SERVER[DOCUMENT_ROOT]/includes/init.php");
    connect_internal_db();
    
    //**************************************************************************
    //VARIABLE VALIDATION START
    //--------------------------------------------------------------------------
    $guid = $_POST['guid']; 
    if(!is_numeric($guid) || $guid<-1)
        die("<h2>ACCESS DENIED!</h2><h4>(E106) Invalid guid!</h4>");
    //--------------------------------------------------------------------------
    $cmd = $_POST['cmd'];
    if($cmd!='')
    {
      $pattern  = '/^[A-Z_]{3,30}$/';
      if (!preg_match($pattern,$cmd)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E100) Command syntax error!</h4>");
    }    
    //--------------------------------------------------------------------------
    $username = $_POST['username'];
    if($username!='')
    {
      $pattern  = '/^[a-zA-Z0-9_.-]{3,15}$/';
      if (!preg_match($pattern,$username)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E102) Invalid Username!</h4>");      
    }
    //--------------------------------------------------------------------------
    $userID = $_POST['userID']; 
    if($userID!='')
    {
      $pattern  = '/^[0-9]{1,10}$/';
      if (!preg_match($pattern,$userID)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E103) Invalid User ID!</h4>");
    }
    //--------------------------------------------------------------------------   
    $filename = $_POST['filename'];    //NAME OF THE FILE ON CLIENT'S   
    if($filename!='')                         //SERVER WHICH HOSTS ICP DIV.
    {
      $pattern  = '/^[a-zA-Z0-9_.-]{4,15}$/';
      if (!preg_match($pattern,$filename)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E104) Invalid File!</h4>");      
    } 
    //--------------------------------------------------------------------------   
    $set_lang = $_POST['lang'];    //LANGUAGE
    if($set_lang!='')                       
    {
      $pattern  = '/^[A-Z]{2,3}$/';
      if (!preg_match($pattern,$set_lang)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E111) Invalid Language Code!</h4>");      
    }   

    //--------------------------------------------------------------------------   
    $client_pg_type = $_POST['client_pg_type'];    //ASP, PHP, ETC
    $iframe_flag = $_POST['iframe']; // MIC 2012-07-20 Flag that is used on GET_META cmd. If it's activated, then only returns the style tag
    //--------------------------------------------------------------------------
     
    //--------------------------------------------------------------------------
    //VARIABLE VALIDATION END
    //**************************************************************************
                 
                                               
    //**************************************************************************
    //AUTHENTICATION START
    /*
    PROCEDURE:
    1- CLIENT SENDS A CURL REQUEST TO SRV WITH USERNAME AND USERID.
    2- SRV MATCHES THIS DATA AND GETS CLIENTS DOMAIN ADDRESS FROM DB TO CALLBACK.
    3- SRV CALLS BACK CLIENT AND REQUESTS TO CREATE A FILE, NAMED AS $TOKEN.TXT,
       WHERE $TOKEN IS A RANDOM NUMBER GENERATED ON SRV (SEE BELOW). 
    4- NOW, SRV TRIES TO OPEN THIS FILE ON CLIENT'S SERVER.
    5- IF THE FILE IS OPENED SUCCESSFULLY, THEN AUTHENTICATION IS SUCCESSFULL.
    6- UPON SUCCESS, SRV REQUESTS CLIENT TO DELETE THIS TEMP FILE AND PROVIDES
       THE REQUESTED INFORMATION TO THE CLIENT. 
    */
    $row = srv_user_autentication ($userID,$user_name);
    if ($row['error'])
    {
        //######################################################################
        //GET CLIENT DOMAIN ADDRESS AND VERIF_TBL
        $client_address = $row['address'];         
        $verif_tbl = $row['verif_tbl'];
        $company = $row['company'];
        $phone = $row['phone'];
        //######################################################################
    }
    else
        die($row['error']);
    //AUTHENTICATION END
    //**************************************************************************
    
    $pg_info = get_pg_info_from_guid($guid);
    if(!$pg_info)
        die("<h1>ACCESS DENIED! (E107)</h1><h4>Missing pg info!");   //PG_INFO CANNOT BE RETRIEVED 
    $pg_fields = query_pg_fields($guid, $pg_info['tbl0']);
    
    //##########################################################################
    //##########################################################################
    //GROUP MANAGER VALIDATION START  
    if(!group_manager_dealer_validation($guid, $pg_info['brand_master_id'], $verif_tbl))
    {
        die("<h1>ACCESS DENIED! (E112)</h1><h4>Group Verification Failed!</h4>");   //GROUPING VERIFICATION FAILED                                              
    }
    //GROUP MANAGER VALIDATION END
    //##########################################################################
    //##########################################################################
    
    //**************************************************************************
    //VERIFICATION START
    $verif_tbl_fields = verify_access_with_verif_tbl($pg_info, $verif_tbl); 
    if($verif_tbl_fields)
    {        
        if($pg_fields['status']==1)
        {
            $pg_fields_x = $pg_fields; 
            while ($pg_fields_x['parent'])
            {
                $parent_fields = query_pg_fields($pg_fields_x['parent'], $pg_info['tbl0']);
                if($parent_fields['status']==1 || $parent_fields['guid']==0)
                {
                    $pg_fields_x = $parent_fields;

                    //Validate the grouping for the parent
                    /*$x_pg_info = get_pg_info_from_guid($pg_fields_x[parent]);
                    $verif_tbl_fields = verify_access_with_verif_tbl($x_pg_info, $verif_tbl);
                    if($verif_tbl_fields)
                    {
                          die("<h1>ACCESS DENIED! (E108)</h1>"); //PAGE/PRODUCT DISABLED
                    } */


                    //VARIFICATION SUCCESSFUL - COLLECTION SUCCESSFUL - PROCEED TO PRESENTATION
                }
                else
                    die("<h1>ACCESS DENIED! (E110)</h1>");  //PARENT DISABLED
            
            }
        }    
        else
            die("<h1>ACCESS DENIED! (E109)</h1>"); //PAGE/PRODUCT DISABLED
    }
    else
      die("<h1>ACCESS DENIED! (E108)</h1>"); //VERIF TABLE ACCESS DENIED: STATUS_BY_PROVIDER AND BY RECIPIENT    
    
    //VERIFICATION END
    //**************************************************************************
    
    srv_access_log($guid, $cmd, $username, $userID, $filename, $client_address, $pg_info['brand_name']);

    //##########################################################################
    //##########################################################################
    //FIND THE DESIRED LANGUAGE - START
    $lang_vars_arr = get_lang_vars($pg_info['brand_master_id'], $userID);
    //CASE1: IF LANGUAGE MANAGER IS ENABLED FOR THE BRAND OF GUID AND LANG ($set_lang) IS NOT SET BY THE REQUESTOR WEBSITE IN THE CURL
    if($lang_vars_arr['brand_language_manager']==1 && $set_lang=='')    
    {          
        //GET THE DEALER DEFAULT LANGUAGE
        $dealer_default_lang = $lang_vars_arr['dealer_default_lang'];
        foreach($lang_vars_arr[brand_set_languages] as $key => $value)
        {
            if($key==$dealer_default_lang)
            {
                //IF THE DEALER DEFAULT LANGUAGE IS SUPPORTED BY THE BRAND
                $set_lang = $dealer_default_lang;
                break;
            }
        }
        if($set_lang=='')   //THIS MEANS THE DEALER DEFAULT LANGUAGE >>>>IS NOT<<<< SUPPORTED BY THE BRAND OF GUID
        {
            //IF DEALER DEFAULT LANGUAGE IS NOT SUPPORTED BY THE BRAND, THEN GET THE BRAND
            //DEFAULT LANGUAGE
            if($lang_vars_arr['brand_default_language']!='')
                $set_lang = $lang_vars_arr['brand_default_language'];
            else
            {
                //THE BRAND DEFAULT LANGUAGE IS NOT SET -->>>>>> GET DATA FROM ANY AVAILABLE LANGUAGE <<<<<<--
                foreach($lang_vars_arr['brand_set_languages'] as $key => $value)
                {
                    $set_lang = $key;
                    break;
                }
            }
        }        
    }
    //CASE2: IF LANGUAGE MANAGER IS ENABLED FOR THE BRAND OF GUID AND LANG ($set_lang) >>>>IS<<<< SET BY THE REQUESTOR WEBSITE IN THE CURL
    else if($lang_vars_arr['brand_language_manager']==1 && $set_lang!='')
    {
        //CHECK TO SEE IF THE $SET_LANG SET IN CURL IS SUPPORTED BY THE BRAND
        $lang_support_by_brand_flag = 0;
        foreach($lang_vars_arr['brand_set_languages'] as $key => $value)
        {
            if($key==$set_lang)
            {
                //IF $SET_LANG SET IN CURL IS SUPPORTED BY THE BRAND, THEN LEAVE THE $SET_LANG INTACT AS IT CAME BY CURL
                $lang_support_by_brand_flag=1;
                break;
            }
        }
        //IF THE $SET_LANG SET IN CURL >>>>IS NOT<<<< SUPPORTED BY THE BRAND THEN GET THE BRAND
        //DEFAULT LANGUAGE
        if($lang_support_by_brand_flag==0)
        {
            $set_lang='';   //UNSET $SET_LANG, WHICH CAME FROM CURL
            if($lang_vars_arr[brand_default_language]!='')
                $set_lang = $lang_vars_arr[brand_default_language];
            else    //THE BRAND DEFAULT LANGUAGE IS NOT SET -->>>>>> GET DATA FROM ANY AVAILABLE LANGUAGE <<<<<<--
            {                
                foreach($lang_vars_arr[brand_set_languages] as $key => $value)
                {
                    $set_lang = $key;
                    break;
                }
            }
        }
    }
    //IF $SET_LANG IS NOT EMPTY BRAND LANGUAGE MANAGER IS ENABLED, THEN THE DESIRED LANG HAS BEEN SET => SET THE FLAG
    $set_lang_flag = 0;
    if($lang_vars_arr[brand_language_manager]==1 && $set_lang!='')
        $set_lang_flag = 1;
    if($set_lang_flag==1)
    {
        $base_brand_db = get_base_brand_db_name_for_lang($pg_info[brand_master_id], $set_lang);
        $tbl_arr = get_brnd_tbl_name($base_brand_db);      
        $lang_pg_fields = query_pg_fields($guid, $tbl_arr[0]);
        if($pg_info[type]==2)
        {
            $lang_item_det = query_pg_fields($guid, $tbl_arr[1]);
            $lang_item_specs_arr = query_get_rows($guid, $tbl_arr[2]); 
            $lang_parent_fields = query_pg_fields($pg_fields[parent], $tbl_arr[0]);
        }
    }
    //FIND THE DESIRED LANGUAGE - END
    //##########################################################################
    //##########################################################################
    
        
    //**************************************************************************
    //COLLECTION START    
        //*** COLLECTION SUCCEEDED IN VERIFICATION SECTION
    //COLLECTION END
    //**************************************************************************
    
    //**************************************************************************
    //PRESENTATION START  
    if($cmd=='GET_META')
    {
        if($set_lang_flag==1)
        {               
            $pg_name =  $lang_pg_fields['pg_name'];
            $meta_title = $lang_pg_fields['meta_title'];
            $meta_description = $lang_pg_fields['meta_description'];
            $meta_keywords = $lang_pg_fields['meta_keywords'];
            //echo '$tbl_arr[0]=', $tbl_arr[0];
            //echo ' --- $set_lang=', $set_lang;
        }
        else
        {
            $pg_name = $pg_fields['pg_name'];
            $meta_title = $pg_fields['meta_title'];
            $meta_description = $pg_fields['meta_description'];
            $meta_keywords = $pg_fields['meta_keywords'];
        }
 
        get_brand_name_new($pg_info['brand_master_id'], &$brand_name);
            
        echo "\n";
        echo '<META http-equiv="Content-Type" content="text/html; charset=UTF-8">';        
        echo "\n";

        if (!$iframe_flag) //// flag iframe false STARTS
        {
                $table0 = $tbl_arr[0];
                
                $res_meta_info = get_meta_info_for_guid($pg_info, $pg_fields, $table0, $brand_name, $meta_title, $meta_description, $title, $meta_keyword, $row);
                $meta_title = $res_meta_info['meta_title'];
                $title = $res_meta_info['title'];
                $meta_description = $res_meta_info['meta_description'];
                $meta_keywords = $res_meta_info['meta_keywords'];
                    
                        
            
                $meta = "<title>".$title."</title>
                ";
                $meta .= '<META name="title" content="'. $meta_title. '">';
                $meta .= "\n";
                
                echo $meta; 
                
                  if($meta_description!='')
                  { 
                      echo '<META name="description" content="', $meta_description, '">';
                      echo "\n";
                  }
                  if($meta_keywords!='')
                  { 
                      echo '<META name="keywords" content="', $meta_keywords, '">';
                      echo "\n";
                  }
                  
                    
        } // flag iframe false ends
        include_stylesheet_on_head($userID,$pg_info);        
        
        //die();
    }
    else
    {          
        if ($iframe_flag==1)
        {
            $iframe_arr = array();
            array_push($iframe_arr,$userID); 
            array_push($iframe_arr,$username);
            $userID = ISHOWCASE_IFRAME_USERID;
            $username = ISHOWCASE_IFRAME_USERNAME;
            $row = srv_user_autentication ($userID,$username);
            if ($row['error'])
                die ($row['error']);
            
            $client_address = $row['address'];      
        }
        $img_path = $_SERVER[DOCUMENT_ROOT]."/".get_img_path_for_brand($pg_info['brand_name'], "RELATIVE");
        $img_db_path = get_img_db_path($pg_info['guid'], $img_path);
        $img_array = get_img_array($img_db_path, $pg_info['brand_name']); 
        $showcase_path = get_showcase_path($pg_info['brand_name']);
        
        
        if(file_exists($_SERVER[DOCUMENT_ROOT]."/".$showcase_path))
            require($_SERVER[DOCUMENT_ROOT]."/".$showcase_path);
    }                
    //PRESENTATION END
    //**************************************************************************



?> 