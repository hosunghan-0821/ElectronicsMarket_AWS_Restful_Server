<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['postNum'])){
        
        $postNum=$_POST['postNum'];
        $sql="SELECT * FROM Post_review where Review_post_no='$postNum'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        $arr['reviewContents']=$Data['Review_contents'];
        $arr['reviewRating']=$Data['Review_rating'];

        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }

?>