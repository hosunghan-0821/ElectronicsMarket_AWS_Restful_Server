<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    if(isset($_POST['email'])){
        $id=$_POST['email'];
        $standardPassword=$_POST['password'];
        $newPassword=$_POST['newPassword'];

        $sql="SELECT Member_password FROM Market_member where Member_id='$id'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        if($Data['Member_password']===$standardPassword){
            $sql="UPDATE Market_member SET Member_password='$newPassword' where Member_id='$id'";
            $updateResult=mysqli_query($db_connect,$sql);
            if($updateResult){
                $arr['isSuccess']=true;
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                return;
            }
            else{
                $arr['isSuccess']=false;
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                return;
            }

        }
        else{
            $arr['isSuccess']=false;
            $arr['message']="기존 비밀번호 다름";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
        
    }
?>
