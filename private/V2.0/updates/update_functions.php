<?php

//******************************************************************************
//******************************************************************************
//CHANGE THESE
//URLs AND ADDRESS
define("NEWURL", "http://www.lesliewatch.com/");
define("IMAGE_FOLDER", "user_images/");
define("BRAND_FOLDER", "ball/");
define("BRAND_NAME", "ball");
define("BRAND_MASTER_ID", "3");
define("PARENT_GUID", "3500001");  //Guid of the home or first guid of the brand
define("USERID", 1); //User that will have granted the brand permissions
define("STARTING_CATID", "119206");

function connect_EXT($dbSwitch = '') {
    $dbSwitch = trim(strtolower($dbSwitch));

    switch ($dbSwitch) {
        case 'hg':
            connect_to_HG_db();
            break;
        case 'lsl':
        default:
            connect_to_LESLIE_db();
            break;
    }
}

function connect_EXT_oop($dbSwitch = '') {
    $dbSwitch = trim(strtolower($dbSwitch));

    switch ($dbSwitch) {
        case 'hg':
            return connect_to_HG_db_oop();
            break;
        case 'lsl':
        default:
            return connect_to_LESLIE_db_oop();
            break;
    }
}

//******************************************************************************
//******************************************************************************


define("SERVER", "localhost");
//LUXNILE
define("LUXNILE_DB_USER", "wwwluxn_luxnile");
define("LUXNILE_DB_PASS", "luxnile09");
define("LUXNILE_DB_NAME", "wwwluxn_luxnile");
//HG
define("HG_DB_USER", "hgbrands_hgdb");
define("HG_DB_PASS", "Uth209@");
define("HG_DB_NAME", "hgbrands_data");
//ITNTDATACENTER
define("ITNTDC_DB_USER", "dejaunis_admin");
define("ITNTDC_DB_PASS", "Dej4#zaq1a");
define("ITNTDC_DB_NAME", "dejaunis_dcenter");
//LESLIE
define("LESLIE_DB_USER", "lesliewa_user1");
define("LESLIE_DB_PASS", "GoldDB123");
define("LESLIE_DB_NAME", "lesliewa_data");


/* Updated by Rhett Lowe - 7/4/12
 * Added $brandInfo = NULL to allow new additions
 * and the following if statement to allow backwards compatability.
 * 
 * all references to the $brandInfo data as constances have been 
 * re-assigned to brandInfo.... Hopefully.
 */

