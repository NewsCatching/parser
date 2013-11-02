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
*/
Class ParseAPI {
  static public $support_url = array(
    '/\/\/tw\.news\.yahoo\.com\/[^\/]+\.html/'=>YahooParser,
    '/\/\/www\.peopo\.org\/news\//'=>PeopoParser
  );
  public static function error($msg){
    return array("error"=>1, "msg"=>$msg);
  }
  public static function request($url, $format="debug"){
    foreach(self::$support_url as $pattern=>$parser){
      if(preg_match($pattern, $url)){
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
        'msg'=>0,
        'guid'=>md5($url),
        'id'=>$data_index,
        'debug'=>$result->toArray()
      ), true));
    default:
      return json_encode(array(
        'msg'=>0,
        'guid'=>md5($url),
        'id'=>$data_index
      ));
      break;
    }
  }
}
$url =$_GET['url'];
if($_SERVER['PHP_SELF'] == "/hackday2013/parse_api.php"){
  if(!$url) $url = "http://blog.xuite.net/grassboy/Tech/49071851-%5BUglyCode%5D+%E5%BF%AB%E9%80%9F%E5%8F%96%E5%BE%97KeyCode%E7%9A%84%E6%96%B9%E6%B3%95";
  exit_r(ParseAPI::request($url, 'debug'));
} else {
  ParseAPI::request($url);
}
?>
