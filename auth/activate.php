<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");


// core configuration

include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/User.php';
include_once '../shared/Utilities.php';
 
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
    echo json_encode(array("success"=>false,"message" => "Your access code was not found."));
}else{
     
    // update status
    $user->status=1;
    
    if($user->updateStatusByAccessCode()){
		// and the redirect
		echo json_encode(array("success"=>true,"message" => "Your account has been verified."));
	}
}
?>