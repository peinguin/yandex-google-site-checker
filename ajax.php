<?php

if(isset($_REQUEST['domains'])){
	include "lib/lib.php";

	$domains = preg_split('/[\n\s\t,;]+/', $_REQUEST['domains']);
	foreach(get_ranges($domains) as $name => $domain){
		echo $name.'|'.$domain['тиц'].'|'.$domain['yandex_pages_indexed'].'|'.$domain['google_page_rank'].'|'.$domain['google_pages_indexed']."<br />";
	}
}