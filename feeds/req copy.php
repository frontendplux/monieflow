<?php
    include __DIR__."/../main-function.php";
    class feeds extends Main{
        public function __construct($conn) { 
            parent::__construct($conn); 
        }

        public function getReel($data){
            $smt=$this->conn->prepare("select * from reels order by id desc limit ?,  25");
            $smt->bind_param('i', $data['limit']);
            $smt->execute();
            $res=$smt->get_result();
            $reel=[];
            while($row=$res->fetch_assoc()){
                $reel[]=json_decode($row['data'],true);
            }
            return $reel;
        }
        public function createReel($data){
            // $smt=new 
        }
    }
    
?>