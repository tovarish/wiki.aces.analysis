<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<link type="text/css" rel="stylesheet" href="css/main.css">
		<script language="javascript" type="text/javascript" src="/wiki-analytics/flot/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="/wiki-analytics/flot/jquery.flot.js"></script>
		<script language="javascript" type="text/javascript" src="/wiki-analytics/flot/jquery.flot.time.js"></script>
		<script language="javascript" type="text/javascript" src="/wiki-analytics/flot/jquery.flot.selection.js"></script>
		
		<title></title>
	</head>
	<body>
		<table border=1>
			<tr>
				<th>Hits</th>
				<th>URL</th>
			</tr>
			
				<?php foreach($result as $val): ?>
				<tr>
					<td><?= $val['hits'] ?></td>
					<td><?= $val['request_line'] ?></td>
				</tr>
				<?php endforeach; ?>
		</table>
	</body>
</html>
		
