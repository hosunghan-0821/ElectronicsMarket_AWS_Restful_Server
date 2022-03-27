<?php 
    
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    if(isset($_POST['type'])){
        $type=$_POST['type'];
        $nickname=$_POST['sendToNickname'];
        $message=$_POST['message'];
        $postNum=$_POST['postNum'];

        $nowDate=Date("Y-m-d H:i:s");
        $sql="INSERT INTO Notification_collect 
        (Notification_type,
        Notification_member,
        Notification_text,
        Notification_reg_time,
        Notification_post_num)
        values('$type','$nickname','$message','$nowDate','$postNum')
        ";
        mysqli_query($db_connect,$sql);
        
    }

?>