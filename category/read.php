<?php
/**
 *  file that will output JSON data based from "categories" database records.
 */

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Category.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$category = new Category($db);

//query cats
$stmt = $category->read();
$num = $stmt->rowCount();


if($num>0){

    $categories_arr=array();
    $categories_arr["records"]=array();

    //retrieve tables
    while($row=$stmt->fetch(PDO::FETCH_ASSOC)){

        extract($row);

        $category_item=array(
            "id" => $id,
            "name" => $name,
            "description" => html_entity_decode($description),
            "icon" => $icon,
            "created_at" => $created

        );

        array_push($categories_arr["records"], $category_item);
    }

    echo json_encode(
        array("success"=>true, "payload" => $categories_arr["records"])
    );
}else{
    echo json_encode(
        array("success"=>false, "message" => "No products found.")
    );
}
