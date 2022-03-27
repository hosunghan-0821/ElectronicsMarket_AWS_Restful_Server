<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');
    if(isset($_POST['postNum'])){

        $InquireAllInfo=array();
        $InquireInfo=array();
        //기본적으로 받을 데이터 변수 받기
        $postNum=$_POST['postNum'];
        $nickname=$_POST['nickname'];
        $finalChatRegTime=$_POST['finalChatRegTime'];
        $phasingNum=$_POST['phasingNum'];
        $purpose=$_POST['purpose'];


        //기본적인 판매데이터
        $sql="SELECT * FROM Post as a INNER JOIN Image as b on(a.Post_no=b.Image_post) where (Post_no='$postNum' and b.Image_order='0') ";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        $InquireAllInfo['postNum']=$Data['Post_no'];
        $InquireAllInfo['postTitle']=$Data['Post_title'];
        $InquireAllInfo['postPrice']=$Data['Post_price'];
        $InquireAllInfo['productImageRoute']=$Data['Image_route'];

        //구매 문의한 목록가져오기
        
        //커서가 존재하지 않을 경우
        if($purpose==="inquireList"){
            if($finalChatRegTime==='0'){
                $sql="SELECT * FROM Chat_room where ( (Room_seller='$nickname' or Room_buyer='$nickname') and Room_post_no='$postNum' ) order by Room_final_caht_reg_time desc limit $phasingNum";
            }
            else{
                $sql="SELECT * FROM Chat_room where ( (Room_seller='$nickname' or Room_buyer='$nickname') and Room_post_no='$postNum' and str_to_date(Room_final_caht_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalChatRegTime' ) order by Room_final_caht_reg_time desc limit $phasingNum";
            }
        }
        //한달전 이내의 채팅목록 불러오는 sql
        else if($purpose==="recentChatList"){
                //커서가 존재 x
            $nowDate =date('Y-m-d H:i:s',mktime(0,0,0,date("m")-1,date("d"),date("Y")));
            if($finalChatRegTime==='0'){
            
                $sql="SELECT * FROM Chat_room where ( (Room_seller='$nickname' or Room_buyer='$nickname') and str_to_date(Room_final_caht_reg_time,'%Y-%m-%d %H:%i:%s')>'$nowDate' ) order by Room_final_caht_reg_time desc limit $phasingNum";
            }
               //커서가 존재 
            else{
                $sql="SELECT * FROM Chat_room where ( (Room_seller='$nickname' or Room_buyer='$nickname') and str_to_date(Room_final_caht_reg_time,'%Y-%m-%d %H:%i:%s')>'$nowDate' and str_to_date(Room_final_caht_reg_time,'%Y-%m-%d %H:%i:%s')<'$finalChatRegTime' ) order by Room_final_caht_reg_time desc limit $phasingNum";
            }

        }
    

        
        $selectResult=mysqli_query($db_connect,$sql);

        while($Data=mysqli_fetch_array($selectResult)){
            if($Data['Room_buyer']==$nickname){
                $roomInquirer=$Data['Room_seller'];
                $arr['nickname']=$roomInquirer;
            }
            else if ($Data['Room_seller']==$nickname){
                $roomInquirer=$Data['Room_buyer'];
                $arr['nickname']=$roomInquirer;
            }
            $arr['finalChatTime']=$Data['Room_final_caht_reg_time'];
            $sql="SELECT * FROM Market_member where Member_nickname='$roomInquirer'";
            $selectResult2=mysqli_query($db_connect,$sql);
            $Data2=mysqli_fetch_array($selectResult2);
            $arr['imageRoute']=$Data2['Member_image_route'];
            
            array_push($InquireInfo,$arr);
        }
        $productNum=count($InquireInfo);
        $InquireAllInfo['inquireNum']=$productNum;
        $InquireAllInfo["inquirerList"]=$InquireInfo;
        echo json_encode($InquireAllInfo,JSON_UNESCAPED_UNICODE);
        


    }
    
?>