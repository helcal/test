<?
    require_once("$_SERVER[DOCUMENT_ROOT]/includes/init.php");
    connect_internal_db();

/*
    echo '<br><br>www.photographerbox.com<br>';
    print_r($HTTP_POST_VARS);
    echo '<br>';
*/  
    
/****************************************************************************
    Replicate the information in the server database.
    Possible commands:
        ### signup.php ###
        ADD_USER
        ADD_USER_ROLE
        ADD_DATA_RECIPIENT
        ### send_password.php ###
        UPDATE_PASSWORD
        ### manage_users.php ###
        UPDATE_USER_INFO
        UPDATE_USER_ROLE
        UPDATE_DATA_RECIPIENT
        BRAND_LIST_FOR_RETAILER
        UPDATE_OWNER_VERIF_FROM_DEALER
*****************************************************************************/

    $userID = $HTTP_POST_VARS[userID];
    $cmd = $HTTP_POST_VARS[cmd];
//    echo '<br>$cmd=',$cmd;

    switch ($cmd) 
    {
    case 'ADD_USER':
          create_new_user_account_srv();
      break;
    case 'ADD_USER_ROLE':
        $user_role = $HTTP_POST_VARS[user_role]; 
        user_role_srv($cmd,$userID,$user_role,$status=1);
      break;  
    case 'ADD_DATA_RECIPIENT':
        $domain_name = $HTTP_POST_VARS[domain_name];
        add_data_recipient_srv($cmd, $userID,$domain_name);
      break;
    case 'UPDATE_PASSWORD':
        $password = $HTTP_POST_VARS[password];
        update_password_srv($userID,$password);
      break;
    case 'UPDATE_USER_INFO':
        update_user_information_srv($cmd,$userID);
      break;
    case 'UPDATE_USER_ROLE':
      $user_role = $HTTP_POST_VARS[user_role];
      update_user_roles_srv($userID,$user_role,$cmd);
      break;
    case 'UPDATE_DATA_RECIPIENT':
      $domain_name = $HTTP_POST_VARS[domain_name];
      update_domain_name_srv($userID,$domain_name,$cmd);
      break;
    case 'MODULE_ACCESS':
      $module_from_form = $HTTP_POST_VARS[module_from_form];
      $access = $HTTP_POST_VARS[access];
      update_module_access_srv($userID,$module_from_form,$access);
      break;
    case 'BRAND_ACCESS':
      $brand_master_id = $HTTP_POST_VARS[brand_master_id];
      $access = $HTTP_POST_VARS[access];
      update_brand_access_srv($userID,$brand_master_id,$access,$cmd);
      break;
    case 'DELETE_USER':
      delete_user($userID, &$normal_msg,&$warning_msg,&$error_msg); 
      break;
    case 'CHANGE_USER_STATUS':
      $enable =  $HTTP_POST_VARS[enable];
      $result = query_general("update users set enable='$enable' where userID='$userID' ");
      break;
    case 'UPDATE_DEALER_CONFIGURATION':
      $operation =  $HTTP_POST_VARS[operation];
      update_dealer_configuration($operation);
      break;
    case 'GET_BRAND_FOR_RETAILER':
      $brand_info = get_brand_for_retailer($userID,"",&$brand_info_str);
      echo $brand_info_str;
      break;
    case 'BRAND_LIST_FOR_RETAILER':
      $brand_master_id_arr = get_brand_list_for_retailer($userID);
      
      $brand_master_id_str = "";
      for ($i=0;$i<count($brand_master_id_arr);$i++)
      {
            if (is_array($brand_master_id_arr[$i][1]))
            {
               $brand_info = implode("%%",$brand_master_id_arr[$i][1]);
               //echo '<br>$brand_info='.$brand_info;
               $brand_master_id_arr[$i][1] = $brand_info;  
            }
            $brand_master_id_str .= implode("|",$brand_master_id_arr[$i]);
            $brand_master_id_str .= '#';
      }
      echo $brand_master_id_str;

      break;
    case 'BRAND_ACCESS_FOR_DEALER': //All brands for the username with verif_tbl and status. User on edit_profile.php
        $brnd_str = brand_access_for_dealer($userID); 
        echo $brnd_str;    
      break;
    case 'BRAND_ACCESS_GENERAL': //All the brands. Used on signup.php before the username is created
        $brnd_str = brand_access_general();
        echo $brnd_str;    
      break;
    case 'UPDATE_OWNER_VERIF_FROM_DEALER':
        $param_str =  $HTTP_POST_VARS[param_str];
        $username = $userID;
        $brands_arr = explode("#",$param_str);
        update_owner_verif_from_dealer($username,$brands_arr);
      break;
    default:
        echo '<br>Error SRV002 - CMD unknown';    	
    	break;
    }
    
    


