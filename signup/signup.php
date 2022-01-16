<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
  
    $asd=123;
    if(isset($_POST['id'])){

        $id=$_POST['id'];
        $password=$_POST['password'];
        $nickname=$_POST['nickname'];
       
        $sql="INSERT INTO Market_member (Member_id,Member_password,Member_nickname) Values('$id','$password','$nickname')";
        $insertResult=mysqli_query($db_connect,$sql);
        
        if($insertResult){
            $arr['message']="회원가입 성공";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }

        else{
            $arr['message']="회원가입 실패";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }
    else{

        $arr['message']="회원 정보 전달 안됨";
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }

    $asd;



?>