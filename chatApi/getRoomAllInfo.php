<?php 
  require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

  if(isset($_POST['nickname'])){

    $chatRoomAllInfo=array();
    $chatRoomInfo=array();

    $nickname=$_POST['nickname'];
    $finalChatRoom=$_POST['cursorChatRoom'];
    $phasingNum=$_POST['phasingNum'];

    //일단 채팅 참여자중에 넘어온 유저 정보가 들어가 있는 채팅방에 대한 정보를 나타내주어야함. // 나중에 여기다 채팅방 저장한거 기록해서 inner join 박아야함
   
    //업데이트가 아닐 경우;
    if($phasingNum!=="update"){

       //커서가 없을 경우
      if($finalChatRoom==0){
        $sql="SELECT * FROM Chat_room where (Room_buyer='$nickname' or Room_seller='$nickname') Order by Room_final_caht_reg_time desc limit $phasingNum ";
      }
      //커서가 존재할 경우
      else{
        $sql="SELECT * FROM Chat_room where ((Room_buyer='$nickname' or Room_seller='$nickname') and str_to_date(Room_final_caht_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalChatRoom' ) Order by Room_final_caht_reg_time desc limit $phasingNum";
      }
    }
    //새로고침일 경우 onResume 
    //이부분 수정해야함
    else{
      //커서가 없을 경우
      if($finalChatRoom==0){
        $phasingNum=7;
        $sql="SELECT * FROM Chat_room where (Room_buyer='$nickname' or Room_seller='$nickname') Order by Room_final_caht_reg_time desc limit $phasingNum";
      }
      //커서가 있을 경우
      else{
        $sql="SELECT * FROM Chat_room where( (Room_buyer='$nickname' or Room_seller='$nickname') and str_to_date(Room_final_caht_reg_time,'%Y-%m-%d %H:%i:%s')>='$finalChatRoom'  ) Order by Room_final_caht_reg_time desc ";
      }
     
    }

    
   
    //보내야할 정보, 채팅방 번호, 상대방 정보(닉네임 이미지), 채팅방 마지막 채팅, 읽지 않은 메시지,
    $selectResult=mysqli_query($db_connect,$sql);

    while($Data=mysqli_fetch_array($selectResult)){

        $roomNum=$Data['Room_no'];
        $arr['roomNum']=$Data['Room_no'];
        if($Data['Room_final_caht_reg_time']===null){
          continue;
        }


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
        $arr['finalChat']=$Data['Room_final_chat'];
        $arr['finalChatTime']=$Data['Room_final_caht_reg_time'];

        //채팅방의 마지막 채팅의 시간과 정보를 보낸다.
        // $sql="SELECT * FROM Chat_content where Chat_room_no=$roomNum Order by Chat_reg_time desc limit 1";
        // $selectResult2=mysqli_query($db_connect,$sql);
        // $finalChatData=mysqli_fetch_array($selectResult2);

        // $arr['finalChat']=$finalChatData['Chat_text'];
        // $arr['finalChatTime']=$finalChatData['Chat_reg_time'];

        //채팅방 읽지 않은 메시지 몇개인지 query해서 이정보도 전달.
        $sql="SELECT * FROM Chat_content where (Chat_member='$otherUserNickname' and Chat_room_no='$roomNum' and Chat_read='0')";
        $noReadData=mysqli_query($db_connect,$sql);
        $noReadMessage=mysqli_num_rows($noReadData);
        $arr['noReadMessageNum']=$noReadMessage;


        //각 존재하는 방마다의 정보를 한 배열안에 차곡차곡담기
        array_push($chatRoomInfo,$arr);
       
    }
    $chatRoomAllInfo['roomList']=$chatRoomInfo;  
    $roomCount=count($chatRoomInfo);
    $chatRoomAllInfo['roomCount']=$roomCount;
    echo json_encode($chatRoomAllInfo,JSON_UNESCAPED_UNICODE);

  }
?>