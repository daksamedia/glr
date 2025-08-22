<?php

//Required headers

header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/User.php';
include_once '../shared/Utilities.php';

//New instances
$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$utils = new Utilities();



// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$user->email = $data->email;
 
if($user->emailExists()){

	// update access code for user
	$access_code=$utils->getToken();

	$user->access_code=$access_code;
	if($user->updateAccessCode()){
		// content email
		$link = "{$home_url}account/reset?access_code={$access_code}";
		$content = '
		<html>
			<head>
			<title></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
			<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,700,900&display=swap" rel="stylesheet">
			</head>
			<body style="width:100%; text-align:center; background:#eaeaea;margin:0 auto;">


			<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0px auto; padding:0px; background-color:#422a66;">
				<tr>
					<td style="text-align: center; padding: 22px 0;">
						<img src="https://daksamedia.id/gelaro/images/email/logo_w.png" width="120" />
					</td>
				</tr>
				<tr>
				<td style="padding:20px 20px 15px;">
				
					<table width="550" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0px auto; padding:0px; background-color:#FFF;border-radius:30px;">
						<tr>
							<td style="padding:14px 14px;font-size:14px;text-align:left;font-family: `Source Sans Pro`, sans-serif;color:#333;line-height:150%;">
							
								
								<table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0px auto; padding:0px; background-color:#FFF;">
									
									<tr>
										<td style="padding:0; line-height:0;text-align:center;">
											<img src="https://daksamedia.id/gelaro/images/email/EMAIL_FORGOT_PASS.png" border="0" align="center" width="50%">
										</td>
									</tr>
									<tr>
										<td style="padding:30px 0; line-height:auto; text-align:center;">
											<h1 style="font-weight:900; font-size:22px; line-height:24px;">ATUR ULANG<br />PASSWORD KAMU</h1>
											<p>Klik tombol di bawah ini untuk membuat password baru</p>
										</td>
									</tr>
									<tr>
										<td style="padding:0; line-height:0;text-align:center;">
											<a href="'. $link .'" target="_blank">
												<img src="https://daksamedia.id/gelaro/images/email/BTN_RESET.png" border="0" align="center" width="45%">
											</a>
										</td>
									</tr>
									
								</table>
								
							</td>
						</tr>
					</table>
				
				</td>
				</tr>
				
				<tr>
					<td style="padding:14px 14px;font-size:14px;text-align:left;font-family:arial, Verdana, Geneva, sans-serif;color:#333;line-height:150%;">		
						
						<table style="margin:0 auto;text-align:center; width:100%;">
							<tr>
								<td width="180" style="text-align:left;">
									<a href="https://gelaro.id">
										<img src="https://daksamedia.id/gelaro/images/email/logo_w.png" width="100" style="margin-left:20px; margin-top:-6px;" />
									</a>
								</td>
								<td width="420" style="padding:0 7px 0 0;line-height:0; text-align:right;">
									<a href="https://instagram.com/gelaro.id"><img border="0" src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Instagram_logo_2016.svg/768px-Instagram_logo_2016.svg.png" width="25" height="25" alt="See our photos on Instagram"  style="margin-top: 20px !important; margin-right:2px !important;"></a>
									<a href="https://www.facebook.com/gelaro.id/"><img border="0" src="https://cdn.iconscout.com/icon/free/png-256/facebook-logo-2019-1597680-1350125.png" width="25" height="25" alt="Follow us on Facebook" style="margin-right:2px !important;"></a>
									<a href="mailto:gelaro.app@gmail.com"><img border="0" src="https://cdn4.iconfinder.com/data/icons/social-media-logos-6/512/112-gmail_email_mail-512.png" width="25" height="25" alt="Subscribe our Blog" style="margin-right:0 !important;"></a>
								</td>
							
							</tr>
						</table>
						
					</td>
				</tr>
				
				<tr>
					
				</tr>
			</table>


			</body>
		</html>
		';
		
		// send reset link
		$body="Hi ". $user->firstname .". Kamu akan mengatur ulang password-mu.<br /><br />";
		$body.=$content;
		$subject="Reset Password Request";
		$send_to_email=$data->email;

		if($utils->sendEmailViaPhpMail($send_to_email, $subject, $body)){
			echo json_encode(array("success" => true,"message" => "Reset link already sent to your email."));
		}else{
			echo json_encode(array("success" => false,"message" => "Unable to send the link. Please check your email"));
		}

		// message if unable to send email for password reset link
		
	}else{ 
		echo json_encode(array("success" => false,"message" => "ERROR: Unable to update access code.")); 
	}

}else{
	echo json_encode(array("success" => false,"message" => "Email is not registered yet.")); 
}

?>
