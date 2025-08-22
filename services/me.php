<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: access-control-allow-origin, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


//Include db and object

include_once '../config/database.php';
include_once '../objects/Service.php';
include_once '../objects/Product.php';

//New instances

$database = new Database();
$db = $database->getConnection();

$service = new Service($db);
$product = new Product($db);

$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

$user_id = $data->data->id;

//Get post data

if ($product->checkIfVendorExist($user_id)) {

    //Query products
    $stmt = $service->read($product->id);
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
} else {
	echo json_encode(
		array("success" => false,"message" => "Business does not exist. Please create it first.")
	);	
}
