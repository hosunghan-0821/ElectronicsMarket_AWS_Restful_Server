<?php 

  
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
  
    if(isset($_POST['postNum'])){
        $postNum=$_POST['postNum'];
        $sql="SELECT * FROM Post where Post_no='$postNum' ";
        
        $selectResult=mysqli_query($db_connect,$sql);
        if($selectResult){
            $Data=mysqli_fetch_array($selectResult);
            $arr['nickname']=$Data['Post_writer'];
            $arr['postTitle']=$Data['Post_title'];
            $arr['postContents']=$Data['Post_contents'];
            $arr['postCategory']=$Data['Post_category'];
            $arr['postSellType']=$Data['Post_sellType'];
            $arr['postImageNum']=$Data['Post_image_num'];
            $arr['postPrice']=$Data['Post_price'];
            $arr['postDelivery']=$Data['Post_deliver_price'];
            $arr['postImageNum']=$Data['Post_image_num'];
        }
        $sql="SELECT * FROM Image where Image_post='$postNum' ";
        $selectResult=mysqli_query($db_connect,$sql);
        if($selectResult){
            $i=1;
            while($Data=mysqli_fetch_array($selectResult)){
              
                $imageRoute=$Data['Image_route'];
                $arr['image'.$i]=$imageRoute;
                $i++;
            }
        }

        $asd;


      
    }


?>