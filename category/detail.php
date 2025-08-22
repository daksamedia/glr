<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Category.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$category = new Category($db);

//Set ID of product to be edited
$category->id = isset($_GET['id']) ? $_GET['id']: die;

//Read details of edited product
$category->readOne();


//Create array
$category_arr = array(
    "id" => $category->id,
    "name" => $category->name,
    "icon" => $category->icon,
    "description" => $category->description,
);

echo json_encode(
	array("success" => true, "payload"=>$category_arr)
);
