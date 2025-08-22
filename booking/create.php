<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Req includes
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/Booking.php';
include_once '../objects/Product.php';
include_once '../objects/User.php';
include_once '../shared/Utilities.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;


//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$booking = new Booking($db);
$utils = new Utilities();
$product = new Product($db);
$user = new User($db);

//Get post data
$input = json_decode(file_get_contents("php://input"));
$headers=getallheaders();

if (isset($headers) && isset($headers['Authorization'])) {
    $authorized = $headers['Authorization'];

    //Decode JWT
    $token="";
    $data="";
    list($bearer, $token) = explode(" ", $authorized);
    list($header, $payload, $signature) = explode(".", $token);
    $decoded=base64_decode($payload);
    $data = json_decode($decoded);
}

//set product values
$booking->business_id   = $input->business_id;
$booking->booking_time  = $input->booking_time;
$booking->status        = "PENDING";
$booking->created       = date('Y-m-d H:i:s');
$booking->expired_date  = date('Y-m-d H:i:s', strtotime(date() . ' +2 day'));


// Get User of Business Owner
$product->id = $input->business_id;
$product->checkVendorOwner();

$user->id = $product->user_id;
$user->readOne();

if (isset($input->service_id)) {
    $booking->service_id = $input->service_id;
}
if (isset($data->data->id)) {
    $booking->user_id   = $data->data->id;
}
if (isset($input->user_data)) {
    $booking->user_data = $input->user_data;
}

//Create booking
if ($booking->business_id && $booking->booking_time) {
    if ($booking->user_data || $booking->user_id) {
        if($booking->create()){
            // run startTime()
            $booking->startTime();

            // send confimation email
			$link_view = "{$home_url}account/admin/bookings/{$booking->id}";
			$link_accept = "{$home_url}account/admin/bookings/{$booking->id}/accept";
            // $raw_booking = str_replace("'", '"', $input->booking_time);               ;
            $data_booking = json_decode($input->booking_time);
            if (isset($data_booking->location)) {
                $location = $data_booking->location;
            } else {
                $location = "(Tidak tersedia)";
            }
            
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
                        
                            <table width="550" border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0px auto; padding:0px; background-color:#f5f5f5;border-radius:30px;">
                                <tr>
                                    <td style="padding:14px 14px;font-size:14px;text-align:left;font-family: `Source Sans Pro`, sans-serif;color:#333;line-height:150%;">
                                    
                                        
                                        <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0px auto; padding:0px; background-color:#f5f5f5;">
                                            
                                            <tr>
                                                <td style="padding:20px 0 0; line-height:0;text-align:center;">
                                                    <img src="https://daksamedia.id/gelaro/images/email/EMAIL_NEW_TRANSACTION.png" border="0" align="center" width="30%">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:30px 0; line-height:auto; text-align:center;">
                                                    <h1 style="font-weight:900; font-size:22px; line-height:24px; ">
                                                        BOOKING BARU
                                                    </h1>
                                                    <p>Hore! Kamu mendapatkan booking baru dari seorang pelanggan.</p>
                                                    <table style="margin-top: 30px; font-weight: bold; text-align: left; padding: 15px; background: #fffad6; border-radius: 10px; width: 100%; line-height: 2.3em;">
                                                        <tr>
                                                            <td>Tanggal</td>
                                                            <td>:</td>
                                                            <td>'. $data_booking->date .'</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Waktu</td>
                                                            <td>:</td>
                                                            <td>'. $data_booking->time .'</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Lokasi</td>
                                                            <td>:</td>
                                                            <td>'. $location .'</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:20px 0 40px; line-height:0;text-align:center;">
                                                    <a href="'. $link_accept .'" target="_blank" style="text-decoration: none; color:#3a3a3a; margin: 0 auto;">
                                                        <div type="button" style="margin:10px auto; width: 60%; padding: 28px 30px; background: rgb(255, 204, 0); border:none; border-radius: 40px; font-weight: bold; color:#333 !important;">TERIMA BOOKING</div>
                                                    </a>
                                                    <a href="'. $link_view .'" target="_blank" style="text-decoration: none; color:white;  margin: 0 auto;">
                                                        <div type="button" style="margin:10px auto; width: 60%; padding: 28px 30px; background: rgb(49, 4, 122); border:none; border-radius: 40px; font-weight: bold;">LIHAT BOOKING</div>
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
			$send_to_email=$user->email;
			$body="Hi {$send_to_email}. Kamu mendapatkan booking baru dari pelangganmu. <br /><br />";
			$body.=$content;
			$subject="Booking Confirmation";
 
			if($utils->sendEmailViaPhpMail($send_to_email, $subject, $body)){
				$user_detail = $user->id;
				echo json_encode(
                    array("success" => true,"message" => "Booking was created.")
                );
			}else{
				echo json_encode(
                    array("success" => true,"message" => "Booking was created but unable to send the email.")
                );
			}
        }else{
            echo json_encode(
                array("success" => false,"message" => "Unable to create booking.")
            );
        }
    } else {
        echo json_encode(
            array("success" => false,"message" => "User ID or User Data is required.")
        );
    }
} else {
    echo json_encode(
        array("success" => false,"message" => "Unable to create booking. Business ID and booking time are required.")
    );
}