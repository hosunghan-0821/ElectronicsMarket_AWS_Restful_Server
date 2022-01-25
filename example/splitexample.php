<?php 

    
    $string ="image0///0///http://ec2-3-36-64-237.ap-northeast-2.compute.amazonaws.com/realMarketServer/Resource/postImage/postNum_47_20220125_104701_1.jpg///1///image2///2///http://ec2-3-36-64-237.ap-northeast-2.compute.amazonaws.com/realMarketServer/Resource/postImage/postNum_47_20220125_104701_2.jpg///3///";
    $arr=explode("///",$string);
    for($i=0;$i<count($arr);$i=$i+1){
        echo $arr[$i];
        echo nl2br("\n");
    }

    $asd;
?>