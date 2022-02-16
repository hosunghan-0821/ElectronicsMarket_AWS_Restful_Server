<?php 
    // ini_set("allow_url_fopen",1);
    // ini_set( "display_errors", 1 );
    include_once 'simple_html_dom.php';



    //get 방식으로 curl 해서 데이터를 크롤링 하려고할 경우.
    function file_get_contents_curl($url){
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

    //posts 방식으로 curl 해서 데이터를 크롤링 하려고할 경우.
    function file_get_contents_curl_post($url,$data){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $headers = array(
            "Content-Type: application/x-www-form-urlencoded",
         );
        curl_setopt($ch,CURLOPT_FAILONERROR,true);
        curl_setopt($ch,CURLOPT_HEADER,$headers);
        curl_setopt($ch,CURLOPT_POST, true);
       
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        // InvNo: 403913118862
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  // 302 found 에러 발생으로 추가
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.3239.132 Safari/537.36");
 
        //Set curl to return the data instead of printing it to the browser.
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    function set_delivery_status($tradeNum){
        require_once $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

    
        //배송상태 변경을 위한 작업
        $sql="UPDATE Trade_delivery_info SET Delivery_status='배송완료' where Delivery_trade_no='$tradeNum' ";
        $updateResult=mysqli_query($db_connect,$sql);
        if($updateResult){
            // 해당하는 거래번호가 어느 게시물의 거래번혼지 확인하고 db변경해주기 위한 작업
          $sql="SELECT * from Post_trade where Trade_no='$tradeNum'";
          $selectResult=mysqli_query($db_connect,$sql);
          $Data=mysqli_fetch_array($selectResult);
          $postNum=$Data['Trade_post_no'];

          $sql="SELECT Post_status FROM Post where Post_no='$postNum'";
          $selectResult=mysqli_query($db_connect,$sql);
          $Data=mysqli_fetch_array($selectResult);

          if($Data['Post_status']!=='S'){
            $sql="UPDATE Post SET Post_status='DF' where Post_no=$postNum ";
            $updateResult=mysqli_query($db_connect,$sql);
          }
        
        }
    }

    if(isset($_POST['deliveryCompany'])){

        //한진택배일 경우 크롤링.
        if($_POST['deliveryCompany']==="한진택배"){
            $deliveryCompany=$_POST['deliveryCompany'];
            $deliveryNum=$_POST['deliveryNum'];
            $tradeNum=$_POST['tradeNum'];
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
            $j=0;
            for($i=$size;$i>0;$i--){
                array_push($timeArr,$dateData[$i-1]->plaintext." ".$timeData[$i-1]->plaintext);
                array_push($placeArr,$placeData[$i-1]->plaintext);
                array_push($detailArr,$detailData[$i-1]->plaintext);
                $j++;
            }
    
            $arr['timeArr']=$timeArr;
            $arr['placeArr']=$placeArr;
            $arr['detailArr']=$detailArr;
            if($size==0){
                $arr['isSuccess']=false;
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);
                return;
            }

            $checkDelivery=$dom->find("p.comm-sec")[0]->plaintext;
            if(preg_match("/배송완료/", $checkDelivery)){
                $arr['deliveryStatus']="배송완료";
                set_delivery_status($tradeNum);
            }
            else{
                $arr['deliveryStatus']="배송중";
            }
            $arr['isSuccess']=true;
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
           
            return;
        }

        //롯데 택배일 경우
        else if($_POST['deliveryCompany']==="롯데택배"){

            $deliveryCompany=$_POST['deliveryCompany'];
            $deliveryNum=$_POST['deliveryNum'];
            $tradeNum=$_POST['tradeNum'];

            $url="https://www.lotteglogis.com/home/reservation/tracking/linkView";
            $data = "InvNo=".$deliveryNum;
            $str = file_get_contents_curl_post($url,$data);

            $dom = new simple_html_dom();
            $dom->load($str);

            $arr=$dom->find("tr");
            $size= count($arr);
            $timeArr=array();
            $placeArr=array();
            $detailArr=array();

            $asd=$arr[3]->find("td")[0]->plaintext;
            $asd=str_replace("\t","",$asd);
            if($asd=="보내시는 고객님께서 상품 발송 준비중입니다."){
                $info['isSuccess']=false;
                echo json_encode($info,JSON_UNESCAPED_UNICODE);
                return;
            }
           
            for($i=3;$i<$size;$i++){
                //echo $arr[$i]."<br>";
                //echo $asd=$arr[$i]->find("td")[1]->plaintext."<br>";
                $timeElement=$arr[$i]->find("td")[1]->plaintext;
                $timeElement=str_replace("&nbsp"," ",$timeElement);
                $timeElement=str_replace(";","",$timeElement);
                array_push($timeArr, $timeElement);
                    
                //echo $arr[$i]->find("td")[2]."<br>";
                $placeElement=$arr[$i]->find("td")[2]->plaintext;
                $placeElement=str_replace(" ","",$placeElement);
                $placeElement=str_replace("\t","",$placeElement);
                array_push($placeArr,$placeElement);
                    
                //echo $arr[$i]->find("td")[3]."<br>";
                $detailElement=$arr[$i]->find("td")[3]->plaintext;
                $detailElement=str_replace("\r\n","  ",$detailElement);
                array_push($detailArr,$detailElement);
            }
            $info['timeArr']=$timeArr;
            $info['placeArr']=$placeArr;
            $info['detailArr']=$detailArr;
            if(preg_match("/배달완료/",$arr[1]->find("td")[3]->plaintext)){
                $info['deliveryStatus']="배송완료";
                set_delivery_status($tradeNum);
            }
            else{
                $info["deliveryStatus"]="배송중";
            }
            
            $info['isSuccess']=true;
            echo json_encode($info,JSON_UNESCAPED_UNICODE);
            return;
         
        }
        //우체국 택배일 경우
        else if($_POST['deliveryCompany']==="우체국택배"){
            $deliveryCompany=$_POST['deliveryCompany'];
            $deliveryNum=$_POST['deliveryNum'];
            $tradeNum=$_POST['tradeNum'];

            $url ="https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1=".$deliveryNum."&displayHeader=N";
            $str = file_get_contents($url);
        
            $dom = new simple_html_dom();
            $dom->load($str);

            $arr=$dom->find("tr");
            $size= count($arr);
            // echo $size;
            
            //asd 이 값이 배송결과 값이다.
            $deliveryCheck=$arr[1]->find("td")[4]->plaintext;
           
           
            // 배달정보가 없을경우
            if($deliveryCheck==""){
                $info['isSuccess']=false;
                echo json_encode($info,JSON_UNESCAPED_UNICODE);
                return;
            }
            if($deliveryCheck=="배달완료"){
                $info['deliveryStatus']="배송완료";
                set_delivery_status($tradeNum);
            }
            else{
                $info['deliveryStatus']="배송중";
            }
          
            $timeArr=array();
            $placeArr=array();
            $detailArr=array();

            for($i=$size;$i>3;$i--){
                //echo $arr[$i]."<br>";
                //echo $asd=$arr[$i]->find("td")[1]->plaintext."<br>";
                $dateElement=$arr[$i-1]->find("td")[0]->plaintext;
                $timeElement=$arr[$i-1]->find("td")[1]->plaintext;
                $timeElement=str_replace("&nbsp"," ",$dateElement." ".$timeElement);
                array_push($timeArr, $timeElement);
                
                //echo $arr[$i]->find("td")[2]."<br>";
                $placeElement=$arr[$i-1]->find("td")[2]->plaintext;
                $placeElement=str_replace(" ","",$placeElement);
                $placeElement=str_replace("\t","",$placeElement);
                $placeElement=str_replace("\r\n","  ",$placeElement);
                array_push($placeArr,$placeElement);
                
                //echo $arr[$i]->find("td")[3]."<br>";
                $detailElement=$arr[$i-1]->find("td")[3]->plaintext;
                $detailElement=str_replace(" ","",$detailElement);
                $detailElement=str_replace("\t","",$detailElement);
                $detailElement=str_replace(";","",$detailElement);
                $detailElement=str_replace("&nbsp","",$detailElement);
                $detailElement=str_replace("\r\n"," ",$detailElement);
                $detailElement=str_replace("(집배원정보보기)","",$detailElement);
                array_push($detailArr,$detailElement);
            }
            $info['timeArr']=$timeArr;
            $info['placeArr']=$placeArr;
            $info['detailArr']=$detailArr;
        
            $info['isSuccess']=true;
            echo json_encode($info,JSON_UNESCAPED_UNICODE);
            return;
        }

        //cj 대한통운일 경우
        else if($_POST['deliveryCompany']==="CJ대한통운"){

            $deliveryCompany=$_POST['deliveryCompany'];
            $deliveryNum=$_POST['deliveryNum'];
            $tradeNum=$_POST['tradeNum'];

            $url="https://www.doortodoor.co.kr/parcel/doortodoor.do";
            $data = "invc_no=".$deliveryNum."&fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT2";
            $str = file_get_contents_curl_post($url,$data);

            $dom = new simple_html_dom();
            $dom->load($str);

            $arr=$dom->find("table");

            $exceptionCheck=$arr[0]->find("tr")[1]->find("td")[0]->plaintext;

             //예외처리 해줘야함
            if(preg_match("/조회된 데이터가 없습니다./", $exceptionCheck)){
                $info['isSuccess']=false;
                echo json_encode($info,JSON_UNESCAPED_UNICODE);
                return;
            }

            $tr=$arr[1]->find("tr");
            $size= count($tr);
        
           
            $timeArr=array();
            $placeArr=array();
            $detailArr=array();

            for($i=$size;$i>1;$i--){
                // echo $tr[$i]->find("td")[1]->plaintext;
               
                $timeElement=$tr[$i-1]->find("td")[1]->plaintext;
                array_push($timeArr,$timeElement);
        
                $placeElement=$tr[$i-1]->find("td")[3]->plaintext;
                $placeElement=str_replace("\t","",$placeElement);
                array_push($placeArr,$placeElement);
        
                $detailElement=$tr[$i-1]->find("td")[2]->plaintext;
                $detailElement=str_replace("\r\n","", $detailElement);
                
                array_push($detailArr,$detailElement);
            }
            $deliveryCheck=$tr[$size-1]->find("td")[0]->plaintext;
            // $deliveryCheck=str_replace("\t","",$deliveryCheck);
            if(preg_match("/배달완료/",$deliveryCheck)){

                $info['deliveryStatus']="배송완료";
                set_delivery_status($tradeNum);

            }
            else{

                $info['deliveryStatus']="배송중";

            }
            $info['timeArr']=$timeArr;
            $info['placeArr']=$placeArr;
            $info['detailArr']=$detailArr;

            $info['isSuccess']=true;
            echo json_encode($info,JSON_UNESCAPED_UNICODE);
            return;
        }
      
    }

?>
