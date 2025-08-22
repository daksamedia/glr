<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/Booking.php';
include_once '../objects/User.php';
include_once '../objects/Statistic.php';
include_once '../objects/Product.php';
include_once '../objects/Rating.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;

// Init connection
$database = new Database();
$db = $database->getConnection();

$booking = new Booking($db);
$user = new User($db);
$product = new Product($db);
$statistic = new Statistic($db);
$rating = new Rating($db);

//Get post data
$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
$data = JWT::decode($token, $key, array('HS256'));

$user_id = $data->data->id;

if ($data) {
    if ($product->checkIfVendorExist($user_id)) {

        // GET Booking
        $booking->business_id=$product->id;
        $books = $booking->read();

        // GET Stats
        $statistic->business_id = $product->id;
        $stats = $statistic->read();

        // GET Ratings
        $rating->business_id = $product->id;
        $rats = $rating->readByVendor();

        $book_arr = array();

        while ($row = $books->fetch(PDO::FETCH_ASSOC)) {
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

            array_push($book_arr, 
                array(
                    "id"            =>  $id,
                    "user_id"       =>  $user_id,
                    "status"        =>  $status,
                    "user"          =>  $user_json,
                    "booking_time"  =>  json_decode($booking_time),
                    "created"       =>  $created
                )
            );
        }

        $rate_arr = array();

        while ($row = $rats->fetch(PDO::FETCH_ASSOC)) {
            $user->id = $row['user_id'];
            $user->readOne();
            
            extract($row);

            array_push($rate_arr, 
                array(
                    "id"            =>  $id,
                    "user_id"       =>  $user_id,
                    "vendor_id"     =>  $vendor_id,
                    "comments"   	=>  html_entity_decode($comments),
                    "rating"   		=>  $rating,
                    "firstname"   	=>  $user->firstname,
                    "lastname"   	=>  $user->lastname,
                    "avatar"        =>  $user->avatar
                )
            );
        }

        $all_stats = array(
            'total_bookings' => count($book_arr),
            'total_reviews' => count($rate_arr),
            'total_views'   => $statistic->views,
            'total_likes'   => $statistic->likes,
            'bookings'      => $book_arr,
            'reviews'       => $rate_arr,
        );

        echo json_encode(
            array(
                'success' => true,
                'message' => 'Data retrieved successfully',
                'payload' => $all_stats
            )
        );
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'Vendor does not exist.',
        ));
    }
} else {
    echo json_encode(array(
        'success' => false,
        'message' => 'You do not have an access to the data.',
    ));
}

?>