<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: access");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Service.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

//Set ID of product to be edited
$service->id = isset($_GET['id']) ? $_GET['id']: die;

//Read details of edited product
$service->readOne();

//Create array
$service_arr = array(
    "id" => $service->id,
    "title" => $service->title,
    "description" => $service->description,
    "price" => $service->price,
    "image" => $service->image
);

echo json_encode(
	array("success" => true, "payload"=>$service_arr)
);
