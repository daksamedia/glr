<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, access-control-allow-origin");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, access-control-allow-origin");
	header("HTTP/1.1 200 OK");
	die();
}

//Include db and object

include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/User.php';
include_once '../shared/jwt/BeforeValidException.php';
include_once '../shared/jwt/ExpiredException.php';
include_once '../shared/jwt/SignatureInvalidException.php';
include_once '../shared/jwt/JWT.php';
use \Firebase\JWT\JWT;

//New instances

$database = new Database();
$db = $database->getConnection();

$user = new User($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$user->email = $data->email;
$email_exists = $user->emailExists();
 
// check if email exists and if password is correct
if($email_exists && password_verify($data->password, $user->password) && $user->isActive()){
 
    $token = array(
       "iss" => $iss,
       "aud" => $aud,
       "iat" => $iat,
       "nbf" => $nbf,
       "data" => array(
           "id" => $user->id,
           "firstname" => $user->firstname,
           "lastname" => $user->lastname,
           "email" => $user->email
       )
    );
 
    // set response code
    http_response_code(200);
 
    // generate jwt
    $jwt = JWT::encode($token, $key);
    echo json_encode(
            array(
                "success" => true,
                "message" => "Successful login.",
                "jwt" => $jwt,
                "data" => array(
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email
                )
            )
        );
 
}else{

    // set response code
    http_response_code(401);

    if(!$email_exists){
        // tell the user login failed
        echo json_encode(array("success" => false, "message" => "Account not found"));
        die();
    }

    if(!$user->isActive()){
        // tell the user login failed
        echo json_encode(array("success" => false, "message" => "Account not active yet. Please verify your account."));
        die();
    }

    if(!password_verify($data->password, $user->password)){
        // tell the user login failed
        echo json_encode(array("success" => false, "message" => "Password is wrong"));
        die();
    }
 
}

?>
