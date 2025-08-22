<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 

//Include db and object

include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/Product.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;

//New instances

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);
$user_id = $data->data->id;

if ($data) {
    $product->checkIfVendorExist($user_id);
}
// get posted data
$input = json_decode(file_get_contents("php://input"));
$data_base64 = $input->base64;

$file_name = $input->id;
// $home_url = 'C:/wamp64/www/gelaro/images/uploads'; 


if($data_base64){

	$datax = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data_base64));

	$filepath = $images_dir .'/cover-'. $file_name .'.jpg'; // or image.jpg

	// Save the image in a defined path
	file_put_contents($filepath,$datax);
	$fileurl = $server_url."images/uploads/cover-". $file_name .".jpg";
	
	
	$product->cover = $fileurl;
	
	// create the product
	if($product->update_cover()){
		
		// set response code
		http_response_code(200);
		 
		// response in json format
		echo json_encode(
			array(
				"success" => true,
				"message" => "Cover vendor was updated."
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

?>