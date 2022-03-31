<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    
    if(isset($_POST['nickname'])){
        
        $notificationAllInfo=array();
        $notificationInfo=array();

        $nickname=$_POST['nickname'];
        $phasingNum=$_POST['phasingNum'];
        $cursorNum=$_POST['finalNotificationNum'];
        $purpose=$_POST['purpose'];
        
        //기존 데이터 업데이트 아닐경우
        if($purpose!=="update"){
            //커서가 존재할 경우
            if($cursorNum==="0"){
                $sql="SELECT * FROM Notification_collect where Notification_member='$nickname' order by Notification_reg_time desc limit $phasingNum ";
            }
            else{
                $sql="SELECT * FROM Notification_collect where Notification_member='$nickname' and Notification_no<$cursorNum order by Notification_reg_time desc limit $phasingNum";
            }
        }
        //기존 내용 업데이트일 경우
        else{
            if($cursorNum==="0"){
                $sql="SELECT * FROM Notification_collect where Notification_member='$nickname' order by Notification_reg_time desc limit $phasingNum ";
            }
            else{
                $sql="SELECT * FROM Notification_collect where Notification_member='$nickname' and Notification_no>=$cursorNum order by Notification_reg_time desc ";
            }

        }
     

        $selectResult=mysqli_query($db_connect,$sql);

        while($Data=mysqli_fetch_array($selectResult)){

            $arr['type']=$Data['Notification_type'];
            $arr['message']=$Data['Notification_text'];
            $arr['postNum']=$Data['Notification_post_num'];
            $arr['notificationNum']=$Data['Notification_no'];
            $arr['notificationRegTime']=$Data['Notification_reg_time'];
            $arr['notificationIsRead']=$Data['Notification_is_read'];

            //만약 후기를 남겨줘야하는 notification일 경우, 리뷰가 존재하는지 확인해야함.
            if($Data['Notification_type']==='0'){

                $postNum=$Data['Notification_post_num'];
                $sql="SELECT * FROM Post_review where Review_post_no='$postNum'";
                $selectResult2=mysqli_query($db_connect,$sql);

                if(mysqli_num_rows($selectResult2)>0){
                    $arr['isReview']=true;
                }
                else{
                    $arr['isReview']=false;
                }

            }

            array_push($notificationInfo,$arr);

        }
        $notificationNum=count($notificationInfo);
        $notificationAllInfo['notificationNum']=$notificationNum;
        $notificationAllInfo['notificationDataList']=$notificationInfo;


        //이 postApi에 들어왔다는 것은, 알림을 읽었다는 뜻이고 알림 읽음 처리 해줘야함.
        $sql="UPDATE Notification_collect SET Notification_is_read='1' where Notification_member='$nickname'";
        mysqli_query($db_connect,$sql);

        echo json_encode($notificationAllInfo,JSON_UNESCAPED_UNICODE);
    }
?>