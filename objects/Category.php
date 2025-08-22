<?php
/**
 * contains properties and methods for "category" database queries.
 */

class Category
{
    //db conn and table
    private $conn;
    private $table_name = "categories";

    //object properties
    public $id;
    public $name;
    public $description;
    public $created;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    //used by select drop-down list
    public function readAll(){

        $query = "SELECT
                    id, name, description, icon
                  FROM " . $this->table_name . " ORDER BY name";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;

    }

    //used by select drop-down list
    public function read(){

        $query = "SELECT
                    id, name, description, icon
                 FROM " . $this->table_name . "
                 ORDER BY name";

        $stmt=$this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
	
	//Read category by id
    function readOne(){
		
		//read single record
        $query = "SELECT
                *
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
        $this->icon=$row['icon'];
        $this->id=$row['id'];

    }
}
