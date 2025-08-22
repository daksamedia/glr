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
include_once '../objects/Product.php';
include_once '../objects/Statistic.php';
include_once '../objects/User.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;


//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$product = new Product($db);
$statistic = new Statistic($db);
$user = new User($db);

//Get post data
$input = json_decode(file_get_contents("php://input"));
$headers=getallheaders();
$authorized = $headers['Authorization'];

//Decode JWT
$token="";
$data="";
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

$jwt = $token ? $token : $input->user_id;

if($jwt) {
	$user_id = $data && $data->data->id? $data->data->id:$input->user_id;
	//set product values
	$product->name         	= $input->name;
	$product->location   	= $input->location;
	$product->location_data = $input->location_data;
	$product->category_id   = $input->category_id;
	$product->user_id		= $user_id;
	$product->created       = date('Y-m-d H:i:s');

	if (isset($input->bio)) {
		$product->bio = $input->bio;
	}
	if (isset($input->address)) {
		$product->address = $input->address;
	}
	if (isset($input->price)) {
		$product->price = $input->price;
	}
	
	//Create product
	if($product->checkIfVendorExist($user_id)) {
		echo json_encode(
			array("success" => false,"message" => "You have your own business already.")
		);
	} else {
		if ($product->name && $product->location && $product->category_id) {
			if($product->create()){
				$product->checkIfVendorExist($user_id);
				$vendor_id = $product->id;

				// add row in Statistic
				$statistic->business_id = $product->id;
				$statistic->views = 0;
				$statistic->likes = 0;
				$statistic->orders = 0;
				$statistic->created = date('Y-m-d H:i:s');
				$statistic->create();
				
				echo json_encode(
					array("success" => true,"message" => "Vendor was created.", "data"=>$vendor_id)
				);
			}else{
				echo json_encode(
					array("success" => false,"message" => "Unable to create vendor.")
				);
			}
		} else {
			$errors=array();
			if (!$product->name) {
				array_push($errors, "Name");
			}
			if (!$product->location) {
				array_push($errors, "Location");
			}
			if (!$product->category_id) {
				array_push($errors, "Category");
			}
			
			
			echo json_encode(
				array("success" => false,"message" => "Unable to create vendor. Please complete these following data", "errors" => $errors)
			);
		}
	}
} else {
	echo json_encode(
		array("success" => false,"message" => "Please do login / registration first.")
	);
}


