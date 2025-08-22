<?php
/**
*contains properties and methods for "product" database queries.
 */

class Gallery
{

    //Db connection and table
    private $conn;
    private $table_name = 'galleries';

    //Object properties
    public $id;
    public $business_id;
    public $url;
    public $created;

    //Constructor with db conn
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //Read product by business id
    function read(){

        //select all
        $query = "SELECT v.id, v.url, v.created, v.business_id
				  FROM `". $this->table_name ."` v
				  WHERE v.business_id = ?
				  ORDER BY v.created DESC";

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
                c.name AS category_name, p.id, p.name, p.cover, p.bio, p.location, p.ratings, p.reviews, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                   LEFT JOIN
                    categories c ON p.category_id = c.id
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
        $this->name=$row['name'];
        $this->price=$row['price'];
        $this->location=$row['location'];
        $this->cover=$row['cover'];
        $this->bio=$row['bio'];
        $this->ratings=$row['ratings'];
        $this->reviews=$row['reviews'];
        $this->category_id=$row['category_id'];
        $this->category_name=$row['category_name'];

    }

    //read my product
    function readMy(){

        //read single record
        $query = "SELECT
                c.name AS category_name, p.id, p.user_id, p.name, p.cover, p.bio, p.location, p.ratings, p.reviews, p.price, p.category_id, p.created
            FROM
                " . $this->table_name . " p
                   LEFT JOIN
                    categories c ON p.category_id = c.id
                   WHERE
                   p.user_id = ? LIMIT 0,1";

        //prepare
        $stmt = $this->conn->prepare($query);

        //bind id of product
        $stmt->bindParam(1, $this->user_id);

        //execute
        $stmt->execute();

        //fetch row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //set values to update
        $this->name=$row['name'];
        $this->id=$row['id'];
        $this->price=$row['price'];
        $this->location=$row['location'];
        $this->cover=$row['cover'];
        $this->bio=$row['bio'];
        $this->ratings=$row['ratings'];
        $this->reviews=$row['reviews'];
        $this->category_id=$row['category_id'];
        $this->category_name=$row['category_name'];

    }

	//create product
	function create(){

		//query insert
		$query = "INSERT INTO
				  ". $this->table_name ."
				  SET
					url=:url, business_id=:business_id, created=:created";

		//Prepare
		$stmt = $this->conn->prepare($query);

		//sanitize
		$this->url=htmlspecialchars(strip_tags($this->url));
		$this->business_id=htmlspecialchars(strip_tags($this->business_id));
		$this->created=htmlspecialchars(strip_tags($this->created));

		//Bind values
		$stmt->bindParam(":url", $this->url);
		$stmt->bindParam(":business_id", $this->business_id);
		$stmt->bindParam(":created", $this->created);

		//execute
		if($stmt->execute()){
			return true;
		}
		return false;
	}

