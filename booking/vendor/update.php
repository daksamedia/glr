<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: access, authorization, content-type, access-control-allow-origin");

$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Methods: PUT, POST, GET, OPTIONS");
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, access-control-allow-origin");
	header("HTTP/1.1 200 OK");
	die();
}
 
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

$input = json_decode(file_get_contents("php://input"));
$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

$product->user_id = $data->data->id;
$product->checkIfVendorExist($data->data->id);

$booking->business_id = $product->id;
//Set ID of product to be edited
$booking->id = $input->id;
//Read details of edited product
$booking->readOne();

if(isset($input->status) && isset($input->id) && isset($booking->business_id)) {
    if ($input->status=='CANCELLED' || $input->status=='CANCEL') {
        if ($input->notes!=='') {
            $booking->notes = $input->notes;
        } else {
            echo json_encode(
                array("success" => false, "message"=>"You need a reason for cancellation.")
            );
        }
    }

    $booking->status = $input->status;
    
    if($booking->update()){
        echo json_encode(
            array("success" => true, "message"=>"Booking has been updated.")
        );
    } else {
        echo json_encode(
            array("success" => false, "message"=>"Unable to update booking. error")
        );
    }
} else {
    echo json_encode(
        array("success" => false, "message"=>"Unable to update booking.", "payload"=>$input)
    );
}
