<?php 
      require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
      
      if(isset($_POST['roomNum'])){


          $roomNum=$_POST['roomNum'];
          $phasingNum=$_POST['phasingNum'];
          $finalChatNum=$_POST['cursorChatNum'];

          //페이징 커서 없을 때,
          if($finalChatNum==0){
              $sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' Order by Chat_reg_time desc limit $phasingNum";
          }
          //기존의 커서가 존재하면 그 커서 기준으로 phasing 개수만큼 가져오기
          else{
              $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and str_to_date(Chat_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalChatNum') Order by Chat_reg_time desc limit $phasingNum ";
          }
          $chatData=array();
          $chatDataAll=array();

          $selectResult=mysqli_query($db_connect,$sql);
          if($selectResult){
              while($Data=mysqli_fetch_array($selectResult)){
                  $arr['nickname']=$Data['Chat_member'];
                  $arr['chat']=$Data['Chat_text'];
                  $arr['chatTime']=$Data['Chat_reg_time'];
                  $arr['isReadChat']=$Data['Chat_read'];
                  array_push($chatData,$arr);
              }
              $chatDataAll['dataChatAllList']=$chatData;
              $chatNum=count($chatData);
              $chatDataAll['chatNum']=$chatNum;
              echo json_encode($chatDataAll,JSON_UNESCAPED_UNICODE);
          }
      }
?>