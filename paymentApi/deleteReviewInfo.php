<?php 

    
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['postNum'])){
        $postNum=$_POST['postNum'];

        $sql="SELECT Review_rating FROM Post_review where Review_post_no='$postNum'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $reviewRating=$Data['Review_rating'];

        $sql="DELETE FROM Post_review where Review_post_no='$postNum'";
        $deleteResult=mysqli_query($db_connect,$sql);


        if($deleteResult){

            $sql="SELECT Post_writer from Post Where Post_no='$postNum' ";
            $selectResult=mysqli_query($db_connect,$sql);
            $Data=mysqli_fetch_array($selectResult);
            $sellerNickName=$Data['Post_writer'];

            $sql="UPDATE Market_member SET Member_review_num=Member_review_num-1,Member_review_score=Member_review_score-$reviewRating where Member_nickname='$sellerNickName'";
            $updateResult=mysqli_query($db_connect,$sql);

            $arr['isSuccess']=true;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
    }
?>