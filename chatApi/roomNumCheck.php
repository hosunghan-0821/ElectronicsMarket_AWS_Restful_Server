<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    //채팅목록을 통해 방체크를 할 경우, 방 정보주기.
    if(isset($_POST['roomNum'])){
        $roomNum=$_POST['roomNum'];
    }
    //채팅목록이 아닌, 채팅 문의하기를 통해 채팅방 입장할 때, 기존의 채팅방이 존재하는지 없는지에 따라서 채팅변경
    else if(isset($_POST['postNum'])){

        $postNum=$_POST['postNum'];
        $seller=$_POST['seller'];
        $buyer=$_POST['buyer'];
        $nowDate = date("Y-m-d H:i:s");
        $sql="SELECT * FROM Chat_room WHERE (Room_post_no='$postNum' and Room_seller='$seller' and Room_buyer='$buyer') ";
        $selectResult=mysqli_query($db_connect,$sql);
      
        //방이 존재하지 않을 경우. 방생성 ..
        if(mysqli_num_rows($selectResult)==0){
            $sql="INSERT INTO Chat_room (
                Room_post_no,
                Room_seller,
                Room_buyer,
                Room_reg_time)
             values ('$postNum','$seller','$buyer','$nowDate') ";
             $insertResult=mysqli_query($db_connect,$sql);

             if($insertResult){
                 $roomNum=mysqli_insert_id($db_connect);
             }
        }
        //방이 존재하다면, 몇번방인지 확인
        else{
            $selectResult=mysqli_query($db_connect,$sql);
            $data=mysqli_fetch_array($selectResult);
            $roomNum=$data['Room_no'];
        }
    }

    //위에서 채팅방이 새로 만드는게 필요한지 확인, 필요하다면 생성
    //그 후에, 게시글 정보 + 파는사람, 사는사람, 방번호, 등 데이터들을 넘겨주어야함

    $sql="SELECT * FROM Chat_room as a INNER JOIN Post as b INNER JOIN Image as c on (a.Room_post_no=b.Post_no and a.Room_post_no =c.Image_post) where (Room_no='$roomNum' and c.Image_order='0') ";
    $selectResult=mysqli_query($db_connect,$sql);
    $Data=mysqli_fetch_array($selectResult);

    $arr['postNum']=$Data['Post_no'];
    $arr['postTitle']=$Data['Post_title'];
    $arr['postPrice']=$Data['Post_price'];
    $arr['postStatus']=$Data['Post_status'];
    $arr['imageRoute']=$Data['Image_route'];
    $arr['roomNum']=$roomNum;
    //위치정보
    if($Data['Post_location_address']==="장소정보 없음"){
    
    $arr['postLocationName']="장소정보 없음";
    $arr['postLocationAddress']="";
    $arr['postLocationLatitude']=0;
    $arr['postLocationLongitude']=0;
    }
    else{
            $arr['postLocationName']=$Data['Post_location_name'];
            $arr['postLocationAddress']=$Data['Post_location_address'];
            $arr['postLocationLatitude']=$Data['Post_location_latitude'];
            $arr['postLocationLongitude']=$Data['Post_location_longitude'];
            $arr['postLocationDetail']=$Data['Post_place_detail'];
    }
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    return;


?>