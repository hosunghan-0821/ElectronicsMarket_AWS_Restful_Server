<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');  
    $postInformation2=array();
   

    $postInfo=array();

    $sql="SELECT * FROM Post ORDER BY Post_no DESC ";
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

          $postInformation2["postInfo"]=$postInfo;
    
          echo json_encode($postInformation2,JSON_UNESCAPED_UNICODE);
    }

    

?>