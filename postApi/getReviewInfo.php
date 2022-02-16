<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    
    if(isset($_POST['email'])){

        $reviewInfo=array();
        $reviewAllInfo=array();
        //기본적으로 넘어온 정보들 변수값 저장
        $email=$_POST['email'];
        $finalReviewNum=$_POST['finalPostNum'];
        $phasingNum=$_POST['phasingNum'];

        $sql="SELECT * FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];
        //SELECT * FROM hosung.Post as a INNER JOIN hosung.Post_review as b on a.Post_no=b.Review_post_no where Post_writer='hosung12' order by b.Review_no desc
        if($finalReviewNum==='0'){
            $sql="SELECT * FROM Post as a INNER JOIN Post_review as b on a.Post_no = b.Review_post_no where Post_writer='$nickname' ORDER BY b.Review_no desc limit $phasingNum";
        }
        else{
            $sql="SELECT * FROM Post as a INNER JOIN Post_review as b on a.Post_no = b.Review_post_no where (Post_writer='$nickname' and b.Review_no<$finalReviewNum) ORDER BY b.Review_no desc limit $phasingNum";
        }

        $selectResult=mysqli_query($db_connect,$sql);

        if($selectResult){

            while($Data=mysqli_fetch_array($selectResult)){
                
                $postNum=$Data['Post_no'];
                $postBuyer=$Data['Post_buyer'];
                
                //상품관련
                $arr['postTitle']=$Data['Post_title'];
                $arr['postNum']=$postNum;
                

                //review 관련
                $arr['reviewNo']=$Data['Review_no'];
                $arr['reviewContents']=$Data['Review_contents'];
                $arr['reviewWriter']=$postBuyer;
                $arr['reviewTime']=$Data['Review_reg_time'];
                $arr['reviewRating']=$Data['Review_rating'];

                $sql="SELECT * FROM Market_member where Member_nickname='$postBuyer'";
                $selectResult2=mysqli_query($db_connect,$sql);
                $profileData=mysqli_fetch_array($selectResult2);
                $arr['reviewWriterProfile']=$profileData['Member_image_route'];
                array_push($reviewInfo,$arr);
            }
            $reviewAllInfo['reviewInfo']=$reviewInfo;
            $reviewNum=count($reviewInfo);

            $reviewAllInfo['reviewNum']=$reviewNum;
            echo json_encode($reviewAllInfo,JSON_UNESCAPED_UNICODE);
        }



    }
?>