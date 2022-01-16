<?php 
     require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
     
     if(isset($_POST['email'])){
         $email=$_POST['email'];
         $sql="SELECT Member_nickname,Member_image_route From Market_member where Member_id='$email'";
         $selectResult=mysqli_query($db_connect,$sql);

         $Data=mysqli_fetch_array($selectResult);
         if($Data['Member_image_route']==null){
             $route="";
         }
         else{
             $route=$Data['Member_image_route'];
         }
         $arr['nickname']=$Data['Member_nickname'];
         $arr['imageRoute']=$route;
         echo json_encode($arr,JSON_UNESCAPED_UNICODE);
         
     }

?>