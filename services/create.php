<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: access-control-allow-origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Req includes
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/Service.php';
include_once '../objects/Product.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$service = new Service($db);
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
//Get post data
$input = json_decode(file_get_contents("php://input"));

if ($product->checkIfVendorExist($user_id)) {
	
	if ($input->base64) {
		$file_name = $product->id;
		$file_code = rand(10,10000);
		
		$data_base64 = $input->base64;

		$datax = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data_base64));

		$filepath = $images_dir .'/service-'. $file_name .'-'. $file_code .'.jpg'; // or image.jpg

		// Save the image in a defined path
		file_put_contents($filepath,$datax);
		$fileurl = $server_url."images/uploads/service-". $file_name ."-". $file_code .".jpg";
	} else {
		$fileurl = $input->image;
	}
	
	
	//set product values
	$service->title         = $input->title;
	$service->description   = $input->description;
	$service->price         = $input->price;
	$service->image 		= $fileurl;
	$service->vendor_id   	= $product->id;
	$service->created       = date('Y-m-d H:i:s');

	//Create product
	if($service->create()){
		echo json_encode(
			array("success" => true,"message" => "Service was created.")
		);
	}else{
		echo json_encode(
			array("success" => false,"message" => "Unable to create service.")
		);
	}
} else {
	echo json_encode(
		array("success" => false,"message" => "Business does not exist. Please create it first.")
	);	
}