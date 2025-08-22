<?php
/**
*contains properties and methods for "product" database queries.
 */

class Venue
{

    //Db connection and table
    private $conn;
    private $table_name = 'venues';

    //Object properties
    public $id;
    public $name;
    public $description;
    public $images;
    public $large_num;
    public $capacity;
    public $composition;
    public $electricity;
    public $parking_lot;
    public $rooms_num;
    public $toilets_num;
    public $prayer_room;
    public $location;
    public $available_status;
    public $price;
    public $ratings;
    public $reviews;
    public $created;

    //Constructor with db conn
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //Read venue
    function read(){

        //select all
        $query = "SELECT
                    p.id, p.name, p.description, p.images, p.large_num, p.capacity, p.composition,  p.electricity, p.parking_lot, p.rooms_num, p.toilets_num, p.prayer_room, p.location, p.available_status, p.price, p.ratings, p.reviews, p.created
                  FROM
                  " . $this->table_name . " p
                  ORDER BY
                    p.created DESC";

        //prepare
        $stmt = $this->conn->prepare($query);

        //execute
        $stmt->execute();

        return $stmt;

    }


    //read single venue
    function readOne(){

        //read single record
        $query = "SELECT
                p.id, p.name, p.description, p.images, p.large_num, p.capacity, p.composition,  p.electricity, p.parking_lot, p.rooms_num, p.toilets_num, p.prayer_room, p.location, p.available_status, p.price, p.ratings, p.reviews, p.created
				FROM
                " . $this->table_name . " p
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
        $this->description=$row['description'];
        $this->images=$row['images'];
        $this->large_num=$row['large_num'];
        $this->capacity=$row['capacity'];
        $this->composition=$row['composition'];
        $this->electricity=$row['electricity'];
        $this->parking_lot=$row['parking_lot'];
        $this->rooms_num=$row['rooms_num'];
        $this->toilets_num=$row['toilets_num'];
        $this->prayer_room=$row['prayer_room'];
        $this->location=$row['location'];
        $this->available_status=$row['available_status'];
        $this->price=$row['price'];
        $this->ratings=$row['ratings'];
        $this->reviews=$row['reviews'];
		
    }

	//create venue
	function create(){
		
		//query insert
		$query = "INSERT INTO
				  ". $this->table_name ."
				  SET
					name=:name, description=:description, location=:location, ratings=:ratings, reviews=:reviews, images=:images,  large_num=:large_num,  capacity=:capacity,  composition=:composition,  parking_lot=:parking_lot,  electricity=:electricity,  rooms_num=:rooms_num,  toilets_num=:toilets_num,  prayer_room=:prayer_room,  available_status=:available_status, price=:price, created=:created";

		//Prepare
		$stmt = $this->conn->prepare($query);

		//sanitize
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->price=htmlspecialchars(strip_tags($this->price));
		$this->location=htmlspecialchars(strip_tags($this->location));
		$this->ratings=htmlspecialchars(strip_tags($this->ratings));
		$this->reviews=htmlspecialchars(strip_tags($this->reviews));
		$this->description=htmlspecialchars(strip_tags($this->description));
		$this->images=htmlspecialchars(strip_tags($this->images));
		$this->capacity=htmlspecialchars(strip_tags($this->capacity));
		$this->large_num=htmlspecialchars(strip_tags($this->large_num));
		$this->composition=htmlspecialchars(strip_tags($this->composition));
		$this->electricity=htmlspecialchars(strip_tags($this->electricity));
		$this->parking_lot=htmlspecialchars(strip_tags($this->parking_lot));
		$this->rooms_num=htmlspecialchars(strip_tags($this->rooms_num));
		$this->toilets_num=htmlspecialchars(strip_tags($this->toilets_num));
		$this->prayer_room=htmlspecialchars(strip_tags($this->prayer_room));
		$this->available_status=htmlspecialchars(strip_tags($this->available_status));
		$this->created=htmlspecialchars(strip_tags($this->created));
		
		//Bind values
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":price", $this->price);
		$stmt->bindParam(":location", $this->location);
		$stmt->bindParam(":ratings", $this->ratings);
		$stmt->bindParam(":reviews", $this->reviews);
		$stmt->bindParam(":description", $this->description);
		$stmt->bindParam(":images", $this->images);
		$stmt->bindParam(":large_num", $this->large_num);
		$stmt->bindParam(":capacity", $this->capacity);
		$stmt->bindParam(":composition", $this->composition);
		$stmt->bindParam(":electricity", $this->electricity);
		$stmt->bindParam(":parking_lot", $this->parking_lot);
		$stmt->bindParam(":rooms_num", $this->rooms_num);
		$stmt->bindParam(":toilets_num", $this->toilets_num);
		$stmt->bindParam(":prayer_room", $this->prayer_room);
		$stmt->bindParam(":available_status", $this->available_status);
		$stmt->bindParam(":created", $this->created);

