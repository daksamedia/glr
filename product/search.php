<?php


//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

//Req includes
include_once '../config/database.php';
include_once '../objects/Product.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$product = new Product($db);

//get keywords
$keywords = isset($_GET["keyword"]) ? $_GET["keyword"] : "";

//query products
$stmt=$product->search($keywords);
$num=$stmt->rowCount();

//check if more than 0 record found
if($num>0){

  //products array
    $products_arr = array();
    $products_arr["records"] = array();

    //retrieve table contents
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){
        // extract row
        // this will make $row['name'] to
        // just $name only
        extract($row);

        $product_item=array(
            "id"            =>  $id,
            "name"          =>  $name,
            "location"   	=>  html_entity_decode($location),
            "ratings"   	=>  $ratings,
            "reviews"   	=>  $reviews,
            "price"         =>  $price,
            "category_id"   =>  $category_id,
            "category_name" =>  $category_name,
            "cover"         =>  $cover
        );

        array_push($products_arr["records"], $product_item);
    }

    echo json_encode(
		 array("success" => true, "payload"=>$products_arr['records'])
	);
}else{
    echo json_encode(
        array("success" => false, "message" => "No vendors found.")
    );
}
