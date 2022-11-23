<?php

    // $postData=json_decode(file_get_contents('php://input'));
    // $mail = json_decode($_POST['email'],true); 
    
        require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
        $verifyNumber = rand(1000,10000);
        $message = '이메일 인증 확인 칸의 다음 값을 넣어주세요 '.$verifyNumber;
        $to = $_POST['email'];
        $arr=array();

        //아이디 중복체크 먼저하고  중복하다면 중복하다고 알려주고 api 종료
        // $sql="SELECT Member_id FROM Market_member";
        $sql="SELECT * From Market_member where Member_id='$to'";
        $selectReuslt=mysqli_query($db_connect,$sql);
        if(mysqli_num_rows($selectReuslt)>=1){

            $arr['isSuccess']="아이디 중복";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }


        include "PHPMailer.php";
        include "SMTP.php";
        //Create a new PHPMailer instance
        $mail = new PHPMailer();

        //Tell PHPMailer to use SMTP
        $mail->isSMTP();

        //Enable SMTP debugging
        //SMTP::DEBUG_OFF = off (for production use)
        //SMTP::DEBUG_CLIENT = client messages
        //SMTP::DEBUG_SERVER = client and server messages
        $mail->SMTPDebug = SMTP::DEBUG_OFF;

        //Set the hostname of the mail server
        $mail->Host = 'smtp.naver.com';
        //Use `$mail->Host = gethostbyname('smtp.gmail.com');`
        //if your network does not support SMTP over IPv6,
        //though this may cause issues with TLS

        //Set the SMTP port number:
        // - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
        // - 587 for SMTP+STARTTLS
        $mail->Port = 465;

        //Set the encryption mechanism to use:
        // - SMTPS (implicit TLS on port 465) or
        // - STARTTLS (explicit TLS on port 587)
        $mail->SMTPSecure = "ssl";

        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;

        $mail->CharSet='UTF-8';
        //Username to use for SMTP authentication - use full email address for gmail
        $mail->Username = 'winsomed96';

        //Password to use for SMTP authentication
        $mail->Password = 'R5Z4J2N1BKT3';

        //Set who the message is to be sent from
        //Note that with gmail you can only use your account address (same as `Username`)
        //or predefined aliases that you have configured within your account.
        //Do not use user-submitted addresses in here
        $mail->setFrom('winsomed96@naver.com', 'UsedElectronicsMarket');

        //Set an alternative reply-to address
        //This is a good place to put user-submitted addresses
        $mail->addReplyTo('winsomed96@naver.com', 'UsedElectronicsMarket');

        //Set who the message is to be sent to
        $mail->addAddress($to,'신규회원');

        //Set the subject line
        $mail->Subject = 'UsedElectronicsMarket 이메일 인증';

        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body

        // $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        $mail->msgHTML($message);
        //Replace the plain text body with one created manually
        // $mail->AltBody = '메일 평문입니다.';

        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');


            // if($mailResult){
            //     echo $verifyNumber;
            // }
            // else{
            //     echo 'false';
            // }
        //send the message, check for errors
        if (!$mail->send()) {
            $arr['isSuccess']="이메일 전송 실패";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            // echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $arr['verifyNumber']=$verifyNumber;
            $arr['isSuccess']="이메일 전송 완료";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }

?>