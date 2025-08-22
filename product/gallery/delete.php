<?php
//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");

//Req includes
include_once '../../config/database.php';
include_once '../../objects/Gallery.php';
include_once '../../objects/Product.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$gallery = new Gallery($db);
$product = new Product($db);

$headers=getallheaders();
$authorized = $headers['Authorization'];

list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);
$user_id = $data->data->id;

if ($data) {
    $product->checkIfVendorExist($user_id);
}

//Get post data
$input = json_decode(file_get_contents("php://input"));

//set Id of gallery to be deleted
$gallery->id = $input->id;

if($product->id == $input->business_id) {
    //delete gallery
    if($gallery->delete()){
        echo '{';
            echo '"message": "Image was deleted."';
        echo '}';
    }else{
        echo '{';
            echo '"message": "Unable to delete Image."';
        echo '}';
    }
} else {
    echo json_encode(
        array("success" => false,"message" => "This image is not belongs to you.")
    );
}
