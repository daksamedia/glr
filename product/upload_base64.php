<?php 
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

// include database and object files
include_once '../config/database.php';
include_once '../config/core.php';
include_once '../shared/Utilities.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();

//	utils
$util = new Utilities();

// get id of product to be edited
$data = json_decode(file_get_contents("php://input"));
$file_name = $data->id;
$file_type = $data->type;
$data_base64 = $data->base64;


if($data_base64){
	
	$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $data_base64));
	$file_id = rand(1000,1000000);

	$filepath = $images_dir .'/'. $file_type .'-'. $file_name .'-'. $file_id .'.jpg'; // or image.jpg

	// Save the image in a defined path
	file_put_contents($filepath,$data);

	echo json_encode(
		array("success" => true,"link" => $filepath)
	);
}
?>