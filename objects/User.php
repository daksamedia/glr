<?php
/**
*contains properties and methods for "product" database queries.
 */

class User
{

    //Db connection and table
    private $conn;
    private $table_name = 'users';

    // object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
	public $access_code;
	public $status;
    public $password;
	public $new_password;
	public $phone;
	public $likes;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
	
	// create new user record
	function create(){
	 
		// insert query
		$query = "INSERT INTO " . $this->table_name . "
				SET
					firstname = :firstname,
					lastname = :lastname,
					email = :email,
					access_code = :access_code,
					password = :password,
					phone = :phone";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->firstname=htmlspecialchars(strip_tags($this->firstname));
		$this->lastname=htmlspecialchars(strip_tags($this->lastname));
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->password=htmlspecialchars(strip_tags($this->password));
		$this->phone=htmlspecialchars(strip_tags($this->phone));
		$this->access_code=htmlspecialchars(strip_tags($this->access_code));
	 
		// bind the values
		$stmt->bindParam(':firstname', $this->firstname);
		$stmt->bindParam(':lastname', $this->lastname);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':phone', $this->phone);
		$stmt->bindParam(':access_code', $this->access_code);
	 
		// hash the password before saving to database
		$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
		$stmt->bindParam(':password', $password_hash);
	 
		// execute the query, also check if query was successful
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	// check if given email exist in the database
	function emailExists(){
	 
		// query to check if email exists
		$query = "SELECT id, firstname, lastname, password
				FROM " . $this->table_name . "
				WHERE email = ?
				LIMIT 0,1";
	 
		// prepare the query
		$stmt = $this->conn->prepare( $query );
	 
		// sanitize
		$this->email=htmlspecialchars(strip_tags($this->email));
	 
		// bind given email value
		$stmt->bindParam(1, $this->email);
	 
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
			$this->firstname = $row['firstname'];
			$this->lastname = $row['lastname'];
			$this->password = $row['password'];
	 
			// return true because email exists in the database
			return true;
		}
	 
