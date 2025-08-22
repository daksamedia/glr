<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// include database and object files
include_once '../config/database.php';
include_once '../objects/Venue.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare product object
$venue = new Venue($db);
 
// get id of product to be edited
$data = json_decode(file_get_contents("php://input"));
 
// set ID property of product to be edited
$venue->id = $data->id;
 
//set product values
$venue->name          = $data->name;
$venue->price         = $data->price;
$venue->description   	  = $data->description;
$venue->images   	  = $data->images;
$venue->large_num     = $data->large_num;
$venue->capacity   	  = $data->capacity;
$venue->composition   = $data->composition;
$venue->electricity   = $data->electricity;
$venue->parking_lot   = $data->parking_lot;
$venue->rooms_num   = $data->rooms_num;
$venue->toilets_num   = $data->toilets_num;
$venue->prayer_room   = $data->prayer_room;
$venue->available_status = $data->available_status;
$venue->location   	= $data->location;
$venue->reviews   	= $data->reviews;
$venue->ratings		= $data->ratings;
$venue->modified       = date('Y-m-d H:i:s');
 
// update the product
if($venue->update()){
 
    // set response code - 200 ok
    http_response_code(200);
 
    // tell the user
    echo json_encode(
		array("success" => true,"message" => "Venue was updated.")
	);
}
 
// if unable to update the product, tell the user
else{
 
    // set response code - 503 service unavailable
    http_response_code(503);
 
    // tell the user
    echo json_encode(
		array("success" => false,"message" => "Unable to update venue.")
	);
}
?>