function recursive_advance($catID, $fp_normal, $fp_error, $fp_guid_cat, $parent_guid, $brandInfo = NULL, $update_EN = false){ //, &$catGiudCross = NULL) {
    //for backwards compatability
    if ($brandInfo === NULL) {
        $brandInfo['ORIG_SITE'] = '';
        $brandInfo['NEWURL'] = NEWURL;
        $brandInfo['IMAGE_FOLDER'] = IMAGE_FOLDER;
        $brandInfo['BRAND_FOLDER'] = BRAND_FOLDER;
        $brandInfo['BRAND_NAME'] = BRAND_NAME;
        $brandInfo['BRAND_MASTER_ID'] = BRAND_MASTER_ID;
        $brandInfo['PARENT_GUID'] = PARENT_GUID;
        $brandInfo['USERID'] = USERID;
        $brandInfo['STARTING_CATID'] = STARTING_CATID;
    }

    $normal_msg = array();
    $error_msg = array();
    //check_folder_existance('images/'.BRAND_FOLDER);
    connect_EXT($brandInfo['ORIG_SITE']);
    $tbl_arr = get_brnd_tbl_name($brandInfo['BRAND_NAME']);

    //FOR EACH CATEGORY:
    //1- GET CATEGORY INFO 
    $query = "select * from category where catID='$catID' ";
    $result = mysql_query($query);
    $num_rows = mysql_num_rows($result);
    $row = mysql_fetch_array($result);
    $st = "Category: catID=$row[catID] name=$row[cat_name] **parent=$row[parent] \n";
    fwrite($fp_normal, $st);
        
    //$catGiudCross[$row['catID']] = array('bc' => getBreadcrumbKey($row, 'EXT'), 'guid' => '');
    
//    var_dump($row);
//    exit;
    
    //2- REGISTER CATEGORY WITH GUID, THEN SAVE THE CORRESPONDING NEW GUID WITH OLD CATID
    //update - Rhett Lowe - 7-10-12
    //IF category exists, don't creat category
    //if there are more then one entry returned, fail for further analysis
    connect_INT();
    
    echo "<br><br>**********************************************************<br>\n";
    echo "escaped: ".escape_query($row['cat_name'])."<br>\n";
    echo "cat name: ".$row['cat_name']."<br>\n";
    
    echo $query = "SELECT guid FROM `" . $tbl_arr[0] . "` WHERE pg_name='" . escape_query($row['cat_name']) . "'";
    $res = query_general($query);
    if ($res === false) {
        var_dump($row, $query);
        exit('cat');
    }
    $catExists = mysql_num_rows($res);
   
    while($catExists > 0 && $thisGuid = mysql_fetch_assoc($res)){
        echo '<br>Internal: '.$thisGuid['guid']."<br>\n";
        echo $intBC = getBreadcrumbKey($thisGuid['guid'], 'INT', $tbl_arr[0]);
        echo '<br>External: '.$row['catID']."<br>\n";
        echo $intBC = getBreadcrumbKey($row['catID'], 'EXT', 'category');
        echo '<br>';
        
        if (getBreadcrumbKey($thisGuid['guid'], 'INT', $tbl_arr[0]) == getBreadcrumbKey($row['catID'], 'EXT', 'category')){
            break;
        }
        else $catExists = 0;
    }
    connect_INT();
    if ($catExists == 0) {// || $catID == $brandInfo['STARTING_CATID']) {
        fwrite($fp_normal, 'GUID does not exhist yet for Category, ' . $row['cat_name'] . ', creating new guid' . "\n");
        $guid = register_guid_advanced($brandInfo['BRAND_NAME'], $brandInfo['BRAND_MASTER_ID'], 1, $normal_msg, $error_msg); /* MIC 2012-07-03 */
        //echo "guid: <<<BR>\n";
    } else if ($catExists > 0) {
        fwrite($fp_normal, 'GUID, ' . $thisGuid['guid'] . ', exhists already, updating' . "\n");
        $guid = $thisGuid['guid'];
    } else if ($catExists === false)
        exit('Error: Check Query: ' . $query);
    else
        exit('too many results found that match incomming data for Categories: ' . stripslashes($row['title']));
        
    echo "catexists: " . $catExists . "<br>\n";
    echo "guid: " . $guid . "<br>\n";

    //connect_EXT($brandInfo['ORIG_SITE']); //pointless
    $st = $row['catID'] . "=$guid\n";
    fwrite($fp_guid_cat, $st);

    //3- ADD CATEGORY TO BRND_TBL: USE PARENT FROM CATEGORY TO FIND PARENT GUID FROM THE TXT FILE
    /*    $conf_arr = read_configuration($guid_cat_path);
      $parent_guid = $conf_arr[$row[parent]];      //$row[parent] IS THE CATID OF THE CATEGORY'S PARENT
      if ($parent=0)
      $parent_guid = PARENT_GUID;
     */
    //4- CAT_NAME GOES TO PAGE_NAME    
    $cat_name = escape_query($row['cat_name']);

    //5- PG_TYPE = 1
    connect_INT();
    $brand_master_id = $brandInfo['BRAND_MASTER_ID'];
    if ($catExists == 0) {
        $result = query_general("insert into `" . $tbl_arr[0] . "` (guid, brand_master_id, status, pg_type, parent, pg_name) 
                                values ('$guid', '$brand_master_id', '1', '1', '$parent_guid', '$cat_name')");
        update_bread_crumb($guid, $tbl_arr[0]);
        if ($result)
            fwrite($fp_normal, "Contents for cat inserted into $tbl_arr[0] for guid=$guid \n");
        else
            fwrite($fp_error, "Contents for cat NOT inserted into $tbl_arr[0] for guid=$guid \n");
    }
    else if ($catExists == 1) {
        $result = query_general("update `" . $tbl_arr[0] . "` SET pg_name='$cat_name' WHERE guid='" . $guid . "'");
        if ($result)
            fwrite($fp_normal, "Contents for cat updated on " . $tbl_arr[0] . " for guid=$guid\n");
        else-
            fwrite($fp_error, "Contents for cat FAILED update on " . $tbl_arr[0] . " for guid=$guid\n");
    }

    /* Update Language (EN) Tables */
    if (is_array($update_EN)){
        if ($catExists == 0) {
            $result = query_general("insert into `" . $update_EN[0] . "` (guid, brand_master_id, status, pg_type, parent, pg_name) 
                                    values ('$guid', '$brand_master_id', '1', '1', '$parent_guid', '$cat_name')");
            update_bread_crumb($guid, $update_EN[0]);
            if ($result)
                fwrite($fp_normal, "Contents for cat inserted into $update_EN[0] for guid=$guid \n");
            else
                fwrite($fp_error, "Contents for cat NOT inserted into $update_EN[0] for guid=$guid \n");
        }
        else if ($catExists == 1) {
            $result = query_general("update `" . $update_EN[0] . "` SET pg_name='$cat_name' WHERE guid='" . $guid . "'");
            update_bread_crumb($guid, $update_EN[0]);
            if ($result)
                fwrite($fp_normal, "Contents for cat updated on " . $update_EN[0] . " for guid=$guid\n");
            else
                fwrite($fp_error, "Contents for cat FAILED update on " . $update_EN[0] . " for guid=$guid\n");
        }
    }
    
    connect_EXT($brandInfo['ORIG_SITE']);
    //GET ITEMS UNDER THIS CATID
    $query = "select * from items where catID='$catID' ";
    $resultxxx = mysql_query($query);
    $num_rows = mysql_num_rows($resultxxx);
    //echo $num_rows;
    //This guid is now the parent guid for the items and collections
    $parent_guid = $guid;

    for ($i = 0; $i < $num_rows; $i++) {
        $row = mysql_fetch_array($resultxxx);
        if ($row['model_number'] == '')
            continue;
        //FOR EACH ITEM:
        //1- REGISTER ITEM WITH GUID
        connect_INT();
        //var_dump($row, $resultxxx);
        $query = "SELECT guid FROM `brnd_" . $brandInfo['BRAND_NAME'] . "_item_det` WHERE model_number='" . $row['model_number'] . "'";
        $itemRes = query_general($query);

        if ($itemRes === false) {
            var_dump($row, $query);
            exit('item');
        }

        $itemExists = mysql_num_rows($itemRes);

        if ($itemExists == 0) {
            fwrite($fp_normal, 'GUID does not exhist yet for item, ' . $row['model_number'] . ', creating new guid' . "\n");
            $guid = register_guid_advanced($brandInfo['BRAND_NAME'], $brandInfo['BRAND_MASTER_ID'], '2', $normal_msg, $error_msg);
        } else if ($itemExists == 1) {
            $thisItemGuid = mysql_fetch_assoc($itemRes);
            fwrite($fp_normal, 'Item GUID, ' . $thisItemGuid['guid'] . ', exhists already, updating' . "\n");
            $guid = $thisItemGuid['guid'];
        } else if ($itemExists === false)
            exit('Error: Check Query: ' . $query);
        else
            exit('too many results found that match incomming data for Items: ' . $row['model_number'] . " :: " . $itemExists . " :: " . $query);

        connect_EXT($brandInfo['ORIG_SITE']);
        //2- ADD THE ITEM TO BRND_TBL: USE PARENT FROM CATEGORY TO FIND PARENT GUID FROM THE TXT FILE
        /*
          $conf_arr = read_configuration($guid_cat_path);
          $parent_guid = $conf_arr[$row[catID]];      //$row[catID] IS THE CATID OF THE ITEM'S PARENT
         */
        //$myarray = array(); //not used
        $arr = get_db_tbl_fields($row, 'item_id,catID,page_type,bread_crumb,enable,cat_status,item_code,stock_num,qty,qty_logs,related_items_code,extra_information1,price,sale_price,wholesale_price,orderable,first_page_display,option1_name,option1_options,option2_name,option2_options,title,caption,relatedItemNotes,embed_movie,item_weight,tab_order,meta_description,meta_tag,meta_title');
        $st = ($itemExists == 1 ? 'Updating' : 'New') . " Item: model_number=$row[model_number] Item Code=$row[item_code] name=$row[title] **parent=$row[catID] item ID=$row[item_id] \n";
        fwrite($fp_normal, $st);

        if ($row['item_code'] != '') {
            //3- TITLE OF THE ITEM GOES INTO PAGE_NAME FIELD            
            //4- PG_TYPE = 2            
            $brand_master_id = $brandInfo['BRAND_MASTER_ID'];
            connect_INT();
            if ($itemExists == 0) {
                $result = query_general("insert into `$tbl_arr[0]` (guid, brand_master_id, status, pg_type, parent, pg_name) 
                                        values ('$guid', '$brand_master_id', '1', '2', '$parent_guid', '" . stripslashes($row['title']) . "' )  ");
                update_bread_crumb($guid, $tbl_arr[0]);
                if ($result)
                    fwrite($fp_normal, "Contents for item inserted into $tbl_arr[0] for guid=$guid and item_id='$row[item_id]' \n");
                else
                    fwrite($fp_error, "Contents fro item NOT inserted into $tbl_arr[0] for guid=$guid and item_id='$row[item_id]' \n");
            }
            else if ($itemExists == 1) {
                $result = query_general("update `" . $tbl_arr[0] . "` 
                                            SET pg_name='" . stripslashes($row['title']) . "' 
                                            WHERE guid='" . $guid . "'");
                if ($result)
                    fwrite($fp_normal, "Contents for item updated on " . $tbl_arr[0] . " for guid=$guid and item_id='" . $row['item_id'] . "'\n");
                else
                    fwrite($fp_error, "Contents for item FAILED update on " . $tbl_arr[0] . " for guid=$guid and item_id='" . $row['item_id'] . "'\n");
            }

             /* Update Language (EN) Tables */
            if (is_array($update_EN)){
                if ($itemExists == 0) {
                $result = query_general("insert into `$update_EN[0]` (guid, brand_master_id, status, pg_type, parent, pg_name) 
                                        values ('$guid', '$brand_master_id', '1', '2', '$parent_guid', '" . stripslashes($row['title']) . "' )  ");
                update_bread_crumb($guid, $update_EN[0]);
                if ($result)
                    fwrite($fp_normal, "Contents for item inserted into $update_EN[0] for guid=$guid and item_id='$row[item_id]' \n");
                else
                    fwrite($fp_error, "Contents fro item NOT inserted into $update_EN[0] for guid=$guid and item_id='$row[item_id]' \n");
            }
            else if ($itemExists == 1) {
                $result = query_general("update `" . $update_EN[0] . "` 
                                            SET pg_name='" . stripslashes($row['title']) . "' 
                                            WHERE guid='" . $guid . "'");
                update_bread_crumb($guid, $update_EN[0]);
                if ($result)
                    fwrite($fp_normal, "Contents for item updated on " . $update_EN[0] . " for guid=$guid and item_id='" . $row['item_id'] . "'\n");
                else
                    fwrite($fp_error, "Contents for item FAILED update on " . $update_EN[0] . " for guid=$guid and item_id='" . $row['item_id'] . "'\n");
            }
            }

            //7- PUT THE SPECS IN 
            query_general("DELETE FROM `" . $tbl_arr[2] . "` WHERE guid='" . $guid . "'");
            if (is_array($update_EN)) query_general("DELETE FROM `" . $update_EN[2] . "` WHERE guid='" . $guid . "'");

            foreach ($arr as $value) {
                if ($row[$value] != '') {

//                    if ($itemExists == 0) 
//                        $query = "insert into $tbl_arr[2] (guid, spec_field, spec_val) values ('$guid', '$value', '$row[$value]')  ";
//                    else if ($itemExists == 1) 
//                        $query = "update $tbl_arr[2] SET spec_val='" . $row[$value] . "' WHERE guid='" . $guid . "' AND spec_field='" . $value . "'";
//                    
                    $query = "insert into `$tbl_arr[2]` (guid, spec_field, spec_val) values ('$guid', '$value', '$row[$value]')";
                    $result = query_general($query);
                    
                    /* update Language EN */
                    if (is_array($update_EN)) query_general ("insert into `$update_EN[2]` (guid, spec_field, spec_val) values ('$guid', '$value', '$row[$value]')");
                    
                    if ($result)
                        fwrite($fp_normal, "Item specs " . ($itemExists == 1 ? 'updated' : 'inserted into') . " " . $tbl_arr[1] . " for guid=$guid and item_id='" . $row['item_id'] . "' \n");
                    else
                        fwrite($fp_error, "Item specs FAILED " . ($itemExists == 1 ? 'update' : 'insert into') . " " . $tbl_arr[1] . " for guid=$guid and item_id='" . $row['item_id'] . "' \n");
                }
            }

            //8- MODEL_NUMBER GOES INTO MODEL_NUMBER, ALSO PRICE AND SALE-PRICE GO INTO RETAIL_PRICE AND SALE_PRICE
            if ($itemExists == 0)
                $query = "insert into `" . $tbl_arr[1] . "` (guid, model_number, retail_price, sale_price) 
                                values ('$guid', '" . $row['model_number'] . "', '" . $row['price'] . "', '" . $row['sale_price'] . "')  ";
            else if ($itemExists == 1)
                $query = "UPDATE `" . $tbl_arr[1] . "` SET sale_price='" . $row['sale_price'] . "' WHERE guid='" . $guid . "' AND model_number='" . $row['model_number'] . "'";

            $result = query_general($query);

            if ($result)
                fwrite($fp_normal, "Item details " . ($itemExists == 1 ? 'updated' : 'inserted into') . " " . $tbl_arr[1] . " for guid=$guid and item_id='" . $row['item_id'] . "' \n");
            else
                fwrite($fp_error, "Item details FAILED " . ($itemExists == 1 ? 'update' : 'insert into') . " " . $tbl_arr[1] . " for guid=$guid and item_id='" . $row['item_id'] . "' \n");

            /* update language EN */
            if (is_array($update_EN)){
                if ($itemExists == 0)
                    $query = "insert into `" . $update_EN[1] . "` (guid, model_number, retail_price, sale_price) 
                                    values ('$guid', '" . $row['model_number'] . "', '" . $row['price'] . "', '" . $row['sale_price'] . "')  ";
                else if ($itemExists == 1)
                    $query = "UPDATE `" . $update_EN[1] . "` SET sale_price='" . $row['sale_price'] . "' WHERE guid='" . $guid . "' AND model_number='" . $row['model_number'] . "'";

                $result = query_general($query);

                if ($result)
                    fwrite($fp_normal, "Item details " . ($itemExists == 1 ? 'updated' : 'inserted into') . " " . $update_EN[1] . " for guid=$guid and item_id='" . $row['item_id'] . "' \n");
                else
                    fwrite($fp_error, "Item details FAILED " . ($itemExists == 1 ? 'update' : 'insert into') . " " . $update_EN[1] . " for guid=$guid and item_id='" . $row['item_id'] . "' \n");

            }
            
            connect_EXT($brandInfo['ORIG_SITE']);
            $image_file = $row['item_code'] . '.jpg';
            $path = $brandInfo['NEWURL'] . $brandInfo['IMAGE_FOLDER'] . $image_file;

            //echo print_r($row);
            //echo '<a href="' . $path . '">' . $path . '</a>' . "<br>\n";
            if (@fopen($path, "r") == true) {   //IF IMAGE FILE EXISTS ON THE TARGET WEBSITE, THEN COPY IT HERE
                //9- CREATE THE IMAGE DB AND ADD THE IMAGE TO IT
                //$root = $_SERVER[DOCUMENT_ROOT] . "/";
                $img_path = get_img_path_for_brand($brandInfo['BRAND_NAME'], 'RELATIVE');
                //$img_path = $root . $img_path;
                //$img_db_path = get_img_db_path($guid, $img_path);
                
                if (reserve_available_img_slot_in_guid_db($guid, $image_file, $img_path, &$available_slot, &$gen_img_name)) {
                    $dest = BRANDS_ROOT_FOLDER . $brandInfo['BRAND_FOLDER'] . "images/" . $image_file;
                    if (!copy($path, $dest)) {
                        $st = "failed to copy $image_file... \n";
                        fwrite($fp_error, $st);
                        echo '<br>', $st;
                    } else {
                        $st = "File $image_file is copied...\n";
                        fwrite($fp_normal, $st);
                        add_img_to_db($guid, $img_path, $available_slot, $image_file, true);
                    }
                } else {
                    $st = "There is no available image slots for this guid=$guid.\n";
                    fwrite($fp_error, $st);
                    add_img_to_db($guid, $img_path, $available_slot, $image_file);
                }
            }
        }
    }

    //GET ALL CATEGORIES WHOSE PARENT=CATID AND GO RECURSIVE
    $query2 = "select * from category where parent='$catID' ";
    $result2 = mysql_query($query2);
    $num_rows2 = mysql_num_rows($result2);
    for ($i = 0; $i < $num_rows2; $i++) {
        $row2 = mysql_fetch_array($result2);
        recursive_advance($row2[catID], $fp_normal, $fp_error, $fp_guid_cat, $parent_guid, $brandInfo, $update_EN);//, $catGiudCross);
    }
}

function getBreadcrumbKey($ID, $srv, $table){
    $str = '';
    $recent_parent = $ID;
    if ($srv == 'EXT'){
        connect_EXT();
        do{
            $parResults = query_general("SELECT parent, cat_name FROM $table WHERE catID='" . $recent_parent . "'");
            if ($parResults != false && mysql_num_rows($parResults) == 1){
                $parentInfo = mysql_fetch_assoc($parResults);
                $str .= str_replace(' ', '', escape_query($parentInfo['cat_name']));
                $recent_parent = $parentInfo['parent'];
            }
            else 
                exit(mysql_num_rows($parResults));
        }while($recent_parent != 0);
    }
    else if ($srv == 'INT'){
        connect_INT();
        do{
            $parResults = query_general("SELECT parent, pg_name FROM $table WHERE guid='" . $recent_parent . "'");
            if ($parResults != false && mysql_num_rows($parResults) == 1){
                $parentInfo = mysql_fetch_assoc($parResults);
                $str .= str_replace(' ', '', escape_query($parentInfo['pg_name']));
                $recent_parent = $parentInfo['parent'];
            }
            else 
                exit(mysql_num_rows($parResults));
        }while($recent_parent != 0);
    }
    return $str;
}

function escape_query($str) {
    return strtr($str, array(
                "\0" => "",
                "'" => "&#39;",
                "\"" => "&#34;",
                "\\" => "&#92;",
                // more secure
                "<" => "&lt;",
                ">" => "&gt;",
            ));
}

function connect_INT() {
    connect_to_ITNTDC_db();
}

function connect_INT_oop() {
    return connect_to_ITNTDC_db_oop();
}

function connect_to_LESLIE_db() {
    @ $db = mysql_pconnect(SERVER, LESLIE_DB_USER, LESLIE_DB_PASS);

    if (!$db) {
        echo 'Error: Could not connect to database LESLIE.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    if (mysql_select_db(LESLIE_DB_NAME))
        return true;
    else
        return false;
}

function connect_to_ITNTDC_db() {
    @ $db = mysql_pconnect(SERVER, ITNTDC_DB_USER, ITNTDC_DB_PASS);

    if (!$db) {
        echo 'Error: Could not connect to database ITNTDC.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    if (mysql_select_db(ITNTDC_DB_NAME))
        return true;
    else
        return false;
}

function connect_to_HG_db() {
    @ $db = mysql_pconnect(SERVER, HG_DB_USER, HG_DB_PASS);

    if (!$db) {
        echo 'Error: Could not connect to database HG.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    if (mysql_select_db(HG_DB_NAME))
        return true;
    else
        return false;
}

function connect_to_LUXNILE_db() {
    @ $db = mysql_pconnect(SERVER, LUXNILE_DB_USER, LUXNILE_DB_PASS);

    if (!$db) {
        echo 'Error: Could not connect to database LUXNILE.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    if (mysql_select_db(LUXNILE_DB_NAME))
        return true;
    else
        return false;
}

function connect_to_ITNTDC_db_oop() {
    @ $db = new mysqli(SERVER, ITNTDC_DB_USER, ITNTDC_DB_PASS, ITNTDC_DB_NAME);

    if (!$db) {
        echo 'Error: Could not connect to database ITNTDC.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    return $db;
}

function connect_to_HG_db_oop() {
    @ $db = new mysqli(SERVER, HG_DB_USER, HG_DB_PASS, HG_DB_NAME);

    if (!$db) {
        echo 'Error: Could not connect to database HG.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    return $db;
}

function connect_to_LUXNILE_db_oop() {
    @ $db = new mysqli(SERVER, LUXNILE_DB_USER, LUXNILE_DB_PASS, LUXNILE_DB_NAME);

    if (!$db) {
        echo 'Error: Could not connect to database LUXNILE.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    return $db;
}

function connect_to_LESLIE_db_oop() {
    @ $db = new mysqli(SERVER, LESLIE_DB_USER, LESLIE_DB_PASS, LESLIE_DB_NAME);

    if (!$db) {
        echo 'Error: Could not connect to database LESLIE.  Please try again later. Loc.: includes/allinone.php connect_to_db()';
        exit;
    }
    return $db;
}

function collect_tag_raw_data($sites){
    
    foreach($sites as $site){
        //blank vars for instanciation
        $spec = '';
        $var = '';
        
        /* @var $db_ext mysqli */
        $db_ext = connect_EXT_oop($site);
        $res = $db_ext->query('select * from items');
        
        $db_int = connect_INT_oop();
        $prepStat = $db_int->prepare("INSERT INTO `tag_cloud_raw_data` (spec, value) VALUES (?, ?)");
        $prepStat->bind_param('ss', $spec, $var);
        
        while($row = $res->fetch_assoc()){
            $row = get_db_tbl_fields_assoc($row, 'item_id,catID,page_type,bread_crumb,enable,cat_status,item_code,stock_num,qty,qty_logs,related_items_code,extra_information1,price,sale_price,wholesale_price,orderable,first_page_display,option1_name,option1_options,option2_name,option2_options,title,caption,relatedItemNotes,embed_movie,item_weight,tab_order,meta_description,meta_tag,meta_title');
            foreach ($row as $spec => $var) {
                //echo "$spec :: $var <br>\n";
                $prepStat->execute();
            }
        }
        
        $prepStat->close();
        $db_ext->close();
        $db_int->close();
    }
}



?>