<?php
define("POST", true);
define("GET", false);
function exit_r($array, $continue=false){
        echo "<xmp>"; print_r($array);
        if($continue!=true)exit();
	else echo "</xmp>";
}
function curl_send($isPost, $url, $args=array(), $config = array()) 
{
	$curl_handler = curl_init();	
	curl_setopt($curl_handler, CURLOPT_URL, $url);
	curl_setopt($curl_handler, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($curl_handler, CURLOPT_POST, $isPost=="POST"?1:0);
	curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl_handler, CURLOPT_SSL_VERIFYHOST, 2); 
	curl_setopt($curl_handler, CURLOPT_SSL_VERIFYPEER, false);
	if($config["USER_AGENT"]){
		curl_setopt($curl_handler, CURLOPT_USERAGENT,$config["USER_AGENT"]);
	}
	if($isPost=="POST")
	{
		$post_args = "";
		foreach($args as $key=>$value)
		{
			$post_args.=sprintf("%s=%s&", $key, urlencode($value));
		}
		curl_setopt($curl_handler, CURLOPT_POSTFIELDS, $post_args);	
	}
	
	if(isset($config["header"])){
		curl_setopt($curl_handler, CURLOPT_HTTPHEADER, $config["header"]);
	}
	
	if(isset($config["refer"])){
		curl_setopt($curl_handler, CURLOPT_REFERER, $config["refer"]);
	}
	
	if($cookies!=""){ 
		curl_setopt($curl_handler, CURLOPT_COOKIE, $cookies); 
	}
	$result = curl_exec($curl_handler);
	if($config["to_data_url"]){
		$filetype = curl_getinfo($curl_handler, CURLINFO_CONTENT_TYPE);
		curl_close($curl_handler);
		return sprintf("data:%s;base64,%s", $filetype, base64_encode($result));
	} else {
		curl_close($curl_handler);
		return $result;
	}
}
function file_get_contents2($url){
  return curl_send("GET", $url, array(), array('USER_AGENT'=>"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:25.0) Gecko/20100101 Firefox/25.0"));
}
if($_SERVER['PHP_SELF'] == "/hackday2013/debug.php"){
  $url =$_GET['url'];
  if(!$url) $url = "http://www.facebook.com/photo.php?fbid=10202501501164490&set=np.413901279.663898857&type=1";
  exit_r(file_get_contents2($url));
}
?>
