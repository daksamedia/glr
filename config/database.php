<?php
/**
 *  file used for connecting to the database.
 */

class Database {


    //Db credentials
    private $host = 'localhost';
    private $db_name = 'gelaro_id';
    private $username = 'root';
    private $password = '';

    public $conn;
	
	

    //Db connection
    /**
     * @return mixed
     */
    public function getConnection()
    {
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        }catch (PDOException $exception){
            echo "Connection error: ". $exception->getMessage();
        }
        return $this->conn;
    }
}
