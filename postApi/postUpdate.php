<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    if(isset($_REQUEST['email'])){
         $id= $id=$_REQUEST['email'];
        (int)$postIntNum=$_POST['postNum'];
        $postNum=$_POST['postNum'];

        $placeName=$_POST['placeName'];
        $addressName=$_POST['addressName'];
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];
        $placeDetail=$_POST['placeDetail'];
        $title=$_POST['title'];
        $price=$_POST['price'];
        $contents=$_POST['contents'];
        $sellType=$_POST['sellType'];
        $category=$_POST['category'];  
        $delivery=$_POST['deliveryCost'];
        $deleteImageRoute=$_POST['deleteImage'];
        if($deleteImageRoute!==""){
            $deleteImageArray=explode("///",$deleteImageRoute);

            for($i=0;$i<count($deleteImageArray)-1;$i++){

                $deletePathName=$deleteImageArray[$i];
                $sql="SELECT Image_file_name FROM Image where Image_route='$deletePathName'";
                $selectImageResult=mysqli_query($db_connect,$sql);
                if($selectImageResult){
                    $Data=mysqli_fetch_array($selectImageResult);
                    $deleteFileName=$Data['Image_file_name'];
                    unlink("../Resource/postImage/".$deleteFileName);
                    
                }
   
              
                $sql="DELETE from Image where Image_route='$deletePathName'";
                $deleteResult=mysqli_query($db_connect,$sql);
            }
        }

        //데이터 전부 엎어치기 해야지
        $tmp_file=$_FILES;

        $sql="UPDATE Post SET Post_place_detail='$placeDetail',Post_sellType='$sellType',Post_deliver_price='$delivery',Post_title='$title',Post_price='$price',Post_contents='$contents',Post_category='$category',Post_location_name='$placeName',Post_location_address='$addressName',Post_location_latitude='$latitude',Post_location_longitude='$longitude' WHERE Post_no=$postIntNum";
        $updateResult=mysqli_query($db_connect,$sql);

        //일단 경로를 imageListInfo 분해해야지

      
        $imageListInfo=$_POST['imageListInfo'];
        $imageListArr=explode("///",$imageListInfo);
        $num=count($imageListArr);
        for($i=0;$i<count($imageListArr)-2;$i+=2){
         
            $checkString=$imageListArr[$i];
          
            ///원래 존재하는 이미지인지 아닌지 경로를 통해확인
            $result=strpos($checkString,"http:");
            $order=$imageListArr[$i+1];
            if($result!==false){
                //이미 존재하는 이미지
                $sql="UPDATE Image SET Image_order='$order' where Image_route='$checkString'";
                $imageUpdateResult=mysqli_query($db_connect,$sql);
            }
            else{
                //새로만든이미지 
                $file_path;
                $file_name='postNum_'.$postNum.'_';
                $nowDate = date("Ymd_His");
                
                $name=$tmp_file[$checkString]['tmp_name'];

                $file_name.=$nowDate.'_';
                $file_name.='update_'.$order.'.jpg';
                $uploadResult=move_uploaded_file($name,'../Resource/postImage/'.$file_name);
                $file_http_path="http://ec2-3-34-199-7.ap-northeast-2.compute.amazonaws.com/realMarketServer/Resource/postImage/".$file_name;

                $sql="INSERT INTO Image (Image_post,Image_route,Image_order,Image_file_name) values($postNum,'$file_http_path','$order','$file_name')";
                $imageUpdateResult=mysqli_query($db_connect,$sql);

            }
        }
        $arr['message']=$postNum;
        $arr['isSuccess']=true;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }
    $arr['isSuccess']=false;
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    return;
?>