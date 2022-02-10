<?php 

  
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
  
    $imageArray=array();

    if(isset($_POST['postNum'])){
        //ASD
        //지금 글을 읽는 client의 아이디를 갖고, 닉네임을 구한 후, 그 닉네임이 이 게시글을 좋아요 누른지 확인하여 값 알려주기
        $email=$_POST['email'];
        if($email!=="update"){
            $sql="SELECT * FROM Market_member where Member_id='$email'";
            $selectResult=mysqli_query($db_connect,$sql);
            $Data=mysqli_fetch_array($selectResult);
           
            $phoneNumber=$Data['Member_phone_number'];
            $nickname=$Data['Member_nickname'];
            $addressDetail=$Data['Member_standard_address'];
            $deliveryRequire=$Data['Member_standard_delivery_require'];
            $receiverName=$Data['Member_standard_receiver_name'];

            $clientNickname=$nickname;
        }
        $postNum=$_POST['postNum'];

        //  $sql = "UPDATE PostTable SET Post_View = Post_View + 1 WHERE Post_Number='$serialNum'";
        if($_POST['purpose']==="read"){

            // $sql="UPDATE Post SET Post_view=Post_view + 1 WHERE Post_no='$postNum' ";
            // mysqli_query($db_connect,$sql);

        }
        //getPost의 목적이 택배결제를 하기위해 불려진것이라면
        if($_POST['purpose']==="delivery"){

            $arr['addressDetail']=$addressDetail;
            $arr['deliveryRequire']=$deliveryRequire;
            $arr['receiverName']=$receiverName;

        }


        // $sql="SELECT * FROM Post where Post_no='$postNum' ";
        $sql ="SELECT * FROM Post left join Post_like on Post_no=Like_post where Post_no='$postNum'";
        $selectResult=mysqli_query($db_connect,$sql);

        if($selectResult){
            $clientIsLike=false;
            while($Data=mysqli_fetch_assoc($selectResult)){
                $tempData=$Data;
                if( $Data['Like_person']===$nickname){
                    $clientIsLike=true;
                    break;
                }
            }

            $selectResult=mysqli_query($db_connect,$sql);
            // $Data=mysqli_fetch_array($selectResult);
            $Data=$tempData;
            $arr['clientIsLike']=$clientIsLike;
         
            // foreach($Data as $key=>$value ){
            //     $keyV=$key;
            //     $valueV=$value;
            // }
            $nickname=$Data['Post_writer'];
            //작성자
            $arr['nickname']=$Data['Post_writer'];
            $arr['clientNickname']=$clientNickname;
            $arr['clientPhoneNumber']=$phoneNumber;

             //작상자 아이디
            $sql="SELECT Member_id from Market_member where Member_nickname='$nickname'";
            $memberId=mysqli_query($db_connect,$sql);
            $userData=mysqli_fetch_array($memberId);
            $arr['memberId']=$userData['Member_id'];
        
            $sql="SELECT * FROM Post where Post_Writer ='$nickname'";
            $selectResult=mysqli_query($db_connect,$sql);
            $productNum=mysqli_num_rows($selectResult);
            $arr['productNum']=$productNum;
            
            //제목 카테고리 판매유형
            $arr['postTitle']=$Data['Post_title'];
            $arr['postContents']=$Data['Post_contents'];
            $arr['postCategory']=$Data['Post_category'];
            $arr['postSellType']=$Data['Post_sellType'];
            $arr['postImageNum']=$Data['Post_image_num'];
            $arr['postPrice']=$Data['Post_price'];
            $arr['postDelivery']=$Data['Post_deliver_price']; 
           
            //좋아요,조회수, 등록시간
            $arr['postRegTime']=$Data['Post_reg_time'];
            $arr['postViewNum']=$Data['Post_view'];
            $arr['postLikeNum']=$Data['Post_like'];

            //판매상태
            $arr['postStatus']=$Data['Post_status'];
            
            //위치정보
            if($Data['Post_location_address']==="장소정보 없음"){
              
            $arr['postLocationName']="장소정보 없음";
            $arr['postLocationAddress']="";
            $arr['postLocationLatitude']=0;
            $arr['postLocationLongitude']=0;
            }
            else{
                 $arr['postLocationName']=$Data['Post_location_name'];
                 $arr['postLocationAddress']=$Data['Post_location_address'];
                 $arr['postLocationLatitude']=$Data['Post_location_latitude'];
                 $arr['postLocationLongitude']=$Data['Post_location_longitude'];
                 $arr['postLocationDetail']=$Data['Post_place_detail'];
            }


        }
        $sql="SELECT Member_image_route FROM Market_member where Member_nickname='$nickname'";
        $selectResult=mysqli_query($db_connect,$sql);
        if($selectResult){
            $Data=mysqli_fetch_array($selectResult);
            $arr['memberImage']=$Data['Member_image_route'];
        }

        $sql="SELECT * FROM Image where Image_post='$postNum' order by Image_order asc ";
        $selectResult=mysqli_query($db_connect,$sql);
        if($selectResult){
            $i=1;
            while($Data=mysqli_fetch_array($selectResult)){
              
                $imageRoute=$Data['Image_route'];
                 array_push($imageArray,$imageRoute); 
                // $arr['image'.$i]=$imageRoute;
                // $i++;
            }

            $arr['imageRoute']=$imageArray;
        }


        echo json_encode($arr,JSON_UNESCAPED_UNICODE);


      
    }


?>