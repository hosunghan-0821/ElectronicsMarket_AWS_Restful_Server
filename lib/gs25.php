


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
    //bot 인식을 막기위해 설정해주는 옵션
    curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.3239.132 Safari/537.36");

    //Set curl to return the data instead of printing it to the browser.
    $data = curl_exec($ch);
    // $dom = new simple_html_dom();
    // $dom->load($data);
    curl_close($ch);
    return $data;
}
//$url = "https://www.cvsnet.co.kr/invoice/tracking.do?invoice_no=".$invoice;
//$url =  "http://nplus.doortodoor.co.kr/web/detail.jsp?slipno=556643121240";
//$url="https://www.lotteglogis.com/home/reservation/tracking/linkView";
//$url ="https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1=6062651970457&displayHeader=N";
$url="https://www.cvsnet.co.kr/invoice/tracking.do?invoice_no=210074072073";
$str = file_get_contents_curl($url);
echo $str;
// echo "여기부터 실험실 <br>";
// $arr=$dom->find("table");
// $tr=$arr[1]->find("tr");
// $size= count($tr);

// $timeArr=array();
// $placeArr=array();
// $detailArr=array();

// for($i=1;$i<$size;$i++){
//     // echo $tr[$i]->find("td")[1]->plaintext;
//     $timeElement=$tr[$i]->find("td")[1]->plaintext;
//     array_push($timeArr,$timeElement);

//     $placeElement=$tr[$i]->find("td")[3]->plaintext;
//     $placeElement=str_replace("\t","",$placeElement);
//     array_push($placeArr,$placeElement);

//     $detailElement=$tr[$i]->find("td")[2]->plaintext;
//     $detailElement=str_replace("\r\n","", $detailElement);
    
//     array_push($detailArr,$detailElement);
// }
//     $info['timeArr']=$timeArr;
//     $info['placeArr']=$placeArr;
//     $info['detailArr']=$detailArr;

//     echo json_encode($info,JSON_UNESCAPED_UNICODE);
//     return;


// for($i=3;$i<$size;$i++){
//     //echo $arr[$i]."<br>";
//     //echo $asd=$arr[$i]->find("td")[1]->plaintext."<br>";
//     $dateElement=$arr[$i]->find("td")[0]->plaintext;
//     $timeElement=$arr[$i]->find("td")[1]->plaintext;
//     $timeElement=str_replace("&nbsp"," ",$dateElement." ".$timeElement);
//     array_push($timeArr, $timeElement);
    
//     //echo $arr[$i]->find("td")[2]."<br>";
//     $placeElement=$arr[$i]->find("td")[2]->plaintext;
//     $placeElement=str_replace(" ","",$placeElement);
//     $placeElement=str_replace("\t","",$placeElement);
//     $placeElement=str_replace("\r\n","  ",$placeElement);
//     array_push($placeArr,$placeElement);
    
//     //echo $arr[$i]->find("td")[3]."<br>";
//     $detailElement=$arr[$i]->find("td")[3]->plaintext;
//     $detailElement=str_replace("\t","",$detailElement);
   
//     $detailElement=str_replace("\r\n"," ",$detailElement);
//     $detailElement=str_replace("(집배원 정보 보기)"," ",$detailElement);
//     array_push($detailArr,$detailElement);
// }
// $info['timeArr']=$timeArr;
// $info['placeArr']=$placeArr;
// $info['detailArr']=$detailArr;

// echo json_encode($info,JSON_UNESCAPED_UNICODE);
// return;

// echo $arr;
// $arr=$dom->find("tr");
// foreach($arr as $td){
//     foreach($td->find('td') as $unit){
//         echo $unit."<br><br>";
//     }
// }


// $url ="http://43.201.72.60/realMarketServer/lib/crawling.php";
// $str = file_get_contents_curl($url);

// $dom = new simple_html_dom();
// $dom->load($str);

// echo $str;


?>

