<?php

/**
 * User Class
 */
class User{
     
    //** database connection and table name */
    private $conn;
    private $table_name = "users";

    //**object properties
    public $id;
    public $firstname;
    public $lastname;
    public $email;
    public $contact_number;
    public $address;
    public $password;
    public $access_level;
    public $access_code;
    public $status;
    public $created;
    public $modified;
 
    //**constructor
    public function __construct($conn){
        $this->conn = $conn;
    }
    function safeinputs($string){
        return trim(htmlspecialchars($string, ENT_QUOTES, "UTF-8"));
    }
    function showAddress(){
        // return $this->address;

        $query = "SELECT address from $this->table_name WHERE email = ?";
        $testemail = "12345#$@gmail.com";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $testemail);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
        $num = $stmt->rowCount();
        return $address;
    }
    //**check if given email exist in the database
    function emailExists($email=null){
    
        //**query to check if email exists
        $query = "SELECT id, firstname, lastname, access_level, password, status
                FROM $this->table_name 
                WHERE email = ?
                LIMIT 0,1";
    
        //**prepare the query
        $stmt = $this->conn->prepare( $query );
    
        //**sanitize
        $this->email= (!empty($email)) ? safeinputs($email, ENT_QUOTES, "UTF-8") : safeinputs($this->email);
    
        //**bind given email value
        $stmt->bindParam(1, $this->email);
    
        //**execute the query
        $stmt->execute();
    
        //**get number of rows
        $num = $stmt->rowCount();
    
        //**if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            //**get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
            //**assign values to object properties
            $this->id = $id;
            $this->firstname = $firstname;
            $this->lastname = $lastname;
            $this->access_level = $access_level;
            $this->password = $password;
            $this->status = $status;
            //** the default original way but more bloated */
            // $this->id = $row['id'];
            // $this->firstname = $row['firstname'];
            // $this->lastname = $row['lastname'];
            // $this->access_level = $row['access_level'];
            // $this->password = $row['password'];
            // $this->status = $row['status'];
    
            //**return true because email exists in the database
            return true;
        }
    
        //**return false if email does not exist in the database
        return false;
    }
    //**create new user record
    function create(){
    
        //**to get time stamp for 'created' field
        $this->created=date('Y-m-d H:i:s');
    
        //**insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
            firstname = :firstname,
            lastname = :lastname,
            email = :email,
            contact_number = :contact_number,
            address = :address,
            password = :password,
            access_level = :access_level,
                    access_code = :access_code,
            status = :status,
            created = :created";
    
        //**prepare the query
        $stmt = $this->conn->prepare($query);
    
        //**sanitize
        $this->firstname = safeinputs($this->firstname);
        $this->lastname = safeinputs($this->lastname);
        $this->email = safeinputs($this->email);
        $this->contact_number = safeinputs($this->contact_number);
        $this->address = safeinputs($this->address);
        $this->password = safeinputs($this->password);
        $this->access_level = safeinputs($this->access_level);
        $this->access_code = safeinputs($this->access_code);
        $this->status = safeinputs($this->status);
    
        //**bind the values
        $stmt->bindParam(':firstname', $this->firstname);
        $stmt->bindParam(':lastname', $this->lastname);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':address', $this->address);
    
        //**hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        $stmt->bindParam(':access_level', $this->access_level);
        $stmt->bindParam(':access_code', $this->access_code);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':created', $this->created);
    
        //**execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }else{
            $this->showError($stmt);
            return false;
        }
    
    }
    
    //**show Error on query execution
    public function showError($stmt){
        echo "<pre>";
            print_r($stmt->errorInfo());
        echo "</pre>";
    }

    //**read all user records
    function readAll($from_record_num, $records_per_page){
    
        //**query to read all user records, with limit clause for pagination
        $query = "SELECT
                    id,
                    firstname,
                    lastname,
                    email,
                    contact_number,
                    address,
                    access_level,
                    created
                FROM " . $this->table_name . "
                ORDER BY id DESC
                LIMIT ?, ?";
    
        //**prepare query statement
        $stmt = $this->conn->prepare( $query );
    
        //**bind limit clause variables
        $stmt->bindParam(1, $from_record_num, PDO::PARAM_INT);
        $stmt->bindParam(2, $records_per_page, PDO::PARAM_INT);
    
        //**execute query
        $stmt->execute();
    
        //**return values
        return $stmt;
    }

    //**used for paging users
    public function countAll(){
    
        //**query to select all user records
        $query = "SELECT id FROM $this->table_name";
    
        //**prepare query statement
        $stmt = $this->conn->prepare($query);
    
        //**execute query
        $stmt->execute();
    
        //**get number of rows
        $num = $stmt->rowCount();
    
        //**return row count
        return $num;
    }

    //** function to validate the name inputs
    public function validName($name){
        if (!preg_match('/[^A-Za-z]/', $name)) {//**'/[^a-z\d]/i' should also work. 
        //**string contains only english letters
            return true;
        }
        return false;
    }

    //** function to validate phone number
    public function validPhone($phone){
        // ** we only allow numbers, +, -, and x
        if(preg_match('/^[0-9+x()-]+$/', $phone)){
            return true;
        }
        return false;
    }

    //**used in email verification feature
    function updateStatusByAccessCode(){
    
        //**update query
        $query = "UPDATE $this->table_name 
                SET status = :status
                WHERE access_code = :access_code";
    
        //**prepare the query
        $stmt = $this->conn->prepare($query);
    
        //**sanitize
        $this->status = safeinputs($this->status);
        $this->access_code = htmlspecialchars($this->access_code);
    
        //**bind the values from the form
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':access_code', $this->access_code);
    
        //**execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
}