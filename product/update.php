<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, PUT, GET, HEAD, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");
 
// include database and object files
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/Product.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;
use Firebase\JWT\Key;
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// prepare product object
$product = new Product($db);

$headers=getallheaders();
$authorized = $headers['Authorization'];

// get current user ID
// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

// get id of product to be edited
$input = json_decode(file_get_contents("php://input"));
 
// set ID property of product to be edited


if($data) {
	$user_id = $data->data->id;
  
  // update the product
  if($product->checkIfVendorExist($user_id)) {
    // set product property values
    $product->modified = date('Y-m-d H:i:s');

    if(!empty($input->name)) {
      $product->name = $input->name;
    }

    if(!empty($input->location)) {
      $product->location = $input->location ; 
    }

    if(!empty($input->location_data)) {
      $product->location_data = $input->location_data ; 
    }

    if(!empty($input->bio)) {
      $product->bio = $input->bio;
    }

    if(!empty($input->price)) {
      $product->price = $input->price;
    }

    if(!empty($input->category_id)) {
      $product->category_id = $input->category_id;
    }


    if($product->update()){
    
        // set response code - 200 ok
        http_response_code(200);
    
        // tell the user
        echo json_encode(
        array("success" => true, "message" => "Vendor was updated.")
      );
    }
    
    // if unable to update the product, tell the user
    else{
    
      // set response code - 503 service unavailable
      http_response_code(503);
  
      // tell the user
      echo json_encode(
        array("success" => false,"message" => "Unable to update vendor.")
      );
    }
  } else {
    echo json_encode(
      array("success" => false,"message" => "Your business does not exist.")
    );
  }
} else {
  echo json_encode(
    array("success" => false,"message" => "Access denied.")
  );
}
?>