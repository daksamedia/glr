<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Req includes
include_once '../../config/core.php';
include_once '../../config/database.php';
include_once '../../objects/Gallery.php';
include_once '../../objects/Product.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$gallery = new Gallery($db);
$product = new Product($db);

//Get Token
$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);
$user_id = $data->data->id;

if ($data) {
    $product->checkIfVendorExist($user_id);
}

//Get post data
$input = json_decode(file_get_contents("php://input"));
$data_base64 = $input->base64;
$file_name = $product->id;
$file_code = rand(10,10000);

if ($data_base64) {
    $datax = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data_base64));

	$filepath = $images_dir .'/gallery-'. $file_name .'-'. $file_code .'.jpg'; // or image.jpg

	// Save the image in a defined path
	file_put_contents($filepath,$datax);
	$fileurl = $server_url."images/uploads/gallery-". $file_name ."-". $file_code .".jpg";
	
	
	
	//set product values
    $gallery->business_id   = $product->id;
    $gallery->url           = $fileurl;
    $gallery->created       = date('Y-m-d H:i:s');

    //Create product
    if($gallery->create()){
        $payload = array(
            "id"          => $gallery->id,
            "url"         => $gallery->url,
            "business_id" => $gallery->business_id
        );
        echo json_encode(
            array("success" => true,"message" => "Image was added.","payload" => $payload)
        );
    }else{
        echo json_encode(
            array("success" => false,"message" => "Unable to add image.")
        );
    }
} else {
    echo json_encode(
        array("success" => false,"message" => "Unable to add image.")
    );
}


