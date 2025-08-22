<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Service.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);

//Set ID of product to be edited
$service->id = isset($_GET['vendor_id']) ? $_GET['vendor_id']: die;

//Query products
$stmt = $service->read($service->id);
$num = $stmt->rowCount();

//Check if more than 0 record found
if($num > 0){

    //products array
    $services_arr = array();
    $services_arr["records"] = array();

    //retrieve table content
    // Difference fetch() vs fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $service_item = array(
            "id"            =>  $id,
            "title"         =>  $title,
            "description"   =>  html_entity_decode($description),
            "image"   		=>  $image,
            "price"         =>  $price,
            "created"       =>  $created
        );

        array_push($services_arr["records"], $service_item);
    }

    
	echo json_encode(
		array("success" => true, "payload"=>$services_arr["records"])
	);
}else{
    echo json_encode(
        array("success" => false, "message" => "No service found.")
    );
}
