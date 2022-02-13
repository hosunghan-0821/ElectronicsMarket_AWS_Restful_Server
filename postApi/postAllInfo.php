<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');  
    $postInformation2=array();
   

    $postInfo=array();

    if(isset($_POST['finalPostNum'])){
        $finalPostNum=$_POST['finalPostNum'];
        $phasingNum=$_POST['phasingNum'];
    }
  
    //새로고침이 아닐 경우, 기본
    if($phasingNum!=='update'){
          //맨 처음 phasing 해줄 때, 즉 커서가 존재하지 않을 때, 처음부터 5개만 제공
        if($finalPostNum==='0'){
            $sql="SELECT * FROM Post ORDER BY Post_no DESC limit $phasingNum";
        }
        //커서가 존재할 경우
        else{
            $sql="SELECT * FROM Post where Post_no<$finalPostNum ORDER BY Post_no DESC limit $phasingNum ";
        }
    }
    
    //새로고침 일 경우, 기존의 data정보들을 다시 쏴줘서 업데이트 해준다.
    else{
        $sql="SELECT * FROM Post where Post_no>=$finalPostNum ORDER BY Post_no DESC";
    }
 
    

    //만약 특정 판매자의 판매중인 정보를 가져오려고 한다면,  $sql 변경.
    if($_POST['purpose']==="sellingInfo"){
        
        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        if($finalPostNum==='0'){
            $sql="SELECT * FROM Post WHERE (Post_writer='$nickname' and Post_status='Y' ) ORDER BY Post_no DESC limit $phasingNum";
        }
        //커서가 존재할 경우
        else{
            $sql="SELECT * FROM Post where (Post_status='Y' and Post_no<$finalPostNum and Post_writer='$nickname' ) ORDER BY Post_no DESC limit $phasingNum ";
        }
    }
    //만약 특정 판매자의 판매완료한 정보를 가져오려고 한다면, $sql 변경
    else if($_POST['purpose']==="soldInfo"){
        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        if($finalPostNum==='0'){
            $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Post_writer='$nickname' and Post_status !='Y' ) ORDER BY Post_no DESC limit $phasingNum";
        }
        //커서가 존재할 경우
        else{
            $sql="SELECT * FROM Post where (Post_status!='Y' and Post_no<$finalPostNum and Post_writer='$nickname' ) ORDER BY Post_no DESC limit $phasingNum ";
        }
    }
    // 만약 특정 구매자의 구매정보를 가져오려고 한다면 , $sql 변경
    else if($_POST['purpose']==="buyingInfo"){
        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        if($finalPostNum==='0'){
            $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Trade_buyer='$nickname' and Post_status !='Y' ) ORDER BY Post_no DESC limit $phasingNum";
        }
        //커서가 존재할 경우
        else{
            $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) where (Post_status!='Y' and Post_no<$finalPostNum and Trade_buyer='$nickname' ) ORDER BY Post_no DESC limit $phasingNum ";
        }

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

            //이 데이터를 가져오는 목적이 판매완료된 제품일 경우 해당하는
            if($_POST['purpose']==="soldInfo"||$_POST['purpose']==="buyingInfo"){
                $arr['tradeNum']=$Data['Trade_no'];
            }

             //좋아요,조회수, 등록시간
            $arr['postRegTime']=$Data['Post_reg_time'];
            $arr['postViewNum']=$Data['Post_view'];
            $arr['postLikeNum']=$Data['Post_like'];
            $arr['postStatus']=$Data['Post_status'];

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
          $productNum=count($postInfo);

          $postInformation2['productNum']=$productNum;
          echo json_encode($postInformation2,JSON_UNESCAPED_UNICODE);
    }

    

?>