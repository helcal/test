<?php
//******************************************************************************
//********************************DEFINES***************************************
//******************************************************************************
//$_SERVER['DOCUMENT_ROOT'] = '/home/dev2jorg/public_html';
define("VERSION", "V2.0");

/* This section, DB info and Server URL info, was moved to env.php */
require_once 'env.php';

//*****************************************************************************
//FOLDERS
define("RETAILER_FOLDER_NAME","brand");
define("ABSOLUTE_BRANDS_ROOT_FOLDER",URL."public/".VERSION."/brands/");
define("BRANDS_ROOT_FOLDER","public/".VERSION."/brands/");      //EXCEPTION
define("PRIVATE_EMAILS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/emails/");
define("PRIVATE_USERS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/users/");
define("PRIVATE_SYSTEM_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/system/");
define("PRIVATE_SETTINGS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/settings/");
define("PRIVATE_BRANDS_SETTINGS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/settings/brands/");
define("PRIVATE_DEALER_SETTINGS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/settings/dealers/");
define("PRIVATE_LOCALE_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/settings/locales/");
define("PRIVATE_LOGS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/logs/");
define("PUBLIC_USERS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/public/".VERSION."/users/");
define("ARCHIVE_ISHOWCASE_FOLDER","archive_ishowcase");
define("ICP_DOWNLOAD_FOLDER","icp_downloads");
define("BRAND_PUBLISH_FOLDER","brand_publish/");
define("IMG_ICONS_FOLDER","images/icons/");           //EXCEPTION
define("LANGUAGE_LIST_FOLDER","language_abbreviations/");
define("PRIVATE_SRV_SETTINGS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/settings/srv/"); /*Task 1082*/
define("PRIVATE_SPEC_SETTINGS_FOLDER",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/specifications/"); 
define("AUTO_UPDATE_FOLDER", $_SERVER['DOCUMENT_ROOT'].'/private/'.VERSION.'/settings/auto_updates/');

/*****************************************************************************
//FILES
*/
define("SPECIFICATION_FILE","Product_Types.csv");
define("GRL_FILE","guid_ranges.txt");
define("SRV_FILE","srv_conf.txt");
define("ICP_CONN_VERIF_FILE","icp_connection_verif.txt");
define("ISHOWCASE_REMOTE_FILENAME","ishowcase_successful_filenames.txt");
define("ICP_ORIGINAL_FILE_PATH_FOR_DOWNLOAD",$_SERVER['DOCUMENT_ROOT']."/private/".VERSION."/icp/icp_V2.0.php");
define("HELP_PAGE_TYPES","help_page_types.pdf");
define("BRAND_PUBLISH_FILE","brand_publish.txt");
define("BRAND_SETTINGS_FILE","brand_settings.txt");
define("DEALER_SETTINGS_FILE","dealer_settings.txt");
define("JOOMLA_DEALER_SETTINGS_FILE","joomla_dealer_settings.txt");
define("AVAILABLE_LANGUAGE_LIST",PRIVATE_SETTINGS_FOLDER.LANGUAGE_LIST_FOLDER."language_abbreviations.txt");
define("AVAILABLE_LANGUAGE_DISPLAY",PRIVATE_SETTINGS_FOLDER.LANGUAGE_LIST_FOLDER."language_display.txt");   //USED TO DISPLAY LANGUAGES ON RETAILER WEBSITES 
define("LANGUAGE_SETTINGS_FILE","language_settings.txt");
define("DEFAULT_LANGUAGE_FILE","default_language.txt");
define("BRAND_PG_TYPE_FILE","pagetype.txt");
define("LOCALE_FILE","locale.php");
define("NO_IMAGE_FILE_NAME","no_image");
define("NO_IMAGE_FILE_TYPE",".jpg");
define("PATH_BULK_UPLOAD_FILENAME","path_bulk.txt");
define("AVAILABLE_TEMPLATES_LIST","templates_names.txt");
define("AUTO_UPDATE_LIST", 'brands_to_update.txt');


define("IRCP_PAGE","index.php"); //Main page in the back end
define("IRCP_TITLE","i-Showcase Portal | Remote Content Management | Data Quality and Security"); //Title on Back End
define("IRCP_META_KEYWORDS","i-Showcase, Remote Content Management, i-Showcase Inc, i-Showcase Portal, i-Showcase Data Center, Data Management, Data Quality, Data Security, Data Sharing and Distribution, Business Intelligence and Data Warehousing, Data Quality Management and Assurance"); //Title on Back End
define("IRCP_META_TITLE","i-Showcase Portal | Remote Content Management | Data Quality and Security");
define("IRCP_META_DESCRIPTION","Welcome to i-Showcase Portal, remote content management system for manufacturers and retailers.");
define("SHOWCASE_META_BRAND_NAME", "Moyer Fine Jewelers");


//*****************************************************************************
//IMAGE RELATED
//THESE ARE USED TO NAME THE THUMBNAILS
define("XSMALL","_xsmall");
define("SMALL","_small");
define("MEDIUM","_medium");
define("LARGE","_large");
define("XLARGE","_xlarge");
//THESE ARE THUMBNAIL DEFAULT SIZES IN PIXELS
define("XSMALL_SIZE","70");
define("SMALL_SIZE","232");
define("MEDIUM_SIZE","280");
define("LARGE_SIZE","500");
//Image ratio and fill options
define("IMG_THUMB_RATIO_HEIGHT",800);
define("IMG_THUMB_RATIO_WIDTH",500);
define("IMG_THUMB_FILL_RED",255);
define("IMG_THUMB_FILL_GREEN",255);
define("IMG_THUMB_FILL_BLUE",255);
define("FORCE_IMG_THUMB_RATIO",1);
//NO IMAGE FILE PATH
define("NO_IMAGE_FILE_PATH","/".RETAILER_FOLDER_NAME."/images/");
define('NO_IMAGE_URL', 'NO_IMAGE_URL');
//AUTO SYSTEM EMAIL ADDRESSES
define("EMAIL","");
define("EMAIL_AUTO","");

//CONTACT AND COMPANY INFORMATION
define("NICKNAME","i-showcase Data Center");
define("PHONE1","1-800-996-0967 ");
define("PHONE2","");
define("FAX","1-888-837-5196");
define("ADDRESS1","21133 Victory Blvd. #222");
define("CITY","Canoga Park");
define("STATE","CA");
define("ZIP","91303");
define("COPYRIGHT","2004-2011 &copy; <a class=\"footer\" href=".HOMEURL.">i-Showcase inc.</a> All rights reserved.");

//*****************************************************************************
//IMAGE SIZES
define ("THUMB_WIDTH_CAT", "200");   //ON CAT.PHP
define ("THUMB_WIDTH_ITEM", "200");  //ON ITEM.PHP
define ("THUMB_WIDTH_ITEM_RELATED", "200"); //ON ITEM.PHP

//*****************************************************************************
//TABLE COLORS
define ("ROW_COLOR1", "#F9F9F9");
define ("ROW_COLOR2", "#F9F9F9");
define ("TABLE_HEADER_COLOR", "#141414");
define ("TABLE_HEADER_TXT_COLOR", "#D3D3D3");
define ("PURCHASABLE_TABLE_HEADER_COLOR", "#FF9900");

//*****************************************************************************
//IMAGE DEFINES
define ("MAX_IMG_SIZE_KB", "1000");   //1000KB=1MB
define ("MAX_IMG_SLOTS_PER_GUID", "10");
define ("IMG_DB_EXTENSION", "db");
define ("MODEL_IMAGE_DB_FOLDER","model"); 
define ("NEWS_IMAGE_DB_FOLDER","news");

define ("SHOWCASE_HOME_PAGE","");

define ("NO_PRODUCTS_MSG", 'There are currently no products in this Collection. We are currently working to bring you the best products possible.');
define ("MAX_SALES_LIMIT", 1000000000);

//define('OM_KEY', 'c2d192a1692cb24962ee398aae2c4410');
//define("AUTHORIZENET_API_LOGIN_ID", "7Bug5PW6aB");
//define("AUTHORIZENET_TRANSACTION_KEY", "6uX8443ykfT8P5Cq");
//define("AUTHORIZENET_SANDBOX", false);
define("GOOGLE_ANALYTICS_TRACKING_NUMBER", '');

define("SHOWCASE_RETAILER_BRAND_MASTER_ID",6);
define('TAX_RATE_USER_ID', 1);
define("SHOWCASE_BRAND_PG_TYPE",108);  //Brand information linked to the retailer collection page type

define('MAIN_BRND_PAGE', 'brand.php');
define("SHOWCASE_REQUEST_EMAIL_TO","");
define("SHOWCASE_EMAIL_FROM","");
define("EMAIL_LOGO", URL.RETAILER_FOLDER_NAME.'/images/logo.png');
define("SHOWCASE_QUOTE_REQUEST_SUBJECT","[Real Brand Name] Quote Request");
define("SHOWCASE_BRAND_NAME","brand");
define('TWITTER_LINK', '#');
define('FACEBOOK_LINK', '#');
define('PINTREST_LINK', '#');

//*****************************************************************************
//Newsletter Suggestions Array

//define('NEWSLETTER_PREVIEW_FILE', 'ns_preview.html');
define('NEWSLETTER_FOLDER_INT', $_SERVER['DOCUMENT_ROOT'].'/'.SHOWCASE_BRAND_NAME.'/newsletters/');
define('NEWSLETTER_FOLDER_EXT', URL.SHOWCASE_BRAND_NAME.'/newsletters/');

$GLOBALS['newsletter_variable_suggestions'] = array(
    'General' => array(
        'site_url',
        'images_folder',
        'user_email',
        'date',
        'unsubscribe_link',
        'message'
    )
);
$GLOBALS['newsletter_test_variables'] = array(
    'site_url' => URL,
    'images_folder' => NEWSLETTER_FOLDER_EXT.'images/',
    'user_email' => 'test_email@test_site.com',
    'date' => strftime('%A, %B %e, %Y'),
    'unsubscribe_link' => URL."#newsletter"
);

//*****************************************************************************
//SHOPPING CART DEFINES
define("SHOPPING_CART_AUTHORIZE_NET_URL","https://test.authorize.net/gateway/transact.dll");
define("SHOPPING_CART_SETTINGS_FOLDER","shopping_cart");
define("SHOPPING_CART_SETTINGS_FILE",'shopping_cart_settings.txt');
define("SHOPPING_CART_SHIPPING_COUNTRIES_FILE","shipping_countries.txt");
define("SHOPPING_CART_FIELDS_FILE","form_fields.txt");
define("SHOPPING_CART_COUNTRIES_FILE","countries.txt");
define("SHOPPING_CART_COUNTRIES_STATES_FILE","countries-states.txt");
define("SHOPPING_CART_ZIPCODES_STATES_FILE","US_zipcodes-states.txt");
define("SHOPPING_CART_STYLESHEET_FILE",'shopping_cart_style_settings.txt');  
define("SHOPPING_CART_TAX_SETTINGS_FILE","shopping_cart_tax_settings.txt");

define("AUTHORIZE_NET_FILE","aim.txt"); //Authorize.net codes

//define("USER_REGISTRATION_PAGE",URL."public/".VERSION."/shopping_cart/user_registration.php");

//Priomotions
//define("PROMO_FLAT_RATE_PER_ITEM",9);

//*****************************************************************************
//PAGE TYPES
//Must match HTACCESS rules
$GLOBALS['PG_TYPES_URL'] = array(
    '1' => 'Category',
    '2' => 'Product',
    '3' => 'Home',
    '4' => 'Collection',
    '7' => 'Category',
    '8' => 'Category',
    '9' => 'Collection',
    '20' => 'Info',
    '40' => 'Diamond',
    '45' => 'Diamond',
    '108' => 'Category',
    '208' => 'Category',
    '1000' => 'Info',
    '1008' => 'Other',
    '1020' => 'Info',
    '1050' => 'Search',
    '1055' => 'Search'
);

//This array is used to resolve website aliases in the URI
//Please keep this 1 <=> 1
//  meaning no duplicates on either side of key or value.
//  Please keep all lower case
$GLOBALS['URI_ALIAS'] = array(
    'home' => SHOWCASE_HOME_PAGE,
    'about-us' => 0,
    'search' => 0,
    'privacy-policy' => 0,
    'contact-us' => 0,
    'return-policy' => 0,
    'shipping' => 0
);

//Sitemap PAGE TYPES to Priority Values mapping
$GLOBALS['PG_TYPES_PRIORITY_VALUE'] = array(
    '1' => '0.8',
    '2' => '0.2',
    '3' => '1.0',
    '4' => '0.5',
    '7' => '0.5',
    '8' => '0.8',
    '9' => '0.8',
    '20' => '0.4',
    '108' => '0.8',
    '208' => '0.8',
    '1008' => '0.1',
    '1050' => '0.3',
    '1055' => '0.3',
);

//Price Variation Options
define('PRICING_VAR_ADMIN', 0);
define('PRICING_VAR_FAST_SHIP', 1);

//Specs to Drop from Views
$GLOBALS['DROP_SPECS_FROM_VIEWS'] = array(
    'Other Stone Shape That Will Fit'
);

//iDiamond Settings
define('IDIAMOND_SWITCH', 'Center Stone Type');
define('IDIAMOND_ANSWER', 'Not included (sold separately)');
$GLOBALS['IDIAMOND_MAP'] = array(
    'shape' => array(
        'Center Stone Shape',
        'Other Stone Shape That Will Fit'
    ),
    'weight' => array(
        'Center Stone Weight Range - ct'
    )
);
        
$GLOBALS['DIAMOND_SPECS_TO_DISPLAY'] = array(
    'shape',
    'cut',
    'carat',
    'color',
    'clarity',
    'polish',
    'size'
);

define ('ISHOWCASE_MONEY_FORMAT','%.2n');

?>