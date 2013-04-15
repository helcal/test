<?

/**
  Validate an email address.
  Provide email address (raw input)
  Returns true if the email address has the email
  address format and the domain exists.
 */
function validateEmail($email) {
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($email, $atIndex + 1);
        $local = substr($email, 0, $atIndex);
        $localLen = strlen($local);
        $domainLen = strlen($domain);
        if ($localLen < 1 || $localLen > 64) {
            // local part length exceeded
            $isValid = false;
        } else if ($domainLen < 1 || $domainLen > 255) {
            // domain part length exceeded
            $isValid = false;
        } else if ($local[0] == '.' || $local[$localLen - 1] == '.') {
            // local part starts or ends with '.'
            $isValid = false;
        } else if (preg_match('/\\.\\./', $local)) {
            // local part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)) {
            // character not valid in domain part
            $isValid = false;
        } else if (preg_match('/\\.\\./', $domain)) {
            // domain part has two consecutive dots
            $isValid = false;
        } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\", "", $local))) {
            // character not valid in local part unless 
            // local part is quoted
            if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\", "", $local))) {
                $isValid = false;
            }
        }
        if ($isValid && !(checkdnsrr($domain, "MX") || checkdnsrr($domain, "A"))) {
            // domain not found in DNS
            $isValid = false;
        }
    }
    return $isValid;
}

function validatePhoneNumber($phone) {
    $msg = "";
    if (!preg_match("#[0-9]#", $phone)) {
        $msg = 'Error: Phone number should be numeric
';
    }
    return $msg;
}

function insertComment($comment, $newsID, $userID) {
    $visitorIP = VisitorIP();
    $tbl_arr = get_brnd_tbl_name_user_info(SHOWCASE_BRAND_NAME);
    $tbl = $tbl_arr[5];
    $query = "INSERT INTO $tbl (newsID,userID,date,comment,status,ip_address)
              VALUES ($newsID,$userID,CURRENT_TIMESTAMP,'$comment',0,'$visitorIP')
              ";
    $result = query_general($query);
    if ($result) {
        echo 'Thanks for posting a comment. Your comment is under revision.';
        $filter = array("tbl1.newsID=$newsID", "tbl1.userID=$userID", "tbl1.status=0");
        $comment_arr = get_comments_for_news(SHOWCASE_BRAND_NAME, $filter, " date desc ");
        $comment_arr = $comment_arr[0];
        $subject = "Comment posted on newsID $newsID, title=" . $comment_arr['title'];
        $str = "Comment on newsID $newsID
                News Title = " . $comment_arr['title'] . "<br><br>
                Date = " . $comment_arr['date_formatted'] . "<br><br>
                User Information<br> 
                User Id = " . $comment_arr['userID'] . "<br>
                User Name = " . $comment_arr['username'] . "<br>
                First and Last Name = " . $comment_arr['fname'] . " " . $comment_arr['lname'] . "<br><br>
                Comment = " . $comment_arr['comment'];
        $from = SHOWCASE_EMAIL_FROM;
        $to = SHOWCASE_COMMENTS_EMAIL_TO;

        sendEmail($subject, $str, $from, $to);
    } else {
        echo 'Error: Your comment could not be saved.';
    }
}

function updateSubscribeInformation($email, $status) {
    $visitorIP = VisitorIP();
    $tbl = getSubscribeTable();
    $query = "UPDATE $tbl 
                  SET status=$status, date=CURRENT_TIMESTAMP, ip_address='$visitorIP'
                  WHERE email='$email'";
    $result = query_general($query);
    return $result;
}

function getSubscribeTable() {
    $tbl_arr = get_brnd_tbl_name_user_info(SHOWCASE_BRAND_NAME);
    return $tbl_arr[4];
}

function insertSubscribeInformation($email) {
    //$tbl_arr = get_brnd_tbl_name_user_info(SHOWCASE_BRAND_NAME);
    $tbl = getSubscribeTable();
    $query = "SELECT * FROM $tbl 
              WHERE email='$email'";
    $result = query_general($query);
    $num_rows = mysql_num_rows($result);
    if ($num_rows == 1) { //The email exists in the subscribe table
        $row = mysql_fetch_array($result);
        $enable = $row['enable'];
        if ($row['status'] == 1)
            echo 'Your email address exists in our records.';
        else
            echo 'Thank you for signing up with <<BRAND NAME HERE>> newsletter';
        updateSubscribeInformation($email, 1);
    }
    else {
        $visitorIP = VisitorIP();
        $query = "INSERT INTO $tbl (email,status,ip_address)
                  VALUE ('$email',1,'$visitorIP')";
        $result = query_general($query);
        echo 'Thank you for signing up with <<BRAND NAME HERE>>  newsletter';
    }
}

