<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin, origin");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, access-control-allow-origin");
	header("HTTP/1.1 200 OK");
	die();
}


//Include db and object

include_once '../config/database.php';
include_once '../objects/User.php';
include_once '../objects/Product.php';
include_once '../objects/Statistic.php';

include_once '../config/core.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;

//New instances

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$product = new Product($db);
$statistic = new Statistic($db);
$input = json_decode(file_get_contents("php://input"));

// encode token
$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
$data = JWT::decode($token, $key, array('HS256'));


// get user
$user->id = $data->data->id;
$like = isset($input->id)? $input->id : "";
$user->readLikes();
$likes = $user->likes;
$like_arr = explode(',',$likes);
foreach (array_keys($like_arr, $like) as $key) {
    unset($like_arr[$key]);
}

// update stats
$statistic->business_id = isset($input->id)? $input->id : "";
$statistic->read();

$statistic->likes = ($statistic->likes - 1);
$statistic->modified = date('Y-m-d H:i:s');
$statistic->updateLike();

if($data && $like){
	try {
        if ($like_arr) {
            $user->likes = implode(',', $like_arr);
        }
		
		// create the product
		if($user->updateLikes()){
			// set response code
			http_response_code(200);
			 
			// response in json format
			echo json_encode(
				array(
                    "success" => true,
					"message" => "User was updated."
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