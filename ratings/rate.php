<?php

//Req headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset:UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//Req includes
include_once '../config/database.php';
include_once '../objects/Rating.php';

//Db conn and instances
$database = new Database();
$db=$database->getConnection();

$rating = new Rating($db);

//Get post data
$data = json_decode(file_get_contents("php://input"));

//set product values
$rating->vendor_id   	= $data->vendor_id;
$rating->user_id        = $data->user_id;
$rating->type        	= $data->type;
$rating->comments   	= $data->comments;
$rating->rating        = $data->rating;
$rating->created       = date('Y-m-d H:i:s');

//Create product
if($rating->create()){
	if($rating->type=='vendor'){
		
		$stmt = $rating->get_average_vendor($rating->vendor_id);
		$num = $stmt->rowCount();
		$vendor = $stmt->fetchAll();
		
		
		if($num > 0){
			$this_score = $vendor[0]["score"];
			$this_count = $vendor[0]["count_num"];
			
			
			if($rating->update_rating_vendor($rating->vendor_id, $this_score, $this_count)) {
				echo json_encode(
					array("success" => true,"message" => "Rating was added.")
				);
			}else{
				echo json_encode(
					array("success" => false,"message" => "Unable to add rating.")
				);
			}
		}else{
			echo json_encode(
				array("success" => false,"message" => "Unable to add rating.")
			);
		}
	}else{
		$stmt = $rating->get_average_venue($rating->vendor_id);
		$num = $stmt->rowCount();
		$vendor = $stmt->fetchAll();
		
		
		if($num > 0){
			$this_score = $vendor[0]["score"];
			$this_count = $vendor[0]["count_num"];
			
			
			if($rating->update_rating_venue($rating->vendor_id, $this_score, $this_count)) {
				echo json_encode(
					array("success" => true,"message" => "Rating was added.")
				);
			}else{
				echo json_encode(
					array("success" => false,"message" => "Unable to add rating.")
				);
			}
		}else{
			echo json_encode(
				array("success" => false,"message" => "Unable to add rating.")
			);
		}
	}
}else{
    echo json_encode(
		array("success" => false,"message" => "Unable to add rating.")
	);
}