<?php 

require $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';
include_once '../lib/simple_html_dom.php';

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
        require $_SERVER['DOCUMENT_ROOT'].'/realMarketServer/lib/dbConnect.php';

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

    if(isset($_POST['tradeNum'])){

        $tradeNum=$_POST['tradeNum'];
        $deliveryNum=$_POST['deliveryNum'];
        $deliveryCompany=$_POST['deliveryCompany'];

        //배송정보에 상태 입력하는 값
        $sql="UPDATE Trade_delivery_info SET Delivery_company_name ='$deliveryCompany', Delivery_delivery_number='$deliveryNum',Delivery_status='배송중' WHERE Delivery_trade_no='$tradeNum'";
        $updateResult=mysqli_query($db_connect,$sql);

        if($updateResult){

            //운송장 번호, 택배사 업데이트 성공 시, 크롤링을 통해서 배송상태를 확인하고,  변경해야함.    
            //운송장 번호, 택배사 입력이 제대로 되어 있으면, 배송상태변경
            
            $sql="SELECT Trade_post_no from Post_trade where Trade_no='$tradeNum'";
            $selectResult=mysqli_query($db_connect,$sql);

            if($selectResult){
                $Data=mysqli_fetch_array($selectResult);
                $postNum=$Data['Trade_post_no'];

                //한진택배 배송정보 크롤링 배송상태 확인해서 배송완료면 배송완료 표시 아니면 배송중 표시
                if($deliveryCompany==="한진택배"){
                    $url="https://www.hanjin.co.kr/kor/CMS/DeliveryMgr/WaybillResult.do?mCode=MN038&wblnum=".$deliveryNum."&schLang=KR&wblnumText=";
                    $str=file_get_contents_curl($url);
                    $dom = new simple_html_dom();
                    $dom->load($str);

                    $dateData=$dom->find("td.w-date");
                    $size=count($dateData);
                    $checkDelivery=$dom->find("p.comm-sec")[0]->plaintext;
                    //배송조회 성공 및 배송완료 표시가 떠잇을 경우, 배송완료 표시로 처리
                    if($size!=0 && preg_match("/배송완료/", $checkDelivery) ){
                        $arr['deliveryStatus']="배송완료";
                        set_delivery_status($tradeNum);
                    }
                    //그렇지 않을 경우, 배송중으로표시
                    else{
                        $sql="UPDATE Post SET Post_status='DS' WHERE Post_no='$postNum' ";
                        mysqli_query($db_connect,$sql);
                    }
                }

                //롯데택배일 경우
                else if($deliveryCompany==="롯데택배"){
                    $url="https://www.lotteglogis.com/home/reservation/tracking/linkView";
                    $data = "InvNo=".$deliveryNum;
                    $str = file_get_contents_curl_post($url,$data);

                    $dom = new simple_html_dom();
                    $dom->load($str);
                    $domArr=$dom->find("tr");
                    
                    $asd=$domArr[3]->find("td")[0]->plaintext;
                    
                    $asd=str_replace("\t","",$asd);
                    if($asd!="보내시는 고객님께서 상품 발송 준비중입니다." &&  preg_match("/배달완료/",$domArr[1]->find("td")[3]->plaintext)){
                        $arr['deliveryStatus']="배송완료";
                        set_delivery_status($tradeNum);
                    }
                    else{
                        $sql="UPDATE Post SET Post_status='DS' WHERE Post_no='$postNum' ";
                        mysqli_query($db_connect,$sql);
                    }
                }

                else if($deliveryCompany==="우체국택배"){
                    $url ="https://service.epost.go.kr/trace.RetrieveDomRigiTraceList.comm?sid1=".$deliveryNum."&displayHeader=N";
                    $str = file_get_contents($url);
                
                    $dom = new simple_html_dom();
                    $dom->load($str);

                    $domArr=$dom->find("tr");
                    $size= count($domArr);
                    
                    $deliveryCheck=$domArr[1]->find("td")[4]->plaintext;
                    if($deliveryCheck=="배달완료"){
                        $arr['deliveryStatus']="배송완료";
                        set_delivery_status($tradeNum);
                    }
                    else{
                        $sql="UPDATE Post SET Post_status='DS' WHERE Post_no='$postNum' ";
                        mysqli_query($db_connect,$sql);
                    }
                }
                else if($deliveryCompany==="CJ대한통운"){

                    $url="https://www.doortodoor.co.kr/parcel/doortodoor.do";
                    $data = "invc_no=".$deliveryNum."&fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT2";
                    $str = file_get_contents_curl_post($url,$data);

                    $dom = new simple_html_dom();
                    $dom->load($str);

                    $domArr=$dom->find("table");
                    $exceptionCheck=$domArr[0]->find("tr")[1]->find("td")[0]->plaintext;
                    $tr=$domArr[1]->find("tr");
                    $size= count($tr);
                    $deliveryCheck=$tr[$size-1]->find("td")[0]->plaintext;

                    if(!preg_match("/조회된 데이터가 없습니다./", $exceptionCheck) && preg_match("/배달완료/",$deliveryCheck) ){
                        $arr['deliveryStatus']="배송완료";
                        set_delivery_status($tradeNum);
                    }
                    else{
                        $sql="UPDATE Post SET Post_status='DS' WHERE Post_no='$postNum' ";
                        mysqli_query($db_connect,$sql);
                    }

                }

                // //크롤링 결과에 따라서, Post_status 변경 해야함
                // $sql="UPDATE Post SET Post_status='DS' WHERE Post_no='$postNum' ";
                // mysqli_query($db_connect,$sql);
            }

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

?>