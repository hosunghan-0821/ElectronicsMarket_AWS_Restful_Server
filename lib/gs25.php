<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</head>
<body>
  
</body>
</html>
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
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.3239.132 Safari/537.36");
 
        //Set curl to return the data instead of printing it to the browser.
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    //$url = "https://www.cvsnet.co.kr/invoice/tracking.do?invoice_no=".$invoice;
    $url =  "http://nplus.doortodoor.co.kr/web/detail.jsp?slipno=556643121240";
    $str = file_get_contents_curl($url);
    echo $str;

    // $url ="http://ec2-3-34-199-7.ap-northeast-2.compute.amazonaws.com/realMarketServer/lib/crawling.php";
    // $str = file_get_contents_curl($url);

    // $dom = new simple_html_dom();
    // $dom->load($str);

    // echo $str;

   
?>
