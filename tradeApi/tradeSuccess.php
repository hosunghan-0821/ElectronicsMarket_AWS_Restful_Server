<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');

    if(isset($_POST['postNum'])){

        $postNum=$_POST['postNum'];
        $buyer=$_POST['buyer'];
        $seller=$_POST['nickname'];
        $nowDate = date("Y-m-d H:i:s");
        
        //직거래 구매자 선택 시 ,
        $sql="UPDATE Post SET Post_status='S',Post_buyer='$buyer',Post_buy_reg_time='$nowDate',Post_trade_confirm_time='$nowDate' where post_no='$postNum'";
        mysqli_query($db_connect,$sql);
        
       
        $sql="INSERT INTO Post_trade
        (Trade_post_no,
        Trade_buyer,
        Trade_seller,
        Trade_type,
        Trade_reg_time)
        values('$postNum','$buyer','$seller','직거래','$nowDate')";
        $insertResult=mysqli_query($db_connect,$sql);
        

    }
 
?>