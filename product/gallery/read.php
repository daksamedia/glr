<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../../config/database.php';
include_once '../../objects/Gallery.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$gallery = new Gallery($db);

//Query gallery
$gallery->business_id = isset($_GET['business_id']) ? $_GET['business_id']: die;
$stmt = $gallery->read();
$num = $stmt->rowCount();

if($num > 0){

    //gallery array
    $products_arr = array();
    $products_arr["records"] = array();

    //retrieve table content
    // Difference fetch() vs fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);

        $product_item = array(
            "id"            =>  $row['id'],
            "business_id"   =>  $row['business_id'],
            "url"           =>  $row['url']
        );

        array_push($products_arr["records"], $product_item);
    }

    echo json_encode(
        array("success"=>true, "message" => "Images has been found.", "payload" => $products_arr["records"])
    );
}else{
    echo json_encode(
        array("success"=>true, "message" => "No images found.")
    );
}
