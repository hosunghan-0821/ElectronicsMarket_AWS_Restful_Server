<?php 

require_once $_SERVER['DOCUMENT_ROOT'].'/lib/dbConnect.php';

    if(isset($_POST['phone'])){
        //제대로 넘어왔을경우
      
       $phone=$_POST['phone'];
       $arr=array();
     
       $sql="SELECT * FROM new_table ";
       
       $sqlResult=mysqli_query($db_connect,$sql);
       $check=false;
       while( $Data = mysqli_fetch_array($sqlResult)){
           $checkNumber=$Data['phone'];
           $message=$Data['message'];
           if($checkNumber===$phone){
              $check=true;
              $arr['phone']=$checkNumber;
              $arr['message']=$message;
            break;
           }
       }
       if($check!==true){
           $arr['phone']='해당하는 폰 번호가 없습니다';
           $arr['message']='해당하는 메시지가 없습니다';
           echo json_encode($arr,JSON_UNESCAPED_UNICODE);
       }
       else{
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
       }
    }
    else{
      
        $asd;
        $asd123;
        echo "데이터 없다";
    }

    // $arr = array('phone'=>1,'message'=>1);
    // echo json_encode($arr);

?>