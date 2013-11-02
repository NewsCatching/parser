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
  public static function error($msg){
    return array("error"=>1, "msg"=>$msg);
  }
  public static function request($url, $format="debug"){
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
    default:
      return json_encode($result);
      break;
    }
  }
}
if($_SERVER['PHP_SELF'] == "/hackday2013/og_api.php"){
  $url =$_GET['url'];
  exit_r(OGAPI::request($url, 'debug'));
}

