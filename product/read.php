<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Include db and object

include_once '../config/database.php';
include_once '../objects/Product.php';
include_once '../objects/Rating.php';
include_once '../objects/User.php';
include_once '../objects/Statistic.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$rating = new Rating($db);
$user = new User($db);
$statistic = new Statistic($db);
$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

$user->id = $data->data->id;
$user->readLikes();
$likes = $user->likes;
$like_arr = explode(',',$likes);

//Query products
$stmt = $product->read();
$num = $stmt->rowCount();

//Check if more than 0 record found
if($num > 0){

    //products array
    $products_arr = array();
    $products_arr["records"] = array();

    //retrieve table content
    // Difference fetch() vs fetchAll()
    // http://stackoverflow.com/questions/2770630/pdofetchall-vs-pdofetch-in-a-loop
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

        // extract row
        // this will make $row['name'] to
        // just $name only
        $rating->vendor_id = $row['id'];
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

        extract($row);

        if (count($like_arr) > 1) {
            if (array_search($id, $like_arr)) {
                $like=true;
            } else {
                $like=false;
            };
        } else {
            if ($likes == $id) {
                $like=true;
            } else {
                $like=false;
            }
        }

        $product_item = array(
            "id"            =>  $id,
            "name"          =>  $name,
            "location"   	=>  html_entity_decode($location),
            "ratings"   	=>  $total_ratings,
            "reviews"   	=>  $ratnum,
            "price"         =>  $price,
            "category_id"   =>  $category_id,
            "category_name" =>  $category_name,
            "is_like"       =>  $like,
            "cover"         =>  $cover
        );

        array_push($products_arr["records"], $product_item);
    }

    echo json_encode($products_arr);
}else{
    echo json_encode(
        array("messege" => "No products found.")
    );
}
