<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json'); 
    if(isset($_POST['email'])){
        $email=$_POST['email'];
        $state=$_POST['state'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];
    }
    else{
        return;
    }
    $postInfo=array();
    $postAllInfo=array();

    //state에 따라서 해당하는 정보들만 추려서 보내줘야함 => sql만 바꿔서 쿼리하고 보내주는 정보는 동일하다. 
    if($state==="loveList"){
        $sql="SELECT * FROM Post_like 
        left join Post on Post_like.Like_post=Post.Post_no 
        where Like_person='$nickname' 
        order by Like_no desc";
    }

    $selectResult=mysqli_query($db_connect,$sql);

    if($selectResult){

        while($Data=mysqli_fetch_array($selectResult)){

            $imageArray=array();
            $postNum=$Data['Post_no'];
            $arr['postTitle']=$Data['Post_title'];
            $arr['postPrice']=$Data['Post_price'];
            $arr['postSellType']=$Data['Post_sellType'];
            $arr['postRegTime']=$Data['Post_reg_time']; 
            
            $arr['postNum']=$postNum;

             //좋아요,조회수, 등록시간
            $arr['postRegTime']=$Data['Post_reg_time'];
            $arr['postViewNum']=$Data['Post_view'];
            $arr['postLikeNum']=$Data['Post_like'];

            //위치정보
            if($Data['Post_location_address']==="장소정보 없음"){
              
            $arr['postLocationName']="장소정보 없음";
            $arr['postLocationAddress']="";
            }
            else{
                $arr['postLocationName']=$Data['Post_location_name'];
                $arr['postLocationAddress']=$Data['Post_location_address'];
            }


            $sql="SELECT * FROM Image where Image_post='$postNum' order by Image_order asc";
            $selectResult2=mysqli_query($db_connect,$sql);
            $imageData=mysqli_fetch_array($selectResult2);
            $imageRoute=$imageData['Image_route'];
            array_push($imageArray,$imageRoute);
            $arr['imageRoute']=$imageArray;
            array_push($postInfo,$arr);

          }
          $postAllInfo["postInfo"]=$postInfo;
          $productNum=count($postInfo);
          $postAllInfo["productNum"]=$productNum;
          echo json_encode( $postAllInfo,JSON_UNESCAPED_UNICODE);
    }
    else{
        return;
    }



?>