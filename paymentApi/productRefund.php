<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['tradeNum'])){

        //post로 넘어온 데이터 변수화
        $tradeNum=$_POST['tradeNum'];
        $id=$_POST['email'];
        $refundReason=$_POST['reason'];
        $deliveryStatus=$_POST['deliveryStatus'];

        //tradeNum 갖고, postNum 알기
        $sql="SELECT * FROM Post_trade where Trade_no='$tradeNum'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $postNum=$Data['Trade_post_no'];
        $tradePayType=$Data['Trade_pay_type'];

        if($tradePayType==="카카오페이"){
            $arr['tradeType']=$tradePayType;
            $arr['kakaoTid']=$Data['Trade_kakao_tid'];
        }

        if($deliveryStatus==="배송대기"){
            $refundStatus="F";
            $sql="UPDATE Post SET Post_status='RF' where Post_no='$postNum'";
            mysqli_query($db_connect,$sql);
        }
        else{
            $refundStatus="R";
        }


        //email을 갖고 작성자 닉네임 얻기
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$id'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        $nowDate = date("Y-m-d H:i:s");
        $sql="INSERT INTO Trade_refund
        (Refund_trade_no,
        Refund_post_no,
        Refund_status,
        Refund_reason,
        Refund_reg_time
        )
        values('$tradeNum','$postNum','$refundStatus','$refundReason','$nowDate')
         ";
        $insertResult=mysqli_query($db_connect,$sql);
        if($insertResult){

            $arr['isSuccess']=true;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
    }
?>