<?php

/* Google give us SDK */
include 'google/src/class.seostats.php';

class Ranger{
	private $domains;

	public function __construct($domains){
		if(!is_array($domains))$domains = array($domains);
		$this->domains = $domains;
	}

	private function yandex_tic($domain){
		$resp = file_get_contents('http://yaca.yandex.ua/yca/cy/ch/'.$domain);
		if(strpos($resp, 'ресурс не описан в Яндекс.Каталоге')){
			preg_match('/Индекс\sцитирования\s\(тИЦ\)\sресурса\s—\s(?P<tic>\d+)/',$resp, $out);
		}else{
			preg_match('/<td\sclass="b-cy_name">[\r\t\n\s]+<a\shref="https?:\/\/(?P<www>www\.)?'.preg_quote($domain).'\/"\starget="_blank">[^<]+<\/a><div>[^<]+<\/div>[\r\t\n\s]+<\/td>[\r\t\n\s]+<td>(?P<tic>\d+)<\/td>/',$resp, $out);
		}
		if(isset($out[1])){
			$tic = $out['tic'];
		}else{
			$tic = 'error';
		}

		return $tic;
	}

	private function yandex_indexed_pages($domain){
		$resp = file_get_contents('http://webmaster.yandex.ua/check.xml?hostname='.$domain);
		if(preg_match('/Сайт\sє\sдзеркалом<\/span>\s([^,]+)/', $resp,$out) && isset($out[1])){
			$resp = file_get_contents('http://webmaster.yandex.ua/check.xml?hostname='.$out[1]);
		}
		if(preg_match('/Сторінки:\s(\d+)/', $resp,$out) && isset($out[1])){
			$indexed_pages = $out[1];
		}else{
			$indexed_pages = 'error';
		}

		return $indexed_pages;
	}

	public function get_ranges(){
		$ret = array();
		foreach ($this->domains as $domain) {
			if(strlen($domain)>0){
				if(strpos($domain, 'http') !== 0)
					$site = 'http://'.$domain;
				else{
					$site = $domain;
					preg_match('/https?:\/\/(.*)$/', $domain,$out);
					if(!isset($out[1])){
						throw new Exception('Domain '.$domain.' not walid');
					}
					$domain = $out[1];
				}
					
				$ret[$domain]['тиц'] = $this->yandex_tic($domain);
				$ret[$domain]['yandex_pages_indexed'] = $this->yandex_indexed_pages($domain);

				
				/* google */
				try{
					$url = new SEOstats($site);

					$ret[$domain]['google_page_rank'] = $url->Google_Page_Rank();
					$ret[$domain]['google_pages_indexed'] = $url->Google_Siteindex_Total/*_API*/();

				}catch(Exception $e){

					$message = 'Domain not exist or connection problems';

					$ret[$domain]['google_page_rank'] = $message;
					$ret[$domain]['google_pages_indexed'] = $message;
				}
			}
		}
		return $ret;
	}
}