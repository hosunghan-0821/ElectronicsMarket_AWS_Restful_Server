<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    if(isset($_POST['postNum'])){
        $postNum=$_POST['postNum'];
        $nowDate = date("Y-m-d H:i:s");
        $sql="UPDATE Post SET Post_status='S',Post_trade_confirm_time='$nowDate' where Post_no='$postNum'";
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
?>