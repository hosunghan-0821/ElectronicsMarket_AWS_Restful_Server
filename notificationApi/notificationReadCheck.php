<?php
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    
    if(isset($_POST['nickname'])){

        $nickname=$_POST['nickname'];
        $sql="SELECT * FROM Notification_collect where Notification_member='$nickname' and Notification_is_read='0'";
        $selectResult=mysqli_query($db_connect,$sql);
        if(mysqli_num_rows($selectResult)>0){
            $arr['isNotification']=true;
        }
        else{
            $arr['isNotification']=false;
        }
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }

?>