<?php

/*
Check if there is a ravinia concert today.
By: Moe Martinez
Url: http://github.com/sfmoe/rvin
ravinia has this getcurrentshows json file here:
https://www.ravinia.org/MobileService2/Shows.svc/GetCurrentShows that you get via POST
*/

Class Rav{



private $cache;
private $memcache;
private $json_cache_file = "./cache.json";
private $cache_time = 96;
private $cache_key = "RAV_MEM_KEY";
private $cache_data = false;


public $url = "https://www.ravinia.org/MobileService2/Shows.svc/GetCurrentShows";
public $isthere = false;
public $theshows = Array();

function __construct(){

		$this->cache_init();
		$this->get_json($this->url);

}//end construct

	public function cache_init(){
		//if we are running in GAE then lets use memcache
		if(isset($_SERVER['APPLICATION_ID'])){
			$this->key = 'ravinia-mem';
		    $this->memcache = new Memcache;
		    $this->cache = "memcache";
		}else{
			$this->cache = "file";
		}
		return true;
	}//end cache_init

	public function get_cache(){

		if($this->cache == "memcache"){
			$this->cache_data = $this->memcache->get($this->cache_key);
			return $this->cache_data;	
		}else{
			$this->cache_data = file_get_contents($this->json_cache_file);
			return $this->cache_data;
		}
		$this->cache_data=false;
		return $this->cache_data;
	}//end get_cache

	public function save_cache($results){

		if($this->cache == "memcache"){
			$this->memcache->set($this->cache_key, $results);
			return true;
		}else{
			$this->cache_data = file_put_contents($this->json_cache_file, $results);
			return true;
		}

		return false;

	}//end save_cache

	public function get_json($url){

        if ($this->cache_data === false) {

		$post_data = ['data' => 'this', 'data2' => 'that']; //other end is expecting some kind of data
		$post_data = http_build_query($post_data);
		$context = [
		'http' => [
		'method' => 'POST',
		'header' => "custom-header: custom-value\r\n" .
		"custom-header-two: custome-value-2\r\n",
		'content' => $post_data
		]
		];
		$context = stream_context_create($context);
		$results = file_get_contents($this->url, false, $context);

		$this->save_cache($results);

		$this->today();

    }else{
    	$this->today();
    }

	}//end get_json


	public function today(){
		$data = $this->get_cache();
		$js = json_decode($data);
		$today = date("l M j");
		foreach($js->d as $j=>$k){
		if($k->ShowDate == $today){
			$this->isthere = true;
			array_push($this->theshows, $k);


		}

		}

	}


}//end rav class



