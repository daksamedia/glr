<?php

class Utilities{
	
	
    public function getPaging($page, $total_rows, $records_per_page, $page_url){

        //paging array
        $paging_arr = array();

        //button for first page
        $paging_arr['firs'] = $page>1 ? "{$page_url}page=1" : "";

        //count all products in db to get total pages
        $total_pages = ceil($total_rows / $records_per_page);

        //range of links to show
        $range = 2;

        //display links to "range of pages' around 'current page'
        $initial_num = $page - $range;
        $condidion_limit_num = ($page + $range) + 1;

        $paging_arr['pages'] = array();
        $page_count = 0;

        for($x=$initial_num; $x<$condidion_limit_num; $x++){
            // be sure '$x is greater than 0' AND 'less than or equal to the $total_pages'
            if(($x>0) && ($x<=$total_pages)){
                $paging_arr['pages'][$page_count]['page'] = $x;
                $paging_arr['pages'][$page_count]['utl'] = "{$page_url}page={$x}";
                $paging_arr['pages'][$page_count]['current_page'] = $x==$page ? "yes" : "no";

                $page_count++;
            }
        }
        //button for last page
        $paging_arr["last"] = $page<$total_pages ? "{$page_url}page={$total_pages}" : "";

        //json
        return $paging_arr;
    }

	public function getToken($length=32){
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		for($i=0;$i<$length;$i++){
			$token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
		}
		return $token;
	}
	
	public function uploadBase64($base64string, $file_name){
		
		$home_url = 'C:/wamp64/www/gelaro/images/uploads/';
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64string));

		$filepath = $home_url .'/'. $file_name .'.jpg'; // or image.jpg

		// Save the image in a defined path
		file_put_contents($filepath,$data);
		
		return $filepath;
	}
	 
	function crypto_rand_secure($min, $max) {
		$range = $max - $min;
		if ($range < 0) return $min; // not so random...
		$log = log($range, 2);
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd >= $range);
		return $min + $rnd;
	}
	
	// send email using built in php mailer
	public function sendEmailViaPhpMail($send_to_email, $subject, $body){
		ini_set("SMTP","https://smtp.gmail.com");
		ini_set("smtp_port","465");
		
		
		$from_name="Gelaro ID";
		$from_email="no_reply@gelaro.id";
	 
		$headers  = "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		$headers .= "From: {$from_name} <{$from_email}> \n";
	 
		if(mail($send_to_email, $subject, $body, $headers)){
			return true;
		}else{
			return false;
		}
	}

}
