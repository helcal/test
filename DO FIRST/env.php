<?php
/* Definition of Environment Variables */

$_SERVER['DOCUMENT_ROOT'] = 'C:\\xampp\\htdocs\\EDC\\public_html';

ini_set('error_reporting', E_ALL);
define("TEST_ENVIRONMENT",true);
define("TEST_ENVIRONMENT_EMAIL","rhett@ishowcaseinc.com");

//************************************************
//SERVER LOCATIONS
define("INT_SERVER","localhost");
define("EXT_SERVER","localhost");

//************************************************
//EXTERNAL DATABASE (ITNTDATACENTER)
define("EXT_DB_USER","");
define("EXT_DB_PASS","");
define("EXT_DB_NAME","");

//************************************************
//LOCAL DATABASE
define("INT_DB_USER","tgeahre");
define("INT_DB_PASS","merlin");
define("INT_DB_NAME","moyerish_dcenter");
//----------------------------------------
//IRBS DB
define("INT_IRBS_USER","tgeahre");
define("INT_IRBS_PASS","merlin");
define("INT_IRBS_DB_NAME","moyerish_dcenter");
//***********************IMPORTANT****************
   
//WEBSITE URL ADDRESSES
define("URL","http://localhost/moyer/public_html/");
define("HOMEURL",URL."moyer.php");
define("SECUREDURL",URL);
define("IMGURL",URL."images");
define("USERIMGURL",URL."user_images");
define("THUMBIMGURL",URL."thumb_images");
define("THUMBRELATEDIMGURL",URL."thumb_related_images");
define("LOGFILE","log_system.txt");
define('EXTERNAL_REPLACE', 'http://localhost/EDC/public_html');

?>
