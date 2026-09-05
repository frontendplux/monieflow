<?php

class mF{
    public mysqli $db;
    public function __construct($conn){
        $this->db = $conn;
    }
    public function Login($username, $password){
        $stmt = $this->db->prepare("
                                 SELECT * FROM users WHERE username
                                  = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}