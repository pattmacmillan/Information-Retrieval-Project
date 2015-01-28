<?php
	include "wiky.inc.php";
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$starttime = $mtime; 
	$articleCount = 0;
	$currCount = 0;
	$i = 0;
	if ($dir = opendir("Data")) {
		while (($file = readdir($dir)) !== false) {
			if ($file == "." || $file == "..") {
				continue;
			}
			$currCount = 0;
			if ($articleCount != 0) {
				echo "\n\n";
			}
			echo "\033[1;32mParsing ".$file."\033[0m\n";
			$handle = fopen("Data/".$file, "r");
			$db = new mysqli("localhost", "root", "halifaxnova", "Wikipedia");
			$db->query("CREATE TEMPORARY TABLE `TempLink` (
				`FromArticle` varchar(255) NOT NULL,
				`ToArticle` varchar(255) NOT NULL,
				`Position` int(11) NOT NULL,
				PRIMARY KEY (`FromArticle`,`ToArticle`),
				KEY `FromArticle` (`FromArticle`,`ToArticle`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1");
			$junk_headers = array("See also", "Notes", "References", "Further reading", "External links");
			$article = "<?xml version=\"1.0\" ?>";
			$started = false;
			$done = false;
			if ($handle) {
				while (($buffer = fgets($handle, 4096)) !== false && !$done) {
					if ($started) {
						$article .= $buffer;
						if (strpos($buffer, "</page>") !== false) {
							$articleCount++;
							$currCount++;
							$xml = simplexml_load_string($article);
							$title = $xml->title[0];
							$l1 = $db->real_escape_string($title);
							$id = $xml->id[0];
							echo "\r$currCount documents parsed";
							$text = $xml->revision->text[0];
							$headerCount = preg_match_all("/==([^=]+)==/", $text, $headers, PREG_OFFSET_CAPTURE);
							$currIndex = 0;
							$headers = $headers[0];
							$currPos = $headers[0][1];
							if (strpos($text, "#REDIRECT") === false && $articleCount != 11936) {
								$introtext = simpleText($text);
								//$intro = $wgParser->parse($text, $l1, new ParserOptions());
								//$introtext = $intro->getText();
								$db->query("INSERT INTO Article VALUES($id, '$title', '".$db->real_escape_string($introtext)."')");			

								preg_match_all("/\[\[(.*?)\]\]/", $text, $links, PREG_OFFSET_CAPTURE);
								//print_r($links);
							
								foreach ($links[1] as $link) {
									$pos = $link[1];
									$link = $link[0];
									//echo $currIndex."\n";
									while ($pos > $currPos && $headerCount > 0 && $currIndex != $headerCount) {
										$currIndex++;
										$currPos = $headers[$currIndex][1];
									}
									if ($link[0] != "#" && strpos($link, 'Category:') === FALSE && strpos($link, 'File:') === FALSE && strpos($link, 'Image:') === FALSE && strpos($link, 'Template:') === FALSE) {
										$l = preg_replace("/(\|.*)/", "", $link);
										$l2 = $db->real_escape_string($l);
										$db->query("INSERT IGNORE INTO TempLink VALUES('$l1', '$l2', $currIndex)");
										//echo "\"$title\" \"$l\"\n";
									}
								}
							}
							$article = "<?xml version=\"1.0\" ?>";
						}
					}
					else if (strpos($buffer, "<page>") !== false) {
						$article .= $buffer;
						$started = true;
					}
				}
				fclose($handle);
			}
		}
	}
	echo "\n\n\033[1;32mConverting titles to IDs for Link table\033[0m\n";
	$db->query("INSERT IGNORE INTO Link SELECT Article1.id as FromArticle,Article2.id as ToArticle,Position FROM TempLink JOIN Article AS Article1 ON TempLink.FromArticle=Article1.title JOIN Article AS Article2 ON TempLink.ToArticle=Article2.title");
	$db->close();
	$mtime = microtime(); 
	$mtime = explode(" ",$mtime); 
	$mtime = $mtime[1] + $mtime[0]; 
	$endtime = $mtime; 
	$totaltime = ($endtime - $starttime); 
	//echo "\n$articleCount documents parsed in ".$totaltime." seconds, or ".($totaltime/$articleCount)." seconds per document.\n"; 
?>