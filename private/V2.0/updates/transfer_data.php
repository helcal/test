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


require ('../../../includes/init.php');
require ('../../../private/V2.0/updates/update_functions.php');


/* $brandInfo must have this structure
 * "ORIG_SITE"          =>  "lsl" or "hg" must modify programming to get others
 * "NEWURL"             =>  "http://www.highglowonline.com/"
 * "IMAGE_FOLDER"       =>  "user_images/"
 * "BRAND_FOLDER"       =>  "montplancpen/"
 * "BRAND_NAME"         =>  "montplancpen"
 * "BRAND_MASTER_ID"    =>  "129"
 * "STARTING_CATID"     =>  "466"
 */

function transfer_data($brandInfo){
    
    $format = '%H_%M_%S';
    $current_time = strftime($format);
    $st = "$current_time -- " . $brandInfo['BRAND_NAME'] . "\n\n";
    echo $st;

    //doesn't use constants =>> doesn't need to change.
    check_folder_existance('get_old_data/' . $brandInfo['BRAND_FOLDER']);

    $normal_file = "get_old_data/" . $brandInfo['BRAND_FOLDER'] . $current_time . "_normal_" . $brandInfo['BRAND_NAME'] . ".txt";
    $error_file = "get_old_data/" .$brandInfo[' BRAND_FOLDER'] . $current_time . "_error_" . $brandInfo['BRAND_NAME'] . ".txt";
    $guid_cat_file = "get_old_data/" . $brandInfo['BRAND_FOLDER'] . $current_time . "_guid_cat_" . $brandInfo['BRAND_NAME'] . ".txt";

    //open logs
    $fp_normal = fopen($normal_file, 'w');
    $fp_error = fopen($error_file, 'w');
    $fp_guid_cat = fopen($guid_cat_file, 'w');
    
    //write to logs
    fwrite($fp_normal, $st);
    fwrite($fp_error, $st);
    fwrite($fp_guid_cat, $st);
    
    $_SESSION['guid'] = 2345;

    recursive_advance($brandInfo['STARTING_CATID'], $fp_normal, $fp_error, $fp_guid_cat, $brandInfo['PARENT_GUID'], $brandInfo);

    //close logs
    fclose($fp_normal);
    fclose($fp_error);
    fclose($fp_guid_cat);
    
    echo '<h3>' . $brandInfo['BRAND_NAME'] . " transfer complete!</h3>\n";
}

/* Comment this section out if you plan to use in another program */

$csv_loc = 'brands_transfer_info.csv';
$csv  = fopen($csv_loc, 'r');

while($row = fgetcsv($csv)){    
    $fields = array(
        0   =>  "ORIG_SITE",
        1   =>  "NEWURL",
        2   =>  "IMAGE_FOLDER",
        3   =>  "BRAND_FOLDER",
        4   =>  "BRAND_NAME",
        5   =>  "BRAND_MASTER_ID",
        6   =>  "PARENT_GUID",
        7   =>  "USERID",
        8   =>  "STARTING_CATID"
    );
    
    $brandInfo = array_combine($fields, $row);
    
    transfer_data($brandInfo);
}

echo '<h1>Script Complete</h1>';



?>