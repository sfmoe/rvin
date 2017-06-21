<?php

/*
Check if there is a ravinia concert today.
By: Moe Martinez
Url: http://github.com/sfmoe/rvin
ravinia has a script tag inside https://ravinia.org/Calendar with all shows listed in them in JSON
*/

Class Rav{



private $cache;
private $memcache;
private $json_cache_file = "./cache.json";
private $cache_time = 96;
private $cache_key = "RAV_MEM_KEY";
private $cache_data = false;


public $url = "https://www.ravinia.org/Calendar";
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

		
		$html = file_get_contents($url);


		$dom = new DOMDocument();
		
		libxml_use_internal_errors(TRUE);

		$dom->loadHTML($html);

		libxml_clear_errors();


		$r = new DOMXPath($dom);

		$nodeList = $r->query( '//script[contains(.,"var shows =")]' );


		if($nodeList->length > 0){
		preg_match('#var shows \\=(.+)toggleDisplayOptions\\(true\\);#s',$nodeList[0]->nodeValue,$matches);
		$clean_match = trim($matches[1]);
		$clean_match = substr($clean_match, 1);
		$clean_match = substr($clean_match, 0, -2);
		$clean_match = "{\"d\":[".$clean_match."]}";
		}


		$this->save_cache($clean_match);

		$this->today();

    }else{
    	$this->today();
    }

	}//end get_json


	public function today(){
		$data = $this->get_cache();

	
		$js = json_decode($data);

		
		$today = date("l M j");
		//uncomment to test with a YES date
		//$today = date("l M j", strtotime("Saturday Jun 18"));
		foreach($js->d as $j=>$k){
		if($k->ShowDate == $today){
			$this->isthere = true;
			array_push($this->theshows, $k);


		}

		}

	}


}//end rav class



