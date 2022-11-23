<?php 
  
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    $file_standard_path='../Resource/';

    if(isset($_FILES['upload'])){

        $result=$_FILES['upload']['error'];
        $id=$_GET['email'];
        $tmp_file=$_FILES['upload']['tmp_name'];
        $nowDate = date("Ymd_His");
        $file_name=$id."_profileImage".$nowDate.".jpg";
       
        //이전 이미지 파일 삭제
        $sql="SELECT Member_image_route FROM Market_member Where Member_id='$id' "; 
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $deleteRoute=explode('Resource/',$Data['Member_image_route']);
        unlink("../Resource/".$deleteRoute[1]);

        $file_path=$file_standard_path.$file_name;
        $result=move_uploaded_file($tmp_file,$file_path);
        if($result){
            $file_http_path="http://43.201.72.60/realMarketServer/Resource/".$file_name;
           
            $sql="UPDATE Market_member SET Member_image_route='$file_http_path' WHERE Member_id='$id'";
            $updateResult=mysqli_query($db_connect,$sql);
            if($updateResult){
                $arr['message']="업로드 성공";
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            }

        }
        else{
            $arr['message']="업로드 실패";
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }
    }

?>