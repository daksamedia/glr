<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../../config/core.php';
include_once '../../config/database.php';
include_once '../../objects/Rating.php';
include_once '../../objects/User.php';
include_once '../../objects/Product.php';
//New instances

$database = new Database();
$db = $database->getConnection();

$rating = new Rating($db);
$user = new User($db);
$product = new Product($db);

//Set ID of product to be edited
$rating->id = isset($_GET['user_id']) ? $_GET['user_id']: die;

//Query products
$stmt = $rating->read_by_user($rating->id);
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

        $product->id = $vendor_id;
        $product->readOne();
    
        $business_arr = array(
            "id" => $product->id,
            "name" => $product->name,
            "cover" => $product->cover,
            "category_name" => $product->category_name,
            "location" => $product->location
        );
		
        $rating_item = array(
            "id"            =>  $id,
            "vendor_id"     =>  $vendor_id,
            "vendor_data"   =>  $business_arr,
            "comments"      =>  html_entity_decode($comments),
            "rating"   		=>  $rating,
            "created"       =>  $created
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