		// return false if email does not exist in the database
		return false;
	}

	//check if user is active
	function isActive(){
	 
		// query to check if email exists
		$query = "SELECT id, firstname, lastname, password
				FROM " . $this->table_name . "
				WHERE email = ?
				AND status = 1
				LIMIT 0,1";
	 
		// prepare the query
		$stmt = $this->conn->prepare( $query );
	 
		// sanitize
		$this->email=htmlspecialchars(strip_tags($this->email));
	 
		// bind given email value
		$stmt->bindParam(1, $this->email);
	 
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
			$this->firstname = $row['firstname'];
			$this->lastname = $row['lastname'];
			$this->password = $row['password'];
	 
			// return true because email exists in the database
			return true;
		}
	 
		// return false if email does not exist in the database
		return false;
	}
	
	//read user account
    function readOne(){

        //read single record
        $query = "SELECT
                p.id, p.email, p.firstname, p.lastname, p.avatar, p.phone, p.address, p.bio, p.modified, p.created
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
        $this->email=$row['email'];
        $this->id=$row['id'];
        $this->firstname=$row['firstname'];
        $this->lastname=$row['lastname'];
        $this->avatar=$row['avatar'];
		$this->phone=$row['phone'];
		$this->address=$row['address'];
		$this->bio=$row['bio'];

    }
	
	//read user likes
    function readLikes(){

        //read single record
        $query = "SELECT
                p.id, p.likes
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
        $this->likes=$row['likes'];

    }
	
	// check if given access_code exist in the database
	function accessCodeExists(){
	 
		// query to check if access_code exists
		$query = "SELECT id
				FROM " . $this->table_name . "
				WHERE access_code = ?
				LIMIT 0,1";
	 
		// prepare the query
		$stmt = $this->conn->prepare( $query );
	 
		// sanitize
		$this->access_code=htmlspecialchars(strip_tags($this->access_code));
	 
		// bind given access_code value
		$stmt->bindParam(1, $this->access_code);
	 
		// execute the query
		$stmt->execute();
	 
		// get number of rows
		$num = $stmt->rowCount();
	 
		// if access_code exists
		if($num>0){
	 
			// return true because access_code exists in the database
			return true;
		}
	 
		// return false if access_code does not exist in the database
		return false;
	 
	}
	
	// update access code for reset password
	function updateAccessCode(){
	 
		// update query
		$query = "UPDATE
					" . $this->table_name . "
				SET
					access_code = :access_code
				WHERE
					email = :email";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->access_code=htmlspecialchars(strip_tags($this->access_code));
		$this->email=htmlspecialchars(strip_tags($this->email));
	 
		// bind the values from the form
		$stmt->bindParam(':access_code', $this->access_code);
		$stmt->bindParam(':email', $this->email);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	// used in email verification feature
	function updateStatusByAccessCode(){
 
		// update query
		$query = "UPDATE " . $this->table_name . "
				SET status = :status
				WHERE access_code = :access_code";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->status=htmlspecialchars(strip_tags($this->status));
		$this->access_code=htmlspecialchars(strip_tags($this->access_code));
	 
		// bind the values from the form
		$stmt->bindParam(':status', $this->status);
		$stmt->bindParam(':access_code', $this->access_code);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	// used for reset password
	function updatePassword(){
	 
		// update query
		$query = "UPDATE " . $this->table_name . "
				SET password = :password
				WHERE access_code = :access_code";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->password=htmlspecialchars(strip_tags($this->password));
		$this->access_code=htmlspecialchars(strip_tags($this->access_code));
	 
		// bind the values from the form
		$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
		$stmt->bindParam(':password', $password_hash);
		$stmt->bindParam(':access_code', $this->access_code);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

	// update a user record
	public function changePassword(){
	 
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
				SET password = :password
				WHERE id = :id";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->password=htmlspecialchars(strip_tags($this->password));

		// bind the values from the form
		$password_hash = password_hash($this->new_password, PASSWORD_BCRYPT);
		$stmt->bindParam(':password', $password_hash);
	 
		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

	// update user likes
	function updateLikes(){
	 
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
				SET
					likes = :likes
				WHERE id = :id";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->likes=htmlspecialchars(strip_tags($this->likes));
	 
		// bind the values from the form
		$stmt->bindParam(':likes', $this->likes);
	 
		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

	// update a user record
	function update(){
	 
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
				SET
					firstname = :firstname,
					lastname = :lastname,
					email = :email,
					phone = :phone,
					bio = :bio,
					address = :address
				WHERE id = :id";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->firstname=htmlspecialchars(strip_tags($this->firstname));
		$this->lastname=htmlspecialchars(strip_tags($this->lastname));
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->phone=htmlspecialchars(strip_tags($this->phone));
		$this->bio=htmlspecialchars(strip_tags($this->bio));
		$this->address=htmlspecialchars(strip_tags($this->address));
	 
		// bind the values from the form
		$stmt->bindParam(':firstname', $this->firstname);
		$stmt->bindParam(':lastname', $this->lastname);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':phone', $this->phone);
		$stmt->bindParam(':bio', $this->bio);
		$stmt->bindParam(':address', $this->address);
		
		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}
	
	// update a user record
	public function updateWithPass(){
	 
		// if password needs to be updated
		$password_set=!empty($this->password) ? ", password = :password" : "";
	 
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
				SET
					firstname = :firstname,
					lastname = :lastname,
					email = :email
					phone = :phone
					{$password_set}
				WHERE id = :id";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->firstname=htmlspecialchars(strip_tags($this->firstname));
		$this->lastname=htmlspecialchars(strip_tags($this->lastname));
		$this->email=htmlspecialchars(strip_tags($this->email));
		$this->phone=htmlspecialchars(strip_tags($this->phone));
	 
		// bind the values from the form
		$stmt->bindParam(':firstname', $this->firstname);
		$stmt->bindParam(':lastname', $this->lastname);
		$stmt->bindParam(':email', $this->email);
		$stmt->bindParam(':phone', $this->phone);
	 
		// hash the password before saving to database
		if(!empty($this->password)){
			$this->password=htmlspecialchars(strip_tags($this->password));
			$password_hash = password_hash($this->password, PASSWORD_BCRYPT);
			$stmt->bindParam(':password', $password_hash);
		}
	 
		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

	// update user's avatar
	public function update_avatar(){
	 
		// if no posted password, do not update the password
		$query = "UPDATE " . $this->table_name . "
				SET
					avatar = :avatar
				WHERE id = :id";
	 
		// prepare the query
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$this->avatar=htmlspecialchars(strip_tags($this->avatar));
	 
		// bind the values from the form
		$stmt->bindParam(':avatar', $this->avatar);
		
		// unique ID of record to be edited
		$stmt->bindParam(':id', $this->id);
	 
		// execute the query
		if($stmt->execute()){
			return true;
		}
	 
		return false;
	}

}