function insertAuthenticityInfo($store_name, $product_style, $product_serial, $name_card, $first_name
, $last_name, $address, $city, $state, $zip, $email, $gender, $birth_date, $aniversary_date) {
    $visitorIP = VisitorIP();
    $tbl_arr = get_brnd_tbl_name_user_info(SHOWCASE_BRAND_NAME);
    $query = "INSERT INTO $tbl_arr[2] (store_name,product_style,product_serial,name_card,
              first_name,last_name,address,city,state,zip
              , email,gender,birth_date,aniversary_date,date, ip_address) 
              VALUES ('$store_name','$product_style', '$product_serial', '$name_card',
              '$first_name', '$last_name', '$address', '$city', '$state', '$zip',
              '$email', '$gender', '$birth_date','$aniversary_date',CURDATE(), '$visitorIP') ";
    $result = query_general($query);
    return $result;
}

function insertContactInfo($name, $email, $phone, $guid, $date, $comments, $status) {
    $tbl_arr = get_brnd_tbl_name_user_info(SHOWCASE_BRAND_NAME);
    $query = "INSERT INTO `" . $tbl_arr[7] . "` 
            (`name`, `email`, `phone`, `guid`, `date`, `comments`, `status`) VALUES 
            ('$name', '$email', '$phone', '$guid', ".($date === NULL ? 'NOW()' : "'$date'").", '$comments', '$status')";
    $result = query_general($query);
    return $result;
}

function updateUserAccount($userId, $first_name, $last_name, $email, $password1, $phone, $address, $zip, $state, $city, $country) {
    $query = "UPDATE users
                  SET fname='$first_name', lname='$last_name', phone='$phone', email='$email', address='$address',
                  city='$city', state='$state', country='$country', zip='$zip'";
    if ($password1)
        $query .=" , password=md5('$password1') ";
    $query .=" WHERE userID=$userId 
                 ";
    $result = query_general($query);
    return $result;
}

