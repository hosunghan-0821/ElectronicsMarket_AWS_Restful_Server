<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    $asd;
    if(isset($_POST['postNum'])){
        $postNum=$_POST['postNum'];
        $sql="SELECT Image_file_name from Image where Image_post='$postNum'";
        
        $selectResult=mysqli_query($db_connect,$sql);
        while($Data=mysqli_fetch_array($selectResult)){
            $deleteFileName=$Data['Image_file_name'];
            unlink("../Resource/postImage/".$deleteFileName);
        }
        $sql="DELETE FROM Image where Image_post='$postNum'";
        $deleteResult=mysqli_query($db_connect,$sql);

        if($deleteResult){
            $sql="DELETE FROM Post where Post_no='$postNum'";
            $deleteResult=mysqli_query($db_connect,$sql);

            $arr['isSuccess']=true;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
    }
    else{
        $arr['isSuccess']=false;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }

?>