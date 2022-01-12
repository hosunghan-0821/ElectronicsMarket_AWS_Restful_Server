<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/lib/dbConnect.php';
    $arr=array();
    if(isset($_POST['nickname'])){
        $nickname=$_POST['nickname'];

        $sql="SELECT * FROM Market_member WHERE Member_nickname='$nickname'";
        $selectResult=mysqli_query($db_connect,$sql);
        if(mysqli_num_rows($selectResult)>=1){
            $arr['message']="닉네임 중복";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
        else{
            $arr['message']="닉네임 사용가능";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);

        }
    }
    else{
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
    
?>