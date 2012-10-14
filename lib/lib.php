<?php

/* Google give us SDK */
include 'google/src/class.seostats.php';

function get_ranges($sites = ''){
	if(!is_array($sites))$sites = array($sites);
	$ret = array();
	foreach ($sites as $site) {
		if(strlen($site)>0){

			if(strpos($site, 'http') !== 0)$site = 'http://'.$site;
			/* yandex */
			preg_match('/https?:\/\/(.*)$/', $site,$out);
			if(isset($out[1])){
				$domain = $out[1];

				$resp = file_get_contents('http://yaca.yandex.ua/yca/cy/ch/'.$domain);
				if(strpos($resp, 'ресурс не описан в Яндекс.Каталоге')){
					preg_match('/Индекс\sцитирования\s\(тИЦ\)\sресурса\s—\s(?P<tic>\d+)/',$resp, $out);
				}else{
					preg_match('/<td\sclass="b-cy_name">[\r\t\n\s]+<a\shref="https?:\/\/(?P<www>www\.)?'.preg_quote($domain).'\/"\starget="_blank">[^<]+<\/a><div>[^<]+<\/div>[\r\t\n\s]+<\/td>[\r\t\n\s]+<td>(?P<tic>\d+)<\/td>/',$resp, $out);
				}
				if(isset($out[1])){
					$ret[$site]['тиц'] = $out['tic'];
				}else{
					$ret[$site]['тиц'] = 'error';
				}

				/* yandex indexed pages */
				$resp = file_get_contents('http://webmaster.yandex.ua/check.xml?hostname='.$domain);
				if(preg_match('/Сайт\sє\sдзеркалом<\/span>\s([^,]+)/', $resp,$out) && isset($out[1])){
					$resp = file_get_contents('http://webmaster.yandex.ua/check.xml?hostname='.$out[1]);

					if(preg_match('/Сторінки:\s(\d+)/', $resp,$out) && isset($out[1])){
						$ret[$site]['yandex_pages_indexed'] = $out[1];
					}
				}else{
					$ret[$site]['yandex_pages_indexed'] = 'error';
				}
			}else{
				$ret[$site]['тиц'] = 'error';
				$ret[$site]['yandex_pages_indexed'] = 'error';
			}
			
			/* google */
			$url = new SEOstats($site);
			$ret[$site]['google_page_rank'] = $url->Google_Page_Rank();
			$ret[$site]['google_pages_indexed'] = $url->Google_Siteindex_Total_API();
		}
	}
	return $ret;
}