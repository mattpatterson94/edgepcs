<?php
session_start();

// verification
if (!empty($_POST['Submit'])) {
    if((isset($_POST['cap']) && !empty($_POST['cap'])) || !isset($_POST['cap'])){
        echo "Form submission failed! Please press 'back' in your browser to try again. 1";
        die();
    } else {
        $time_limit = 3; // seconds
        // do another test if there is a page_load_stamp present
        // if page_send_stamp is less than or equal to page_load_stamp + $time_limit, we assume it's a bot and die
        if(isset($_POST['vf_page_load_stamp'])){
            if(((int)$_POST['vf_page_send_stamp'] <= ((int)$_POST['vf_page_load_stamp'])+$time_limit) || empty($_POST['vf_page_send_stamp'])){
                echo "Form submission failed. Please press 'back' in your browser to try again. 2";
                die();
            }
        } else {
            echo "Form submission failed. Please press 'back' in your browser to try again. 3";
            die();
        }
        $subject = "EDGE PCs - Quick Enquiry";
        $recipient = "enquire@edgepcs.com.au";
        $cc = $_POST['cc'];
        $bcc = $_POST['bcc'];
        $from = $_POST['name'];
        $fromemail = $_POST['email'];
        $htmlmessage = "<style type='text/css'>body {font-family:Arial, Helvetica; font-size:12px; color:#333333;} b {color:#5c7893;}</style><html><body>";
        $textmessage = "";

        // name 
        if (isset($_POST['name'])) {
            $htmlmessage .= "Name: " . $_POST['name'];
        } else {
            $htmlmessage .= "No Name";
        }

        $htmlmessage .= "<br><br>";

        // email 
        if (isset($_POST['email'])) {
            $htmlmessage .= "Email: " . $_POST['email'];
        } else {
            $htmlmessage .= "No Email";
        }        

        $htmlmessage .= "<br><br>";
       
        // type of enquiry
        if (isset($_POST['type'])) {
            $htmlmessage .= "Enquiry about: " . $_POST['type'];
        } else {
            $htmlmessage .= "Error";
        }

        $htmlmessage .= "<br><br>";

        // enquiry
        if (isset($_POST['message'])) {
            $htmlmessage .= wordwrap($_POST['message'], 70, "\r\n");
        } else {
            $htmlmessage .= "No message was left";
        }

        $htmlmessage .= "</body></html>";
    }
    $attachments = $_FILES;
    send_email($subject,$recipient,$from,$fromemail,$htmlmessage,$textmessage,$attachments,$debug=false,$cc,$bcc);
    $confirmmessage = "Thank you for your email";
    if(isset($_POST['send_confirmation']) && $_POST['send_confirmation'] == true) { 
        send_email($subject,$recipient,"EDGE PCs","noreply@".$_SERVER['HTTP_HOST'],$confirmmessage,$textmessage,$debug=false);
    }
    // remove safe_form cookie
    @setcookie("safe_form_".$_POST['form_name'],"", time()-3600,"/");
    header("Location: /index.html");
    exit;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// send email
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function send_email($subject,$recipient,$from,$fromemail,$htmlmessage,$textmessage,$attachments,$debug=false,$cc="",$bcc=""){
    //SMTP needs accurate times, and the PHP time zone MUST be set
    //This should be done in your php.ini, but this is how to do it if you don't have access to that
    date_default_timezone_set('Australia/Brisbane');
    require 'PHPMailer/PHPMailerAutoload.php';


    //Create a new PHPMailer instance
    $mail = new PHPMailer();
    // $mail->addReplyTo('replyto@example.com', 'First Last');
    //Set who the message is to be sent to
    $mail->addAddress('enquire@edgepcs.com.au', 'Edge PCs');
    //Set the subject line
    $mail->Subject = $subject;
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    // $mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
    $mail->msgHTML($htmlmessage);
    //Replace the plain text body with one created manually
    $mail->AltBody = 'This is a plain-text message body';
    //Attach an image file
    // $mail->addAttachment('images/phpmailer_mini.png');

    //send the message, check for errors
    if (!$mail->send()) {
        error_log("Error " . $mail->ErrorInfo);
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
        error_log("Sent");
        echo "Message sent!";
    }
}
?>