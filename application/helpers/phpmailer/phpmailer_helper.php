<?php
require_once('PHPMailerAutoload.php');

function sendphpMailer($tieude,$noidung){
	$CI= & get_instance();

	$mail = new PHPMailer;

	//$mail->SMTPDebug = 3;                               // Enable verbose debug output

	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $CI->Dindex->getSettings('MAIL_USER');
	$mail->Password = $CI->Dindex->getSettings('MAIL_PASS');

	$mail->SMTPSecure = 'tls';                          // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 587;                                    // TCP port to connect to

	$mail->setFrom($CI->Dindex->getSettings('MAIL_USER'), $CI->Dindex->getSettings('MAIL_NAME'));
	$mail->addAddress($CI->Dindex->getSettings('MAIL_NHAN'),'');
	// $mail->addAddress('thanhminh182@gmail.com','');
	$mail->CharSet = 'UTF-8';
	// $mail->addReplyTo($email, $name);
	/*$mail->addCC('cc@example.com');
	$mail->addBCC('bcc@example.com');*/

	/*$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
	$mail->addAttachment('/tmp/image.jpg', 'new.jpg'); */   // Optional name
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $tieude;
	$mail->Body    = $noidung;
	$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	$mail->send();

	/*if(!$mail->send()) {
		echo 'Không thể gửi thư.';
		echo 'Lỗi: ' . $mail->ErrorInfo;
	} else {
		echo 'Thư đã được gửi';
	}*/
}
?>