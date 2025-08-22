<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/Service.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare product object
$service = new Service($db);
 
// get id of product to be edited
$data = json_decode(file_get_contents("php://input"));
 
// set ID property of product to be edited
$service->id = $data->id;
 
// set product property values
$service->title = $data->title;
$service->price = $data->price;
$service->description = $data->description;
$service->image = $data->image;
$service->modified = date('Y-m-d H:i:s');
 
// update the product
if($service->update()){
 
    // set response code - 200 ok
    http_response_code(200);
 
    // tell the user
    echo json_encode(
		array("success" => true,"message" => "Service was updated.")
	);
}
 
// if unable to update the product, tell the user
else{
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(
		array("success" => false,"message" => "Service unable to update.")
	);
}
?>