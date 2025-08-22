<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, Access-Control-Allow-Origin");


// core configuration
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/User.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// initialize objects
$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set access code
$user->access_code = $data->access_code;
 
// verify if access code exists
if(!$user->accessCodeExists()){
    // die("ERROR: Access code not found.");
    echo json_encode(array("success"=>false,"message" => "Please insert your email first."));
}else{
    
    // set values to object properties
    $user->password = $data->password;
	if($user->password && $user->updatePassword()){
        echo json_encode(array("success"=>true,"message" => "Password has been reset."));
    }else{
        if (!$user->password) {
            echo json_encode(array("success"=>false,"message" => "Password field is empty."));
        } else {
            echo json_encode(array("success"=>false,"message" => "Unable to reset password."));
        }
        
    }
}
?>