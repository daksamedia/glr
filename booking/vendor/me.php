<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../../config/database.php';
include_once '../../objects/Booking.php';
include_once '../../objects/User.php';
include_once '../../objects/Product.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$product = new Product($db);
$user = new User($db);

//Get post data
$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

//Query gallery
$user_id = $data->data->id;

if ($data) {
    $product->checkIfVendorExist($user_id);
    $booking->business_id=$product->id;
}
$stmt = $booking->read();
$num = $stmt->rowCount();

if($num > 0){

    //products array
    $booking_arr = array();
    $booking_arr["records"] = array();

    //retrieve table content
    // Difference fetch() vs fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

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
            $user_json = json_decode($user_data);
        }

        $booking_item = array(
            "id"            =>  $id,
            "business_id"   =>  $business_id,
            "service_id"    =>  $service_id,
            "user_data"     =>  $user_json,
            "booking_time"  =>  json_decode($booking_time),
            "status"        =>  $status,
            "modified"      =>  $modified,
            "created"       =>  $created
        );

        array_push($booking_arr["records"], $booking_item);
    }

    
    echo json_encode(
        array("success" => true, "payload"=>$booking_arr["records"])
    );
}else{
    echo json_encode(
        array("success" => false, "message" => "No bookings found.")
    );
}
