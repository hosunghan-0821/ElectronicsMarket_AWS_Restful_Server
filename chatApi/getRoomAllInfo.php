<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

  if(isset($_POST['nickname'])){

    $chatRoomAllInfo=array();
    $chatRoomInfo=array();

    $nickname=$_POST['nickname'];
    //일단 채팅 참여자중에 넘어온 유저 정보가 들어가 있는 채팅방에 대한 정보를 나타내주어야함. // 나중에 여기다 채팅방 저장한거 기록해서 inner join 박아야함
    $sql="SELECT * FROM Chat_room where (Room_buyer='$nickname' or Room_seller='$nickname') ";

    //보내야할 정보, 채팅방 번호, 상대방 정보(닉네임 이미지), 채팅방 마지막 채팅, 읽지 않은 메시지,
    $selectResult=mysqli_query($db_connect,$sql);

    while($Data=mysqli_fetch_array($selectResult)){
        $arr['roomNum']=$Data['Room_no'];
        //유저가 구매자 입장일 경우, 판매자 정보를 전달

        if($nickname==$Data['Room_buyer']){
            
            $otherUserNickname=$Data['Room_seller'];
            $sql="SELECT * FROM Market_member where Member_nickname='$otherUserNickname' ";
            $selectResult2=mysqli_query($db_connect,$sql);
            $otherUserData=mysqli_fetch_array($selectResult2);
            $arr['otherUserImageRoute']=$otherUserData['Member_image_route'];
            $arr['otherUserNickname']=$otherUserNickname;
        }

        //유저가 판매자 일 경우, 구매자 정보를 전달
        else if($nickname==$Data['Room_seller']){
            $otherUserNickname=$Data['Room_buyer'];
            $sql="SELECT * FROM Market_member where Member_nickname='$otherUserNickname' ";
            $selectResult2=mysqli_query($db_connect,$sql);
            $otherUserData=mysqli_fetch_array($selectResult2);
            $arr['otherUserImageRoute']=$otherUserData['Member_image_route'];
            $arr['otherUserNickname']=$otherUserNickname;

        }
        //각 존재하는 방마다의 정보를 한 배열안에 차곡차곡담기
        array_push($chatRoomInfo,$arr);
       
    }
    $chatRoomAllInfo['roomList']=$chatRoomInfo;  
    echo json_encode($chatRoomAllInfo,JSON_UNESCAPED_UNICODE);

  }
?>