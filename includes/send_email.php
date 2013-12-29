<?
session_start();

// verification
if (!empty($_POST['Submit'])) {
    if((isset($_POST['cap']) && !empty($_POST['cap'])) || !isset($_POST['cap'])){
        echo "Form submission failed! Please press 'back' in your browser to try again.";
        die();
    } else {
        $time_limit = 3; // seconds
        // do another test if there is a page_load_stamp present
        // if page_send_stamp is less than or equal to page_load_stamp + $time_limit, we assume it's a bot and die
        if(isset($_POST['vf_page_load_stamp'])){
            if(((int)$_POST['vf_page_send_stamp'] <= ((int)$_POST['vf_page_load_stamp'])+$time_limit) || empty($_POST['vf_page_send_stamp'])){
                echo "Form submission failed. Please press 'back' in your browser to try again.";
                die();
            }
        } else {
            echo "Form submission failed. Please press 'back' in your browser to try again.";
            die();
        }
        $subject = "EDGE PCs - Enquiry";
        $recipient = 'enquire@edgepcs.com.au';
        $from = $_POST['name'];
        $fromemail = $_POST['email'];
        $htmlmessage = "<style type='text/css'>body {font-family:Arial, Helvetica; font-size:12px; color:#333333;} b {color:#5c7893;}</style><html><body>";
        $textmessage = "";

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
    send_email($subject,$recipient,$from,$fromemail,$htmlmessage,$textmessage,$attachments,$debug=true,$cc,$bcc);
    $confirmmessage = "Thank you for your email";
    if(isset($_POST['send_confirmation']) && $_POST['send_confirmation'] == true) { 
        send_email($subject,$recipient,"EDGE PCs","noreply@".$_SERVER['HTTP_HOST'],$confirmmessage,$textmessage,$debug=true);
    }
    // remove safe_form cookie
    @setcookie("safe_form_".$_POST['form_name'],"", time()-3600,"/");
    header("Location: /");
    exit;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// send email
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function send_email($subject,$recipient,$from,$fromemail,$htmlmessage,$textmessage,$attachments,$debug=false,$cc="",$bcc=""){
    if($debug){
        error_reporting(E_ALL);
        /*
                  echo "<b>Subject:</b> $subject <br/><b>Recipient:</b> $recipient <br/><b>From:</b> $from <br/><b>From Email:</b> $fromemail <br/><b>HTML:</b> $htmlmessage <br/> TEXT:</b> $textmessage <br/><br/>";
                  echo "<pre>";
                  print_r($attachments);
                  echo "</pre>";
                  exit;
                  */
    }
    include_once('Mail.php');
    include_once('Mail/mime.php');


    $recipient = (!empty($cc))?$recipient.", ".$cc:$recipient;
    $to = $recipient;
    $recipient = (!empty($bcc))?$recipient.", ".$bcc:$recipient;


    $from = $from.' <'.$fromemail.'>';
    $hdrs = array(
        'To' => "$to",
        'From' => "$from",
        'Subject' => "$subject"
    );
    if(!empty($cc)){
        $hdrs['Cc'] = "$cc";
    }


    $crlf = "\n";
    $mime = new Mail_mime($crlf);
    if(!$mime->setTxtBody($textmessage)){
        echo "Failed making text body.<br/>";
    }
    if(!$mime->setHTMLBody($htmlmessage)){
        echo "Failed making HTML body.<br/>";
    }
    if(!empty($attachments)){
        foreach($attachments as $file){
            if(!$mime->addAttachment($file['tmp_name'], $file['type'], $file['name'], true, "base64")){
                echo "Failed adding attachment.<br/>";
                exit;
            }
        }
    }
    $body = $mime->get();
    $hdrs = $mime->headers($hdrs);
    $mail =& Mail::factory('sendmail');
    if(!$mail->send($recipient, $hdrs, $body)){
        echo "Failed sending mail.";
    } else {
        //echo "Mail was sent.";
    }
    //echo "GOT HERE!!";
}
?>