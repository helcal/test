<?php
/*
 * Page Types Definition
 * =====================
 * For page type definitions please view General Config File @ public_html/private/V2.0/config/config.php
 * 
 */

require_once 'showcase_functions.php';
$popup_msg = '';


/* Quote Request, Newsleter signup, then Newsleter quit */
if (isset($_POST['q_req'])) {
    $inquire_errors = '';

    if (!isset($_POST['q_req']['name']) || empty($_POST['q_req']['name']) || $_POST['q_req']['name'] == 'Name'
            || !isset($_POST['q_req']['email']) || empty($_POST['q_req']['email']) || $_POST['q_req']['email'] == 'Email Address'
            //|| !isset($_POST['q_req']['msg']) || empty($_POST['q_req']['msg']) || $_POST['q_req']['msg'] == 'Message'
            || !isset($_POST['q_req']['phone']) || empty($_POST['q_req']['phone']) || $_POST['q_req']['phone'] == 'Phone Number') {
        $inquire_errors = 'All Fields Must be Filled Out';
        //$popup_msg = "<h2>Sorry</h2><p>$inquire_errors</p>";
    } else {
        if (send_request($_POST['q_req'], 'quote_request')) {
            $popup_msg = '<h2>Thank You!</h2><p>Thank you for your interest in Moyer Fine Jewelers. We will get back to you as soon as possible.</p>';
        } else {
            $popup_msg = "<h2>Sorry</h2><p>Your message was not successfully sent. Please feel free to Call us at <> or email us at <>.</p>";
        }
    }
} else if (isset($_POST['news'])){
    
    if (isset($_POST['news']['join'])){
        if (!isset($_POST['news']['join']['email']) || empty($_POST['news']['join']['email']) || $_POST['news']['join']['email'] == 'Email Address'){
            $newsletter_err_msg = 'Please enter a valid email address.';
            $popup_msg = '<h3>'.$newsletter_err_msg.'</h3>';
        }
        else if (!validateEmail($_POST['news']['join']['email'])){
            $newsletter_err_msg = 'Please enter a valid email address.';
            $popup_msg = '<h3>'.$newsletter_err_msg.'</h3>';
        }
        else {
            $check = join_newsletter(trim($_POST['news']['join']['email']));
            $popup_msg = '<h2>Thank You!</h2><p>Thank you for your interest in Moyer Fine Jewelers. We have added you to our newsletter.</p>';
        }
    }
    else if (isset($_POST['news']['quit'])){
        if (!isset($_POST['news']['quit']['email']) || empty($_POST['news']['quit']['email']) || $_POST['news']['quit']['email'] == 'Email Address'){
            $newsletter_err_msg = 'Please enter an email address.';
            $popup_msg = '<h3>'.$newsletter_err_msg.'</h3>';
        }
        else if (!validateEmail($_POST['news']['quit']['email'])){
            $newsletter_err_msg = 'Please enter a valid email address.';
            $popup_msg = '<h3>'.$newsletter_err_msg.'</h3>';
        }
        else {
            $check = quit_newsletter(trim($_POST['news']['quit']['email']));
            $popup_msg = '<h2>Sorry to see you go.</h2><p>We at Moyer Fine Jewelers are sad to see you go and hope you will return soon.</p>';
        }
    }   
}
//Set Language or non language variables
if ($set_lang_flag == 1) {
    $table0 = $tbl_arr[0];
    $table1 = $pg_info['tbl1'];
    $table2 = $tbl_arr[2];
    $pg_fields = $lang_pg_fields;
} else {
    $table0 = $pg_info['tbl0'];
    $table1 = $pg_info['tbl1'];
    $table2 = $pg_info['tbl2'];
    $pagefields = $pg_fields;
}

$pretty_url = 1;

$caption1 = $pg_fields['caption1'];
$caption2 = $pg_fields['caption2'];
$caption3 = $pg_fields['caption3'];
$caption4 = $pg_fields['caption4'];
$caption5 = $pg_fields['caption5'];
$caption6 = $pg_fields['caption6'];
$pg_name = $pg_fields['pg_name'];
$title = $pg_fields['title'];
$description = $desc = $pg_fields['description'];

$pg_item_det = query_pg_fields($guid, $table1);
$pg_item_det = ((bool) $pg_item_det ? $pg_item_det : array());

//Get Homepage link and data for homepage
$home_page_data = query_get_rows_super_advanced(SHOWCASE_HOME_PAGE, 'brnd_' . SHOWCASE_BRAND_NAME . (trim($set_lang) != '' ? "_" . $set_lang : ''), 'GUID');
$home_page_data = $home_page_data[0];
$home_page_link = URL . generate_uri($home_page_data, $set_lang);

if ($pretty_url)
    $option = 2;
else
    $option = 0;

//This pages URL
$this_url = URL . generate_uri($pg_fields, $set_lang);

//*****************************************************
//Start Codeing Here
//Do Header and Footer computations
//*****************************************************



//*****************************************************
//End Header/Footer Computations
//
//Start Page Based Code
// if ($pg_fields['pg_type'] == X){}
//*****************************************************




//*****************************************************
//Add Header View
//*****************************************************
?>   




<?php

//*****************************************************
//Add Page based on Page type
//*****************************************************


if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/moyer/pg_types/' . $pg_fields['pg_type'] . '.php'))
    include $_SERVER['DOCUMENT_ROOT'] . '/moyer/pg_types/' . $pg_fields['pg_type'] . '.php';
else
    require $_SERVER['DOCUMENT_ROOT'] . '/moyer/pg_types/no_pg_type.php';




//*****************************************************
//Add Footer View
//*****************************************************
?>



<!--Poppup Message & Unsubscribe box :: only works if fancybox is linked -->
<?php if ((isset($popup_msg) && !empty($popup_msg))) { ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $.fancybox('<?= $popup_msg ?>');
        });
    </script>
<?php } else if ((isset($unsubscribe) && $unsubscribe == true) || strpos(strtolower($_SERVER['REQUEST_URI']), '?unsubscribe') != false) { ?>
    <script type="text/javascript">
        $(function() {
            $("a.newsletter-link").fancybox().trigger('click');
            $('#unsubscribe-txt').focus();
        });
    </script>
<?php } ?>
<div style="display:none">
    <div id="newsletter">
        <?= (isset($newsletter_err_msg) && !empty($newsletter_err_msg) ? '<p><span class="error">'.$newsletter_err_msg.'</span></p>' : '' ) ?>
        <form action="<?= "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>" id="quit_news" method="post" name="quit_news">
            <h3>Unsubscribe from our Newsletter</h3>
            <input type="hidden" name="news[quit][show_popup]" value="1" />
            <input id="unsubscribe-txt" type="text" id="textField" name="news[quit][email]" class="textField" placeholder="Your Email" />
            <input class="button submit" type="submit" value="Unsubscribe from our newsletter" />
        </form>
    </div>
</div>
