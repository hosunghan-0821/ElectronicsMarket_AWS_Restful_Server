<?php 

  
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
  
    $imageArray=array();
    if(isset($_POST['postNum'])){
        $postNum=$_POST['postNum'];

        //  $sql = "UPDATE PostTable SET Post_View = Post_View + 1 WHERE Post_Number='$serialNum'";
        $sql="UPDATE Post SET Post_view=Post_view + 1 WHERE Post_no='$postNum' ";
        mysqli_query($db_connect,$sql);

        $sql="SELECT * FROM Post where Post_no='$postNum' ";
        
        $selectResult=mysqli_query($db_connect,$sql);
        if($selectResult){

            $Data=mysqli_fetch_array($selectResult);
            $nickname=$Data['Post_writer'];
           //작성자
            $arr['nickname']=$Data['Post_writer'];

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