<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    
    if(isset($_POST['email'])){

        $id=$_POST['email'];
        $nickname=$_POST['nickname'];
        $sql="SELECT * FROM Market_member WHERE Member_nickname='$nickname'";
        $selectResult=mysqli_query($db_connect,$sql);
        if(mysqli_num_rows($selectResult)>=1){
            $arr['isSuccess']=false;
            $arr['message']="닉네임 중복";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
        else{
            $sql="UPDATE Market_member SET Member_nickname='$nickname' WHERE Member_id='$id'";
            $updateResult=mysqli_query($db_connect,$sql);
            if($updateResult){
                $arr['isSuccess']=true;
                $arr['message']="닉네임 변경";
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
            else{
                $arr['message']="DB 수정실패";
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }
        }

    }
?>