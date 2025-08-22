<?php
/**
*contains properties and methods for "product" database queries.
 */

class Rating	
{

    //Db connection and table
    private $conn;
    private $table_name = 'ratings';
	private $table_user = 'users';

    //Object properties
    public $id;
    public $vendor_id;
    public $user_id;
    public $rating;
    public $reviews;
    public $comments;
    public $firstname;
    public $lastname;
    public $avatar;
    public $created;
    public $modified;

    //Constructor with db conn
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //Read rating by vendor
    function read_by_user($u_id){

       //select all
        // $query = "SELECT `id`, `vendor_id`, `user_id`, `rating`, `comments`, `type`, `created`, `modified` FROM " . $this->table_name . " WHERE user_id =". $u_id ." AND type=". $type ."";
        $query = "SELECT `id`, `vendor_id`, `user_id`, `rating`, `comments`, `type`, `created`, `modified` FROM " . $this->table_name . " WHERE user_id =". $u_id;

        //prepare
        $stmt = $this->conn->prepare($query);

        //execute
        $stmt->execute();

        return $stmt;
    }
	
	//Read rating by vendor
    function readByVendor(){

        //select all
        $query = "SELECT v.rating, v.user_id, v.id, v.comments, v.vendor_id, v.created, v.type  
				  FROM `". $this->table_name ."` v
				  WHERE v.vendor_id = ?
				  AND v.type = ?
				  ORDER BY v.created DESC";
		// $param = "vendor_id = ". $v_id ."";


		// $query = "SELECT * FROM `ratings` WHERE ". $param ." ";

        //prepare
        $stmt = $this->conn->prepare($query);

		//bind queries
		$stmt->bindParam(1, $this->vendor_id);
		$stmt->bindParam(2, $this->type);

        //execute
        $stmt->execute();

        return $stmt;

    }
	
	//post rating
	function create(){
		//query insert
		$query = "INSERT INTO
				  ". $this->table_name ."
				  SET
					 vendor_id=:vendor_id, user_id=:user_id, type=:type,  rating=:rating, comments=:comments, created=:created";
					 
		

		//Prepare
		$stmt = $this->conn->prepare($query);

		//sanitize
		$this->vendor_id=htmlspecialchars(strip_tags($this->vendor_id));
		$this->user_id=htmlspecialchars(strip_tags($this->user_id));
		$this->type=htmlspecialchars(strip_tags($this->type));
		$this->comments=htmlspecialchars(strip_tags($this->comments));
		$this->rating=htmlspecialchars(strip_tags($this->rating));
		$this->created=htmlspecialchars(strip_tags($this->created));

		//Bind values
		$stmt->bindParam(":vendor_id", $this->vendor_id);
		$stmt->bindParam(":user_id", $this->user_id);
		$stmt->bindParam(":type", $this->type);
		$stmt->bindParam(":comments", $this->comments);
		$stmt->bindParam(":rating", $this->rating);
		$stmt->bindParam(":created", $this->created);

		//execute
		if($stmt->execute()){
			return true;
		}
		return false;
	}
	
	function get_average_vendor($v_id){
		//select all
        $query = "SELECT count(*) as count_num, AVG(rating) as score FROM `ratings` WHERE 1 AND vendor_id = ". $v_id ." AND type='vendor'";
		
		//prepare
        $stmt = $this->conn->prepare($query);

        //execute
        $stmt->execute();

        return $stmt;
	}
	
	function update_rating_vendor($id, $num1, $num2){
		//put query
		$query = "UPDATE vendors
		SET
			ratings = ". round($num1, 2) .",
			reviews = ". $num2 ."
		WHERE
			id = ". $id ."";
		
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->rating=htmlspecialchars(strip_tags($this->rating));
		$this->reviews=htmlspecialchars(strip_tags($this->reviews));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(":rating", $this->rating);
		$stmt->bindParam(":reviews", $this->reviews);
		$stmt->bindParam(":id", $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

	function get_average_venue($v_id){
		//select all
        $query = "SELECT count(*) as count_num, AVG(rating) as score FROM `ratings` WHERE 1 AND vendor_id = ". $v_id ." AND type='venue'";
		
		//prepare
        $stmt = $this->conn->prepare($query);

        //execute
        $stmt->execute();

        return $stmt;
	}
	
	function update_rating_venue($id, $num1, $num2){
		//put query
		$query = "UPDATE venues
		SET
			ratings = ". round($num1, 2) .",
			reviews = ". $num2 ."
		WHERE
			id = ". $id ."";
		
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->rating=htmlspecialchars(strip_tags($this->rating));
		$this->reviews=htmlspecialchars(strip_tags($this->reviews));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(":rating", $this->rating);
		$stmt->bindParam(":reviews", $this->reviews);
		$stmt->bindParam(":id", $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

}
