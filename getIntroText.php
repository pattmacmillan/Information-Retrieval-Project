<?php
	if (isset($_GET['title'])) {
		$output = array();
		$db = new mysqli("localhost", "root", "halifaxnova", "Wikipedia");
		$res = $db->query("SELECT * FROM Article WHERE title='".$_GET['title']."'");
		if ($res->num_rows == 0) {
			echo "{\"introtext\": \"No preview available\"}";
		}
		else {
			$t = $res->fetch_assoc();
			echo json_encode($t);
		}
	}
?>