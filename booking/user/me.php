<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../../config/core.php';
include_once '../../config/database.php';
include_once '../../objects/Booking.php';
include_once '../../objects/User.php';
include_once '../../objects/Product.php';
include_once '../../shared/jwt/BeforeValidException.php';
include_once '../../shared/jwt/ExpiredException.php';
include_once '../../shared/jwt/SignatureInvalidException.php';
include_once '../../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;
//New instances

$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$user = new User($db);
$product = new Product($db);

//Get post data
$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
$data = JWT::decode($token, $key, array('HS256'));

//Query gallery
$user_id = $data->data->id;

if ($data) {
    $booking->user_id=$user_id;
}

$stmt = $booking->readMy();
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

        $product->id = $business_id;
        $product->readOne();
    
        $business_arr = array(
            "id" => $product->id,
            "name" => $product->name,
            "cover" => $product->cover,
            "category_name" => $product->category_name,
            "location" => $product->location
        );

        $booking_item = array(
            "id"            =>  $id,
            "business_id"   =>  $business_id,
            "business_data" =>  $business_arr,
            "service_id"    =>  $service_id,
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
        array("success" => false, "message" => "No bookings found.", "data" => $user_id)
    );
}
