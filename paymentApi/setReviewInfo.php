<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['postNum'])){

        //email 갖고 닉네임 획득하기
        $id=$_POST['email'];
        $postNum=$_POST['postNum'];
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$id'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        //데이터로 넘어온 후기 및 평점
        $reviewContents=$_POST['review'];
        $reviewRating=$_POST['reviewRating'];
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
             echo json_encode($arr,JSON_UNESCAPED_UNICODE);
             return;
         }
    }
?>