//FUNCTIONS FOR REPLICATING THE INFORMATION
function update_module_access_srv($userID,$module_from_form,$access)
{
      $result = query_general("update module_access set access='$access' where userID='$userID' and mod_name='$module_from_form' ");
      if ($result) 
      {
          $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
          
          if(file_exists($file_path))
          {
              $conf_arr = read_configuration($file_path);
              $srv_type = trim($conf_arr['srv_type']);
              if ($srv_type=='central') 
              {
                   srv_curl_brand_access($userID,$brand_master_id,$access); 
              }
          }
      	
      }
}

function update_brand_access_srv($userID,$brand_master_id,$access)
{
      if ($brand_master_id)
      {
          if (!$access) 
          {
              $access=0;	
          }
          $result = query_general("update brand_access set brand_access='$access' where userID='$userID' and brand_master_id='$brand_master_id' ");
          if ($result) 
          {
              $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
              
              if(file_exists($file_path))
              {
                  $conf_arr = read_configuration($file_path);
                  $srv_type = trim($conf_arr['srv_type']);
                  if ($srv_type=='central') 
                  {
                       srv_curl_module_access($userID_from_form,$module_from_form,$access); 
                  }
              }
          	
          }
      
      }

}
function update_domain_name_srv($userID,$domain_name,$cmd)
{
    if ($domain_name)
    {
         $query = "UPDATE assigned_domain 
                   SET address='$domain_name'
                   WHERE userID=$userID";
         $result = query_general($query); 
          if ($result) 
          {
              $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
              
              if(file_exists($file_path))
              {
                  $conf_arr = read_configuration($file_path);
                  $srv_type = trim($conf_arr['srv_type']);
                  if ($srv_type=='central') 
                  {
                       srv_curl_data_recipient($userID, $domain_name, $cmd);
                  }
              }
          	
          }
    
    }


}

function update_password_srv($userID,$password)
{
    if ($password)
    {
         $query = "UPDATE users 
                   SET password='$password'";
         $query .= " WHERE userID=$userID";
         $result = query_general($query);
          if ($result) 
          {
              $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
              
              if(file_exists($file_path))
              {
                  $conf_arr = read_configuration($file_path);
                  $srv_type = trim($conf_arr['srv_type']);
                  if ($srv_type=='central') 
                  {
                      srv_curl_update_password($userID,$password);
                  }
              }
          	
          }
    
    }
  
}
function update_user_roles_srv($userID,$user_role,$cmd)
{
    if ($user_role)
    {
         $query = "UPDATE user_roles 
                   SET ";
         $query .= $user_role;
         $query .= " WHERE userID=$userID";
         $result = query_general($query);
    
          if ($result) 
          {
              $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
              
              if(file_exists($file_path))
              {
                  $conf_arr = read_configuration($file_path);
                  $srv_type = trim($conf_arr['srv_type']);
                  if ($srv_type=='central') 
                  {
                       srv_curl_user_role($userID,$user_role, $cmd);
                  }
              }
          	
          }
    
    }
  

}


