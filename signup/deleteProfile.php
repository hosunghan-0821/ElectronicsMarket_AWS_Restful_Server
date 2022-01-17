<?php 

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    if(isset($_POST['email'])){
        $id=$_POST['email'];
        
        $sql="SELECT Member_image_route FROM Market_member Where Member_id='$id' "; 
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $deleteRoute=explode('Resource/',$Data['Member_image_route']);
        unlink("../Resource/".$deleteRoute[1]);

        $sql="UPDATE Market_member SET Member_image_route=null WHERE Member_id='$id'";
        $updateResult=mysqli_query($db_connect,$sql);

        if($updateResult){
            $arr['isSuccess']=true;
            $arr['message']="업로드 성공";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }

?>