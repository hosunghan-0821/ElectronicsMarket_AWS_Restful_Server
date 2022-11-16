<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/lib/dbConnect.php';

    $arr=array();
    if(isset($_POST['phone'])){

        $phone=$_POST['phone'];
        $sql = "DELETE FROM new_table WHERE phone='$phone'";
        
        $sqlResult=mysqli_query($db_connect,$sql);
        if($sqlResult){
            $arr['phone']=$phone;
            $arr['message']="성공";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }

     
        
    }
    else{
        $arr['message']="실패";
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
?>