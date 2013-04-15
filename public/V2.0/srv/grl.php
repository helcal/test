<?
    require_once("$_SERVER[DOCUMENT_ROOT]/includes/init.php");
    connect_internal_db();

/****************************************************************************
  Returns the GRL for the guid received
    // 1-Validate parameters
    // 2-Validate that the user is set in assigned domain
    // 3-Get the record of the GRL where this guid belongs to
    // 4-Returns the record 

*****************************************************************************/
/*$_POST['username'] =  "mrgan";
$_POST['userID'] =  "137";
$_POST['guid'] = "1000123"; 
*/    
    $username = $_POST['username'];
    $userID = $_POST['userID'];
    $guid = $_POST['guid'];
    //--------------------------------------------------------------------------
    // 1-Validate parameters
    //--------------------------------------------------------------------------
    $guid = $_POST['guid']; 
    if(!is_numeric($guid) || $guid<-1)
        die("<h2>ACCESS DENIED!</h2><h4>(E106) GRL Invalid guid!</h4>");
    if($username!='')
    {
      $pattern  = '/^[a-zA-Z0-9_.-]{3,15}$/';
      if (!preg_match($pattern,$username)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E102) GRL Invalid Username!</h4>");      
    }
    $userID = $_POST['userID']; 
    if($userID!='')
    {
      $pattern  = '/^[0-9]{1,10}$/';
      if (!preg_match($pattern,$userID)) 
          die("<h2>ACCESS DENIED!</h2><h4>(E103) GRL Invalid User ID!</h4>");
    }

    //--------------------------------------------------------------------------
    // 2-Validate that the user is set in assigned domain
    //--------------------------------------------------------------------------
    $result = query_general("select a.address, a.verif_tbl, u.username from assigned_domain a, users u where a.userID='$userID' and u.userID='$userID' ");
    
    $row = mysql_fetch_array($result);
    if($username==$row['username'] && $username!='')
    {   
        //--------------------------------------------------------------------------
        // 3-Get the record of the GRL where this guid belongs to
        //--------------------------------------------------------------------------
        $range_str = get_range_for_guid($guid);

        //--------------------------------------------------------------------------
        // 4-Returns the record 
        //--------------------------------------------------------------------------
        echo $range_str;  
    }
    else
    {
          die("<h2>ERROR</h2><h4>(E104) Invalid User ID!</h4>");
    }
    

?>
