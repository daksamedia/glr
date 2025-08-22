<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: access");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Product.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

//Set ID of product to be edited
$product->user_id = $data->data->id;

//Read details of edited product
$product->readMy();

//Create array
$product_arr = array(
    "id" => $product->id,
    "user_id" => $product->user_id,
    "name" => $product->name,
    "location" => $product->location,
    "location_data" => $product->location_data,
    "ratings" => $product->ratings,
    "reviews" => $product->reviews,
    "price" => $product->price,
    "category_id" => $product->category_id,
    "category_name" => $product->category_name,
    "bio" => $product->bio,
    "cover" => $product->cover
);

echo json_encode(
	array("success" => true, "payload"=>$product_arr)
);
