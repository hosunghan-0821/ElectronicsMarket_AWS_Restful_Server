<?php
    $tmp_file=$_FILES;
    $imageNumber=count($tmp_file);
    for($i=0;$i<$imageNumber;$i++){
       
        $name=$tmp_file['image'.$i]['tmp_name'];
        $result=move_uploaded_file($name,'../Resource/'.'image'.$i.'.jpg');
        $name = $_REQUEST['name'];
    }
 
    $result=$_FILES['error'];
    $asd;
    $arr['isSuccess']=false;
    echo json_encode($arr,JSON_UNESCAPED_UNICODE);
?>