    // update the product
	function updatenew(){

        // update variable
        $name_set=!empty($this->name) ? "name = :name" : "";
        $price_set=!empty($this->price) ? ", price = :price" : "";
        $location_set=!empty($this->location) ? ", location = :location" : "";
        $bio_set=!empty($this->bio) ? ", bio = :bio" : "";
        $category_id_set=!empty($this->category_id) ? ", category_id = :category_id" : "";
	 
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					{$name_set}
					{$price_set}
					{$location_set}
                    {$bio_set}
					{$category_id_set}
					modified = :modified
				WHERE
					id = :id";	
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
        if (!empty($this->name)) {
            $this->name=htmlspecialchars(strip_tags($this->name));
            $stmt->bindParam(":name", $this->name);
        }

        if (!empty($this->price)) {
            $this->price=htmlspecialchars(strip_tags($this->price));
            $stmt->bindParam(":price", $this->price);
        }

        if (!empty($this->location)) {
            $this->location=htmlspecialchars(strip_tags($this->location));
            $stmt->bindParam(":location", $this->location);
        }

        if (!empty($this->category_id)) {
            $this->category_id=htmlspecialchars(strip_tags($this->category_id));
            $stmt->bindParam(":category_id", $this->category_id);
        }
		
		if (!empty($this->bio)) {
            $this->bio=htmlspecialchars(strip_tags($this->bio));
            $stmt->bindParam(":bio", $this->bio);
        }
		
		$this->modified=htmlspecialchars(strip_tags($this->modified));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(":modified", $this->modified);
		$stmt->bindParam(":id", $this->id);
	 
		// execute the query
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
					name = :name,
					price = :price,
                    bio = :bio,
					location = :location,
					category_id = :category_id,
					modified = :modified
				WHERE
					id = :id";	
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->price=htmlspecialchars(strip_tags($this->price));
		$this->location=htmlspecialchars(strip_tags($this->location));
        $this->bio=htmlspecialchars(strip_tags($this->bio));
		$this->category_id=htmlspecialchars(strip_tags($this->category_id));
		$this->modified=htmlspecialchars(strip_tags($this->modified));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":price", $this->price);
		$stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":bio", $this->bio);
		$stmt->bindParam(":category_id", $this->category_id);
		$stmt->bindParam(":modified", $this->modified);
		$stmt->bindParam(":id", $this->id);
	 
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
	
	//advanced search products
	function search_filter($location, $category, $price, $rating){
		//select all query
        $query = "SELECT
                  c.name AS category_name, p.id, p.name, p.cover, p.location, p.ratings, p.reviews, p.price, p.category_id, p.created
                  FROM " . $this->table_name. " p
                  LEFT JOIN
                    categories c ON p.category_id = c.id";
		if($location) {
		//$location_data = implode("','", $_POST["location"]);
		$query .= "
		WHERE p.location LIKE '%". $location ."%'";
		}
		if($category){
		//$ramFilterData = implode("','", $_POST["ram"]);
		$query .= "
		AND p.category_id IN('".$category."')";
		}
		if($price) {
		//$storageFilterData = implode("','", $_POST["storage"]);
		$query .= "
		AND p.price IN('".$price."')";
		}
		if($rating) {
		//$storageFilterData = implode("','", $_POST["storage"]);
		$query .= "
		AND p.rating IN('".$rating."')";
		}
		$query .= " ORDER By p.created";

        //prepare
        $stmt =$this->conn->prepare($query);

        //execute
        $stmt->execute();

        return $stmt;
	}
	
    //read products with pagination
    public function readPaging($from_record_num, $records_per_page){

        //select
        $query = "SELECT
                  c.name AS category_name, p.id, p.name, p.location, p.ratings, p.reviews, p.price, p.category_id, p.created
                  FROM " . $this->table_name . " p
                  LEFT JOIN
                    categories c ON p.category_id = c.id
                  ORDER BY p.created DESC
                  LIMIT ?, ?";

        //prepare
        $stmt = $this->conn->prepare($query);

        //bind
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);

        //execute
        $stmt->execute();

        //return values from db
        return $stmt;
    }

    //paging products
    public function count(){
        $query = "SELECT COUNT(*) as total_rows FROM " . $this->table_name . "";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row['total_rows'];

    }

    // update cover
    public function update_cover(){
	 
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
				SET
                cover = :cover
				WHERE id = :id";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->cover=htmlspecialchars(strip_tags($this->cover));
	 
		// bind the values from the form
		$stmt->bindParam(':cover', $this->cover);
		
		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

    // check vendor with same id is exist
    public function checkIfVendorExist($uid){
        // query to check if email exists
		$query = "SELECT id
        FROM " . $this->table_name . "
        WHERE user_id = ".$uid."
        LIMIT 0,1";

        // prepare the query
        $stmt = $this->conn->prepare( $query );

        // sanitize
        $this->id=htmlspecialchars(strip_tags($this->id));

        // bind given email value
        $stmt->bindParam(1, $this->id);

        // execute the query
        $stmt->execute();

        // get number of rows
        $num = $stmt->rowCount();

        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
	 
			// get record details / values
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
	 
			// assign values to object properties
			$this->id = $row['id'];
	 
			// return true because email exists in the database
			return true;
		}

        // return false if email does not exist in the database
        return false;
    }
}
