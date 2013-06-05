<?php
$db=mysql_connect ("localhost","root","root");
mysql_select_db("local_wiki_analytics",$db);
$result = mysql_fetch_row(mysql_query("select size from file_attributes"));
$oldSize = $result[0];
//$oldTime = 1367496733;
$newSize = filesize('/var/www/qaz/access.log');
//echo $oldSize." = ".$newSize;die;
//$newTime = filemtime('/var/www/access.log');
//echo $newTime;
if($newSize > $oldSize) {
	$string = file_get_contents('/var/www/qaz/access.log', null, null, $oldSize, $newSize);
	parser($string);
	$query = "update file_attributes set size = {$newSize} where id = 1";
	mysql_query($query);
} elseif($newSize < $oldSize) {
	$query = "update file_attributes set size = 0 where id = 1";
	mysql_query($query);
}
mysql_close($db);
function parser($string) {
	//$string = file_get_contents('/var/www/access.log');

	preg_match_all('/(.*?) - - \[(.*?)\] "(.*?)" ([0-5]{3}) ([0-9]*) "(.*?)" "(.*?)"/', $string, $result);

	$query = "insert into `access_log` (`client_IP`, `datatime`, `request_line`, `status_code`,`size_object_returned`, `referer`, `user_agent`) values ";

	foreach($result[1] as $key => $resIp){
		$resIp = addslashes($resIp);
		$items[$key] = "('{$resIp}', ";
	}
	foreach($result[2] as $key => $resDatetime){
		$dateTime = strtotime($resDatetime);
		$items[$key] .= "'{$dateTime}', ";
	}
	foreach($result[3] as $key => $resRequestLine){
		$resRequestLine = addslashes($resRequestLine);
		$items[$key] .= "'{$resRequestLine}', ";
	}
	foreach($result[4] as $key => $resStatusCode){
		$resStatusCode = addslashes($resStatusCode);
		$items[$key] .= "'{$resStatusCode}', ";
	}
	foreach($result[5] as $key => $resSizeObjectReturned){
		$resSizeObjectReturned = addslashes($resSizeObjectReturned);
		$items[$key] .= "'{$resSizeObjectReturned}', ";
	}
	foreach($result[6] as $key => $resReferer) {
		$resReferer = addslashes($resReferer);
		$items[$key] .= "'{$resReferer}', ";
	}
	foreach($result[7] as $key => $resUserAgent){
		$resUserAgent = addslashes($resUserAgent);
		$items[$key] .= "'{$resUserAgent}'),";
	}
	foreach($items as $item){
		$query .= $item;
	}
	$query = rtrim($query, ',');

	mysql_query($query);

	//127.0.0.1 - - [10/Oct/2000:13:55:36 -0700] "GET /apache_pb.gif HTTP/1.0" 200 2326 "http://www.example.com/start.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"
}
