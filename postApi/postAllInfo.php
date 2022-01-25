<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');  
    $postInformation2=array();
   

    $postInfo=array();

    $sql="SELECT * FROM Post ORDER BY Post_no DESC ";
    $selectResult=mysqli_query($db_connect,$sql);
   

    if($selectResult){

        while($Data=mysqli_fetch_array($selectResult)){
            $imageArray=array();
            $postNum=$Data['Post_no'];
            $arr['postTitle']=$Data['Post_title'];
            $arr['postPrice']=$Data['Post_price'];
            $arr['postSellType']=$Data['Post_sellType'];
            $arr['postRegTime']=$Data['Post_reg_time']; 
            $arr['postNum']=$postNum;

            $sql="SELECT * FROM Image where Image_post='$postNum' order by Image_reg_time desc, Image_no asc";
            $selectResult2=mysqli_query($db_connect,$sql);
            $imageData=mysqli_fetch_array($selectResult2);
            $imageRoute=$imageData['Image_route'];
            array_push($imageArray,$imageRoute);
            $arr['imageRoute']=$imageArray;
            array_push($postInfo,$arr);
          }

          $postInformation2["postInfo"]=$postInfo;
    
          echo json_encode($postInformation2,JSON_UNESCAPED_UNICODE);
    }

    

?>