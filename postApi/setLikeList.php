<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');  
    
    
    if(isset($_GET['postNum'])){

        $writer=$_GET['writer'];
        $postNum=$_GET['postNum'];
        $email=$_GET['email'];
        $sql="SELECT Member_nickname FROM Market_member where Member_id='$email'";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);
        $nickname=$Data['Member_nickname'];

        //찜목록 등록 할 경우
        if($_GET['state']==="insert"){
        
            //$sql="SELECT * FROM Post_like left join Market_member on Post_like.Like_person= Market_member.Member_nickname where Member_id='$email'";
            $sql="SELECT * FROM Post_like where Like_person='$nickname'";
            $selectResult=mysqli_query($db_connect,$sql);
            
            //예외처리 여러번 눌렀을 경우 한번만 데이터에 들어갈 수 있도록.
            while($Data=mysqli_fetch_array($selectResult)){
                if($Data['Like_post']==$postNum){
                    $arr['isSuccess']=false;
                    $notiSql="SELECT * FROM Notification_collect where Notification_post_num='$postNum' and Notification_member='$writer' and Notification_type='2'";
                    $notiResult=mysqli_query($db_connect, $notiSql);
                    
                    if(mysqli_num_rows($notiResult)>0){
                        $arr['likeNotification']=false;
                    }
                    else{
                        $arr['likeNotification']=true;
                    }

                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                    return;
                }
            }

            $notiSql="SELECT * FROM Notification_collect where Notification_post_num='$postNum' and Notification_member='$writer' and Notification_type='2' and Notification_sender='$nickname'";
            $notiResult=mysqli_query($db_connect, $notiSql);
            
            if(mysqli_num_rows($notiResult)>0){
                $arr['likeNotification']=false;
            }
            else{
                $arr['likeNotification']=true;
            }

            
            //찜목록 테이블에 해당 정보들 insert
            $sql="INSERT INTO Post_like(Like_post,Like_person) values('$postNum','$nickname')";
            $insertResult=mysqli_query($db_connect,$sql);
            if($insertResult){
                $sql="UPDATE Post SET Post_like=Post_like + 1 WHERE Post_no='$postNum' ";
                mysqli_query($db_connect,$sql);    
                $arr['isSuccess']=true;
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                return;
            }


        }
        //찜목록 삭제할 경우.
        else{
            $sql="DELETE FROM Post_like where (Like_post=$postNum and Like_person='$nickname')";
            $deleteResult=mysqli_query($db_connect,$sql);
            if($deleteResult){
                $sql="UPDATE Post SET Post_like=Post_like - 1 WHERE Post_no='$postNum' ";
                mysqli_query($db_connect,$sql);  

                $arr['isSuccess']=true;
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                return;
            }
            else{
                $arr['isSuccess']=false;
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                return;
            }
        }


    }
    else{
        $arr['isSuccess']=false;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        return;
    }

?>