		//execute
		if($stmt->execute()){
			return true;
		}
		return false;
	}

    // update the venue
	function update(){
	 
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					name=:name, description=:description, location=:location, ratings=:ratings, reviews=:reviews, images=:images,  large_num=:large_num,  capacity=:capacity,  composition=:composition,  parking_lot=:parking_lot,  electricity=:electricity,  rooms_num=:rooms_num,  toilets_num=:toilets_num,  prayer_room=:prayer_room,  available_status=:available_status, price=:price, modified=:modified
				WHERE
					id = :id";	
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->id=htmlspecialchars(strip_tags($this->id));
		$this->name=htmlspecialchars(strip_tags($this->name));
		$this->price=htmlspecialchars(strip_tags($this->price));
		$this->location=htmlspecialchars(strip_tags($this->location));
		$this->ratings=htmlspecialchars(strip_tags($this->ratings));
		$this->reviews=htmlspecialchars(strip_tags($this->reviews));
		$this->description=htmlspecialchars(strip_tags($this->description));
		$this->images=htmlspecialchars(strip_tags($this->images));
		$this->capacity=htmlspecialchars(strip_tags($this->capacity));
		$this->large_num=htmlspecialchars(strip_tags($this->large_num));
		$this->composition=htmlspecialchars(strip_tags($this->composition));
		$this->electricity=htmlspecialchars(strip_tags($this->electricity));
		$this->parking_lot=htmlspecialchars(strip_tags($this->parking_lot));
		$this->rooms_num=htmlspecialchars(strip_tags($this->rooms_num));
		$this->toilets_num=htmlspecialchars(strip_tags($this->toilets_num));
		$this->prayer_room=htmlspecialchars(strip_tags($this->prayer_room));
		$this->available_status=htmlspecialchars(strip_tags($this->available_status));
		$this->modified=htmlspecialchars(strip_tags($this->modified));
	 
		// bind new values
		$stmt->bindParam(":id", $this->id);
		$stmt->bindParam(":name", $this->name);
		$stmt->bindParam(":price", $this->price);
		$stmt->bindParam(":location", $this->location);
		$stmt->bindParam(":ratings", $this->ratings);
		$stmt->bindParam(":reviews", $this->reviews);
		$stmt->bindParam(":description", $this->description);
		$stmt->bindParam(":images", $this->images);
		$stmt->bindParam(":large_num", $this->large_num);
		$stmt->bindParam(":capacity", $this->capacity);
		$stmt->bindParam(":composition", $this->composition);
		$stmt->bindParam(":electricity", $this->electricity);
		$stmt->bindParam(":parking_lot", $this->parking_lot);
		$stmt->bindParam(":rooms_num", $this->rooms_num);
		$stmt->bindParam(":toilets_num", $this->toilets_num);
		$stmt->bindParam(":prayer_room", $this->prayer_room);
		$stmt->bindParam(":available_status", $this->available_status);
		$stmt->bindParam(":modified", $this->modified);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

    //delete venue
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

	//search venue
    function search($keywords){

        //select all query
        $query = "SELECT
                  p.id, p.name, p.description, p.images, p.large_num, p.capacity, p.composition,  p.electricity, p.parking_lot, p.rooms_num, p.toilets_num, p.prayer_room, p.location, p.available_status, p.price, p.ratings, p.reviews, p.created
                  FROM " . $this->table_name. " p
                  WHERE
                    p.name LIKE ? OR p.location LIKE ?
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
}
