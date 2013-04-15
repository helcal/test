<?php

/* Bulk Create Brands
 * RL - 7/3/12
 * 
 * Description: creates brands from a csv
 * If you plan to use this as a stand alone program, leave the bottom uncommented. 
 * If you plan on using it in conjunction with another program, comment out the 
 * bottom starting with connect_internal_db().
 * 
 *  $csv_loc - CSV Location. 
 */

require_once('./includes/init.php');

function create_bulk_brands($fields = NULL, $line = NULL){
    
    $real_brand_name = $fields['brand_real_name'];
    $brand_name = $fields['brand_name'];
    //$brand_master_id = $row[$fields['guid_from']];
    $guid_from = $fields['guid_from']; 
    $guid_to = $fields['guid_to']; 

    $error_msg = array();
    $normal_msg = array();
    $warning_msg = array();

    $pattern_brand  = '/^[a-zA-Z0-9_]{1,30}$/';
    if (!preg_match($pattern_brand,$brand_name))
    {
        array_push($error_msg, "Pattern not match for brand name! [a-zA-Z0-9_]{1,30}");
    }
    else if($real_brand_name=='')
    {
        array_push($error_msg, "Real Brand Name cannot be empty!");
    }
    else
    {
        $result = query_general("select count(brand_master_id) from brand_master 
                where brand_name='$brand_name' or real_brand_name='$real_brand_name' ");
        $row = mysql_fetch_array($result);
        $num_rows=$row['count(brand_master_id)'];
        if($num_rows>0)
        {
            array_push($error_msg, "Brand Name already exists.");
        }
        else
        {
            //CREATE TABLES BRND_BRAND_NAME AND BRND_BRAND_NAME_ITEM_SPECS AND BRND_BRAND_NAME_ITEM_DET
            create_db_tables_for_brand($brand_name, $error_msg, $normal_msg, $warning_msg,1,$guid_from,$guid_to);

            //CREATE FOLDERS FOR BRAND
            $result = mkdir(BRANDS_ROOT_FOLDER.$brand_name);
            if($result)                    
                array_push($normal_msg, "Folder <b>".BRANDS_ROOT_FOLDER.$brand_name."</b> created.");                    
            else
                array_push($error_msg, "Folder <b>".BRANDS_ROOT_FOLDER.$brand_name."</b> was NOT created.");
            $result = mkdir(BRANDS_ROOT_FOLDER.$brand_name."/images");
            if($result)                    
                array_push($normal_msg, "Folder <b>".BRANDS_ROOT_FOLDER.$brand_name."/images</b> created.");                    
            else
                array_push($error_msg, "Folder <b>".BRANDS_ROOT_FOLDER.$brand_name."/images</b> was NOT created.");
            $result = mkdir(BRANDS_ROOT_FOLDER.$brand_name."/showcase");
            if($result)                    
                array_push($normal_msg, "Folder <b>".BRANDS_ROOT_FOLDER.$brand_name."/showcase</b> created.");                    
            else
                array_push($error_msg, "Folder <b>".BRANDS_ROOT_FOLDER.$brand_name."/showcase</b> was NOT created.");

            //REGISTER THE BRAND IN BRAND_MASTER TBL
            $seed =  strtotime("now");
            $token = $brand_name.'_'.$seed;
            $result = query_general("insert into brand_master (token, brand_name, real_brand_name) values ('$token', '$brand_name', '$real_brand_name')  ");                    
            if($result) 
            {                   
                array_push($normal_msg, "Brand <b>$brand_name, $real_brand_name</b> created in <i>brand_master</i> tbl.");
                $result = query_general("select brand_master_id from brand_master where token='$token'  ");                        
                $row = mysql_fetch_array($result);
                $brand_master_id = $row['brand_master_id'];
            }
            else                    
                array_push($error_msg, "Brand $brand_name, $real_brand_name was NOT created in <i>brand_master</i> tbl.");


            //REGISTER CAT IN GUID AND ADD IT WITH PARENT=0 IN CAT TBL OF THE BRAND
            $guid = register_guid_advanced($brand_name, $brand_master_id, '1', $normal_msg, $error_msg);

            if($guid!='')
            {
                $tbl = get_brnd_tbl_name($brand_name);
                $result = query_general("insert into $tbl[0] (guid, brand_master_id, pg_name, pg_type, parent, bread_crumb) 
                    values ('$guid', '$brand_master_id', '$real_brand_name', '1', '0', '0')  ");                    
                if($result)                    
                    array_push($normal_msg, "Category with <b>guid=$guid</b> and parent=0 is added to <i>$tbl[0]</i>.");                    
                else                    
                    array_push($error_msg, "Category with <b>guid=$guid</b> and parent=0 is NOT added to <i>$tbl[0]</i>.");
            }
        }
    }

    echo 'Normal: '.print_r($normal_msg, true)."<br />\n";
    echo 'Warnings: '.print_r($warning_msg, true)."<br />\n";
    echo 'Errors: '.print_r($error_msg, true)."<br />\n";
    if (isset($_GET['line'])) echo '<a href=\'http://dejaun.ishowcaseinc.com/private/V2.0/updates/transfer_data_update.php?'.($line === NULL ? '' : 'line='.$line.'&').'mid='.$brand_master_id.'&step=1&size=all\'>Next Step</a><br>'."\n";
    echo 'finish brand'."<br />\n"."<br />\n";
    
    return $brand_master_id;
    
}

/* Comment this section out if you plan to use in another program */
if(!isset($_GET['line'])) exit;

connect_internal_db();

$csv_loc = 'brands.csv';
$csv = fopen($csv_loc, 'r');

$fields = array(
    0   =>  'brand_real_name',
    1   =>  'brand_name',
    2   =>  'guid_from',
    3   =>  'guid_to',
    //4   =>  'orig_server'
);

$run = (isset($_GET['run']) ? intval($_GET['run']) : 1);
if (isset($_GET['line'])) $line = intval($_GET['line']);

$count = -1;
while ($row = fgetcsv($csv)) {
    $count++;
    if ($count < $line || $count > $line+$run-1) 
        continue;
    
    $brandInfo = array_combine($fields, $row);

    $master_id = create_bulk_brands($brandInfo, $count);
    echo $brandInfo['brand_real_name'] . ' Brand MasterID: ' . $master_id . "<br /><br /><br />\n";
    
}
echo '<h1>success</h1>';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>