function insertNewUser($username, $first_name, $last_name, $email, $password1, $password2, $phone, $address, $zip, $state, $city, $country) {
    $result = query_general("insert into users (enable, fname, lname, username, password, phone, email, address, city,state,country,zip) 
                                 values(1, '$first_name', '$last_name', '$username', md5('$password1'), '$phone', '$email','$address','$city','$state','$country', '$zip') ");
    if ($result) {
        //GET THE NEWLY CREATED USERID
        $result = query_general("select userID from users where username='$username' ");
        $row = mysql_fetch_array($result);
        $userID = $row['userID'];
        //CREATE USER_ROLE
        $result = query_general("insert into user_roles (userID, front_end) values ($userID, '1') ");
        if ($result) {   //USER ROLES IS CREATED - NOW PERFORM THE NECESSARY STEPS ACCORDING TO USER ROLE
            echo "Username $username is created!";
            //echo "User Role for UserID=<b>$userID</b> is created.";
        }
        else
            echo '<font color="red">' . "Error: User Role for UserID=<b>$userID</b> is NOT created.</font>";
    }
    else {
        echo '<font color="red">' . "Error: User account is not created for $username.</font>";
    }
}

/*
  $guid: guid of the product to be requested. used also as flag to send and email with attachment...
 */

function sendEmail($subject, $str, $from, $to, $guid = "") {

    $now = new DateTime;
    $loc = $_SERVER['DOCUMENT_ROOT'] . "/files/";

    if ($guid) {
        //$img_path = $_SERVER[DOCUMENT_ROOT]."/".get_img_path_for_brand(SHOWCASE_BRAND_NAME, "RELATIVE");
        $message = $str;
        /* -----------------------------------------------------------------
          CREATE THE FILE
          ----------------------------------------------------------------- */
        $filename = create_report_quote_request($guid, $message);
        $fp = fopen($loc . $filename, "r");
        $content = fread($fp, filesize($loc . $filename));
        $attach[0] = array("name" => $loc . $filename,
            "content" => $content,
            "type" => 'text/html');
        fclose($fp);

        //$filename = $now->format( 'YmdHis' ).".html";
    } else {

        $filename = $now->format('YmdHis') . ".html";

        //die($filename);
        $new_file = fopen($loc . $filename, "w+") or die("<br>Couldn't create or open the file.");

        @fwrite($new_file, $str);
        fclose($new_file);

        $fp = fopen($loc . $filename, "r");
        $content = fread($fp, filesize($loc . $filename));
        $attach[0] = array("name" => $loc . $filename,
            "content" => $content,
            "type" => 'text/html');
        fclose($fp);
    }


    sendMailWithAttachment($from, $to, $subject, $content, $loc . $filename);

    if (file_exists($loc . $filename)) {
        @unlink($loc . $filename);
    }
}

function sendMailWithAttachment2($from, $to, $subject, $message, $attachment) {
    $fileatt = $attachment; // Path to the file                  
    $fileatt_type = "application/octet-stream"; // File Type 
    $start = strrpos($attachment, '/') == -1 ? strrpos($attachment, '//') : strrpos($attachment, '/') + 1;
    $fileatt_name = substr($attachment, $start, strlen($attachment)); // Filename that will be used for the file as the 	attachment 

    $email_from = $from; // Who the email is from 
    $email_subject = $subject; // The Subject of the email 
    $email_txt = $message; // Message that the email has in it 

    $email_to = $to; // Who the email is to
    $headers = "From: " . $email_from;

    $file = fopen($fileatt, 'rb');
    $data = fread($file, filesize($fileatt));
    fclose($file);
    //$msg_txt="Test Attachment";

    $semi_rand = md5(time());
    $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

    $headers .= "\nMIME-Version: 1.0\n" .
            "Content-Type: multipart/mixed;\n" .
            " boundary=\"{$mime_boundary}\"";

    $email_message .= "This is a multi-part message in MIME format.\n\n" .
            "--{$mime_boundary}\n" .
            "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" .
            $email_txt . "\n\n";

    $data = chunk_split(base64_encode($data));

    $email_message .= "--{$mime_boundary}\n" .
            "Content-Type: {$fileatt_type};\n" .
            " name=\"{$fileatt_name}\"\n" .
            //"Content-Disposition: attachment;\n" . 
            //" filename=\"{$fileatt_name}\"\n" . 
            "Content-Transfer-Encoding: base64\n\n" .
            $data . "\n\n" .
            "--{$mime_boundary}--\n";

    //Only send the email if every field is complete
    if (( strlen($email_to) > 0 ) && ( strlen($email_subject) > 0 )
            && ( strlen($email_message) > 0 ) && ( strlen($headers) > 0 )
    ) {
        $ok = @mail($email_to, $email_subject, $email_message, $headers);
        if ($ok) {
            echo "<br>Your message has been sent!";
        } else {
            echo "<br>Error! Email failed to be sent! - Location: mail.php line 136";
        }
    }
    else
        echo "<br>Error! Email failed to be sent!";
}

/*
  CREATE A FILE USING THE EMAIL TEMPLATE
  @guid   For naming purposes
  @img_path   Image to be displayed in the email
  @$message   Message to be formated
 */

function create_report_quote_request($guid, $message) {
    $loc = $_SERVER['DOCUMENT_ROOT'] . "/files/";

    $formated_message = email_body($message);
    $now = new DateTime;
    $file_name = $now->format('Ymdhms') . '-' . $guid . ".html";
    $new_file = @fopen($loc . $file_name, "w+") or die("<br>Couldn't create or open the file.");
    @fwrite($new_file, $formated_message); //Top - Retailer designed
    fclose($new_file);
    return $file_name;
}

function VisitorIP() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $TheIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        $TheIp = $_SERVER['REMOTE_ADDR'];
    return trim($TheIp);
}

function validate_model_number($model_number) {
    $tbl_arr = get_brnd_tbl_name(SHOWCASE_BRAND_NAME);
    $query = "SELECT * FROM $tbl_arr[1] WHERE model_number='$model_number'";
    $result = query_general($query);
    $num_rows = mysql_num_rows($result);
    if ($num_rows > 0)
        return true;
    else {
        $query = "SELECT * FROM simong_old_styles WHERE model_number='$model_number'";
        $result = query_general($query);
        $num_rows = mysql_num_rows($result);
        if ($num_rows > 0)
            return true;
        else
            return false;
    }
}

