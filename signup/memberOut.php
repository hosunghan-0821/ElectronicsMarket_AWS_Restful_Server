<?php 


    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    
    if(isset($_GET['email'])){
        $email=$_GET['email'];
        
        $sql="DELETE FROM Market_member WHERE Member_id='$email'";
        $deleteResult=mysqli_query($db_connect,$sql);
        if($deleteResult){
            $arr['message']="회원탈퇴";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
        else{
            $arr['message']="실패";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }

    }

?>