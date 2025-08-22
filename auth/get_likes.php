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
include_once '../objects/Product.php';
include_once '../objects/Rating.php';

use \Firebase\JWT\JWT;

//New instances

$database = new Database();
$db = $database->getConnection();
$headers=getallheaders();

$user = new User($db);
$product = new Product($db);
$rating = new Rating($db);
$authorized = $headers['Authorization'];

// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded = base64_decode($payload);
$data = json_decode($decoded);

// get user
$user->id = $data->data->id;

$user->readLikes();

$likes = $user->likes;
// $user_arr = array(
//     "id" => $user->id,
//     "likes" => $user->likes,
// );
$likes_arr = explode(',', $likes);
$all_likes = array();

foreach ($likes_arr as $idProd) {
    $rating->vendor_id = $idProd;
    $rating->type = 'vendor';
    $ratings = $rating->readByVendor();
    $ratnum = $ratings->rowCount();
    $total_ratings = 0;

    if($ratnum) {
        $total = 0;
        while ($value = $ratings->fetch(PDO::FETCH_ASSOC)){
            $total = $total + (int)$value["rating"];
        }
        $total_ratings = $total/$ratnum;
    }

    $product->id = $idProd;
    $product->readOne();
    $product_arr = array(
        "id" => $product->id,
        "name" => $product->name,
        "location" => $product->location,
        "ratings" => $total_ratings,
        "reviews" => $ratnum,
        "price" => $product->price,
        "category_id" => $product->category_id,
        "category_name" => $product->category_name,
        "cover" => $product->cover
    );
    array_push($all_likes, $product_arr);
}

if ($token) {
    if (count($all_likes) > 0) {
        echo json_encode(
            array("success" => true, "payload"=>$all_likes, "message"=>"Got Data Succesfully")
        );
    } else {
        echo json_encode(
            array("success" => true, "payload"=>$all_likes, "message"=>"Got Data Succesfully. Data is empty.")
        );
    }
} else {
    echo json_encode(
        array("success" => false, "message"=>"This account does not belongs to you. Please login.")
    );
}

