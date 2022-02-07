<?php

    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    
    if(isset($_REQUEST['email'])){

       
        $id=$_REQUEST['email'];
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$id'";
        $selectResult=mysqli_query($db_connect,$sql);

        $tmp_file=$_FILES;
        $imageNumber=count($tmp_file);
        
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        $placeName=$_POST['placeName'];
        $addressName=$_POST['addressName'];
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];
        $title=$_POST['title'];
        $price=$_POST['price'];
        $contents=$_POST['contents'];
        $sellType=$_POST['sellType'];
        $category=$_POST['category'];  
        $delivery=$_POST['deliveryCost'];
        $placeDetail=$_POST['placeDetail'];

        $nowDate = date("Ymd-His");
        $sql=
        "INSERT INTO Post
        (Post_writer,
        Post_title,
        Post_price,
        Post_contents,
        Post_status,
        Post_image_num,
        Post_category,
        Post_deliver_price,
        Post_sellType,
        Post_location_name,
        Post_location_address,
        Post_location_latitude,
        Post_location_longitude,
        Post_reg_time,
        Post_place_detail)
        values('$nickname','$title','$price','$contents','Y','$imageNumber','$category','$delivery','$sellType','$placeName','$addressName','$latitude','$longitude','$nowDate','$placeDetail')
        ";
        
        $insertResult=mysqli_query($db_connect,$sql);
        //기본 정보 넣고, 그 이후에, 해당하는 이미지들 폴더 저장하고, 
        // 이미지 경로를 DB에 저장하기.
        
        if($insertResult){
            $postNum = mysqli_insert_id($db_connect);
            //파일경로
            $file_path;
            $file_name='postNum_'.$postNum.'_';
         
            for($i=0;$i<$imageNumber;$i++){
                $file_name='postNum_'.$postNum.'_';
                $name=$tmp_file['image'.$i]['tmp_name'];
                $file_name.=$nowDate.'_';
                $file_name.=$i.'.jpg';
                $result=move_uploaded_file($name,'../Resource/postImage/'.$file_name);
                $file_http_path="http://ec2-3-34-199-7.ap-northeast-2.compute.amazonaws.com/realMarketServer/Resource/postImage/".$file_name;
                $sql="INSERT INTO Image (Image_post,Image_route,Image_file_name,Image_order) values($postNum,'$file_http_path','$file_name','$i')";
                $insertResult=mysqli_query($db_connect,$sql);
                
            }
            $arr['message']=$postNum;
    
        }
        else{
            mysqli_error($db_connect);
        }
    }
    else{
        $arr['isSuccess']=false;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }
    $arr['isSuccess']=true;
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    

?>