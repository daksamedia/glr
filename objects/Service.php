<?php
/**
*contains properties and methods for "product" database queries.
 */

class Service	
{

    //Db connection and table
    private $conn;
    private $table_name = 'services';

    //Object properties
    public $id;
    public $vendor_id;
    public $title;
    public $description;
    public $price;
    public $image;
    public $created;
    public $modified;

    //Constructor with db conn
    public function __construct($db)
    {
        $this->conn = $db;
    }


    //Read product
    function read($v_id){

       //select all
        $query = "SELECT `id`, `vendor_id`, `title`, `description`, `price`, `image`, `created`, `modified` FROM " . $this->table_name . " WHERE vendor_id =". $v_id ."";

        //prepare
        $stmt = $this->conn->prepare($query);

        //execute
        $stmt->execute();

        return $stmt;

    }


    //read single product
    function readOne(){

        //read single record
        $query = "SELECT
                p.id, p.vendor_id, p.title, p.description, p.price, p.image, p.modified, p.created
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
        $this->id=$row['id'];
        $this->title=$row['title'];
        $this->description=$row['description'];
        $this->price=$row['price'];
        $this->image=$row['image'];

    }

	//create product
	function create(){

		//query insert
		$query = "INSERT INTO
				  ". $this->table_name ."
				  SET
					 vendor_id=:vendor_id, title=:title, description=:description, price=:price, image=:image, created=:created";

		//Prepare
		$stmt = $this->conn->prepare($query);

		//sanitize
		$this->vendor_id=htmlspecialchars(strip_tags($this->vendor_id));
		$this->title=htmlspecialchars(strip_tags($this->title));
		$this->description=htmlspecialchars(strip_tags($this->description));
		$this->price=htmlspecialchars(strip_tags($this->price));
		$this->image=htmlspecialchars(strip_tags($this->image));
		$this->created=htmlspecialchars(strip_tags($this->created));

		//Bind values
		$stmt->bindParam(":vendor_id", $this->vendor_id);
		$stmt->bindParam(":title", $this->title);
		$stmt->bindParam(":description", $this->description);
		$stmt->bindParam(":price", $this->price);
		$stmt->bindParam(":image", $this->image);
		$stmt->bindParam(":created", $this->created);

		//execute
		if($stmt->execute()){
			return true;
		}
		return false;
	}

    // update the product
	function update(){
	 
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					title = :title,
					price = :price,
					description = :description,
					image = :image,
					modified = :modified
				WHERE
					id = :id";
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->title=htmlspecialchars(strip_tags($this->title));
		$this->price=htmlspecialchars(strip_tags($this->price));
		$this->description=htmlspecialchars(strip_tags($this->description));
		$this->image=htmlspecialchars(strip_tags($this->image));
		$this->modified=htmlspecialchars(strip_tags($this->modified));
		$this->id=htmlspecialchars(strip_tags($this->id));
	 
		// bind new values
		$stmt->bindParam(':title', $this->title);
		$stmt->bindParam(':price', $this->price);
		$stmt->bindParam(':description', $this->description);
		$stmt->bindParam(':image', $this->image);
		$stmt->bindParam(':modified', $this->modified);
		$stmt->bindParam(':id', $this->id);
	 
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
                  c.name AS category_name, p.id, p.name, p.location, p.ratings, p.reviews, p.price, p.category_id, p.created
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
