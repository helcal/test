<?    
   /*
   #############################################################################
   # PROTOCOL NAME: ICP (I-SHOWCASE COMMUNICATION PROTOCOL)                    #
   # FILE NAME: ICP.PHP                                                        #
   # VERSION: 2.07                                                             #
   # © 2011-2012 I-SHOWCASE INC. ALL RIGHTS RESERVED.                          #
   #############################################################################
   
   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
   @###########################################################################@
   @#           --->>>DO NOT CHANGE ANYTHING ON THIS FILE.<<<---              #@
   @# ISHOWCASE CORPORATION IS THE COPYRIGHT HOLDER OF THE FOLLOWING CODE,    #@
   @# AND UNDER THE COPYRIGHT LAW, ISHOWCASE CORPORATION PROHIBITS ANY        #@  
   @# UNAUTHORIZED MODIFICATION, COPY, TRANSFER, OR RE-CREATION OF THE        #@ 
   @# FOLLOWING PORTION OF THE CODE.                                          #@
   @#                                                                         #@   
   @# YOU ARE RESPONSIBLE TO PROTECT THIS CODE FROM UN-AUTHORIZED             #@
   @# MODIFICATIONS BY PROTECTING YOUR WEB HOSTING LOGIN CREDENTIALS. ANY     #@
   @# UN-AUTHORIZED ACCESS OR MODIFICATION TO THIS CODE MAY JEOPARDIZE YOUR   #@
   @# HOSTING SECURITY. PLEASE NOTIFY YOUR CUSTOMER REPRESENTATIVE AT         #@
   @# I-SHOWCASE CORPORATION IN CASE YOU BECOME AWARE OF ANY UN-AUTHORIZED    #@
   @# ACCESS TO YOUR WEB SERVER BY CALLING 1-800-996-0967 OR SENDING EMAIL    #@
   @# TO ICP.CLIENT@ISHOWCASEINC.COM.                                         #@   
   @#                                                                         #@
   @# I-SHOWCASE CORPORATION IS NOT RESPONSIBLE FOR ANY LOSES OR DAMAGES      #@
   @# AROSE FROM ANY UN-AUTHORIZED MODIFICATION OF THIS FILE.                 #@
   @#                                                                         #@
   @###########################################################################@
   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
   */

INSERTUSERID                                              
INSERTUSERNAME
    $file_path = PRIVATE_SRV_SETTINGS_FOLDER."/".SRV_FILE;
    $baseURL = "http://staging.portal.ishowcaseinc.com";
    $version = '2.07';
    //**************************************************************************

