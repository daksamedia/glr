<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: authorization, access-control-allow-origin");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Product.php';
include_once '../objects/User.php';
use \Firebase\JWT\JWT;

//New instances

$database = new Database();
$db = $database->getConnection();
$headers=getallheaders();

$user = new User($db);
$product = new Product($db);
$authorized = $headers['Authorization'];

// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

// get user
$user->id = $data->data->id;
$user->readOne();

// get vendor
$product->checkIfVendorExist($data->data->id);

//Create array
$user_arr = array(
    "id" => $user->id,
    "email" => $user->email,
    "firstname" => $user->firstname,
    "lastname" => $user->lastname,
    "avatar" => $user->avatar,
    "phone" => $user->phone,
    "bio" => $user->bio,
    "address" => $user->address,
    "business" => $product->id,
);

if ($token) {
    echo json_encode(
        array("success" => true, "payload"=>$user_arr, "message"=>"Got Data Succesfully")
    );
} else {
    echo json_encode(
        array("success" => false, "message"=>"This account does not belongs to you. Please login.")
    );
}

