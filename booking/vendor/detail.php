<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: access");

//Include db and object

include_once '../../config/database.php';
include_once '../../objects/Booking.php';
include_once '../../objects/User.php';
include_once '../../objects/Product.php';
include_once '../../objects/Service.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$product = new Product($db);
$user = new User($db);
$service = new Service($db);

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
$product->checkIfVendorExist($data->data->id);

$booking->business_id = $product->id;
$booking->id = isset($_GET['id']) ? $_GET['id']: die;
//Read details of edited product
$booking->readOne();
$user_id = $booking->user_id;

if($user_id!=="0") {
    $user->id = $user_id;
    $user->readOne();

    $user_arr = array(
        "id" => $user->id,
        "email" => $user->email,
        "name" => $user->firstname.' '.$user->lastname,
        "phone" => $user->phone,
        "address" => $user->address
    );

    $user_json = $user_arr;
}else{
    $user_json = json_decode($booking->user_data);
}

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
    "user_data" => $user_json,
    "business_id" => $booking->business_id,
    "service_id" => $booking->service_id,
    "service" => $service_arr,
    "booking_time" => json_decode($booking->booking_time),
    "status" => $booking->status,
    "notes" => $booking->notes,
    "expired_date" => $booking->expired_date,
    "created" => $booking->created,
    "modified" => $booking->modified,
);

echo json_encode(
	array("success" => true, "payload"=>$booking_arr)
);
