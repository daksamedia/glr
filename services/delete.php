<?php
//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: access-control-allow-origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Req includes
include_once '../config/database.php';
include_once '../objects/Service.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$service = new Service($db);

//Get post data
$data = json_decode(file_get_contents("php://input"));

//set Id of product to be deleted
$service->id = $data->id;


//delete product
if($service->delete()){
    echo json_encode(
		array("success" => true,"message" => "Service was removed.")
	);
}else{
    echo json_encode(
		array("success" => false,"message" => "Unable to remove service.")
	);
}
