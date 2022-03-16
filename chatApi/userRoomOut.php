<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    
    if(isset($_POST['roomNum'])){
        $roomNum=$_POST['roomNum'];
        $nickname=$_POST['nickname'];
        $otherUserNickname=$_POST['otherUserNickname'];


        

        //채팅방 나간 시점 확인해서 표시해야함
        $sql="SELECT * FROM Chat_content where (Chat_room_no=$roomNum and (Chat_room_out_check_1='$nickname' or Chat_room_out_check_1='$otherUserNickname'))";
        $selectResult = mysqli_query($db_connect,$sql);
        //채팅방 나간적이 아무도 없을 경우
        if(mysqli_num_rows($selectResult)==0){
            //이 경우는 room_out_check1 의 마지막 채팅에 표시
            $sql="SELECT * FROM Chat_content where Chat_room_no=$roomNum order by Chat_no Desc";
            $selectResult=mysqli_query($db_connect,$sql);
            $Data=mysqli_fetch_array($selectResult);
            $chatNum=$Data['Chat_no'];

            $sql="UPDATE Chat_content set Chat_room_out_check_1='$nickname' where Chat_no=$chatNum";
            $updateResult=mysqli_query($db_connect,$sql);
            $arr['isSuccess']=true;
        }
        //채팅방 나간적이 있는 유저.
        else{
            $selectResult=mysqli_query($db_connect,$sql);
            $Data=mysqli_fetch_array($selectResult);

            //처음으로 나간적이 있는  유저일 경우
            if($Data['Chat_room_out_check_1']===$nickname){
                //이 경우, data의 있는 chat_no를 얻고, 그 chat_no에 있는 표시를 옮겨줘야함
                $chatNum=$Data['Chat_no'];
                $sql="UPDATE Chat_content set Chat_room_out_check_1=NULL WHERE Chat_no=  $chatNum  ";
                $updateResult=mysqli_query($db_connect,$sql);

                $sql="SELECT * FROM Chat_content where Chat_room_no=$roomNum order by Chat_no Desc";
                $selectResult=mysqli_query($db_connect,$sql);
                $Data=mysqli_fetch_array($selectResult);
                $chatNum=$Data['Chat_no'];
    
                $sql="UPDATE Chat_content set Chat_room_out_check_1='$nickname' where Chat_no=$chatNum";
                $updateResult=mysqli_query($db_connect,$sql);
                $arr['isSuccess']=true;
            }
            //두번째 칼럼에서 내가 나간적 있는지 확인해야함

            else if($Data['Chat_room_out_check_1']===$otherUserNickname){
                //이 경우, chat_room_out_check_2를 검사해야함

                $sql="SELECT * FROM Chat_content where (Chat_room_no=$roomNum and Chat_room_out_check_2='$nickname')";
                $selectResult = mysqli_query($db_connect,$sql);
                //chat_room_out_check_2에 없을 떄,
                if(mysqli_num_rows($selectResult)==0){
                    //이 경우는 room_out_check1 의 마지막 채팅에 표시
                    $sql="SELECT * FROM Chat_content where Chat_room_no=$roomNum order by Chat_no Desc";
                    $selectResult=mysqli_query($db_connect,$sql);
                    $Data=mysqli_fetch_array($selectResult);
                    $chatNum=$Data['Chat_no'];
        
                    $sql="UPDATE Chat_content set Chat_room_out_check_2='$nickname' where Chat_no=$chatNum";
                    $updateResult=mysqli_query($db_connect,$sql);
                    $arr['isSuccess']=true;
                }
                //chat_room_out_check_1에 존재할 떄,
                else{

                    //이 경우, data의 있는 chat_no를 얻고, 그 chat_no에 있는 표시를 옮겨줘야함
                    $selectResult=mysqli_query($db_connect,$sql);
                    $Data=mysqli_fetch_array($selectResult);
                    $chatNum=$Data['Chat_no'];
                    $sql="UPDATE Chat_content set Chat_room_out_check_2=NULL WHERE Chat_no=  $chatNum  ";
                    $updateResult=mysqli_query($db_connect,$sql);

                    $sql="SELECT * FROM Chat_content where Chat_room_no=$roomNum order by Chat_no Desc";
                    $selectResult=mysqli_query($db_connect,$sql);
                    $Data=mysqli_fetch_array($selectResult);
                    $chatNum=$Data['Chat_no'];
        
                    $sql="UPDATE Chat_content set Chat_room_out_check_2='$nickname' where Chat_no=$chatNum";
                    $updateResult=mysqli_query($db_connect,$sql);
                    $arr['isSuccess']=true;
                }
            }
        }
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
    }
?>