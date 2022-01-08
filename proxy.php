<?php 

set_time_limit(0);
ini_set('memory_limit', '-1');
date_default_timezone_set("Asia/Jakarta");

//INPUT LISTS
system("clear");
echo banner();
enterlist:
$listname = readline("\n[+] Enter your list (eg: list.txt) >> ");
if(empty($listname) || !file_exists($listname)) {
    echo PHP_EOL."[!] list not found [!]".PHP_EOL;
    goto enterlist;
}
$lists = array_unique(explode("\n",str_replace("\r","",file_get_contents($listname))));

//COUNT

$s     = 0;
$d     = 0;
$e     = 0;
echo ct($lists);

//LOOPING

foreach($lists as $list){
    
    //EXPLODE
    if(strpos($list, "|") !== false) list($proxy, $port) = explode("|", $list);
    else if(strpos($list, ":") !== false) list($proxy, $port) = explode(":", $list);
    else $proxy = $list;
    if(empty($proxy)) continue;
    $proxy = str_replace(" ", "", $proxy);
    
    //CURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://api.banditcoding.xyz/proxy/?submit=check&list=$list");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $res = curl_exec($ch);
    curl_close($ch);

    //RESPONSE
    if(strpos($res, '"msg":"Proxy valid!"')){
        $s++;
        file_put_contents("result/valid.txt", "[".jam()."] VALID => ".$proxy.":".$port." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[".jam()."] VALID => $list @Zlaxtert".PHP_EOL;
    }elseif(strpos($res, '"msg":"Code error 400!"')){
        $d++;
        file_put_contents("result/bad.txt", "[".jam()."] BAD => ".$proxy.":".$port." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[".jam()."] BAD => $list @Zlaxtert".PHP_EOL;
    }elseif(strpos($res, '"msg":"Failed connect proxy!"')){
        $d++;
        file_put_contents("result/bad.txt", "[".jam()."] FAILED => ".$proxy.":".$port." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[".jam()."] FAILED => $list @Zlaxtert".PHP_EOL;
    }elseif(strpos($res, '"msg":"Proxy not valid!"')){
        $d++;
        file_put_contents("result/bad.txt", "[".jam()."] BAD => ".$proxy.":".$port." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[".jam()."] BAD => $list @Zlaxtert".PHP_EOL;
    }else{
        $e++;
        file_put_contents("result/error.txt", "[".jam()."] ERROR => ".$proxy.":".$port." @Zlaxtert".PHP_EOL, FILE_APPEND);
        echo "[".jam()."] ERROR => $list @Zlaxtert".PHP_EOL;
    }
}

echo PHP_EOL;
echo "=================[SUCCES]=================".PHP_EOL;
echo " INFO :".PHP_EOL;
echo "    - TOTAL $total".PHP_EOL;
echo "    - VALID $s".PHP_EOL;
echo "    - BAD $d".PHP_EOL;
echo "    - ERROR $e".PHP_EOL;
echo "=================[THANKS]=================".PHP_EOL;
echo "   FILE RESULT SAVED IN FOLDER 'result' ".PHP_EOL;
echo PHP_EOL;

function banner(){
    date_default_timezone_set("Asia/Jakarta");
    $date = date("l, d-m-Y (H:m:s)");

    // CURL 
    $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.banditcoding.xyz/dev/");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $ok = curl_exec($ch);
    $js = json_decode($ok, TRUE);
    $ip      = $js['data']['info']['connection']['ip'];
    $isp     = $js['data']['info']['connection']['isp'];
    $country = $js['data']['info']['connection']['country'];
    
    // BANNER

    $banner = "
   ___  ___  ____  _  ____  __  _______ _____________ _________ 
  / _ \/ _ \/ __ \| |/_/\ \/ / / ___/ // / __/ ___/ //_/ __/ _ \
 / ___/ , _/ /_/ />  <   \  / / /__/ _  / _// /__/ ,< / _// , _/
/_/  /_/|_|\____/_/|_|   /_/  \___/_//_/___/\___/_/|_/___/_/|_| 

               $date
==============================================================
    YOUR IP      : $ip
    YOUR ISP     : $isp
    YOUR COUNTRY : $country
==============================================================
";
    return $banner;
}
function jam(){
    $date = new DateTime();
    $jam = $date->format("H:m:s");
    return $jam;
}
function ct($x){
    $total = count($x);
    $ct ="\n[!] TOTAL $total PROXY LISTS [!]".PHP_EOL.PHP_EOL;
    return $ct;
}

?>