<?
    //THIS FILE GENERATES THE TEMPORARY CONTENTS TO BE SHIPPED TO THE CLIENT 
    //HOSTING SITE AND TO UPDATE ICP. A TEMPORARY FILE "UPDATE_ICP.PHP" WILL
    //BE CREATED ON THE CLIENT'S WEB HOSTING SERVER.
    
    //*********************************************************************
    //ANYTHING THAT IS PRINTED OUT FROM THIS FILE WILL BE WRITTEN TO THE
    //"UPDATE_ICP.PHP" FILE ON THE CLIENT SERVER.
    
    require_once("$_SERVER[DOCUMENT_ROOT]/includes/init.php");
    connect_internal_db();
    $username = $HTTP_POST_VARS[username];
    $userID = $HTTP_POST_VARS[userID];
    
    $result = query_general("select a.address, a.verif_tbl, u.username from assigned_domain a, users u where a.userID='$userID' and u.userID='$userID' ");
    $row = mysql_fetch_array($result);
    if($username==$row[username])
    {   
        $file_path = "$_SERVER[DOCUMENT_ROOT]/includes/ishowcase_auto_setup_functions/ICP_update_temp.php";
        if(file_exists($file_path))
        {
            $ICP_update_temp = file_get_contents($file_path);
            print_r($ICP_update_temp);
            //echo 'test';
        }
        else
            echo 'ICPUPDATEE402'; 
    }
    else
        echo 'ICPUPDATEE401';
    
?>