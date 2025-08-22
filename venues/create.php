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
$venue->created       = date('Y-m-d H:i:s');

//Create product
if($venue->create()){
    echo json_encode(
		array("success" => true,"message" => "Venue was created.")
	);
}else{
    echo json_encode(
		array("success" => false,"message" => "Unable to create venue.")
	);
}


