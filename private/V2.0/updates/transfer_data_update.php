<?

/*
 *   THIS FUNCTION CONNECTS TO OUR OLD DATABASES (HG, LESLIE, LUXNILE, ETC...) GET THE DATA AND IMAGES,
 *   AND COPY THE DATA INTO THE NEW DB FORMAT (IRCP AND ISHOWCASE) AND COPY ALL THE IMAGES INTO THE
 *   CORRESPONDING IMAGE FOLDERS OF THE BRAND AND ALSO UPDATE THE IMAGE DATABASES.*
 *
 *   INSTRUCTIONS:
 *   1- LOGIN TO IRCP AS AN ADMINISTRATOR, GOTO MANAGE BRANDS, AND CREATE A NEW BRAND, THEN REMEMBER THE BRAND_NAME AND BRAND_MASTER_ID
 *   2- GOTO UPDATE_FUNCTIONS.PHP AND DEFINE THE FOLLOWINGS:
 *   NEWURL
 *   IMAGE_FOLDER
 *   BRAND_FOLDER
 *   BRAND_NAME
 *   BRAND_MASTER_ID
 *   STARTING_CATID
 *   connect_EXT()
 *   3- RUN get_old_data.php
 *   4- IF THERE ARE NO ERRORS (CHECK GET_OLD_DATA_BRAND_NAME/*ERROR*.TXT), GOTO BRND_BRANDNAME DATABASE AND DELETE
 *   THE FIRST RECORD WHOSE STATUS=0 AND PARENT=0.
 *   5- LOGIN TO IRCP AS AN ADMINISTRATOR, GOTO MANAGE USERS, AND GIVE ACCESS FOR THIS BRAND TO DESIRED USERS
 */

define("BRANDS_ROOT_FOLDER", "../../../public/V2.0/brands/");
require ('../../../includes/init.php');
require ('update_functions.php');

/* $brandInfo must have this structure
 * "ORIG_SITE"          =>  "lsl" or "hg" must modify programming to get others
 * "NEWURL"             =>  "http://www.highglowonline.com/"
 * "IMAGE_FOLDER"       =>  "user_images/"
 * "BRAND_FOLDER"       =>  "montplancpen/"
 * "BRAND_NAME"         =>  "montplancpen"
 * "BRAND_MASTER_ID"    =>  "129"
 * "STARTING_CATID"     =>  "466"
 */

