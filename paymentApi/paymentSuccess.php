<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

   
    if(isset($_POST['buyerId'])){
      
        //request로 넘어온 변수들 정의 거래정보
        $email=$_POST['buyerId'];
        $postNum=$_POST['postNum'];
        $payType=$_POST['payType'];
        $tradeType=$_POST['tradeType'];
        $receiverName=$_POST['buyerName'];
      
        //request로 넘어온 변수들 정의 주소지 관련
        $address=$_POST['address'];
        $addressDetail=$_POST['addressDetail'];
        $deliveryDetail=$_POST['deliveryDetail'];
        $deliveryDetail2=$_POST['deliveryDetail2Body'];

        $finalAddress=$address."__".$addressDetail;
        $finalDeliveryDetail=$deliveryDetail."__".$deliveryDetail2;


        //택배 결제완료가 되었다. post_status 를 변경시켜주자
        $sql="UPDATE Post SET Post_status='DR' where Post_no='$postNum'";
        mysqli_query($db_connect,$sql);

        //기본주소지 설정을 했을 때, 정보 변경해주기
        if(isset($_POST['setStandardAddress'])){
            $sql="UPDATE Market_member SET Member_standard_address='$finalAddress',Member_standard_delivery_require='$finalDeliveryDetail',Member_standard_receiver_name='$receiverName' where Member_id='$email' ";
            $updateResult=mysqli_query($db_connect,$sql);

        }
        //구매자 아이디 얻어서, 닉네임 얻기,
        $sql="SELECT * FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        $buyerPhoneNumber=$Data['Member_phone_number'];
        $buyerNickname=$Data['Member_nickname'];

        //postNumber 이용해서 판매자 닉네임 얻기,
        $sql="SELECT * FROM Post where Post_no='$postNum' ";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        $sellerNickname=$Data['Post_writer'];
        $nowDate = date("Y/m/d-H:i:s");
        $buySuccessDate= date("Y-m-d H:i:s");
        //post table update 해줘야함 누가 이 게시글 제품을 샀는지.
        $sql="UPDATE Post SET Post_buyer='$buyerNickname',Post_buy_reg_time='$buySuccessDate' where Post_no='$postNum'";
        mysqli_query($db_connect,$sql);

        //결제정보 모아놓는 trade table에 정보 기입
        $sql="INSERT INTO Post_trade
        (Trade_post_no,
        Trade_buyer,
        Trade_seller,
        Trade_type,
        Trade_pay_type,
        Trade_reg_time) 
        values('$postNum','$buyerNickname','$sellerNickname','$tradeType','$payType','$nowDate')";
        $insertResult=mysqli_query($db_connect,$sql);

        if($insertResult){
           
            //등록된 일련번호로 고유주문번호 만들기
            $tradeNum= mysqli_insert_id($db_connect);
            $tradeNo=$tradeNum;
            $tradeNum=strval($tradeNum);
            $tradelength=strlen($tradeNum);
            for($i=$tradelength;$i<6;$i++){
                $tradeNum=$tradeNum."0";
            }
            $sql="UPDATE Post_trade SET Trade_order_no='$tradeNum' where Trade_no='$tradeNo'";
            $updateResut=mysqli_query($db_connect,$sql);
        }
        //결제 번호를 포함한 배송정보에 대한 값들을 넣는다.

        $sql="INSERT INTO Trade_delivery_info
        (Delivery_trade_no,
        Delivery_receiver,
        Delivery_phone_number,
        Delivery_address,
        Delivery_require)
        values('$tradeNo','$receiverName','$buyerPhoneNumber','$finalAddress','$finalDeliveryDetail')";
        $insertResult=mysqli_query($db_connect,$sql);
       
        $arr['isSuccess']=true;
        $arr['tradeNum']=$tradeNo;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }
    else{
        $arr['isSuccess']=false;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;

    }
   
?>