<?php
session_start();
require_once('includes/init.php');
require_once('brand/pageUI.php');

setlocale(LC_MONETARY, 'en_US.UTF-8');

connect_internal_db();
$userID = 1;

if (isset($_GET['alias'])){
    $guid = resolve_uri_alias(strtolower($_GET['alias']));
} else {
    $guid = (isset($_GET['guid']) ? $_GET['guid'] : SHOWCASE_HOME_PAGE);        //GUID
}

$guid2 = 0;
$cmd = '';

$set_lang = (isset($_GET['lang']) ? $_GET['lang'] : 'EN');    //LANGUAGE
$pg = (isset($_GET['pg']) ? $_GET['pg'] : 1);          //PAGE NUMBER Only for pagination of Collections (PG_TYPE=8)
$search = (isset($_GET['cmd']) ? $_GET['cmd'] : '');    //Search parameter for PG_TYPE=50 

//VALIDATE GUID
if (!is_numeric($guid) || $guid < 0)
    die("INVALID GUID!");  //INVALID GUID

//SET PAGE
if (is_numeric($pg))
    if ($pg < 1 || $pg === NULL)
        $pg = 1;
if ($pg === NULL || trim($pg) == '')
    $pg = 1;
if (strtoupper ($pg) == 'ALL')
    $pg == 'ALL';

//VERIFY LANGUAGE CODE
if ($set_lang != '') {
    $pattern = '/^[A-Z]{2,3}$/';
    if (!preg_match($pattern, $set_lang))
        die("INVALID LANGUAGE CODE!");
}

set_ishowcase_variables($guid, $userID, $set_lang, $set_lang_flag, $pg_info, $pg_fields, $tbl_arr, $lang_pg_fields, $lang_vars_arr, $lang_item_det, $lang_item_specs_arr, $lang_parent_fields);

//f page is 108 (Symbolic Link) Follow Link
if ($pg_fields['pg_type'] == 108){
    $guid2 = $guid;
    
    $new_pg = follow_symbolic_link(($set_lang_flag ? $lang_pg_fields : $pg_fields), $set_lang);
    $guid = $new_pg['guid'];
    
    set_ishowcase_variables($guid, $userID, $set_lang, $set_lang_flag, $pg_info, $pg_fields, $tbl_arr, $lang_pg_fields, $lang_vars_arr, $lang_item_det, $lang_item_specs_arr, $lang_parent_fields);
} 

//If the guid is disabled, redirect to the home page
if ($pg_fields['status'] == 0) {
    $guid = SHOWCASE_HOME_PAGE;
    set_ishowcase_variables($guid, $userID, $set_lang, $set_lang_flag, $pg_info, $pg_fields, $tbl_arr, $lang_pg_fields, $lang_vars_arr, $lang_item_det, $lang_item_specs_arr, $lang_parent_fields);
}

if ($set_lang_flag == 1)
    $pg_for_meta = $lang_pg_fields;
else
    $pg_for_meta = $pg_fields;

$meta = get_ishowcase_content_meta($pg_for_meta, $pg_info, $set_lang_flag, $set_lang, $pg, $tbl_arr, $search);

$homepage = new PageUI();
$homepage->PageUIConstructor($pg_fields['pg_type']);
$homepage->Setbreadcrumb_type('Home');
$homepage->set_guid($guid);

if ($search != NULL && $pg_fields['pg_type'] == 1050){
    $cmd = $search;
}

$homepage->setMeta($meta);
$homepage->Display(1, 2, $guid, $userID, $set_lang, $pg, $guid2, $cmd);

function contents($command, $instruction, $guid, $userID, $set_lang, $pg, $guid2, $cmd) {
    $main_brnd_page = "brand.php";
    set_ishowcase_variables($guid, $userID, $set_lang, $set_lang_flag, $pg_info, $pg_fields, $tbl_arr, $lang_pg_fields, $lang_item_det, $lang_item_specs_arr, $lang_parent_fields);
    
    //i don't think this is used ?? Vistigial
    $img_path = $_SERVER['DOCUMENT_ROOT'] . "/" . get_img_path_for_brand($pg_info["brand_name"], "RELATIVE");
    $img_db_path = get_img_db_path($pg_info["guid"], $img_path);
    $img_array = get_img_array($img_db_path, $pg_info["brand_name"]);

    if (file_exists($_SERVER['DOCUMENT_ROOT'] . "brand/showcase.php"))
        require($_SERVER['DOCUMENT_ROOT'] . "brand/showcase.php");
}
?>