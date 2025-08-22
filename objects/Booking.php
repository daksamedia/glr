<?php
/**
*contains properties and methods for "product" database queries.
 */

class Booking
{

    //Db connection and table
    private $conn;
    private $table_name = 'bookings';

    //Object properties
    public $id;
    public $user_id;
    public $user_data;
    public $business_id;
    public $service_id;
    public $booking_time;
    public $status;
    public $created;
    public $modified;

    //Constructor with db conn
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //Read product by business id
    function read(){

        //select all
        $query = "SELECT
                    p.id, p.user_id, p.user_data, p.business_id, p.service_id, p.booking_time, p.status, p.created, p.modified
				  FROM `". $this->table_name ."` p
				  WHERE p.business_id = ?
				  ORDER BY p.created DESC";

        //prepare
        $stmt = $this->conn->prepare($query);

		//bind queries
		$stmt->bindParam(1, $this->business_id);

        //execute
        $stmt->execute();

        return $stmt;

    }

    //read single product
    function readOne(){

        //read single record
        $query = "SELECT
                 p.id, p.user_id, p.user_data, p.business_id, p.service_id, p.booking_time, p.status, p.created, p.modified, p.notes, p.expired_date
				  FROM `". $this->table_name ."` p
                WHERE
                p.id = ? LIMIT 0,1";

        //prepare
        $stmt = $this->conn->prepare($query);

        //bind id of product
        $stmt->bindParam(1, $this->id);

        //execute
        $stmt->execute();

        //fetch row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //set values to update
        $this->id=$row['id'];
        $this->user_id=$row['user_id'];
        $this->user_data=$row['user_data'];
        $this->business_id=$row['business_id'];
        $this->service_id=$row['service_id'];
        $this->booking_time=$row['booking_time'];
        $this->status=$row['status'];
        $this->created=$row['created'];
        $this->modified=$row['modified'];
        $this->notes=$row['notes'];
        $this->expired_date=$row['expired_date'];

    }

    //read my bookings as customer
    function readMy(){

        //select all
        $query = "SELECT
                    p.id, p.user_id, p.user_data, p.business_id, p.service_id, p.booking_time, p.status, p.created, p.modified
				  FROM `". $this->table_name ."` p
				  WHERE p.user_id = ?
				  ORDER BY p.created DESC";

        //prepare
        $stmt = $this->conn->prepare($query);

		//bind queries
		$stmt->bindParam(1, $this->user_id);

        //execute
        $stmt->execute();

        return $stmt;

    }

	//create booking
	function create(){

		//query insert
		$query = "INSERT INTO
				  ". $this->table_name ."
				  SET
                  user_id=:user_id, user_data=:user_data, business_id=:business_id, service_id=:service_id, booking_time=:booking_time, status=:status, expired_date=:expired_date, created=:created";
                  //   UPDATE ". $this->table_name ." SET status='expired' WHERE NOW() >= ADDDATE(enter_date, period_allowed)


		//Prepare
		$stmt = $this->conn->prepare($query);

		//sanitize
		$this->user_id=htmlspecialchars(strip_tags($this->user_id));
		$this->user_data=htmlspecialchars(strip_tags($this->user_data));
		$this->business_id=htmlspecialchars(strip_tags($this->business_id));
		$this->service_id=htmlspecialchars(strip_tags($this->service_id));
		$this->booking_time=$this->booking_time;
		$this->status=htmlspecialchars(strip_tags($this->status));
        $this->expired_date=htmlspecialchars(strip_tags($this->expired_date));
		$this->created=htmlspecialchars(strip_tags($this->created));

		//Bind values
		$stmt->bindParam(":user_id", $this->user_id);
		$stmt->bindParam(":user_data", $this->user_data);
		$stmt->bindParam(":business_id", $this->business_id);
		$stmt->bindParam(":service_id", $this->service_id);
		$stmt->bindParam(":booking_time", $this->booking_time);
		$stmt->bindParam(":status", $this->status);
		$stmt->bindParam(":expired_date", $this->expired_date);
		$stmt->bindParam(":created", $this->created);

		//execute
        $stmt->execute();
		// if($stmt->execute()){
		// 	return true;
		// }
		// return false;

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //set values to update
        $this->id=$row['id'];
	}

    // update the product
	function startTime(){
        
        // update query
        $query = "CREATE EVENT myevent ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 2 MINUTE DO UPDATE
					" . $this->table_name . "
				SET
					status = 'EXPIRED'
				WHERE
					id = :id";

        // prepare
		$stmt = $this->conn->prepare($query);

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind
        $stmt->bindParam(":id", $this->id);

        // execute
		if($stmt->execute()){
			return true;
		}
		return false;
	}

    function update(){
	 
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					status = :status,
                    notes = :notes
				WHERE
					id = :id";	
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->status=htmlspecialchars(strip_tags($this->status));
		$this->notes=htmlspecialchars(strip_tags($this->notes));
	 
		// bind new values
		$stmt->bindParam(":id", $this->id);
		$stmt->bindParam(":status", $this->status);
		$stmt->bindParam(":notes", $this->notes);
        
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

    //delete product
    function delete(){

        //delete query
        $query = " DELETE FROM " . $this->table_name . " WHERE id = ?";

        //prepare
        $stmt = $this->conn->prepare($query);

        //sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        //bind id
        $stmt->bindParam(1, $this->id);

        //execute
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    //search products
    function search($keywords){

        //select all query
        $query = "SELECT
                  c.name AS category_name, p.id, p.name, p.cover, p.location, p.ratings, p.reviews, p.price, p.category_id, p.created
                  FROM " . $this->table_name. " p
                  LEFT JOIN
                    categories c ON p.category_id = c.id
                  WHERE
                    p.name LIKE ? OR p.location LIKE ? OR c.name LIKE ?
                  ORDER BY
                    p.created DESC";

        //prepare
        $stmt =$this->conn->prepare($query);

        //sanitize
        $keywords = htmlspecialchars(strip_tags($keywords));
        $keywords = "%{$keywords}%";

        //bind
        $stmt->bindParam(1, $keywords);
        $stmt->bindParam(2, $keywords);
        $stmt->bindParam(3, $keywords);

        //execute
        $stmt->execute();

        return $stmt;
    }
}
