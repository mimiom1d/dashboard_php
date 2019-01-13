<?php

class Database{

    //** specify database credentials */
    private $host = "localhost";
    private $db_name = "jplangmd";
    private $username = "root";
    private $password = "";
    public $conn;

    function __construct(){
        // print __CLASS__ . " Initiated.\n";
    }
    function __destruct() {
        // print "Destroying " . __CLASS__ . "\n";
    }
    //** get the database connection */
    public function getConnection(){
        $this->conn = null;

        try{
            $this->conn = new PDO("mysql:host=".$this->host.";dbname=".$this->db_name, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}