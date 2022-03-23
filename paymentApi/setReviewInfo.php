<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['postNum'])){

         //데이터로 넘어온 후기 및 평점
         $reviewContents=$_POST['review'];
         $reviewRating=$_POST['reviewRating'];
         $id=$_POST['email'];
         $postNum=$_POST['postNum'];

         //postnum을 갖고 거래 게시글 판매자의 정보를 확인 하고, 그 사람에게 평점을 주는 것이기 떄문에
         $sql="SELECT Post_writer from Post Where Post_no='$postNum' ";
         $selectResult=mysqli_query($db_connect,$sql);
         $Data=mysqli_fetch_array($selectResult);
         $sellerNickName=$Data['Post_writer'];

         $sql="UPDATE Market_member SET Member_review_num=Member_review_num+1,Member_review_score=Member_review_score+$reviewRating where Member_nickname='$sellerNickName'";
         $updateResult=mysqli_query($db_connect,$sql);
         
        //email 갖고 닉네임 획득하기
       
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$id'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

       
        $nowDate = date("Ymd-His");
        $sql="INSERT INTO Post_review
         (Review_post_no,
         Review_writer,
         Review_contents,
         Review_rating,
         Review_reg_time
         )
         values('$postNum','$nickname','$reviewContents','$reviewRating','$nowDate')
         ";
         $insertResult=mysqli_query($db_connect,$sql);
         if($insertResult){
             $arr['isSuccess']=true;
             $arr['sellerNickname']=$sellerNickName;
             echo json_encode($arr,JSON_UNESCAPED_UNICODE);
             return;
         }
    }

?>