<?php 
    ini_set("allow_url_fopen",1);
    ini_set( "display_errors", 1 );
    include_once 'simple_html_dom.php';
    function file_get_contents_curl($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_FAILONERROR,true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // 302 found 에러 발생으로 추가
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.3239.132 Safari/537.36");
 
        //Set curl to return the data instead of printing it to the browser.
        $data = curl_exec($ch);
        // $dom = new simple_html_dom();
        // $dom->load($data);
        curl_close($ch);
        return $data;
    }
    // $url = "https://www.hanjin.co.kr/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&wblnum=421804437784&schLang=KR&wblnumText=";
    // $str = file_get_contents_curl($url);
    // echo $str;

    // $data=$str->find("div.waybill-tbl");
    // // echo $data;
    // foreach($data as $unit){
    //     echo $unit;
    // }

    if(isset($_POST['deliveryCompany'])){
        $deliveryCompany=$_POST['deliveryCompany'];
        $deliveryNum=$_POST['deliveryNum'];
        $url="https://www.hanjin.co.kr/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&wblnum=".$deliveryNum."&schLang=KR&wblnumText=";
        $str=file_get_contents_curl($url);
        $dom = new simple_html_dom();
        $dom->load($str);
        // echo $dom;

        $timeArr=array();
        $placeArr=array();
        $detailArr=array();
       
        $dateData=$dom->find("td.w-date");
        $timeData=$dom->find("td.w-time");
        $placeData=$dom->find("td.w-org");
        $detailData=$dom->find("span.stateDesc");

        $size=count($dateData);
        for($i=0;$i<$size;$i++){
            array_push($timeArr,$dateData[$i]->plaintext." ".$timeData[$i]->plaintext);
            array_push($placeArr,$placeData[$i]->plaintext);
            array_push($detailArr,$detailData[$i]->plaintext);
        }

        $arr['timeArr']=$timeArr;
        $arr['placeArr']=$placeArr;
        $arr['detailArr']=$detailArr;
        if($size==0){
            $arr['isSuccess']=false;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
            return;
        }
        $arr['isSuccess']=true;
        echo json_encode($arr,JSON_UNESCAPED_UNICODE);
       
        return;
    }

    $asd;
        // $deliveryNum="421804437784";
    // $url ="http://ec2-3-34-199-7.ap-northeast-2.compute.amazonaws.com/realMarketServer/lib/gs25.php?invoice=210074072073";
    // $str = file_get_contents_curl($url);
    // $dom = new simple_html_dom();
    // $dom->load($str);
    // echo $str;

    // $data=$dom->find("li");
    // // echo $data;
    // foreach($data as $unit){
    //     echo $unit;
    // }
    // echo $str; 
?>
