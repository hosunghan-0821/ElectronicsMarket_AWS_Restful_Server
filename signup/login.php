<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    if(isset($_POST['id'])&&isset($_POST['password'])){

        $id=$_POST['id'];
        $password=$_POST['password'];

        $sql="SELECT Member_id, Member_password FROM Market_member Where Member_id='$id'";
        $selectResult= mysqli_query($db_connect,$sql);
        $Data= mysqli_fetch_array($selectResult);

        if($Data!=null){

            if($Data['Member_password']===$password){
                $arr['message']='로그인 성공';
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
            else{
                $arr['message']='로그인 실패';
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
        }

        else{
            $arr['message']='로그인 실패';
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
        
    }
    else{
        $arr['message']='회원정보 부족 실패';
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);

    }

?>