<?php 
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

$response = array();
$upload_dir = 'C:/wamp64/www/gelaro/images/uploads/';
//server URL
$server_url = 'https://daksamedia.id/gelaro/images/uploads';

if($_FILES['avatar'])
{
    $avatar_name = $_FILES["avatar"]["name"];
    $avatar_tmp_name = $_FILES["avatar"]["tmp_name"];
    $error = $_FILES["avatar"]["error"];

    if($error > 0){
        $response = array(
            "status" => "error",
            "success" => false,
            "message" => "Error uploading the file!"
        );
    }else 
    {
        $random_name = rand(1000,1000000)."-".$avatar_name;
        $upload_name = $upload_dir.strtolower($random_name);
        $upload_name = preg_replace('/\s+/', '-', $upload_name);
    
        if(move_uploaded_file($avatar_tmp_name , $upload_name)) {
            $response = array(
                "success" => true,
                "message" => "File uploaded successfully",
                "url" => $server_url."/".$upload_name // Streaming or Online only
                // "url" => $upload_name
              );
        }else
        {
            $response = array(
				"success" => false,
                "message" => "Error uploading the file!"
            );
        }
    }



    

}else{
    $response = array(
        "status" => "error",
        "success" => false,
        "message" => "No file was sent!"
    );
}

echo json_encode($response);
?>