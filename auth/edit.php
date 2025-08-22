<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
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

// get posted data
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

// get posted data
$input = json_decode(file_get_contents("php://input"));
 
// if jwt is not empty
if($token){
	try {
	 
		// decode jwt

		
		// set user property values
		$user->firstname = $input->firstname;
		$user->lastname = $input->lastname;
		$user->email = $input->email;
		$user->phone = $input->phone;
		$user->bio = $input->bio;
		$user->address = $input->address;
		$user->id = $data->data->id;
		
		// create the product
		if($user->update()){
			// we need to re-generate jwt because user details might be different
			// $token = array(
			//    "iss" => $iss,
			//    "aud" => $aud,
			//    "iat" => $iat,
			//    "nbf" => $nbf,
			//    "data" => array(
			// 	   "id" => $user->id,
			// 	   "firstname" => $user->firstname,
			// 	   "lastname" => $user->lastname,
			// 	   "email" => $user->email
			//    )
			// );
			// $jwt = JWT::encode($token, $key);
			 
			// set response code
			http_response_code(200);
			 
			// response in json format
			echo json_encode(
				array(
					"success" => true,
					"message" => "User profile was updated."
				)
			);
		}else{
			// set response code
			http_response_code(401);
		 
			// show error message
			echo json_encode(array("message" => "Unable to update user."));
		}
	}
	catch (Exception $e){
	 
		// set response code
		http_response_code(401);
	 
		// show error message
		echo json_encode(array(
			"message" => "Access denied.",
			"error" => $e->getMessage()
		));
	}
}else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
 

?>
