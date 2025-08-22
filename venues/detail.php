<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: access");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Venue.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$venue = new Venue($db);

//Set ID of product to be edited
$venue->id = isset($_GET['id']) ? $_GET['id']: die;

//Read details of edited product
$venue->readOne();

//Create array
$venue_arr = array(
    "id" => $venue->id,
    "name" => $venue->name,
    "description" => $venue->description,
    "images" => $venue->images,
    "large_num" => $venue->large_num,
    "capacity" => $venue->capacity,
    "composition" => $venue->composition,
    "electricity" => $venue->electricity,
    "parking_lot" => $venue->parking_lot,
    "rooms_num" => $venue->rooms_num,
    "toilets_num" => $venue->toilets_num,
    "prayer_room" => $venue->prayer_room,
    "location" => $venue->location,
    "available_status" => $venue->available_status,
    "ratings" => $venue->ratings,
    "reviews" => $venue->reviews,
    "price" => $venue->price
);

echo json_encode(
	array("success" => true, "payload"=>$venue_arr)
);
