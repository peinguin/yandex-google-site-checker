<?php

if(isset($_REQUEST['domains'])){
	include "lib/lib.php";

	$domains = preg_split('/[\n\s\t,;]+/', $_REQUEST['domains']);

	$ranger = new Ranger($domains);

	$ranges = $ranger->get_ranges();

	echo json_encode($ranges);
}