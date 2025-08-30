<?php
/**

 * file used for core configuration
 */

//show error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

//home page url
$server_url="https://daksameedia.com/gelaro/";
// $home_url="/home/daksamed/public_html/gelaro/API";
$home_url="https://gelaro.id/";

$file_url="/home/daksamed/public_html/gelaro/";
$avatar_dir=$file_url."images/avatars";
$images_dir=$file_url."images/uploads";

// set your default time-zone
date_default_timezone_set('Asia/Manila');
 
// variables used for jwt
$key = "example_key";
$iss = "http://gelaro.id";
$aud = "http://gelaro.id";
$iat = time();
$nbf = 1357000000;

//page given in url parameter, default page is one
$page = isset($_GET['page']) ? $_GET['page'] : 1;

//set number of records per page
$records_per_page = 5;

//calculate for query limit clause
$from_record_num = ($records_per_page * $page) - $records_per_page;
