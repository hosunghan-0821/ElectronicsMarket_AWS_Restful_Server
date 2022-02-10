<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['email'])){
        $email=$_POST['email'];
        $tradeNum=$_POST['tradeNum'];
        $sql="SELECT * FROM Post_trade as a INNER JOIN Post as b  Inner Join Trade_delivery_info as c INNER JOIN Image as d on (a.Trade_post_no=b.Post_no and c.Delivery_trade_no=a.Trade_no )where (Trade_no='$tradeNum' and (d.Image_post=Trade_post_no and d.Image_order=0))";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        //상품정보 데이터 select 하기
        $arr['postNum']=$Data['Post_no'];
        $arr['tradeTitle']=$Data['Post_title'];
        $arr['tradePrice']=$Data['Post_price'];
        $arr['tradeImageRoute']=$Data['Image_route'];
         //결제방식
        $arr['tradeType']=$Data['Trade_type'];
       

        //구매정보 데이터 select하기
       
        //결제방법
        $arr['tradeRegTime']=$Data['Trade_reg_time'];
        $arr['tradeSeller']=$Data['Trade_seller'];
        $arr['tradePayType']=$Data['Trade_pay_type'];

        //배송정보.
        $arr['tradeReceiver']=$Data['Delivery_receiver'];
        $arr['tradeReceiverPhoneNum']=$Data['Delivery_phone_number'];
        $arr['tradeAddress']=$Data['Delivery_address'];
        $arr['tradeDeliveryStatus']=$Data['Delivery_status'];
        $arr['tradeDeliveryRequire']=$Data['Delivery_require'];
        $arr['tradeDeliveryCompany']=$Data['Delivery_company_name'];
        $arr['tradeDeliveryNum']=$Data['Delivery_delivery_number'];

        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }


   
?>