<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With,access-control-allow-origin");

//Req includes
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/Rating.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$service = new Rating($db);

//Get me user data
$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
$data = JWT::decode($token, $key, array('HS256'));

//Get post data
$input = json_decode(file_get_contents("php://input"));

//set product values
$service->vendor_id   	= $input->vendor_id;
$service->user_id       = $data->data->id;
$service->comments   	= $input->comments;
$service->rating        = $input->rating;
$service->type        	= $input->type;
$service->created       = date('Y-m-d H:i:s');

//Create product
if($service->create()){
    echo json_encode(
		array("success" => true,"message" => "Rating was added.")
	);
}else{
    echo json_encode(
		array("success" => false,"message" => "Unable to add rating.")
	);
}