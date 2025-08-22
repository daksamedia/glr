<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, access-control-allow-origin");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Product.php';
include_once '../objects/User.php';
include_once '../objects/Statistic.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$user = new User($db);
$statistic = new Statistic($db);
$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

$user->id = $data->data->id;
$user->readLikes();

//Set ID of product to be edited
$product->id = isset($_GET['id']) ? $_GET['id']: die;
$product->readOne();

//Get statistics
$statistic->business_id = isset($_GET['id']) ? $_GET['id']: die;
$statistic->read();

$statistic->views = ($statistic->views + 1);
$statistic->modified = date('Y-m-d H:i:s');
$statistic->updateView();

$likes = $user->likes;
$like_arr = explode(',',$likes);

if (count($like_arr) > 1) {
    if (array_search($_GET['id'], $like_arr)) {
        $like=true;
    } else {
        $like=false;
    };
} else {
    if ($likes == $_GET['id']) {
        $like=true;
    } else {
        $like=false;
    }
}

//Create array
$product_arr = array(
    "id" => $product->id,
    "name" => $product->name,
    "bio" => $product->bio,
    "location" => $product->location,
    "location_data" => $product->location_data,
    "ratings" => $product->ratings,
    "reviews" => $product->reviews,
    "price" => $product->price,
    "category_id" => $product->category_id,
    "category_name" => $product->category_name,
    "image_url" => $product->cover,
    "is_liked" => $like,
    "total_likes" => $statistic->likes,
    "total_views" => $statistic->views,
    "total_order" => $statistic->orders,
);

echo json_encode(
	array("success" => true, "payload"=>$product_arr)
);
