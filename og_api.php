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
include('../include/debug.php');
require_once('OpenGraph.php');

function error($msg){
  return array("error"=>1, "msg"=>$msg);
}
function request($url, $format="debug"){
  if(!$url) return error('URL 未指定');
  $graph = @OpenGraph::fetch($url);
  if(!$graph) return error('OG 資訊不存在');
  $result = array();
  foreach ($graph as $key => $value) {
    $result[$key] = $value;
  }
  if(!$result['url']){
    return error("OG URL 不存在");
  }
  $result = array(
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
if($_SERVER['PHP_SELF'] == "/hackday2013/og_api.php"){
  $url =$_GET['url'];
  exit_r(request($url, 'debug'));
}