function get_brand_pg_info($pg_fields, $lang, $children_from_top = 1) {

    if ($pg_fields['brand_master_id'] != SHOWCASE_RETAILER_BRAND_MASTER_ID) {
        $res = query_general("SELECT * FROM brnd_" . SHOWCASE_BRAND_NAME . $lang . " WHERE pg_type='108' AND caption1='{$pg_fields['brand_master_id']}' AND parent='" . DESIGNERS_GUID . "'");
        if (mysql_num_rows($res) > 0) {
            return mysql_fetch_assoc($res);
        } else {
            return $pg_fields;
        }
    } else {
        $crumbs = explode(',', $pg_fields['bread_crumb']);
        for ($i = $children_from_top; $i > 1; $i--) {
            array_pop($crumbs);
        }
        $temp = query_get_rows_super_advanced(array_pop($crumbs), 'brnd_' . SHOWCASE_BRAND_NAME . $lang, 'GUID');
        return $temp[0];
    }
}

function get_bread_brumb_array($pg_fields, $brand_name, $lang) {
    $output = array();

    $crumbs = $pg_fields['bread_crumb'];
    if ($crumbs != 0) {
        $crumbs = explode(',', $crumbs);
        $crumbs = array_reverse($crumbs);

        foreach ($crumbs as $crumb) {
            $temp_guid = query_get_rows_super_advanced($crumb, 'brnd_' . $brand_name . $lang, 'GUID');
            $temp_guid = $temp_guid[0];
            if ($temp_guid['guid'] == SHOWCASE_HOME_PAGE) {
                $output[] = array(
                    'guid' => $temp_guid['guid'],
                    'link' => URL . gen_page_link(10, SHOWCASE_BRAND_NAME . '.php', $temp_guid['pg_type'], 'Home', SHOWCASE_HOME_PAGE, substr($lang, 1), "Home"),
                    'title' => 'Home',
                    'brand_master_id' => $temp_guid['brand_master_id']
                );
            } else {
                $output[] = array(
                    'guid' => $temp_guid['guid'],
                    'link' => URL . gen_page_link(2, SHOWCASE_BRAND_NAME . '.php', $temp_guid['pg_type'], $temp_guid['pg_name'], $temp_guid['guid'], substr($lang, 1), $GLOBALS['PG_TYPES_URL'][$temp_guid['pg_type']]),
                    'title' => $temp_guid['pg_name'],
                    'brand_master_id' => $temp_guid['brand_master_id']
                );
            }
        }
        if ($output[0]['brand_master_id'] != SHOWCASE_RETAILER_BRAND_MASTER_ID) {
            $parent = get_brand_pg_info($output[0], $lang);
            if ($parent != $output[0]) {
                $all_parents = get_bread_brumb_array($parent, SHOWCASE_BRAND_NAME, $lang);
                $output = array_merge($all_parents, $output);
            }
        }
    } else if ($pg_fields['brand_master_id'] != SHOWCASE_RETAILER_BRAND_MASTER_ID) {
        $parent = get_brand_pg_info($pg_fields, $lang);
        if ($parent != $pg_fields) {
            $all_parents = get_bread_brumb_array($parent, SHOWCASE_BRAND_NAME, $lang);
            $output = $all_parents;
        }
    }

    return $output;
}

function get_num_products_under($guid, $tbl) {
    $total_products = 0;
    $set = query_get_rows_super_advanced($guid, $tbl, 'PARENT');
    //var_dump($set);
    foreach ($set as $item) {
//        var_dump($GLOBALS['PG_TYPES_URL'][$item['pg_type']]);
//        var_dump($item);
        if ($GLOBALS['PG_TYPES_URL'][$item['pg_type']] == 'Category' || $GLOBALS['PG_TYPES_URL'][$item['pg_type']] == 'Collection')
            $total_products += get_num_products_under($item['guid'], $tbl);
        else if ($GLOBALS['PG_TYPES_URL'][$item['pg_type']] == 'Product')
            $total_products++;
    }
    return $total_products;
}

function make_transaction_det_html($resp, $cart) {
    $str = '
<table width="100%">';
    if (isset($cart) && count($cart) > 0) {
        $str .= '
    <tr>
        <th>
            Collection / Model 
        </th>
        <th>
            Specifications
        </th>
        <th>
            Price
        </th>
        <th>
            Quantity
        </th>
        <th>
            Extended
        </th>
    </tr>';
        foreach ($cart as $key => $item) {
            $str .= '
    <tr>
        <td>' . '&nbsp;' . '</td>
        <td>' . $item['spec_str'] . '</td>
        <td>' . money_format('%n', floatval($item['price'])) . '</td>
        <td> x ' . $item['qty'] . '</td>
        <td>' . money_format('%n', floatval($item['price']) * $item['qty']) . '</td>
    </tr>';
        }
    }
    $str .= '
    <tr align="right">
        <td colspan="7" class="subtotal">
            ' . money_format('Subtotal: %n', $resp->amount - $resp->tax) . '<br />
            ' . money_format('Tax: %n', $resp->tax) . '<br />
            <span class="total">' . money_format('Total: %n', $resp->amount) . '</span>
        </td>
    </tr>
</table>';

    return $str;
}

