<?php
//Grab posted url
$url     = filter_var($_POST['url'], FILTER_SANITIZE_STRING);

if($url){

function file_get_contents_curl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
	//browser's user agent string (UA) 
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.47 Safari/537.36');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
//fetching all the content from url
$data    = file_get_contents_curl($url);



$start = preg_quote('<script id="__NEXT_DATA__" type="application/json" crossorigin="anonymous">', '/');
$end = preg_quote('</script>', '/');

preg_match("/$start(.*?)$end/", $data, $matches);

//checking if we got the data or not
if(!$matches){
 $response['status'] = 'fail';	
 echo json_encode($response);
 exit;
}
//getting json from content
$json = $matches[1];
//decoding json to process
$data = json_decode($json, true);


if(!$data['props']['pageProps']['statusCode']){
$response['status']="success";
//Getting owners info
$response['name'] =$data['props']['pageProps']['videoData']['authorInfos']['nickName'];
$response['profile_pic_url']=$data['props']['pageProps']['videoData']['authorInfos']['covers'];
$response['profileurl'] = $data['props']['pageProps']['videoObjectPageProps']['videoProps']['creator']['url'];
$response['username'] = $data['props']['pageProps']['videoData']['authorInfos']['uniqueId'];

//getting content urls

$response['flag']="video";
$response['thumbnailUrl'] = $data['props']['pageProps']['shareMeta']['image']['url'];

$response['videourl'] = $data['props']['pageProps']['videoData']['itemInfos']['video']['urls'];

//Sending data
echo json_encode($response);
}else{
$response['status']="fail";	
//Sending data
echo json_encode($response);
}
}

?>