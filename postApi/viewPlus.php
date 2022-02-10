<?php 
     require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
     if(isset($_POST['postNum'])){

        $postNum=$_POST['postNum'];
        $sql="UPDATE Post SET Post_view=Post_view + 1 WHERE Post_no='$postNum' ";
        $updateReuslt=mysqli_query($db_connect,$sql);
        $arr['isSuccess']=true;

        $sql="SELECT Post_view FROM Post where Post_no='$postNum'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $arr['postViewNum']=$Data['Post_view'];
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
     }

?>