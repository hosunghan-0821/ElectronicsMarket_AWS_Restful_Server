<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['postNum'])){
        $reviewContents=$_POST['review'];
        $reviewRating=$_POST['reviewRating'];
        $id=$_POST['email'];
        $postNum=$_POST['postNum'];
        $beforeRating=$_POST['beforeRating'];

        //postnum을 갖고 거래 게시글 판매자의 정보를 확인 하고, 그 사람에게 평점을 주는 것이기 떄문에
        $sql="SELECT Post_writer from Post Where Post_no='$postNum' ";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $sellerNickName=$Data['Post_writer'];

        $sql="UPDATE Market_member SET Member_review_score=Member_review_score+$reviewRating-$beforeRating where Member_nickname='$sellerNickName'";
        $updateResult=mysqli_query($db_connect,$sql);

        $sql="UPDATE Post_review SET Review_contents='$reviewContents',Review_rating='$reviewRating' where Review_post_no = '$postNum'";
        $updateResult=mysqli_query($db_connect,$sql);

        if($updateResult){
            $arr['isSuccess']=true;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
        
    }
?>