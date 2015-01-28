<?php
	if (isset($_GET['id'])) {
		$output = array();
		$db = new mysqli("localhost", "root", "halifaxnova", "proj");
		$res = $db->query("SELECT * FROM articles JOIN pagerank ON articles.id=pagerank.id WHERE articles.id=".$_GET['id']);
		$t = $res->fetch_assoc();
		$output['article'] = $t;
		$output['flinks'] = array();
		$output['flinks'] = array();
		$flinks = $db->query("SELECT * FROM relations JOIN articles ON relations.to=articles.id JOIN pagerank ON pagerank.id=relations.to WHERE relations.from=".$_GET['id']." GROUP BY articles.id ORDER BY pagerank.pagerank DESC LIMIT 0,5");
		while ($row = $flinks->fetch_assoc()) {
			$output['flinks'][] = $row;
		}
		$blinks = $db->query("SELECT * FROM relations JOIN articles ON relations.from=articles.id JOIN pagerank ON pagerank.id=relations.from WHERE relations.to=".$_GET['id']." GROUP BY articles.id ORDER BY pagerank.pagerank DESC LIMIT 0,5");
		while ($row = $blinks->fetch_assoc()) {
			$output['blinks'][] = $row;
		}
		echo str_replace("_", " ", json_encode($output));
	}
?>