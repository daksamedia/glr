<?php
/**
*contains properties and methods for "product" database queries.
 */

class Statistic
{

    //Db connection and table
    private $conn;
    private $table_name = 'statistics';

    //Object properties
    public $id;
    public $business_id;
    public $likes;
    public $views;
    public $orders;
    public $created;

    //Constructor with db conn
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //create stats
	function create(){

		//query insert
		$query = "INSERT INTO
				  ". $this->table_name ."
				  SET
					business_id=:business_id, views=:views, likes=:likes, orders=:orders, created=:created";

		//Prepare
		$stmt = $this->conn->prepare($query);

		//sanitize
		$this->business_id=htmlspecialchars(strip_tags($this->business_id));
		$this->likes=htmlspecialchars(strip_tags($this->likes));
		$this->views=htmlspecialchars(strip_tags($this->views));
		$this->orders=htmlspecialchars(strip_tags($this->orders));
		$this->created=htmlspecialchars(strip_tags($this->created));

		//Bind values
		$stmt->bindParam(":business_id", $this->business_id);
		$stmt->bindParam(":views", $this->views);
		$stmt->bindParam(":likes", $this->likes);
		$stmt->bindParam(":orders", $this->orders);
		$stmt->bindParam(":created", $this->created);

		//execute
		if($stmt->execute()){
			return true;
		}
		return false;
	}

    //get stats by business ID
    function read(){
        //select all
        $query = "SELECT
                s.id, s.business_id, s.views, s.likes, s.orders, s.created
            FROM
                " . $this->table_name . " s
                   WHERE
                   s.business_id = ? LIMIT 0,1";

        //prepare
        $stmt = $this->conn->prepare($query);

		//bind queries
		$stmt->bindParam(1, $this->business_id);

        //execute
        $stmt->execute();

        //fetch row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //set values to update
        $this->id=$row['id'];
        $this->business_id=$row['business_id'];
        $this->views=$row['views'];
        $this->likes=$row['likes'];
        $this->orders=$row['orders'];
        $this->created=$row['created'];
    }

    //set stats
    function updateView() {
        // update query
		$query = "UPDATE
        " . $this->table_name . "
            SET
                views = :views,
                modified = :modified
            WHERE
                business_id = :business_id";	

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->views=htmlspecialchars(strip_tags($this->views));
        $this->modified=htmlspecialchars(strip_tags($this->modified));
        $this->business_id=htmlspecialchars(strip_tags($this->business_id));

        // bind new values
        $stmt->bindParam(":views", $this->views);
        $stmt->bindParam(":modified", $this->modified);
        $stmt->bindParam(":business_id", $this->business_id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function updateLike() {
        // update query
		$query = "UPDATE
        " . $this->table_name . "
            SET
                likes = :likes,
                modified = :modified
            WHERE
                business_id = :business_id";	

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->likes=htmlspecialchars(strip_tags($this->likes));
        $this->modified=htmlspecialchars(strip_tags($this->modified));
        $this->business_id=htmlspecialchars(strip_tags($this->business_id));

        // bind new values
        $stmt->bindParam(":likes", $this->likes);
        $stmt->bindParam(":modified", $this->modified);
        $stmt->bindParam(":business_id", $this->business_id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function updateOrder() {
        // update query
		$query = "UPDATE
        " . $this->table_name . "
            SET
                orders = :orders,
                modified = :modified
            WHERE
                business_id = :business_id";	

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->orders=htmlspecialchars(strip_tags($this->orders));
        $this->modified=htmlspecialchars(strip_tags($this->modified));
        $this->business_id=htmlspecialchars(strip_tags($this->business_id));

        // bind new values
        $stmt->bindParam(":orders", $this->orders);
        $stmt->bindParam(":modified", $this->modified);
        $stmt->bindParam(":business_id", $this->business_id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }
}
