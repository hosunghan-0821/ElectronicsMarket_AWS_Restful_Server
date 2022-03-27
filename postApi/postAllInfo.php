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
    if($_POST['purpose']==="allInfo"){
        if($phasingNum!=='update'){
            //맨 처음 phasing 해줄 때, 즉 커서가 존재하지 않을 때, 처음부터 5개만 제공
          if($finalPostNum==='0'){
              $sql="SELECT * FROM Post where Post_status='Y' ORDER BY Post_no DESC limit $phasingNum";
          }
          //커서가 존재할 경우
          else{
              $sql="SELECT * FROM Post where (Post_no<$finalPostNum and Post_status='Y') ORDER BY Post_no DESC limit $phasingNum ";
          }
      }
      
      //새로고침 일 경우, 기존의 data정보들을 다시 쏴줘서 업데이트 해준다.
      else{
          $sql="SELECT * FROM Post where (Post_no>=$finalPostNum and Post_status='Y') ORDER BY Post_no DESC";
      }
    }

    

    //만약 특정 판매자의 판매중인 정보를 가져오려고 한다면,  $sql 변경.
    if($_POST['purpose']==="sellingInfo"){
        
        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        //onresume이 아닐경우.
        if($phasingNum!=='update'){
            if($finalPostNum==='0'){
                $sql="SELECT * FROM Post WHERE (Post_writer='$nickname' and Post_status='Y' ) ORDER BY Post_no DESC limit $phasingNum";
            }
            //커서가 존재할 경우
            else{
                $sql="SELECT * FROM Post where (Post_status='Y' and Post_no<$finalPostNum and Post_writer='$nickname' ) ORDER BY Post_no DESC limit $phasingNum ";
            }
        }
        //onresume 새로고침할 경우
        else{
            $sql="SELECT * FROM Post where (Post_no>=$finalPostNum and Post_writer='$nickname' and Post_status='Y' ) ORDER BY Post_no DESC";

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

        if($phasingNum!=='update'){
            if($finalPostNum==='0'){
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Post_writer='$nickname' and Post_status !='Y' and Post_status !='RF' and Post_status !='RR' ) ORDER BY Post_buy_reg_time DESC limit $phasingNum";
            }
            //커서가 존재할 경우
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade As b on (a.post_no=b.Trade_post_no) where (Post_status!='Y'and Post_status !='RF' and Post_status !='RR' and str_to_date(Post_buy_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalPostNum' and Post_writer='$nickname' ) ORDER BY Post_buy_reg_time DESC limit $phasingNum ";
            }
        }
        else{
            if($finalPostNum==='0'){
                $phasingNum=5;
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Post_writer='$nickname' and Post_status !='Y' and Post_status !='RF' and Post_status !='RR' ) ORDER BY Post_buy_reg_time DESC limit $phasingNum";
            }
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) where (str_to_date(Post_buy_reg_time,'%Y-%m-%d %H:%i:%s')>='$finalPostNum' and Post_writer='$nickname' and Post_status !='Y' and Post_status !='RF' and Post_status !='RR' ) ORDER BY Post_buy_reg_time DESC";
            }
          
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
        
        //onresume이 아닐경우
        if($phasingNum!=='update'){
            if($finalPostNum==='0'){
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Trade_buyer='$nickname' and Post_status !='Y' and Post_status !='S' AND Post_status !='RF' AND Post_status !='RR'  ) ORDER BY Post_buy_reg_time DESC limit $phasingNum";
            }
            //커서가 존재할 경우
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) where (Post_status!='Y'and  Post_status !='S'  AND Post_status !='RF' AND Post_status !='RR'  and str_to_date(Post_buy_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalPostNum' and Trade_buyer='$nickname' ) ORDER BY Post_buy_reg_time DESC limit $phasingNum ";
            }
        }
        //onresume이 일 경우
        else{
             //커서가 없을경우
            if($finalPostNum==='0'){
                $phasingNum=5;
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Trade_buyer='$nickname' and Post_status !='Y' and Post_status !='S' AND Post_status !='RR' AND Post_status !='RF'   ) ORDER BY Post_buy_reg_time DESC limit $phasingNum";
            }
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) where (str_to_date(Post_buy_reg_time,'%Y-%m-%d %H:%i:%s')>='$finalPostNum' and Trade_buyer='$nickname' and Post_status!='Y' and Post_status !='S' AND Post_status !='RR' AND Post_status !='RF' ) ORDER BY Post_buy_reg_time DESC";
            }
        
        }
    }

    else if($_POST['purpose']==="boughtInfo"){
        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        //onresume이 아닐경우
        if($phasingNum!=='update'){
            if($finalPostNum==='0'){
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Trade_buyer='$nickname' and Post_status ='S' ) ORDER BY Post_trade_confirm_time desc limit $phasingNum";
            }
            //커서가 존재할 경우
            else{

                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) where (Post_status ='S' and str_to_date(Post_trade_confirm_time,'%Y-%m-%d %H:%i:%s')<'$finalPostNum' and Trade_buyer='$nickname' ) ORDER BY Post_trade_confirm_time DESC limit $phasingNum ";
            }
        }
        //onresume이 일 경우
        else{
            //커서가 없을경우
            if($finalPostNum==='0'){
                $phasingNum=5;
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) WHERE (Trade_buyer='$nickname' and Post_status ='S' ) ORDER BY Post_trade_confirm_time desc limit $phasingNum";
            }
            //커서가 있을경우.
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b on (a.Post_no=b.Trade_post_no) where ( str_to_date(Post_trade_confirm_time,'%Y-%m-%d %H:%i:%s')>='$finalPostNum' and Trade_buyer='$nickname' and Post_status ='S' ) ORDER BY Post_trade_confirm_time DESC";
            }
        
        }
    }
    else if($_POST['purpose']==="cancelInfo"){

        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        //onresume이 아닐경우
        if($phasingNum!=='update'){
            if($finalPostNum==='0'){
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) WHERE (Trade_buyer='$nickname' and (Post_status ='RF' OR Post_status='RR')) ORDER BY c.Refund_reg_time desc limit $phasingNum";
            }
            //커서가 존재할 경우
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) where ( (Post_status ='RF' OR Post_status='RR') and str_to_date(c.Refund_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalPostNum' and Trade_buyer='$nickname' ) ORDER BY c.Refund_reg_time desc limit $phasingNum";
            }
        }
         //onresume이 일 경우
        else{

            if($finalPostNum==='0'){
                $phasingNum=5;
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) WHERE (Trade_buyer='$nickname' and (Post_status ='RF' OR Post_status='RR')) ORDER BY c.Refund_reg_time desc limit $phasingNum";
            }
            //커서가 있을경우.
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) where ( (Post_status ='RF' OR Post_status='RR') and str_to_date(c.Refund_reg_time,'%Y-%m-%d %H:%i:%s')>='$finalPostNum' and Trade_buyer='$nickname' ) ORDER BY c.Refund_reg_time desc ";
            }

        }

    }
    else if($_POST['purpose']==="cancelSellInfo"){

        $email=$_POST['email'];

        //email 통해서 닉네임 가져오기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];


         //onresume이 아닐경우
         if($phasingNum!=='update'){
            if($finalPostNum==='0'){
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) WHERE (Trade_Seller='$nickname' and (Post_status ='RF' OR Post_status='RR')) ORDER BY c.Refund_reg_time desc limit $phasingNum";
            }
            //커서가 존재할 경우
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) where ( (Post_status ='RF' OR Post_status='RR') and str_to_date(c.Refund_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalPostNum' and Trade_Seller='$nickname' ) ORDER BY c.Refund_reg_time desc limit $phasingNum";
            }
        }
         //onresume이 일 경우
        else{

            if($finalPostNum==='0'){
                $phasingNum=5;
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) WHERE (Trade_Seller='$nickname' and (Post_status ='RF' OR Post_status='RR')) ORDER BY c.Refund_reg_time desc limit $phasingNum";
            }
            //커서가 있을경우.
            else{
                $sql="SELECT * FROM Post AS a INNER JOIN Post_trade AS b INNER JOIN Trade_refund AS c on (a.Post_no=b.Trade_post_no and a.Post_no=c.Refund_post_no) where ( (Post_status ='RF' OR Post_status='RR') and str_to_date(c.Refund_reg_time,'%Y-%m-%d %H:%i:%s')>='$finalPostNum' and Trade_Seller='$nickname' ) ORDER BY c.Refund_reg_time desc ";
            }

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
            $arr['postTradeType']=$Data['Trade_type'];
            
          
            $arr['postNum']=$postNum;

            //해당 상품의 리뷰가 존재하는지 true/ false로 응답해주자
            //이 데이터를 가져오는 목적이 판매완료된 제품일 경우 해당하는
            if($_POST['purpose']==="soldInfo"||$_POST['purpose']==="buyingInfo"||$_POST['purpose']==="boughtInfo"){
                $arr['tradeNum']=$Data['Trade_no'];

                $sql="SELECT * FROM Post_review where Review_post_no='$postNum'";
                $isHasReview=mysqli_query($db_connect,$sql);
                if(mysqli_num_rows($isHasReview)>=1){
                    $arr['isReview']=true;
                }
                else{
                    $arr['isReview']=false;
                }
            }

            //환불시간
            $arr['refundRegTime']=$Data['Refund_reg_time'];

            //구매확정 시간
            $arr['tradeConfirmTime']=$Data['Post_trade_confirm_time'];
            //구매 시간
            $arr['buyRegTime']=$Data['Post_buy_reg_time'];

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