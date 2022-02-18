<?php

    $invoice = $_GET['invoice'];
    ini_set("allow_url_fopen",1);
    ini_set( "display_errors", 1 );
    include_once 'simple_html_dom.php';

    function file_get_contents_curl($url) {
        $ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_FAILONERROR,true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // 302 found 에러 발생으로 추가
        //bot 인식을 막기위해 설정해주는 옵션
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.3239.132 Safari/537.36");
    
        //Set curl to return the data instead of printing it to the browser.
        $data = curl_exec($ch);
        // $dom = new simple_html_dom();
        // $dom->load($data);
        curl_close($ch);
        return $data;
    }
    function findStringBetweenAnB($dest,$A,$B){
        $fristFindIdx=strpos($dest,$A);
        $fristFindIdx=$fristFindIdx+strlen($A);
        $secondFindIdx= strpos($dest,$B,$fristFindIdx);
        $finalSearString= trim(substr($dest,$fristFindIdx,$secondFindIdx-$fristFindIdx));
        return $finalSearString;
    }

    $url="https://www.cvsnet.co.kr/invoice/tracking.do?invoice_no=210074072073";
    $str = file_get_contents_curl($url);

    $nowWeather=findStringBetweenAnB($str,'"trackingDetails":',',"carrierType');
    $nowWeather='"trackingDetails":'.$nowWeather;
    // $object=json_decode($nowWeather);

    echo '{'.$nowWeather.'}';
?>