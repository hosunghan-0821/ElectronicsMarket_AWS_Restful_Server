<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_POST['nickname'])){
        $i=0;
        $postInfo=array();
        $nickName=$_POST['nickname'];
        $sql="SELECT * FROM Market_member WHERE Member_nickname='$nickName'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        $allInfo['imageRoute']=$Data['Member_image_route'];

        if($selectResult){
            $sql="SELECT * FROM Post WHERE Post_writer='$nickName' order by Post_no DESC";
            $selectResult=mysqli_query($db_connect,$sql);
            if($selectResult){

                while($Data=mysqli_fetch_array($selectResult)){
                    $imageArray=array();
                    $postNum=$Data['Post_no'];
                    $i++;
                    $arr['postTitle']=$Data['Post_title'];
                    $arr['postPrice']=$Data['Post_price'];
                    $arr['postNum']= $postNum;
                    
                    $sql="SELECT * FROM Image where Image_post='$postNum' order by Image_order asc";
                    $selectResult2=mysqli_query($db_connect,$sql);
                    $imageData=mysqli_fetch_array($selectResult2);
                    $imageRoute=$imageData['Image_route'];
                    array_push($imageArray,$imageRoute);
                    $arr['imageRoute']=$imageArray;
                    array_push($postInfo,$arr);
                }
                $allInfo['productNum']="$i";
    
            }
      
        }
        $allInfo['postInfo']=$postInfo;
        echo json_encode($allInfo,JSON_UNESCAPED_UNICODE);

    }

?>