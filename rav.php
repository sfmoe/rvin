<?php
date_default_timezone_set('America/Chicago');
/*
Check if there is a ravinia concert today.
By: Moe Martinez
Url: http://github.com/sfmoe/rvin
ravinia has this getcurrentshows json file here:
https://www.ravinia.org/MobileService2/Shows.svc/GetCurrentShows that you get via POST
*/

Class Rav{

public $url = "https://www.ravinia.org/MobileService2/Shows.svc/GetCurrentShows";
public $json_cache_file = "./cache.json";
public $cache_time = 24;

public $isthere = false;
public $theshows = Array();

function __construct(){


		$this->getjson($this->url);

}//end construct

	public function getjson($url){

		if(file_exists($this->json_cache_file) && (time() - filemtime($this->json_cache_file) < ($this->cache_time * 60 * 60))) {
		$this->today($this->json_cache_file);
		} else {

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, array());
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$results = curl_exec($ch);
			curl_close($ch);
			file_put_contents($this->json_cache_file, $results);
			$this->today($this->json_cache_file);
		}



	}

	public function today($data){
		$js = json_decode(file_get_contents($data));
		$today = date("l M j");

		foreach($js->d as $j=>$k){
		if($k->ShowDate == $today){
			$this->isthere = true;
			array_push($this->theshows, $k);


		}

		}

	}


}//end rav class



