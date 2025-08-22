<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, access-control-allow-origin");

//Include db and object

include_once '../../config/database.php';
include_once '../../objects/Booking.php';
include_once '../../objects/Product.php';
include_once '../../objects/User.php';
include_once '../../objects/Service.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$product = new Product($db);
$user = new User($db);
$service = new Service($db);

// read authorization data
$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

//Set ID of product to be edited
$booking->user_id = $data->data->id;
$booking->id = isset($_GET['id']) ? $_GET['id']: die;
//Read details of edited product
$booking->readOne();

$product->id = $booking->business_id;
$product->readOne();

$business_arr = array(
    "id" => $product->id,
    "name" => $product->name,
    "cover" => $product->cover,
    "category_name" => $product->category_name,
    "location" => $product->location
);

$service_arr="";
if(isset($booking->service_id)) {
    $service->id = $booking->service_id;
    $service->readOne();
    $service_arr = array(
        "id" => $service->id,
        "title" => $service->title,
        "description" => $service->description,
        "price" => $service->price,
        "image" => $service->image
    );
}

//Create array
$booking_arr = array(
    "id" => $booking->id,
    "business_id" => $booking->business_id,
    "business_data" => $business_arr,
    "service_id" => $booking->service_id,
    "service" => $service_arr,
    "booking_time" => json_decode($booking->booking_time),
    "status" => $booking->status,
    "notes" => $booking->notes,
    "created" => $booking->created,
    "modified" => $booking->modified,
);

echo json_encode(
	array("success" => true, "payload"=>$booking_arr)
);
