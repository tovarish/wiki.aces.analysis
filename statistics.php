<?php

class Statistics {
	protected $db;
	
	function __construct() {
		$this->db=mysql_connect ("localhost","root","root");
		mysql_select_db("local_wiki_analytics",$this->db);
		//var_dump(mktime(0, 0, 0, date('m'), date('d'), date('Y')));
		
		$methods = explode('/', $_SERVER['REQUEST_URI']);
		
		if(isset($methods['5']) && $methods['5']) {
			$this->$methods['3']($methods['4'], $methods['5']);
		} elseif(isset($methods['4']) && $methods['4']) {
			$this->$methods['3']($methods['4']);
		} else {
			$this->$methods['3']();
		}
	}
	
	private function totalURLs() {
		$result = array();
		$sites = array('mediawiki', 'ciaowiki', 'icteriwiki', 'ontodocwiki', 'a-boa');
		$result[] = mysql_fetch_assoc(mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where request_line like '%mediawiki%'"));
		$result[] = mysql_fetch_assoc(mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where request_line like '%ciaowiki%'"));
		$result[] = mysql_fetch_assoc(mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where request_line like '%icteriwiki%'"));
		$result[] = mysql_fetch_assoc(mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where request_line like '%ontodocwiki%'"));
		$result[] = mysql_fetch_assoc(mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where request_line like '%a-boa%'"));
		foreach ($result as $key => $row) {
			$hits[$key]  = $row['hits'];
			$result[$key]['request_line']  = $sites[$key];
		}
		array_multisort($hits, SORT_DESC, $result);
		include('totalURLs.php');
	}
	
	private function getDays($site = null) {
		$day = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		$like = $site ? " request_line like '%{$site}%' and " : '';
		$whereLike = $site ? " where request_line like '%{$site}%' " : '';
		$result = array();
		$query = mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where {$like} datatime > {$day} group by request_line order by hits desc");
		while ($row = mysql_fetch_assoc($query)) {
			$result[] = $row;
		}
		include('totalURLs.php');
	}
	
	private function getMonth($site = null) {
		$month = mktime(0, 0, 0, date('m'), 1, date('Y'));
		$like = $site ? " request_line like '%{$site}%' and " : '';
		$whereLike = $site ? " where request_line like '%{$site}%' " : '';
		$result = array();
		$query = mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where {$like} datatime > {$month} group by request_line order by hits desc");
		while ($row = mysql_fetch_assoc($query)) {
			$result[] = $row;
		}
		include('totalURLs.php');
	}
	
	private function getYear($site = null) {
		$year = mktime(0, 0, 0, 1, 1, date('Y'));		
		$like = $site ? " request_line like '%{$site}%' and " : '';
		$whereLike = $site ? " where request_line like '%{$site}%' " : '';
		$result = array();
		$query = mysql_query("SELECT request_line, count(request_line) as hits FROM `access_log` where {$like} datatime > {$year} group by request_line order by hits desc");
		while ($row = mysql_fetch_assoc($query)) {
			$result[] = $row;
		}
		include('totalURLs.php');
	}

	private function forecastYear($site = null, $count = 5) {
		$year = mktime(0, 0, 0, 1, 1, date('Y'));
		//for(){
			$result = array();
			$query = mysql_query("SELECT request_line, datatime FROM `access_log` group by request_line order by datatime asc limit 0, 1");
			//echo mysql_error();
			$row_first_year = mysql_fetch_assoc($query);
			$first_year = mktime(0, 0, 0, 1, 1, date('Y',$row_first_year['datatime']));
			$query = mysql_query("SELECT request_line, datatime FROM `access_log` group by request_line order by datatime desc limit 0, 1");
			$row = mysql_fetch_assoc($query);
			$last_year = mktime(0, 0, 0, 1, 1, date('Y',$row['datatime']));
			for($i = $first_year, $j=1; $i<=$last_year; $i = mktime(0, 0, 0, 1, 1, date('Y',$row_first_year['datatime'])+$j), $j++) {
				var_dump(date('d-m-Y',$i));
			}
			//include('totalURLs.php');
		//}
	}
	
	private function forecastMonth($site = null, $count = 5) {
		$year = mktime(0, 0, 0, 1, 1, date('Y'));
		$result = array();
		$like = $site ? " request_line like '%{$site}%' and " : '';
		$whereLike = $site ? " where request_line like '%{$site}%' " : '';
		$query = mysql_query("SELECT request_line, datatime FROM `access_log` {$whereLike} group by request_line order by datatime asc limit 0, 1");
		//echo mysql_error();
		$row_first_month = mysql_fetch_assoc($query);
		$first_month = mktime(0, 0, 0, date('m',$row_first_month['datatime']), 1, date('Y',$row_first_month['datatime']));
		$query = mysql_query("SELECT request_line, datatime FROM `access_log` {$whereLike} group by request_line order by datatime desc limit 0, 1");
		$row = mysql_fetch_assoc($query);
		$previousDataMonth = 0;
		$data = '';
		$last_month = mktime(0, 0, 0, date('m',$row['datatime']), 1, date('Y',$row['datatime']));
		for($i = $first_month, $j=1; $i<=$last_month; $i = mktime(0, 0, 0, date('m',$row_first_month['datatime'])+$j, 1, date('Y',$row_first_month['datatime'])), $j++) {
			$nextMonth = mktime(0, 0, 0, date('m',$row_first_month['datatime'])+$j, 1, date('Y',$row_first_month['datatime']));
			$query = mysql_query("SELECT count(*) as hits FROM `access_log` where {$like} datatime >= {$i} and datatime < {$nextMonth}");
			$hits = mysql_fetch_assoc($query);
			$Ki[] = $previousDataMonth ? $hits['hits']/$previousDataMonth : 0; 
			//echo $j.'  '.date('d/m/Y',$i)." {$hits['hits']} ".date('d-m-Y', $nextMonth).'<br/>';
			$viewData[] = array('date' => date('M, Y',$i), 'hits' => $hits['hits']);
			$data .= "['".date('Y/m/d',$i)."', {$hits['hits']}],";
			$previousDataMonth = $hits['hits'];
		}
		$K = 0; //коэфициент роста
		foreach ($Ki as $val) {
			$K += $val;
		}
		$K = $K/(count($Ki)-1);
		$data = rtrim($data, ",");
		
		$data2 = '';
		for($i = 1; $i<$count; $i++) {
			$data2 .= "['".date('Y/m/d',mktime(0, 0, 0, date('m')+$i, 1, date('Y')))."', ".round($hits['hits']*pow($K, $i))."],";
		}
		$data2 = rtrim($data2, ",");
		include('graph.php');
	}
	
	private function forecastDay($site = null, $count = 5) {
		$result = array();
		$like = $site ? " request_line like '%{$site}%' and " : '';
		$whereLike = $site ? " where request_line like '%{$site}%' " : '';
		$query = mysql_query("SELECT request_line, datatime FROM `access_log` {$whereLike} group by request_line order by datatime asc limit 0, 1");
		//echo mysql_error();
		$row_first_month = mysql_fetch_assoc($query);
		$month1 = date('m',$row_first_month['datatime']);
		$first_month = mktime(0, 0, 0, $month1, date('d',$row_first_month['datatime']), date('Y',$row_first_month['datatime']));
		$query = mysql_query("SELECT request_line, datatime FROM `access_log` {$whereLike} group by request_line order by datatime desc limit 0, 1");
		$row = mysql_fetch_assoc($query);
		$previousDataMonth = 0;
		$data = '';
		$last_month = mktime(0, 0, 0, $month1, date('d',$row['datatime']), date('Y',$row['datatime']));
		for($i = $first_month, $j=1; $i<=$last_month; $i = mktime(0, 0, 0, $month1, date('d',$row_first_month['datatime'])+$j, date('Y',$row_first_month['datatime'])), $j++) {
			$nextMonth = mktime(0, 0, 0, $month1, date('d',$row_first_month['datatime'])+$j, date('Y',$row_first_month['datatime']));
			$query = mysql_query("SELECT count(*) as hits FROM `access_log` where {$like} datatime >= {$i} and datatime < {$nextMonth}");
			$hits = mysql_fetch_assoc($query);
			$Ki[] = $previousDataMonth ? $hits['hits']/$previousDataMonth : 0; 
			//echo $j.'  '.date('d/m/Y',$i)." {$hits['hits']} ".date('d-m-Y', $nextMonth).'<br/>';
			$viewData[] = array('date' => date('M, Y',$i), 'hits' => $hits['hits']);
			$data .= "['".date('Y/m/d',$i)."', {$hits['hits']}],";
			$previousDataMonth = $hits['hits'];
		}
		$K = 0; //коэфициент роста
		foreach ($Ki as $val) {
			$K += $val;
		}
		$K = $K/(count($Ki)-1);
		$data = rtrim($data, ",");
		
		$data2 = '';
		for($i = 1; $i<$count; $i++) {
			$data2 .= "['".date('Y/m/d',mktime(0, 0, 0, date('m', $row['datatime']), date('d',$row['datatime'])+$i, date('Y')))."', ".round($hits['hits']*pow($K, $i))."],";
		}
		$data2 = rtrim($data2, ",");
		include('graph.php');
	}
	
	function __destruct() {
		mysql_close($this->db);
	}
}

new Statistics;