function transfer_data_update($brandInfo) {

    global $HTTP_POST_VARS;
    connect_INT();
    
    /* Log Related Stuff */
        $format = '%H_%M_%S';
        $current_time = strftime($format);
        echo $st = '<h2>' . $current_time . " -- " . $brandInfo['BRAND_NAME'] . "</h2>\n\n";

        //doesn't use constants =>> doesn't need to change.
        check_folder_existance('transfer_update/' . $brandInfo['BRAND_FOLDER']);

        $normal_file = "transfer_update/" . $brandInfo['BRAND_FOLDER'] . $current_time . "_normal_" . $brandInfo['BRAND_NAME'] . ".txt";
        $error_file = "transfer_update/" . $brandInfo[' BRAND_FOLDER'] . $current_time . "_error_" . $brandInfo['BRAND_NAME'] . ".txt";
        $guid_cat_file = "transfer_update/" . $brandInfo['BRAND_FOLDER'] . $current_time . "_guid_cat_" . $brandInfo['BRAND_NAME'] . ".txt";

        //open logs
        $fp_normal = fopen($normal_file, 'w');
        $fp_error = fopen($error_file, 'w');
        $fp_guid_cat = fopen($guid_cat_file, 'w');

        //write to logs
        fwrite($fp_normal, $st);
        fwrite($fp_error, $st);
        fwrite($fp_guid_cat, $st);
    /* End Log related Stuff */

    foreach ($brandInfo as $key=>$value)
        echo $key . ' :: ' . $value . "<br />\n";

    echo '<br>***********************<br /><br />';
        
    //Brand Access Update
        echo '<br>***********************';
        echo '<br>** Brand access **';

        $result = query_general("update brand_access set brand_access='1' where 
                                userID='" . $brandInfo['USERID'] . "' and 
                                brand_master_id='" . $brandInfo['BRAND_MASTER_ID'] . "' ");
        if ($result) {
            $st = "Brand access on " . $brandInfo['BRAND_NAME'] . " for userID=" . $brandInfo['USERID'] . " granted.";
            fwrite($fp_normal, $st);
        } else {
            $st = "Brand access on " . $brandInfo['BRAND_NAME'] . " for userID=" . $brandInfo['USERID'] . " failed.";
            fwrite($fp_error, $st);
        }

        fwrite($fp_normal, 'Brand Configuration');
        
        echo '<br>** Updated';
        echo '<br>***********************<br>';
    //End brand access update
    
    //Begin Language Updates
        //Enable languages for the brand
        $HTTP_POST_VARS['language_manager'] = 1;
        $_SESSION['validated_userID'] = $brandInfo['USERID'];
        
        read_brand_configuration($brandInfo['BRAND_MASTER_ID']);
        
        fwrite($fp_normal, 'Languages');
        echo '<br>***********************';
        echo '<br>** Languages **';
        //1. Create the default language for the brand
        $error_msg = array();
        $normal_msg = array();
        $warning_msg = array();

        $HTTP_POST_VARS['language'] = "EN";
        $language = $HTTP_POST_VARS['language'];
        $HTTP_POST_VARS['default_language'] = "EN";
        $HTTP_POST_VARS['confirmation'] = "CONFIRMED";

        $st = 'Add the language=' . $language;
        echo '<br>' . $st;
        fwrite($fp_normal, $st);
        
        //see if this is first run or if it is update 
        //if update the for loop below wont run
        $EN_tbls_res = query_general("SHOW TABLES LIKE 'brnd_".$brandInfo['BRAND_NAME']."_EN'");
        $first_run = (mysql_num_rows($EN_tbls_res) == 0);
        echo "<br>First Run: ".$first_run."<br>\n";
        
        update_language_settings($brandInfo['BRAND_MASTER_ID'], $error_msg, $normal_msg, $warning_msg);
        echo '<br>Error:';
        print_r($error_msg);
        echo '<br>Normal:';
        print_r($normal_msg);
        echo '<br>Warning:';
        print_r($warning_msg);

        $error_msg = array();
        $normal_msg = array();
        $warning_msg = array();
        $st = 'Default language=' . $HTTP_POST_VARS['default_language'];
        echo '<br>' . $st;
        fwrite($fp_normal, $st);


        change_default_language($brandInfo['BRAND_MASTER_ID'], $error_msg, $normal_msg, $warning_msg);
        echo '<br>Error:';
        print_r($error_msg);
        echo '<br>Normal:';
        print_r($normal_msg);
        echo '<br>Warning:';
        print_r($warning_msg);

        //2. Copy the information from the original tables to the language tables
        $brnd_tbl_arr = get_brnd_tbl_name($brandInfo['BRAND_NAME']);
        $db_tbl_name_base = get_base_brand_db_name_for_lang($brandInfo['BRAND_MASTER_ID'], $language);
        $lang_brnd_tbl_arr = get_brnd_tbl_name($db_tbl_name_base);
        
        if ($first_run){
            for ($i = 0; $i < count($brnd_tbl_arr); $i++) {

                $query = "SHOW TABLES LIKE '$lang_brnd_tbl_arr[$i]'";
                $result = query_general($query);
                $num_rows = mysql_num_rows($result);

                if ($num_rows == 1) {
                    $query = "INSERT INTO $lang_brnd_tbl_arr[$i]
                                SELECT * FROM $brnd_tbl_arr[$i]";
                    $result = query_general($query);
                    $st = "Information from $brnd_tbl_arr[$i] inserted on $lang_brnd_tbl_arr[$i]";
                    fwrite($fp_normal, $st);
                }
            }
        }

        echo '<br>***********************';
    //End Language Tables
    
    recursive_advance($brandInfo['STARTING_CATID'], $fp_normal, $fp_error, $fp_guid_cat, $brandInfo['PARENT_GUID'], $brandInfo, $lang_brnd_tbl_arr);//, array());

    fclose($fp_normal);
    fclose($fp_error);
    fclose($fp_guid_cat);

    echo "<h1>" . $brandInfo['BRAND_NAME'] . " image transfer and language updates complete!</h1>\n";
}

function force_generate_thumbnails($brandInfo, $size = true){
    connect_INT();

    global $HTTP_POST_VARS;
    $HTTP_POST_VARS['language_manager'] = 1;
    
    if (!isset($_SESSION['validated_userID'])) $_SESSION['validated_userID'] = $brandInfo['USERID'];
    
    echo '<br>***********************';
    echo '<br>** Images **';

    //3. Thumbsnails
    
    $HTTP_POST_VARS['thumb_ratio_height'] = 800;
    $HTTP_POST_VARS['thumb_ratio_width'] = 500;
    
    $HTTP_POST_VARS['img_xsmall'] = 94;
    $HTTP_POST_VARS['img_xsmall_height'] = (int)($HTTP_POST_VARS['img_xsmall'] * $HTTP_POST_VARS['thumb_ratio_height']) / $HTTP_POST_VARS['thumb_ratio_width'];
    
    $HTTP_POST_VARS['img_small'] = 160;
    $HTTP_POST_VARS['img_small_height'] = (int)($HTTP_POST_VARS['img_small'] * $HTTP_POST_VARS['thumb_ratio_height']) / $HTTP_POST_VARS['thumb_ratio_width'];
    
    $HTTP_POST_VARS['img_medium'] = 270;
    $HTTP_POST_VARS['img_medium_height'] = (int)($HTTP_POST_VARS['img_medium'] * $HTTP_POST_VARS['thumb_ratio_height']) / $HTTP_POST_VARS['thumb_ratio_width'];
    
    $HTTP_POST_VARS['img_large'] = 500;
    $HTTP_POST_VARS['img_large_height'] = (int)($HTTP_POST_VARS['img_large'] * $HTTP_POST_VARS['thumb_ratio_height']) / $HTTP_POST_VARS['thumb_ratio_width'];
    
    $HTTP_POST_VARS['thumb_ratio_color'] = array('r'=>255,'g'=>255,'b'=>255);
    
    set_time_limit(5184000);

    //Generate the thumbnails
    if (intval($size) === 0 || $size === true || $size == 'all') update_brand_configuration($brandInfo['BRAND_MASTER_ID'], 'xsmall'); //Rhett Lowe - allow forced update to reduce need for shorting if statement
    if (intval($size) === 1 || $size === true || $size == 'all') update_brand_configuration($brandInfo['BRAND_MASTER_ID'], 'small');
    if (intval($size) === 2 || $size === true || $size == 'all') update_brand_configuration($brandInfo['BRAND_MASTER_ID'], 'medium');
    if (intval($size) === 3 || $size === true || $size == 'all') update_brand_configuration($brandInfo['BRAND_MASTER_ID'], 'large');
}

/* Comment this section out if you plan to use in another program */

if(!isset($_GET['line'])) exit;

$csv_loc = 'brands_transfer_info_dejaun.csv';
$csv = fopen($csv_loc, 'r');

$fields = array(
    0 => "ORIG_SITE",
    1 => "NEWURL",
    2 => "IMAGE_FOLDER",
    3 => "BRAND_FOLDER",
    4 => "BRAND_NAME",
    5 => "BRAND_MASTER_ID",
    6 => "PARENT_GUID",
    7 => "USERID",
    8 => "STARTING_CATID"
);

$run = (isset($_GET['run']) ? intval($_GET['run']) : 1);
if (isset($_GET['line'])) $line = intval($_GET['line']);

$count = -1;
while ($row = fgetcsv($csv)) {
    $count++;
    if ($count < $line || $count > $line+$run-1) 
        continue;

    $brandInfo = array_combine($fields, $row);

    if (isset($_GET['mid'])) $brandInfo['BRAND_MASTER_ID'] = intval($_GET['mid']);
    
    if (isset($_GET['step']) && (intval($_GET['step']) == 1 || $_GET['step'] == 'all')) {
        transfer_data_update($brandInfo);
    }
    if (isset($_GET['step']) && (intval($_GET['step']) == 2 || $_GET['step'] == 'all')) {
        if (!isset($_GET['size'])) force_generate_thumbnails($brandInfo);
        else if (isset($_GET['size'])) force_generate_thumbnails($brandInfo, $_GET['size']);
    }
    
}

echo '<h1>Script Complete</h1>';
?>