<?php
//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Req includes
include_once '../config/database.php';
include_once '../objects/Venue.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$venue = new Venue($db);

//Get post data
$data = json_decode(file_get_contents("php://input"));

//set Id of product to be deleted
$venue->id = $data->id;


//delete product
if($venue->delete()){
    echo json_encode(
		array("success" => true,"message" => "Venue was deleted.")
	);
}else{
	echo json_encode(
		array("success" => false,"message" => "Unable to delete Venue.")
	);
}
