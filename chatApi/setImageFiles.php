<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');  

    if(isset($_POST['roomNum'])){
        $imageRoute=array();
        $imageRouteAll=array();
        $tmp_file=$_FILES;
        $imageNumber=count($tmp_file);
    
        $nickname=$_POST['nickname'];
        $roomNum=$_POST['roomNum']; 
    
        //이미지 받아서, 업로드 시키기
        for($i=0;$i<$imageNumber;$i++){
            //$nowDate = date("Ymd-His");
            //$now = DateTime::createFromFormat('U.u', number_format(microtime(true), 2, '.', ''));
            $now = new DateTime( "NOW" );
            $nowDate = $now->format("Y-m-d_H:i:s:v");
            $file_name='chatRoom_'.$roomNum.'_';
            $name=$tmp_file['image'.$i]['tmp_name'];
            $file_name.=$nowDate.'_';
            $file_name.=$i.'.jpg';
            $result=move_uploaded_file($name,'../Resource/chatImage/'.$file_name);
            $file_http_path="http://ec2-3-34-199-7.ap-northeast-2.compute.amazonaws.com/realMarketServer/Resource/chatImage/".$file_name;
            $sql="INSERT INTO Chat_image (Image_room_num,Image_route,Image_file_name,Image_reg_time) values($roomNum,'$file_http_path','$file_name','$nowDate')";
            $insertResult=mysqli_query($db_connect,$sql);
            //업로드 및 db 저장 성공시 해당 경로 client에게 전달.
            array_push($imageRoute,$file_http_path);
        }
        $imageRouteAll['imageRoute']=$imageRoute;
        echo json_encode($imageRouteAll,JSON_UNESCAPED_UNICODE);

    }
 
?>