global $_POST;

    //VERIFY CMD PATTERN
    if (isset($_POST['cmd']))
    {
        $cmd = $_POST['cmd'];
        $pattern  = '/^[A-Z_]{5,24}$/';
        if (!preg_match($pattern,$cmd)) 
            die("<h1>ACCESS DENIED!</h1>");
    }
    else
        $cmd = "";
        
    //VERIFY FILE PATTERN
    if (isset($_POST['file']))
    {
        $file = $_POST['file'];
        $pattern  = '/^[0-9]{6,14}$/';
        if (!preg_match($pattern,$file)) 
            die("<h1>ACCESS DENIED!</h1>");
    
    }
     else
        $file=""; 

    //**************************************************************************
    
      
    function get_ishowcase_content($userID="",$username="",$baseURL="")
    {
        global $_GET;
        if (!$userID)
            global $userID;
        if (!$username)
            global $username;
        if (!$baseURL)
            global $baseURL;
        
        if (isset($_GET['guid']))    
            $guid = $_GET['guid'];
        else 
            $guid = "";
            
        //VALIDATE GUID
        if(!is_numeric($guid) || $guid<0)
          die("INVALID GUID!");  //INVALID GUID
        
        if (isset($_GET['lang']))
            $set_lang = $_GET['lang'];    //LANGUAGE
        else
            $set_lang = "";
            
        //VERIFY LANGUAGE CODE
        if($set_lang!='')                       
        {
          $pattern  = '/^[A-Z]{2,3}$/';
          if (!preg_match($pattern,$set_lang)) 
              die("INVALID LANGUAGE CODE!");      
        }      
        
        //Task 1090
        if (isset($_GET['pg']))
            $pg = $_GET['pg'];
        else
            $pg = "";
        if (isset($_GET['search']))    
            $search = $_GET['search'];
        else
            $search = "";
            
        //********************************************************************
        //********************************************************************
        //GET THE RANGE OF THE GUID AND THE URL ASSOCIATED FOR CALLING THE CURL
        $flag_guid_in_array = 0; // If the guid was belongs to a range asked before, this flag turns 1


        //Get previus ranges get. Validate if the guid belongs to one of this ranges
        if (isset($_SESSION['GRL']))
        {
            $array =$_SESSION['GRL']; //$array takes the value of the session variable
            for ($i=0;$i<count($array);$i++)
            {
               $record_range_arr = $array[$i];
               if ( ($guid>=$record_range_arr['0']) && ($guid<=$record_range_arr['1']) )
               {
                  $flag_guid_in_array = 1; //guid found in a range
                  $baseURL = trim($record_range_arr['2']); //baseURL using in the curl for getting the meta and body
                  break;
               }
    
            }        
                     
        }
        else  //Session Variable not created yet.
        {
            $array = array(); //$array is an empty array
        }
        
        if ($flag_guid_in_array==0) //guid not found before. Curl the central server for getting the range list
        {
              $ch = curl_init("$baseURL/public/V2.0/srv/grl.php");
              curl_setopt($ch, CURLOPT_HEADER, 0);
              curl_setopt($ch, CURLOPT_POST, 1);  
              curl_setopt($ch, CURLOPT_TIMEOUT, 5);   
              curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&&userID=$userID&&guid=$guid");
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
              $range_str = ltrim(curl_exec($ch));       
              curl_close($ch);              
              if (!$range_str) 
              {
                  //echo "ICPR01 - Guid not found";
                  //die();	
                  $res['body']="ICPR01 - Guid not found";
                  return $res;
              }
              else
              {

                  $range_arr = explode(",",$range_str);
    
                  //range_arr[0] guid from
                  //range_arr[1] guid to
                  //range_arr[2] url
                  array_push($array,$range_arr);
                  $_SESSION['GRL']=$array;
            
                  //if ( ($guid>=$range_arr[0]) && ($guid<=$range_arr[1]) ) //This validation was done in grp.php
                  //{
                  $baseURL = trim($range_arr['2']);
                  //      break;
                  //}
              
              }



        }            
            
            
        //********************************************************************
        //********************************************************************
        //INITIAL THE CURL TO THE SERVER
        $ch = curl_init("$baseURL/public/V2.0/srv/srv.php");
        //--------------------------------------------------------------------
        //GET META
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);  
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);   
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&&userID=$userID&&cmd=GET_META&&guid=$guid&&lang=$set_lang");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $meta = ltrim(curl_exec($ch));       
        //--------------------------------------------------------------------
        //GET BODY
        $params = "username=$username&&userID=$userID&&guid=$guid&&lang=$set_lang";
        if ($pg)
            $params .= "&&pg=$pg"; 
        if ($search)
            $params .= "&&search=$search"; 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 0);    
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $body = ltrim(curl_exec($ch)); 
        //CLODE THE CURL
        curl_close($ch);        
        //********************************************************************
        //********************************************************************
        $res['body'] = $body;
        $res['meta'] = $meta;
        return $res;
    } 
    
    //**************************************************************************
    //COMMANDS START
    if ($cmd=='ISHOWCASE_SEND_MAIL')
    {
        /*---------------------------------------------------
        RECOMMEND THE PRODUCT
        ---------------------------------------------------*/
        //----------------------
        // 1-get Parameters
        //----------------------
        $model = ""; $guid = ""; $sender_name = ""; $from_add = ""; $receipt_name="";
        $to_add = "";  $sender_msg = "";  $client_url = "";   $set_lang = ""; $logo_path=""; $img_path ="";

        if (isset($_POST["pg_name"]))
              $model=str_replace("\'","",$_POST["pg_name"]);
              
        if (isset($_POST["guid"]))
            $guid=$_POST["guid"];
        
        if (isset($_POST["senderName"]))
            $sender_name=$_POST["senderName"];
        
        if (isset($_POST["from"]))
            $from_add = $_POST["from"];
        
        if (isset($_POST["receiptName"]))
            $receipt_name=$_POST["receiptName"];

        if (isset($_POST["to"]))            
            $to_add = $_POST["to"];
            
        if (isset($_POST["message"]))    
            $sender_msg = $_POST["message"];
        
        if (isset($_POST["client_address"]))
            $client_url = $_POST["client_address"];
        
        if (isset($_POST["set_lang"]))
            $set_lang = $_POST["set_lang"];
        
        if (isset($_POST["logo_path"]))
            $logo_path = $_POST["logo_path"];
        
        if (isset($HTTP_POST_VARS["img_path"]))
            $img_path = $HTTP_POST_VARS["img_path"];

        //----------------------
        //  2-If everything has been validated, send the information 
        //----------------------      
        $ch = curl_init("$baseURL/public/V2.0/shopping_cart/email_sender.php");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);    
        curl_setopt($ch, CURLOPT_POSTFIELDS, "model=$model&&guid=$guid&&sender_name=$sender_name&&from_add=$from_add&&receipt_name=$receipt_name&&to_add=$to_add&&sender_msg=$sender_msg&&client_url=$client_url&&set_lang=$set_lang&&logo_path=$logo_path&&img_path=$img_path");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = ltrim(curl_exec($ch));       
        curl_close($ch);
        //Message from the curl is displayed on the calling page
        echo $content;
    }
    else if ($cmd=='ISHOWCASE_QUOTE_REQUEST')
    {
        /*---------------------------------------------------
        QUOTE REQUEST
        ---------------------------------------------------*/
        $client_address="";
        if (isset($_SERVER['HTTP_REFERER']))
        {
            $referer = $_SERVER['HTTP_REFERER'];
            $client = explode("/", $referer);
            $client_address = $client[0].'/'.$client[1].'/'.$client[2];
        
        }
    
        //----------------------
        // 1-Validate fields
        //----------------------
    
        $guid = ""; $name=""; $email="";$phone="";$cell="";
        
        if (isset($_POST["guid"]))
            $guid=$_POST["guid"];
        if (isset($_POST["name"]))
            $name=$_POST["name"]; 
        if (isset($_POST["email"]))
            $email=$_POST["email"];
        if (isset($_POST["phone"]))
            $phone=$_POST["phone"];
        if (isset($_POST["comment"]))
            $cell=$_POST["comment"];

        $msg = "";
        $error = 0;
        
        if($name=='' ||  $email=='' || $phone=='')   
           $error=1;                      
             
        if($error ==1)
          $msg = '<p class="ishowcase_center_body" align="left"><font color="#FF0000">&nbsp;ERROR: All fields are required</font></p>';        
        
        if( !preg_match("/^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $phone)) 
        {
            $error=1;                      
            $msg = $msg . '<p class="ishowcase_center_body"  align="left"><font color="red">&nbsp;ERROR: Phone number '.$phone.' seems to be wrong! (Format: 888-888-8888)</font></p>';
        }
    
        if (!ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $email))
        {
          $error=1;                      
          $msg = $msg . '<p class="ishowcase_center_body"  align="left"><font color="red">&nbsp;ERROR: Email format seems to be wrong! (Format: you@domain.com)</font></p>';         
        }      
    
        if ($error == 0)
        {
            //----------------------
            //  2-If everything has been validated, send the information 
            //----------------------      
            $ch = curl_init("$baseURL/public/V2.0/shopping_cart/quote_request.php");
            
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1); 
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);    
            curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&&userID=$userID&&guid=$guid&&email=$email&&name=$name&&phone=$phone&&cell=$cell&&client_address=$client_address&&client_name=$username");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $content = ltrim(curl_exec($ch));       
            curl_close($ch);
            //Message from the curl is displayed on the calling page
            echo $content;             
        }
        //----------------------
        //  3-Show $msg variable
        //----------------------
        echo $msg;
        /*---------------------------------------------------
        End QUOTE REQUEST
        ---------------------------------------------------*/    
    }
    else if($cmd=='CHECK_ISHOWCASE_TEMP')
    {
        $folder = 'ishowcase_temp';
        if (!file_exists($folder))
        {
          if(mkdir($folder)) 
              echo "ICP101";
          else
              echo "ICP102";
        }
        else
          echo "ICP101";
        die();
    }
    else if($cmd=='AUTHENTICATE')
    {
        if($file!='')
        {
            $fp = fopen("ishowcase_temp/$file.txt", "w"); 
            fclose($fp);
            echo 'ICP700';
        }
        else
            echo 'ICP701';     
        die();
    }
    else if($cmd=='VERIFIED')
    {
        if(unlink("ishowcase_temp/$file.txt"))
            echo 'ICP600';
        else
            echo 'ICP601';
        die();
    }     
    else if($cmd=='ISHOWCASE_AUTO_SETUP')
    {
        $ch = curl_init("$baseURL/public/V2.0/ishowcase_auto_setup/$file.html");    
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 0); 
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = ltrim(curl_exec($ch));               
        curl_close($ch);
        if($content!='')
        {
            $fp = fopen("ishowcase.php", "w");
            if(fwrite($fp, $content))
                echo 'ICP200';
            else
                echo 'ICP201';
            fclose($fp);
        } 
        else
            echo 'ICP202';             
        die();
    }
    else if($cmd=='UPDATE_ICP')
    {
        $ch = curl_init("$baseURL/public/V2.0/remote_updates/get_icp_update_temps.php"); 
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&&userID=$userID");
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = ltrim(curl_exec($ch));               
        curl_close($ch);
        if($content!='')
        {
            $fp = fopen("ishowcase_temp/update_icp.php", "w");
            if(fwrite($fp, $content))
                echo 'ICP300';
            else
                echo 'ICP301';
            fclose($fp);
        } 
        else
            echo 'ICP302';             
        die();
    }
    else if($cmd=='DELETE_UPDATE_ICP')
    {
        if(unlink("ishowcase_temp/update_icp.php"))
            echo 'ICP500';
        else
            echo 'ICP501';
        die();
    }
    else if($cmd=='GET_VERSION')
    {
        echo $version;
    }
    else if($cmd=='CHANGE_CHMOD')
    {
         chmod("icp.php",0777);    
    }   
    else if($cmd=='CHECK_CURL')
    {
        $ch = curl_init("http://www.yahoo.com");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&&userID=$userID");
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);    
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $content = ltrim(curl_exec($ch));               
        curl_close($ch);
        if($content!='')
            echo $content;
        else
            echo 'ICP401';
    }     
    //COMMANDS END
    //**************************************************************************
?>