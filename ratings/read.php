<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Rating.php';
include_once '../objects/User.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$rating = new Rating($db);
$user = new User($db);

//Set ID of product to be edited
$rating->vendor_id = isset($_GET['vendor_id']) ? $_GET['vendor_id']: die;
$rating->type = isset($_GET['type']) ? $_GET['type']: die;

//Query products
$stmt = $rating->readByVendor();

//Check if more than 0 record found
if(count($stmt)){

    //products array
    $ratings_arr = array();
    $ratings_arr["records"] = array();

    //retrieve table content
    // Difference fetch() vs fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // extract row
        // this will make $row['name'] to
        // just $name only
        $user->id = $row['user_id'];
        $user->readOne();

        extract($row);
        

        $rating_item = array(
            "id"            =>  $id,
            "user_id"       =>  $user_id,
            "vendor_id"     =>  $vendor_id,
            "comments"   	=>  html_entity_decode($comments),
            "rating"   		=>  $rating,
            "firstname"   	=>  $user->firstname,
            "lastname"   	=>  $user->lastname,
            "avatar"        =>  $user->avatar
        );

        array_push($ratings_arr["records"], $rating_item);
    }

    echo json_encode(
		array("success" => true, "payload"=>$ratings_arr)
	);
}else{
    echo json_encode(
        array("success" => false, "messege" => "No ratings found.")
    );
}

// echo json_encode(
//     array("success" => true, "messege" => "No ratings found.")
// );