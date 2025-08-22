<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: access");

//Include db and object

include_once '../config/database.php';
include_once '../objects/User.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

//Set ID of product to be edited
$user->email = isset($_GET['email']) ? $_GET['email']: die;

//Read details of edited product
$user->readOne();

//Create array
$user_arr = array(
    "id" => $user->id,
    "email" => $user->email,
    "firstname" => $user->firstname,
    "lastname" => $user->lastname,
    "avatar" => $user->avatar
);

echo json_encode(
	array("success" => true, "payload"=>$user_arr)
);
