<?
/*
og data api
input:
  url
  format
output
  error
    0
    1
  msg
  guid: md5
  og: 
*/
include('debug.php');
include('OpenGraph.php');
Class OGAPI {
  public static $format;
  public static function error($msg){
    switch(self::$format){
    case "debug":
      return sprintf("<xmp>%s</xmp>", print_r(array("error"=>1, "msg"=>$msg), true));
      break;
    default:
      return json_encode(array("error"=>1, "msg"=>$msg));
      break;
    }
  }
  public static function request($url, $format="debug"){
    self::$format = $format;
    if(!$url) return self::error('URL 未指定');
    $graph = @OpenGraph::fetch($url);
    if(!$graph) return self::error('OG 資訊不存在');
    $result = array();
    foreach ($graph as $key => $value) {
      $result[$key] = $value;
    }
    if(!$result['url']){
      $result['url'] = $url;
    }
    $result = array(
      'error'=>0,
      'msg'=>'',
      'guid'=>md5($url),
      'og'=>$result
    );
    switch($format){
    case "debug":
      return sprintf("<xmp>%s</xmp>", print_r($result, true));
      break;
    default:
      return json_encode($result);
      break;
    }
  }
}

$url =$_GET['url'];
$format = $_GET['format'];
if($_SERVER['PHP_SELF'] == "/hackday2013/og_api.php"){
  if(!$url) $url = "http://blog.xuite.net/grassboy/Tech/49071851-%5BUglyCode%5D+%E5%BF%AB%E9%80%9F%E5%8F%96%E5%BE%97KeyCode%E7%9A%84%E6%96%B9%E6%B3%95";
  exit_r(OGAPI::request($url, 'debug'));
} else {
  echo OGAPI::request($url, $format);
}
