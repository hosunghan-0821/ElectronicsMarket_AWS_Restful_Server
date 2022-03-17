<?php 
      require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
      
      if(isset($_POST['roomNum'])){

          $nickname=$_POST['nickname'];
          $roomNum=$_POST['roomNum'];
          $phasingNum=$_POST['phasingNum'];
          $finalChatNum=$_POST['cursorChatNum'];


          //채팅방에 들어왔을 경우, 데이터 읽음 처리를 해야한다. 
          $sql="UPDATE Chat_content set Chat_read='1' where (Chat_room_no='$roomNum' and Chat_member !='$nickname') ";
          $updateResult=mysqli_query($db_connect,$sql);

          $sql="SELECT Chat_no From Chat_content where ((Chat_room_out_check_1='$nickname' or Chat_room_out_check_2='$nickname') and Chat_room_no='$roomNum') ";
          $selectResult0=mysqli_query($db_connect,$sql);
          $checkedData=mysqli_fetch_array($selectResult0);
          $chatCheckedNum=$checkedData['Chat_no'];

          //페이징 커서 없을 때,
          if($phasingNum!=="update"){

                if($finalChatNum==0){

                    if($chatCheckedNum==null){
                        $sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' Order by Chat_no desc limit $phasingNum";
                    }
                    else{
                        $sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' and Chat_no>$chatCheckedNum Order by Chat_no desc limit $phasingNum";
                    }
                   
                    //$sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' Order by Chat_reg_time desc limit $phasingNum";
                }
                //기존의 커서가 존재하면 그 커서 기준으로 phasing 개수만큼 가져오기
                else{
                    if($chatCheckedNum==null){
                        $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and Chat_no<'$finalChatNum') Order by Chat_no desc limit $phasingNum ";
                    }
                    else{
                        $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and Chat_no<'$finalChatNum' and Chat_no>$chatCheckedNum) Order by Chat_no desc limit $phasingNum ";
                    }
                
                    //$sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and str_to_date(Chat_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalChatNum') Order by Chat_reg_time desc limit $phasingNum ";
                }
          }
          else{

            //커서가 없을 경우
            if($finalChatNum==0){
                
                if($chatCheckedNum==null){
                    $phasingNum=10;
                    $sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' Order by Chat_no desc limit $phasingNum";
                }
                else{
                    $phasingNum=10;
                    $sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' and Chat_no>$chatCheckedNum Order by Chat_no desc limit $phasingNum";
                }
             
                //$sql="SELECT * FROM Chat_content where Chat_room_no='$roomNum' Order by Chat_reg_time desc limit $phasingNum";
            }
            //커서가 존재할 경우
            else{
                if($chatCheckedNum==null){
                    $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and Chat_no>='$finalChatNum') Order by Chat_no desc" ;
                }
                else{
                    $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and Chat_no>='$finalChatNum' and Chat_no>$chatCheckedNum) Order by Chat_no desc" ;
                }
            }

          }

          $chatData=array();
          $chatDataAll=array();

          $selectResult=mysqli_query($db_connect,$sql);
          if($selectResult){
              $skipDate=0;
              while($Data=mysqli_fetch_array($selectResult)){

                //나가기 기록이 있고, 서버 날짜 채팅일 경우.
                if( $Data['Chat_member']=='server'){
                    
                    $chatDate=$Data['Chat_text'];
                    $chatNo=$Data['Chat_no'];
                    if( $chatCheckedNum!=null){

                        $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and Chat_no>$chatCheckedNum and Chat_member='server' and Chat_text='$chatDate') order by Chat_no asc";
                        $selectLastChatDate= mysqli_query($db_connect,$sql);
                        $lastDateData=mysqli_fetch_array($selectLastChatDate);
                        if($chatNo!=$lastDateData['Chat_no']){
                            $skipDate++;
                            continue;
                        }
                    }
                    else{
                        $sql="SELECT * FROM Chat_content where (Chat_room_no='$roomNum' and Chat_member='server' and Chat_text='$chatDate') order by Chat_no asc";
                        $selectLastChatDate= mysqli_query($db_connect,$sql);
                        $lastDateData=mysqli_fetch_array($selectLastChatDate);
                        if($chatNo!=$lastDateData['Chat_no']){
                            $skipDate++;
                            continue;
                        }
                    }
                }

                  $arr['chatNum']=$Data['Chat_no'];
                  $arr['nickname']=$Data['Chat_member'];
                  $arr['chat']=$Data['Chat_text'];
                  $arr['chatTime']=$Data['Chat_reg_time'];
                  $arr['isReadChat']=$Data['Chat_read'];
                  $arr['chatType']=$Data['Chat_type'];
                  array_push($chatData,$arr);
              }
              $chatDataAll['dataChatAllList']=$chatData;
              $chatNum=count($chatData);
              //여기 추가하는 이유는, phasing을 넘어오는 데이터 개수10개인지 확인하면서 phasing 추가하는데 날짜 스킵해버리면 
              //데이터 비는 문제.. 이걸 갯수 세서 표시
              $chatNum+=$skipDate;
              $chatDataAll['chatNum']=$chatNum;
              echo json_encode($chatDataAll,JSON_UNESCAPED_UNICODE);
          }

      }
?>