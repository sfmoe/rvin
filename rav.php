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
public $cache_time = 96;

public $isthere = false;
public $theshows = Array();

function __construct(){


		$this->getjson($this->url);

}//end construct

	public function getjson($url){


	
    $key = 'ravinia-00-mem';
    $memcache = new Memcache;
    $data = $memcache->get($key);
    if ($data === false) {

		$datas = ['data' => 'this', 'data2' => 'that'];
		$datas = http_build_query($datas);
		$context = [
		'http' => [
		'method' => 'POST',
		'header' => "custom-header: custom-value\r\n" .
		"custom-header-two: custome-value-2\r\n",
		'content' => $datas
		]
		];
		$context = stream_context_create($context);
		$results = file_get_contents($this->url, false, $context);


		$memcache->set($key, $results);
		$data = $memcache->get($key); 
		
		$this->today($data);

      
    }else{
    	$this->today($data);

    }



	

	}

	public function today($data){

		$js = json_decode($data);
		$today = date("l M d");
		
		foreach($js->d as $j=>$k){
		if($k->ShowDate == $today){
			$this->isthere = true;
			array_push($this->theshows, $k);
			

		}

		}

	}


}//end rav class



