<?php
include('./parser.php');
$db=mysql_connect ("localhost","root","root");
mysql_select_db("local_wiki_analytics",$db);
foreach (glob("/var/www/qaz/access.log.*") as $file) {
    openAccessLog($file);
    //echo "<br/>===================================<br/><br/>";
}
mysql_close($db);
function openAccessLog($fileName) {
	//$fileName = "/var/www/access.log.2.gz";
	$fp = gzopen($fileName, "r");

	ob_start();
		gzpassthru($fp);
		$string = ob_get_contents();
	ob_end_clean();
	parser($string);
	//echo $string;
	//echo gzread($fp, filesize($fileName));
	gzclose($fp);
}

