<?php
$string = file_get_contents('/var/www/access.log.1');

preg_match_all('/(.*?) - - \[(.*?)\] "(.*?)" ([0-5]{3}) ([0-9]*) "(.*?)" "(.*?)"/', $string, $result);

$query = "insert into `access_log` (`client_IP`, `datatime`, `request_line`, `status_code`,`size_object_returned`, `referer`, `user_agent`) values ";

foreach($result[1] as $key => $resIp){
	$items[$key] = "('{$resIp}', ";
}
foreach($result[2] as $key => $resDatetime){
	$items[$key] .= "'{$resDatetime}', ";
}
foreach($result[3] as $key => $resRequestLine){
	$items[$key] .= "'{$resRequestLine}', ";
}
foreach($result[4] as $key => $resStatusCode){
	$items[$key] .= "'{$resStatusCode}', ";
}
foreach($result[5] as $key => $resSizeObjectReturned){
	$items[$key] .= "'{$resSizeObjectReturned}', ";
}
foreach($result[6] as $key => $resReferer){
	$items[$key] .= "'{$resReferer}', ";
}
foreach($result[7] as $key => $resUserAgent){
	$items[$key] .= "'{$resUserAgent}'),";
}
foreach($items as $item){
	$query .= $item;
}
$query = rtrim($query, ',');

$db=mysql_connect ("localhost","root","root");
mysql_select_db("local_wiki_analytics",$db);
mysql_query($query);
mysql_close($db);

//127.0.0.1 - - [10/Oct/2000:13:55:36 -0700] "GET /apache_pb.gif HTTP/1.0" 200 2326 "http://www.example.com/start.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"
