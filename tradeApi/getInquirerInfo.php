<?php 
    require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
    header('Content-Type: application/json');
    if(isset($_POST['postNum'])){

        $InquireAllInfo=array();
        $InquireInfo=array();
        //기본적으로 받을 데이터 변수 받기
        $postNum=$_POST['postNum'];
        $nickname=$_POST['nickname'];

        //기본적인 판매데이터
        $sql="SELECT * FROM Post as a INNER JOIN Image as b on(a.Post_no=b.Image_post) where (Post_no='$postNum' and b.Image_order='0') ";
        $selectResult=mysqli_query($db_connect,$sql);
        $Data=mysqli_fetch_array($selectResult);

        $InquireAllInfo['postNum']=$Data['Post_no'];
        $InquireAllInfo['postTitle']=$Data['Post_title'];
        $InquireAllInfo['postPrice']=$Data['Post_price'];
        $InquireAllInfo['productImageRoute']=$Data['Image_route'];

        $sql="SELECT * FROM Chat_room where ( (Room_seller='$nickname' or Room_buyer='$nickname') and Room_post_no='$postNum' ) order by Room_final_caht_reg_time desc";
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
        $InquireAllInfo["inquirerList"]=$InquireInfo;
        echo json_encode($InquireAllInfo,JSON_UNESCAPED_UNICODE);
        


    }
    
?>