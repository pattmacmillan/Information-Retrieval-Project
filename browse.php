<?php
	if (isset($_GET['id'])) {
		$db = new mysqli("localhost", "root", "halifaxnova", "proj");
		$res = $db->query("SELECT * FROM articles JOIN pagerank ON articles.id=pagerank.id WHERE articles.id=".$_GET['id']);
		$t = $res->fetch_assoc();
		$flinks = $db->query("SELECT * FROM relations JOIN articles ON relations.to=articles.id JOIN pagerank ON pagerank.id=relations.to WHERE relations.from=".$_GET['id']." GROUP BY articles.id ORDER BY pagerank.pagerank DESC LIMIT 0,5");
		$blinks = $db->query("SELECT * FROM relations JOIN articles ON relations.from=articles.id JOIN pagerank ON pagerank.id=relations.from WHERE relations.to=".$_GET['id']." GROUP BY articles.id ORDER BY pagerank.pagerank DESC LIMIT 0,5");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			#introText {
				background-color: white;
				border: 1px solid #ddd;
				margin-top: 60px;
				margin-left: -60px;
				margin-right: -60px;
				text-align: left;
				padding: 10px;
			}
			#fullContext {
				width: 5040px;
				height: 3150px;
				position: absolute;
				top: -1050px;
				left: -1680px;
				padding-left: 1680px;
				padding-top: 1050px;
			}
			#newCanvas {
				z-index: -1;
				position: absolute;
				top: 0px;
				left: 3360px;
			}
			#canvas {
				z-index: -1;
			}
			body {
				background-color: black;
				font-family: Helvetica, sans-serif;
				width: 1680px;
				height: 1050px;
				overflow: hidden;
			}
			a, a:visited, a:active, a:hover {
				text-decoration: none;
				color: #333;
			}
			#readmore {
				color: #006699;
				text-decoration: underline;
			}
			#previewPane {
				position: absolute;
				left: 30px;
				top: 30px;
				right: 30px;
				bottom: 30px;
				z-index: 100;
			}
			#previewPane iframe {
				width: 100%;
				height: 100%;
			}
			#closeButton {
				background-color: #eee;
				position: absolute;
				right: 0px;
				color: #333;
				z-index: 1000;
				padding: 10px;
				text-decoration: underline;
				cursor: pointer;
			}
			#currentArticle {
				position: absolute;
				text-align: center;
				width: 300px;
				height: 100px;
				left: 2370px;
				top: 1525px;
				border: 1px solid #999;	
				border-radius: 5px;			
				background: #ffffff; /* Old browsers */
				background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 35%, #e6e6e6 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(35%,#f6f6f6), color-stop(100%,#e6e6e6)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* IE10+ */
				background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e6e6e6',GradientType=0 ); /* IE6-9 */
				color: #333;
		}
		#currentArticle h1 {
			margin-top: 30px;
		}
			<?php
			for ($i = 0; $i < 5; $i++) {
			?>
			#flink-<?php echo $i; ?> {
				position: absolute;
				top: <?php echo 1305+$i*127; ?>px;
				left: 2995px;
				padding: 5px;
			}
			<?php
			}
			?>
			<?php
			for ($i = 0; $i < 5; $i++) {
			?>
			#blink-<?php echo $i; ?> {
				position: absolute;
				top: <?php echo 1305+$i*127; ?>px;
				left: 1995px;
				padding: 5px;
			}
			<?php
			}
			?>	
			.link {
				border: 1px solid #999;	
				border-radius: 5px;			
				background: #ffffff; /* Old browsers */
				background: -moz-linear-gradient(top,  #ffffff 0%, #f6f6f6 35%, #e6e6e6 100%); /* FF3.6+ */
				background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(35%,#f6f6f6), color-stop(100%,#e6e6e6)); /* Chrome,Safari4+ */
				background: -webkit-linear-gradient(top,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* Chrome10+,Safari5.1+ */
				background: -o-linear-gradient(top,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* Opera 11.10+ */
				background: -ms-linear-gradient(top,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* IE10+ */
				background: linear-gradient(to bottom,  #ffffff 0%,#f6f6f6 35%,#e6e6e6 100%); /* W3C */
				filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#e6e6e6',GradientType=0 ); /* IE6-9 */
				color: #333;
				font-weight: bold;
			}
		</style>
		<script src="jquery.min.js"></script>
	</head>
	<body>
		<div id="fullContext">
		<div id="currentArticle">
			<h1><?php echo str_replace("_", " ", $t['name']); ?></h1>
			<div id="introText"></div>
		</div>
		<?php
			$i = 0;
			while ($row = $flinks->fetch_assoc()) {
				echo "<div class=\"link\" id=\"flink-$i\"><a href=\"getJSON.php?id=".$row['id']."\" data-intro=\"getIntroText.php?title=".urlencode(str_replace("_", " ", $row['name']))."\">".str_replace("_", " ", $row['name'])."</a></div>";
				$i++;
			}
		?>
		<?php
			$i = 0;
			while ($row = $blinks->fetch_assoc()) {
				echo "<div class=\"link\" id=\"blink-$i\"><a href=\"getJSON.php?id=".$row['id']."\" data-intro=\"getIntroText.php?title=".urlencode(str_replace("_", " ", $row['name']))."\">".str_replace("_", " ", $row['name'])."</a></div>";
				$i++;
			}
		?>
		<?php if (isset($_GET['debug'])): ?>
		<canvas id="canvas" width="1680" height="1050" style="background-color: green"></canvas>
		<canvas id="newCanvas" width="1680" height="1050" style="background-color: blue"></canvas>
		<?php else: ?>
		<canvas id="canvas" width="1680" height="1050"></canvas>
		<canvas id="newCanvas" width="1680" height="1050"></canvas>		
		<?php endif; ?>
		</div>
		<div id="previewPane">
			<a id="closeButton">Close</a>
			<iframe src="http://en.wikipedia.org"></iframe>
		</div>
		<script>
			var c = document.getElementById('canvas');
			var ctx = c.getContext('2d');
			var c2 = document.getElementById('newCanvas');
			var ctx2 = c2.getContext('2d');
<?php
			$flinks->data_seek(0);
			$i = 0;
			while ($row = $flinks->fetch_assoc()) {
			$w = $row['pagerank']/5;
			?>
			ctx.beginPath();
			ctx.strokeStyle = 'rgba(0,255,255,0.5)';
			ctx.moveTo(990, 525);
			ctx.lineTo(1680, <?php echo ($i)*(1050/4); ?>);
			ctx.stroke();
			ctx2.beginPath();
			ctx2.strokeStyle = 'rgba(0,255,255,0.5)';
			ctx2.moveTo(990, 525);
			ctx2.lineTo(1680, <?php echo ($i)*(1050/4); $i++; ?>);
			ctx2.stroke();
			<?php
			}
			?>
<?php
			$blinks->data_seek(0);
			$i = 0;
			while ($row = $blinks->fetch_assoc()) {
			$w = $row['pagerank']/5;
			?>
			ctx.beginPath();
			ctx.strokeStyle = 'rgba(255,255,0,0.5)';
			ctx.moveTo(690, 525);
			ctx.lineTo(0, <?php echo ($i)*(1050/4); ?>);
			ctx.stroke();
			ctx2.beginPath();
			ctx2.strokeStyle = 'rgba(255,255,0,0.5)';
			ctx2.moveTo(690, 525);
			ctx2.lineTo(0, <?php echo ($i)*(1050/4); $i++; ?>);
			ctx2.stroke();
			<?php
			}
			?>
			ctx.beginPath();
			ctx.moveTo(600, 525);
			ctx.lineTo(590, 515);
			ctx.lineTo(590, 535);
			ctx.lineTo(600, 525);
			ctx.closePath();
			ctx.fillStyle = 'rgba(255,255,0,0.5)';
			ctx.fill();
			ctx.beginPath();
			<?php if ($flinks->num_rows > 0): ?>
			ctx.moveTo(1100, 525);
			ctx.lineTo(1090, 515);
			ctx.lineTo(1090, 535);
			ctx.lineTo(1100, 525);
			ctx.closePath();
			ctx.fillStyle = 'rgba(0,255,255,0.5)';
			ctx.fill();
			<?php endif; ?>
			ctx2.beginPath();
			ctx2.moveTo(600, 525);
			ctx2.lineTo(590, 515);
			ctx2.lineTo(590, 535);
			ctx2.lineTo(600, 525);
			ctx2.closePath();
			ctx2.fillStyle = 'rgba(255,255,0,0.5)';
			ctx2.fill();
			ctx2.beginPath();
			<?php if ($flinks->num_rows > 0): ?>
			ctx2.moveTo(1100, 525);
			ctx2.lineTo(1090, 515);
			ctx2.lineTo(1090, 535);
			ctx2.lineTo(1100, 525);
			ctx2.closePath();
			ctx2.fillStyle = 'rgba(0,255,255, 0.5)';
			ctx2.fill();
			<?php endif; ?>
			$(document).ready(function() {
				$("#previewPane").hide();
				$("#closeButton").click(function() {
					$("#previewPane").fadeOut(500);
				});
				$(document).on('click', "#readmore", function() {
					$("#previewPane iframe").attr('src', "http://en.wikipedia.org/wiki/"+$("#currentArticle h1").text());
					$("#previewPane").fadeIn(500);
				});
				$("#flink-0 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("top", "0px");
					$("#newCanvas").css("left", "3360px");
					$("#fullContext").animate({
						'left': '-3360px',
						'top': '0px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#flink-1 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("top", "263px");
					$("#newCanvas").css("left", "3360px");
					$("#fullContext").animate({
						'left': '-3360px',
						'top': '-263px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#flink-2 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("top", "525px");
					$("#newCanvas").css("left", "3360px");
					$("#fullContext").animate({
						'left': '-3360px',
						'top': '-525px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#flink-3 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("top", "788px");
					$("#newCanvas").css("left", "3360px");
					$("#fullContext").animate({
						'left': '-3360px',
						'top': '-788px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});							
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#flink-4 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("top", "1050px");
					$("#newCanvas").css("left", "3360px");
					$("#fullContext").animate({
						'left': '-3360px',
						'top': '-1050px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#blink-0 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("left", "0px");
					$("#newCanvas").css("top", "0px");
					$("#fullContext").animate({
						'left': '0px',
						'top': '0px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#blink-1 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("left", "0px");
					$("#newCanvas").css("top", "263px");
					$("#fullContext").animate({
						'left': '0px',
						'top': '-263px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#blink-2 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("left", "0px");
					$("#newCanvas").css("top", "525px");
					$("#fullContext").animate({
						'left': '0px',
						'top': '-525px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#blink-3 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("left", "0px");
					$("#newCanvas").css("top", "788px");
					$("#fullContext").animate({
						'left': '0px',
						'top': '-788px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
				$("#blink-4 a").click(function() {
					var link = $(this).attr('href');
					var introlink = $(this).attr('data-intro');
					$("#newCanvas").css("opacity", "0");
					$("#newCanvas").css("left", "0px");
					$("#newCanvas").css("top", "1050px");
					$("#fullContext").animate({
						'left': '0px',
						'top': '-1050px',
						'opacity': '0'
					}, 2000, function () { 
						$.getJSON(link, function(data) {
							$("#currentArticle h1").text(data.article.name);
							$.each(data.flinks, function (i, item) {
								$("#flink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.each(data.blinks, function (i, item) {
								$("#blink-"+i+" a").text(item.name).attr('href', 'getJSON.php?id='+item.id).attr('data-intro', 'getIntroText.php?title='+encodeURI(item.name));
							
							});
							$.getJSON(introlink, function(data) {
								$("#introText").html(data.introtext.substr(0, 1000)+"...<br/>&nbsp;<br/><a href=\"#\" id=\"readmore\">Read More</a>");
							});
							$("#newCanvas,#fullContext").animate({'opacity': '1'}, 500);
							$("#fullContext").css("top", "-1050px");
							$("#fullContext").css("left", "-1680px");
							$("#newCanvas").attr('id', 'canvas1');
							$("#canvas").attr('id', 'newCanvas');
							$("#canvas1").attr('id', 'canvas');
						});
					});
					return false;
				});
			});
		</script>
	</body>
</html>