function update_user_information_srv($cmd,$userID)
{
          global $HTTP_POST_VARS;

          $fname = trim($HTTP_POST_VARS[fname]);
          $lname = trim($HTTP_POST_VARS[lname]);
          $username = trim($HTTP_POST_VARS[username]);
          $password = trim($HTTP_POST_VARS[password]);
          $email = trim($HTTP_POST_VARS[email]);
          $company = trim($HTTP_POST_VARS[company]);
          $phone = trim($HTTP_POST_VARS[phone]);
          $address = trim($HTTP_POST_VARS[address]);

          if ($userID && (count($error_msg)==0) )
          {
               $query = "UPDATE users 
                         SET ";
               if ($password)
               {
                   $query .=" password='$password', ";
               }
               $query .=" 
                         fname='$fname',
                         lname='$lname',
                         company='$company',
                         phone='$phone',
                         email='$email'
                     WHERE userID=$userID";
               $result = query_general($query); 
              
               if ($address) 
               {
                   $query = "SELECT count(1) FROM assigned_domain 
                             WHERE userID=$userID";
                   $result = query_general($query); 
                   $num_rows = mysql_num_rows($result);
                   
                   if ($num_rows>0) 
                   {
                       $query = "UPDATE assigned_domain 
                                 SET address='$address'
                                 WHERE userID=$userID";
                       $result = query_general($query); 

                   }

               }

          }
}

function add_data_recipient_srv($cmd, $userID,$domain_name)
{
        $error_msg = array();
        $normal_msg = array();
        $warning_msg = array();
        
        create_db_tables_for_data_recipients($userID, &$error_msg, &$normal_msg, &$warning_msg, $domain_name, $domain_name);
        $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
        
        if(file_exists($file_path))
        {
            $conf_arr = read_configuration($file_path);
            $srv_type = trim($conf_arr['srv_type']);
            if ($srv_type=='central') 
            {
                  srv_curl_data_recipient($userID, $domain_name, $cmd);
            }
        }
    	
}

function user_role_srv($cmd,$userID,$user_role,$status)
{
        $result = query_general("insert into user_roles (userID, $user_role) values ($userID, '$status') ");
        if ($result) 
        {
            $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
            
            if(file_exists($file_path))
            {
                $conf_arr = read_configuration($file_path);
                $srv_type = trim($conf_arr['srv_type']);
                if ($srv_type=='central') 
                {
                    srv_curl_user_role($userID,$user_role, $cmd);
                }
            }
        	
        }

}

function create_new_user_account_srv()
{
    //$USER_ROLE VALUES:
        //admin
        //data_provider
        //data_recipient
        //group_manager
    global $HTTP_POST_VARS;
    $userID = $HTTP_POST_VARS[userID];
    $fname = $HTTP_POST_VARS[fname];
    $lname = $HTTP_POST_VARS[lname];
    $company = $HTTP_POST_VARS[company];
    $phone = $HTTP_POST_VARS[phone];
    $email = $HTTP_POST_VARS[email];
    $username = $HTTP_POST_VARS[username];
    $password = $HTTP_POST_VARS[password];
    $enable = $HTTP_POST_VARS[enable]; 
    $cmd = $HTTP_POST_VARS[cmd];
    //Validate the information

    //Verify that the userID does not exist in the database
     $result = query_general("select * from users where userID=$userID ");
     $num_rows = mysql_num_rows($result);
    
    if ($num_rows==0) 
    {
        //Insert the information
        $result = query_general("insert into users (userID, enable, fname, lname, username, password, company, phone, email) values($userID, $enable, '$fname', '$lname', '$username', '$password', '$company', '$phone', '$email') ");
        
        //If it is a central server, replicate the information in all the servers
        if ($result) 
        {
            $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
            
            if(file_exists($file_path))
            {
                $conf_arr = read_configuration($file_path);
                $srv_type = trim($conf_arr['srv_type']);
                if ($srv_type=='central') 
                {
                    srv_curl_user_information($userID, $enable, $fname, $lname, $username, $password, $company, $phone, $email,$cmd);
                }
            }
        	
        }
    	
    }
}  
?>