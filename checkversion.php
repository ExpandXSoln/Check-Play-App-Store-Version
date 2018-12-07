<?php

$app = $_GET['app']; //pass android or apple
$id = $_GET['id'];
$version = [];
$name = !empty($_GET['name'])?$_GET['name']:'';
$request_headers = array();

if($app == 'android'){
    $url = 'https://play.google.com/store/apps/details?id='.$id;
    $User_Agent = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.31 (KHTML, like Gecko) Chrome/26.0.1410.43 Safari/537.31';
    $request_headers[] = 'User-Agent: '.$User_Agent;
    $request_headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    $request_headers[] = 'Content-Type: application/x-www-form-urlencoded;charset=utf-8';
}else{
    //$url = 'https://itunesconnect.apple.com/WebObjects/iTunesConnect.woa/ra/ng/app/'.$id.'/ios/versioninfo/deliverable';
    $url = 'https://itunes.apple.com/in/app/'.$name.'/id'.$id;

   //echo $url;
}

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);

$page = curl_exec($curl);
//print_r($page);exit;

if(curl_errno($curl)) // check for execution errors
{
        echo 'Scraper error: ' . curl_error($curl);
        exit;
}
 
curl_close($curl);
$position = 1;
if($app == 'android'){
    //$regex = '`<div class="content" itemprop="softwareVersion">(.*?)</div>`';
    
    $regex = '`<div class="(.+?)"><div class="(.+?)">Current Version</div><span class="(.+?)"><div class="(.+?)><span class="(.+?)">(.*?)</span></div></span></div>`';
    $position = 6;
}else{

    //$regex = '`<span itemprop="softwareVersion">(.*?)</span>`';
    $regex='`<p class="l-column small-6 medium-12 whats-new__latest__version">(.*?)</p>`';
}

if(preg_match($regex, $page, $list)){
    $version['version'] = preg_replace('`[\s"]`','',$list[$position]);
    $version['version'] = str_replace('Version','',$version['version'] );
}
else

    $version['version'] = "";


echo json_encode($version);

?>

