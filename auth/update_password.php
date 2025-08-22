<?php

//Required headers

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
	header('Access-Control-Allow-Origin: *');
	header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
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
$input = json_decode(file_get_contents("php://input"));
 
$headers=getallheaders();
$authorized = $headers['Authorization'];

// encode token
list($bearer, $token) = explode(" ", $authorized);
list($header, $payload, $signature) = explode(".", $token);
$decoded=base64_decode($payload);
$data = json_decode($decoded);

$user->email = $data->data->email;
$email_exists = $user->emailExists();
 
// check if email exists and if password is correct
if($email_exists && password_verify($input->password, $user->password) && $user->isActive()){
 
    // if jwt is not empty
    if($token){
        try {

            // set user property values
            $user->new_password = $input->new_password;
            $user->password = $input->password;
            $user->id = $data->data->id;
            
            // create the product
            if($user->changePassword()){
                
                // set response code
                http_response_code(200);
                
                // response in json format
                echo json_encode(
                    array(
                        "message" => "Password was updated.",
                        "success" => true
                    )
                );
            }else{
                // set response code
                http_response_code(401);
            
                // show error message
                echo json_encode(array(
                    "message" => "Unable to update password.",
                    "success" => false
                ));
            }
        }
        catch (Exception $e){
        
            // set response code
            http_response_code(401);
        
            // show error message
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    }else{
    
        // set response code
        http_response_code(401);
    
        // tell the user access denied
        echo json_encode(array("message" => "Access denied."));
    }
 
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
        echo json_encode(array("success" => false, "message" => "Account not active yet"));
        die();
    }

    if(!password_verify($input->password, $user->password)){
        // tell the user login failed
        echo json_encode(array("success" => false, "message" => "Password is wrong"));
        die();
    }
 
}

?>
