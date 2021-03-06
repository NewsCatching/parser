<?
include('parser.php');
/*
og data api
input:
  url
  format
output
  error
  msg 
  guid: md5
  id
*/
Class ParseAPI {
  public static function error($msg){
    return array("error"=>1, "msg"=>$msg);
  }
  public static function request($url, $format=""){
    foreach(Parser::$support_url as $pattern=>$parser){
      if(preg_match($pattern, $url, $matches)){
        if(strpos($matches[1], 'disp.cc')!==false && $matches[1] && $matches[2]){
          $url = 'http://'.$matches[1].$matches[2];
        }
        $result = new $parser($url, "");
      }
    }
    if(!$result){
      $result = new DefaultParser($url, "");
    }
    $data_index = $result->toDB();
    switch($format){
    case "debug":
      return sprintf("<xmp>%s</xmp>", print_r(array(
        'error'=>0,
        'msg'=>'',
        'guid'=>md5($url),
        'id'=>$data_index,
        'debug'=>$result->toArray()
      ), true));
      break;
    default:
      return json_encode(array(
        'error'=>0,
        'msg'=>'',
        'guid'=>md5($url),
        'id'=>$data_index
      ));
      break;
    }
  }
}
$url =$_GET['url'];
$format = $_GET['format'];
if($_SERVER['PHP_SELF'] == "/hackday2013/parse_api.php"){
  if(!$url) $url = "http://blog.xuite.net/grassboy/Tech/49071851-%5BUglyCode%5D+%E5%BF%AB%E9%80%9F%E5%8F%96%E5%BE%97KeyCode%E7%9A%84%E6%96%B9%E6%B3%95";
  exit_r(ParseAPI::request($url, 'debug'));
} else {
  echo ParseAPI::request($url, $format);
}
?>
