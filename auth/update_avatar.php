<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS");
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, access-control-allow-origin");
	header("HTTP/1.1 200 OK");
	die();
}
 

//Include db and object

include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/User.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;

//New instances

$database = new Database();
$db = $database->getConnection();
$headers=getallheaders();

$user = new User($db);
$authorized = $headers['Authorization'];

// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

// get posted data
$input = json_decode(file_get_contents("php://input"));
$data_base64 = $input->base64;

$file_name = $data->data->id;
// $home_url = 'C:/wamp64/www/gelaro/images/uploads'; 

if($token){

	try {
		if($data_base64){

			$datax = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data_base64));

			$filepath = $avatar_dir .'/avatar-'. $file_name .'.jpg'; // or image.jpg

			// Save the image in a defined path
			file_put_contents($filepath,$datax);
			$fileurl = $server_url."images/avatars/avatar-". $file_name .".jpg";
			
			
			$user->avatar   = $fileurl;
			$user->id   	= $data->data->id;
			
			// create the product
			if($user->update_avatar()){
				
				// set response code
				http_response_code(200);
				
				// response in json format
				echo json_encode(
					array(
						"success" => true,
						"message" => "Avatar was updated."
					)
				);
			}else{
				// set response code
				http_response_code(401);
			
				// show error message
				echo json_encode(array("success" => false,"message" => "Unable to update avatar."));
			}
			
		}else{
			echo json_encode(array(
				"success" => false,
				"message" => "No image has been attached."
			));
		}
	}catch (Exception $e){
	 
		// set response code
		http_response_code(401);
	 
		// show error message
		echo json_encode(array(
			"message" => "Access denied.",
			"error" => $e->getMessage()
		));
	}
} else {
	// set response code
	http_response_code(401);
	 
	// show error message
	echo json_encode(array(
		"message" => "Access denied.",
		"success" => falqse
	));
}

    
	

 




?>