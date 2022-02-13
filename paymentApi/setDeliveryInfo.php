<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['tradeNum'])){
        $tradeNum=$_POST['tradeNum'];
        $deliveryNum=$_POST['deliveryNum'];
        $deliveryCompany=$_POST['deliveryCompany'];

        $sql="UPDATE Trade_delivery_info SET Delivery_company_name ='$deliveryCompany', Delivery_delivery_number='$deliveryNum',Delivery_status='배송중' WHERE Delivery_trade_no='$tradeNum'";
        $updateResult=mysqli_query($db_connect,$sql);

        if($updateResult){
            //운송장 번호, 택배사 입력이 제대로 되어 있으면, 배송상태변경
            $sql="SELECT Trade_post_no from Post_trade where Trade_no='$tradeNum'";
            $selectResult=mysqli_query($db_connect,$sql);
            if($selectResult){
                $Data=mysqli_fetch_array($selectResult);
                $postNum=$Data['Trade_post_no'];
                $sql="UPDATE Post SET Post_status='DS' WHERE Post_no='$postNum' ";
                mysqli_query($db_connect,$sql);
            }

            $arr['isSuccess']=true;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
        else{
            $arr['isSuccess']=false;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }

    }

?>