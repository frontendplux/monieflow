<?php

   class auth{
    //   private $conn;
      public function __construct($conn) {
         $this->conn = $conn;
      }
      public function login($data) {
         $username = $data['username'] ?? '';
         $password = $data['password'] ?? '';
         if(empty($username) || empty($password)) return ['success' => false, 'message' => 'Username and password are required'];
         $smt = $this->conn->prepare("SELECT * FROM users WHERE username=? or email=? or phone=? limit 1");
         $smt->execute([$username, $username, $username]);
         $result = $smt->get_result();
         if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if(password_verify($password, $user['password'])) {
                $uid = uniqid(explode('@', $user['email'])[0].'_').'-'.$user['id'];
                $update = $this->conn->prepare("UPDATE users SET uid=? WHERE id=?");
                $update->execute([$uid, $user['id']]);
                return ['success' => true, 'user' => $uid];
            } 
            else {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            } else {
                return ['success' => false, 'message' => 'User not found'];
            }
      }

     public function register($data) {
         $firstname = $data['firstname'] ?? '';
         $lastname = $data['lastname'] ?? '';
         $email = $data['email'] ?? '';
         $phone = $data['phone'] ?? '';
         $dob= $data['dob'] ?? '';
         $gender= $data['gender'] ?? '';
         $password = $data['password'] ?? '';
         if(empty($firstname) || empty($lastname) || empty($email) || empty($phone) || empty($password) || empty($dob) || empty($gender)) return ['success' => false, 'message' => 'All fields are required'];
         $smt = $this->conn->prepare("SELECT * FROM users WHERE email=? limit 1");
         $smt->execute([$email]);
         $result = $smt->get_result();
         if($result->num_rows > 0) {
            return ['success' => false, 'message' => 'email already exists try login or use another email'];
         } else {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $insert = $this->conn->prepare("INSERT INTO users (firstname, lastname, email, phone, dob, sex, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert->execute([$firstname, $lastname, $email, $phone, $dob, $gender, $password]);
            $uid = uniqid(explode('@', $email)[0].'_').'-'. $insert->insert_id;
            $update = $this->conn->prepare("UPDATE users SET uid=? WHERE id=?");
            $update->execute([$uid, $insert->insert_id]);
            return ['success' => true, 'user' => $uid, 'message' => 'User registered successfully'];
         }
      }

      public function myProfile($data) {
        $user_id= explode('-',$data)[1] ?? '';
        $uid=$data ?? '';
        if(empty($user_id) || empty($uid)) return['success' => false, 'message' => 'Invalid user ID'];
        $smt=$this->conn->prepare("SELECT * FROM users WHERE id=? AND uid=? limit 1");
        $smt->execute([$user_id,$uid]);
        $result=$smt->get_result();
        if($result->num_rows > 0){
            $user=$result->fetch_assoc();
            return ['success' => true, 'data' => $user];
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
      }

    public function userProfile($data) {
        $user_id= $data ?? '';
        if(empty($user_id)) return['success' => false, 'message' => 'Invalid user ID'];
        $smt=$this->conn->prepare("SELECT * FROM users WHERE id=? limit 1");
        $smt->execute([$user_id]);
        $result=$smt->get_result();
        if($result->num_rows > 0){
            $user=$result->fetch_assoc();
            return ['success' => true, 'data' => $user];
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
      }

      public function userAuth($data) {
        $user_id= $data['id'] ?? '';
        $token= $data['token'].'-'.$user_id ?? '';
        if(empty($user_id) || empty($token)) return['success' => false, 'message' => 'Invalid user ID or token'];
        $smt=$this->conn->prepare("SELECT * FROM users WHERE id=? AND uid=? limit 1");
        $smt->execute([$user_id, $token]);
        $result=$smt->get_result();
        if($result->num_rows > 0){
            $user=$result->fetch_assoc();
            return ['success' => true, 'data' => $user];
        } else {
            return ['success' => false, 'message' => 'User not found'.$token];
        }
      }

   }