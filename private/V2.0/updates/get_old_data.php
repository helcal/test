<?
    /*
    THIS FUNCTION CONNECTS TO OUR OLD DATABASES (HG, LESLIE, LUXNILE, ETC...) GET THE DATA AND IMAGES, 
    AND COPY THE DATA INTO THE NEW DB FORMAT (IRCP AND ISHOWCASE) AND COPY ALL THE IMAGES INTO THE
    CORRESPONDING IMAGE FOLDERS OF THE BRAND AND ALSO UPDATE THE IMAGE DATABASES.
     
    INSTRUCTIONS:
    1- LOGIN TO IRCP AS AN ADMINISTRATOR, GOTO MANAGE BRANDS, AND CREATE A NEW BRAND, THEN REMEMBER THE BRAND_NAME AND BRAND_MASTER_ID
    2- GOTO UPDATE_FUNCTIONS.PHP AND DEFINE THE FOLLOWINGS:
        NEWURL
        IMAGE_FOLDER
        BRAND_FOLDER
        BRAND_NAME
        BRAND_MASTER_ID
        STARTING_CATID
        connect_EXT()
    3- RUN get_old_data.php
    4- IF THERE ARE NO ERRORS (CHECK GET_OLD_DATA_BRAND_NAME/*ERROR*.TXT), GOTO BRND_BRANDNAME DATABASE AND DELETE
        THE FIRST RECORD WHOSE STATUS=0 AND PARENT=0.
    5- LOGIN TO IRCP AS AN ADMINISTRATOR, GOTO MANAGE USERS, AND GIVE ACCESS FOR THIS BRAND TO DESIRED USERS 
    */
    require ('../../../includes/init.php');
    require ('../../../private/V2.0/updates/update_functions.php');
    
    $format = '%H_%M_%S';
    $current_time = strftime($format);
    $st = "$current_time -- ".BRAND_NAME."\n\n";
    echo $st;

    echo '<br>Working on '.BRAND_NAME;
    echo '<br>........... brand_master_id='.BRAND_MASTER_ID;
    echo '<br>........... parent_guid='.PARENT_GUID;
    
    check_folder_existance('get_old_data/'.BRAND_FOLDER);
    
    $normal_file = "get_old_data/".BRAND_FOLDER.$current_time."_normal_".BRAND_NAME.".txt";    
    $error_file = "get_old_data/".BRAND_FOLDER.$current_time."_error_".BRAND_NAME.".txt";
    $guid_cat_file = "get_old_data/".BRAND_FOLDER.$current_time."_guid_cat_".BRAND_NAME.".txt";   
    
    $fp_normal = fopen($normal_file, 'w');
    fwrite($fp_normal, $st);
    
    $fp_error = fopen($error_file, 'w');    
    fwrite($fp_error, $st);
    
    $fp_guid_cat = fopen($guid_cat_file, 'w');
    fwrite($fp_guid_cat, $st);
    
    $_SESSION[guid] = 2345;
    
    recursive_advance(STARTING_CATID, $fp_normal, $fp_error, $fp_guid_cat, PARENT_GUID);
    
    fclose($fp_normal);
    fclose($fp_error);
    fclose($fp_guid_cat);

    echo "<h1> DONE!</h1>";


?>