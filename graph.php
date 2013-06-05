<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<link type="text/css" rel="stylesheet" href="css/main.css">
		<script language="javascript" type="text/javascript" src="/wiki-analytics/flot/jquery.js"></script>
		<script language="javascript" type="text/javascript" src="/wiki-analytics/flot/jquery.flot.js"></script>

		
	</head>
	<body><?php /*
		<table border=1>
			<tr>
				<td>&nbsp;</td>
				<td>Date</td>
				<td>Hits</td>
			</tr>
			<?php $i=0; foreach($viewData as $val): ?>
			<tr>
				<td><?= ++$i ?></td>
				<td><?= $val['date'] ?></td>
				<td><?= $val['hits'] ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		*/ ?>
		<div id = 'placeholder' style="width:100%;height:300px"></div>
	</body>
	<script type="text/javascript">
			

		var d = [
  { label: "current data", data: [<?= $data ?>]},
  { label: "forecast", data: [<?= $data2 ?>]}
  ];
		for (var j = 0; j < d.length; ++j) {
		for (var i = 0; i < d[j].data.length; ++i) {
			d[j].data[i][0] = Date.parse(d[j].data[i][0]);// += 60 * 60 * 1000;
		}
	}

		var plot_conf = {
		 series: {
		   lines: { 
			 show: true,
			 lineWidth: 2 
		   },
		   points: { show: true }
		 },
		 xaxis: {
		   mode: "time",
		   timeformat: "%d %b, %y",
		   tickSize: [1, "day"]
		 }
		 
		 
		};
		// выводим график
		var p = $.plot($("#placeholder"), d, plot_conf);
		series = p.getData();
		for (var j = 0; j < d.length; ++j) {
			var color = series[j].color;
		$.each(p.getData()[j].data, function(i, el){ 
			var o = p.pointOffset({x: el[0], y: el[1]}); 
			$('<div class="data-point-label">' + el[1] + '</div>').css( { 
				position: 'absolute', 
				color: color,
				left: o.left + 4, top: o.top - 23, 
				display: 'none' }).appendTo(p.getPlaceholder()).fadeIn('slow'); 
			}); 
		}
		</script>
</html>