function send_success_trans_email($resp, $cart) {
    //echo 'starting email';
    //get email template

    $name = $resp->first_name . ' ' . $resp->last_name;
    $fill_arr = array(
        'name' => $name,
        'purchase_details' => make_transaction_det_html($resp, $cart),
        'site_url' => URL,
        'unsubscribe_link' => URL . '?unsubscribe'
    );
    $event_name = 'transaction_success';
    $mail_template = get_email_template($event_name);
    $event_other_info = get_event_other_info($event_name);
    $mail_template->set_vars($fill_arr);
    $now = new DateTime;

    $subject = $event_other_info['subject'];
    $to = $event_other_info['email'];

    //echo 'checkpoint 1';
    //Send the email
    //var_dump($resp);

    $str = "<br />Name: $name<br>";
    $str .= "Phone Number: {$resp->phone}<br>";
    $str .= "User Address: {$_SERVER['REMOTE_ADDR']}<br>";
    $str .= "Date: " . $now->format('m-d-Y H:i:s') . "<br /><br />";
    $str .= $fill_arr['purchase_details'];
    //$str = email_body($str);
    $from = $resp->email_address;

    //echo 'checkpoint 2';

    sendEmail($subject, $str, $from, $to);

    $from = $to;
    $to = $resp->email_address;
    $str = (string) $mail_template;

    //echo 'checkpoint 3';
    //var_dump($subject, $str, $from, $to, $name);
    sendEmail($subject, $str, $from, $to, $name);

    //echo 'success';

    return true;
}

function send_request($q_info, $event_name = 'quote_request') {
    $name = $q_info['name'];
    $email = $q_info['email'];
    $message = $q_info['msg'];
    $guid = $q_info['guid'];
    $phone = $q_info['phone'];
    $title = $q_info['title'];
    $model_number = $q_info['model_number'];
    $brand = $q_info['brand'];
    $REMOTE_ADDR = $q_info['REMOTE_ADDR'];
    $HTTP_REFERER = $q_info['HTTP_REFERER'];
    $FULL_URL = $q_info['FULL_URL'];
    $now = new DateTime;
    $error = 0;
    
    //Validation of fields
    if (trim($name) == "" || trim($email) == "") {
        $error = 1;
        echo 'Error: Please fill all required fields';
    }
    //If there is an error, don't proceed with the db
    if ($error == 1) {
        //echo '<div id="error" style="display:none">Error</div>';
    } else {

        //Save the information in the database
        $msg = validateEmail($email);


        if ($msg) {
            //Information to db
            $result = insertContactInfo($name, $email, $phone, $guid, NULL, $message, 1);
            if ($result) {

                $pg_info = get_pg_info_from_guid($guid);
                $tbl = get_brnd_tbl_name($pg_info['brand_name']);
                $product_info = query_get_rows_super_advanced($guid, $tbl[0], 'GUID');
                $pg_item_det = query_pg_fields($guid, $tbl[1]);

                //get email template
                $mail_template = get_email_template($event_name);

                if ($mail_template) {

                    //var_dump($mail_template->needed_vars());

                    $fill_arr = array(
                        'name' => $name,
                        'submit_page' => $FULL_URL,
                        'product_name' => $title,
                        'model_number' => $model_number,
                        'brand_name' => $brand,
                        'link' => $FULL_URL,
                        'unsubscribe_link' => URL . '?unsubscribe',
                        'site_url' => URL,
                        'logo' => EMAIL_LOGO
                    );

                    $event_other_info = get_event_other_info($event_name);
                    $mail_template->set_vars($fill_arr);

                    //Send the email

                    $str = "<br />Name: $name<br>";
                    $str .= "Phone Number: $phone<br>";
                    $str .= "Product: <a href=\"" . URL . gen_page_link(0, SHOWCASE_BRAND_NAME . '.php', $product_info[0]['pg_type'], $product_info[0]['pg_name'], $product_info[0]['guid'], 'EN', $GLOBALS['PG_TYPES_URL'][$product_info[0]['pg_type']]) . "\">" . $product_info[0]['pg_name'] . " " . $pg_item_det['model_number'] . "</a><br>";
                    $str .= "User Address: $REMOTE_ADDR<br>";
                    $str .= "Referal Site: $HTTP_REFERER<br>";
                    $str .= "Page Customer Inquired from: <a href=\"$FULL_URL\">$title</a><br>";
                    $str .= "Date: " . $now->format('m-d-Y H:i:s') . "<br /><br />";
                    $str .= "Message: $message<br>";
                    //$str = email_body($str);
                    $from = $email;
                    mail_sendEmailByEvent($event_other_info['subject'], $str, $from, $event_other_info['email'], $event_name);

                    $str = (string) $mail_template;
                    //var_dump($str);
                    mail_sendEmailByEvent($event_other_info['subject'], $str, $event_other_info['email'], $from, $event_name);
                }

                return true;
                //                        echo 'Message Sent! '.$str;
            } else {
                echo 'Error: Information could not be saved';
            }
        } else { //Validation failed
            echo $msg;
        }
    }
    return false;
}

