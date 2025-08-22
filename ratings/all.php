<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Rating.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$rating = new Rating($db);

//Set ID of product to be edited
$rating->id = isset($_GET['vendor_id']) ? $_GET['vendor_id']: die;

//Query products
$stmt = $rating->readByVendor($_GET['vendor_id']);
$num = $stmt->rowCount();

//Check if more than 0 record found
if($num > 0){

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
        extract($row);

        $rating_item = array(
            "id"            =>  $id,
            "user_id"       =>  $user_id,
            "vendor_id"     =>  $vendor_id,
            "comments"   	=>  html_entity_decode($comments),
            "rating"   		=>  $rating,
            "firstname"   	=>  $u_firstname,
            "lastname"   	=>  $u_lastname,
            "avatar"   		=>  $u_avatar
        );

        array_push($ratings_arr["records"], $rating_item);
    }

    echo json_encode(
		array("success" => true, "payload"=>$ratings_arr["records"])
	);
}else{
    echo json_encode(
        array("success" => false, "messege" => "No products found.")
    );
}
