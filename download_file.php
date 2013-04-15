<?
    session_start();
    session_regenerate_id();
    if (isset($_SESSION['download_file_path']))
    {
        $file=$_SESSION['download_file_path']; 
    }
    else
    {
        define("VERSION", "V2.0");
        define("PRIVATE_USERS_FOLDER","$_SERVER[DOCUMENT_ROOT]/private/".VERSION."/users/");
        $file_name = $_GET['file'];
        $file = PRIVATE_USERS_FOLDER.$_SESSION['validated_userID'].'/'.$file_name;
    }    
   
   
   
    if(file_exists($file)) 

    {

            header('Content-Description: File Transfer');
            header('Content-Disposition: attachment; filename='.basename($file));

        if (!$_SESSION['download_file_path'])
        {
             header('Content-type: text/plain');
        }
        else
        {
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
        }


        header('Expires: 0');

        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header('Pragma: public');

        header('Content-Length: ' . filesize($file));

        ob_clean();

        flush();

        readfile($file);

        if (isset($_SESSION['download_file_path']))
        {
            unset($_SESSION['download_file_path']);
            //unlink($file);
        
        }
        
    }

    else 

    { 

        echo "File $file does not exist "; 

    } 
?>