function join_newsletter($email) {
    $uid = db_validate_email_existance($email);
    //var_dump($uid);
    if ($uid != NULL) {
        return db_update_user_subscriptions($uid, 1);
    } else {
        return db_create_new_user('', '', $email, '', 1);
    }
}

function quit_newsletter($email) {
    $uid = db_validate_email_existance($email);
    if ($uid) {
        return db_update_user_subscriptions($uid, 0);
    }
}

function get_tree_day_items($guid, $lang) {
    $items = query_get_rows_super_advanced($guid, 'brnd_' . SHOWCASE_BRAND_NAME . '_' . $lang, 'PARENT');
    //var_dump($items[0]['guid'], 'brnd_'.SHOWCASE_BRAND_NAME.'_item_det');
    foreach ($items as &$item) {
        //var_dump($item);
        $temp = query_pg_fields($item['guid'], 'brnd_' . SHOWCASE_BRAND_NAME . '_item_det');
        $temp = mysql_fetch_assoc($temp);

        $item = array_merge($item, $temp);
    }
    return $items;
}

function get_matching_three_day_items($guid, $lang, $model) {
    $items = get_tree_day_items($guid, $lang);
    $matching = array();
    foreach ($items as $item) {
        if ($item['model_number'] == $model) {
            $matching[] = $item;
        }
    }
    return $matching;
}

function get_product_title($pg_fields, $tbl_arr = "", $table1 = '', $lan = 'EN') {
    $title = false;
    
    get_brand_name_new($pg_fields['brand_master_id'], $brand_name);
    if (!$tbl_arr) {
        $base_brand_db = get_base_brand_db_name_for_lang($pg_fields['brand_master_id'], $lan);
        $tbl_arr = get_brnd_tbl_name($base_brand_db);
    }

    /* if (!$table1)
      {
      $tbl_arr2 = get_brnd_tbl_name($brand_name['brand_name']);
      $table1=$tbl_arr2[1];

      } */

    $table2 = (is_array($tbl_arr) ? $tbl_arr[2] : $tbl_arr);
    $specs = array();
    $results = query_get_rows($pg_fields['guid'], $table2);
    $title_arr = array();
    while ($res = mysql_fetch_assoc($results)) {
        if (!isset($specs[$res['spec_field']])) {
            $specs[$res['spec_field']] = $res['spec_val'];
        } else {
            $specs[$res['spec_field']] .= ', ' . $res['spec_val'];
        }
    }
    if (isset($specs['Product Type'])) {
        switch ($specs['Product Type']) {
            case 'Watches':
                $title_arr[0] = $brand_name['real_brand_name']; //brand name
                if (isset($specs['Series']))
                    $title_arr[1] = $specs['Series']; //series
                if (isset($specs['Gender']))
                    $title_arr[2] = 'for ' . $specs['Gender']; //for gender
                if (isset($specs['Movement']))
                    $title_arr[3] = $specs['Movement']; //movement
                break;
            default:
                $title_arr[0] = $brand_name['real_brand_name']; //brand name
                $title_arr[1] = $specs['Product Type'];
                break;
        }
        $title = implode(' ', $title_arr);
    }

    if (!$title) {
        /* if ($pg_fields['pg_type']==2)
          {
          $pg_item_det = query_pg_fields($pg_fields['guid'], $table1);
          $title=$pg_item_det['model_number'];
          } */

        if (!$title)
            $title = $pg_fields['pg_name'];
    }

    return $title;
}

?>