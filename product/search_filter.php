<?php


//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin, Origin");

//Req includes
include_once '../config/database.php';
include_once '../objects/Product.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$product = new Product($db);

//get keywords
$data = json_decode(file_get_contents("php://input"));

if(isset($data->keyword)){
	$product->keyword = $data->keyword;
}
if(isset($data->category_id)){
	$product->category_id = $data->category_id;
}
if(isset($data->province)){
	$product->province = $data->province;
}
if(isset($data->city)){
	$product->city = $data->city;
}
if(isset($data->district)){
	$product->district = $data->district;
}
if(isset($data->min_price)){
	$product->min_price = $data->min_price;
}
if(isset($data->max_price)){
	$product->max_price = $data->max_price;
}
if(isset($data->order_by)){
	$product->order_by = $data->order_by;
}
if(isset($data->order_type)){
	$product->order_type = $data->order_type;
}


//query products
$stmt=$product->search_filter();
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
        array("success"=>false,"message" => "No vendors found.","payload"=>[])
    );
}
