<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Venue.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$venue = new Venue($db);

//Query products
$stmt = $venue->read();
$num = $stmt->rowCount();

//Check if more than 0 record found
if($num > 0){

    //products array
    $venues_arr = array();
    $venues_arr["records"] = array();

    //retrieve table content
    // Difference fetch() vs fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $venue_item = array(
            "id"            =>  $id,
            "name"          =>  $name,
            "description"   =>  html_entity_decode($description),
            "images"   		=>  $images,
            "large_num"   	=>  $large_num,
            "capacity"   	=>  $capacity,
            "composition"   =>  $composition,
            "electricity"   =>  $electricity,
            "parking_lot"   =>  $parking_lot,
            "location"   	=>  $location,
            "available_status" =>  $available_status,
            "ratings"   	=>  $ratings,
            "reviews"   	=>  $reviews,
            "price"         =>  $price
        );
		

        array_push($venues_arr["records"], $venue_item);
    }

    echo json_encode(array("success" => true, "payload" => $venues_arr));
}else{
    echo json_encode(
        array("messege" => "No products found.")
    );
}
