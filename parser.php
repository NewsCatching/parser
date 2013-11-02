<?
include('debug.php');
include('mysql.php');
include('phpquery.php');
class Parser {
  public static $mysqli_link;
  public static $support_url;
  public $title;
  public $body;
  public $url;
  public $guid;
  public $og_image;
  public $pic_path;
  public $thumb_path;
  public $og_title;
  public $og_description;
  public $referral;
  public $rss_referral;
  public $publish_time;
  public $create_time;
  public $update_time;
  public $raw;
  public $is_support;
  public $is_headline;
  function __construct($url, $rss_referral="") {
    if($rss_referral == "FROM_DB"){
      $result = mysqli_query_new(Parser::$mysqli_link, "SELECT * FROM `news` WHERE `guid` = '%s' LIMIT 1", md5($url));
      if(mysqli_num_rows($result)) {
        $rows = mysqli_fetch_array($result);
        $this->title = $rows['title'];
        $this->body = $rows['body'];
        $this->url = $rows['url'];
        $this->guid = $rows['guid'];
        $this->og_image = $rows['og_image'];
        $this->pic_path = $rows['pic_path'];
        $this->thumb_path = $rows['thumb_path'];
        $this->og_title = $rows['og_title'];
        $this->og_description = $rows['og_description'];
        $this->referral = $rows['referral'];
        $this->rss_referral = $rows['rss_referral'];
        $this->publish_time = $rows['publish_time'];
        $this->create_time = $rows['create_time'];
        $this->update_time = $rows['update_time'];
        $this->raw = $rows['raw'];
        $this->is_support = $rows['is_support'];
        $this->is_headline = $rows['is_headline'];
        return;
      } else {
        throw new Exception("the url: ".$url." is not in DB");
      }
    } 
    $this->url = $url;
    $this->rss_referral = $rss_referral;
    $this->guid = md5($url);
    $this->raw = file_get_contents2($url);
    $this->is_support = 0;
    $this->is_headline = 0;
    $this->create_time = date("Y-m-d H:i:s");
    $this->update_time = date("Y-m-d H:i:s");
    $this->publish_time = "0000-00-00 00:00:00";
    $this->referral = "Internet";
  }
  public function query(){
    $self_full = self::toArray();
    $args = func_get_args();
    $result = array();
    foreach($args as $key){
      if(isset($self_full[$key])){
        $result[$key] = $self_full[$key];
      }
    }
    return $result;
  }
  public function toArray(){
    $result = array(
      "title"=>$this->title,
      "body"=>$this->body,
      "url"=>$this->url,
      "guid"=>$this->guid,
      "og_image"=>$this->og_image,
      "pic_path"=>$this->pic_path,
      "thumb_path"=>$this->thumb_path,
      "og_title"=>$this->og_title,
      "og_description"=>$this->og_description,
      "referral"=>$this->referral,
      "rss_referral"=>$this->rss_referral,
      "publish_time"=>$this->publish_time,
      "create_time"=>$this->create_time,
      "update_time"=>$this->update_time,
      "raw"=>$this->raw,
      "is_support"=>$this->is_support,
      "is_headline"=>$this->is_headline
    );
    return $result;
  }
  public function toString(){
    return print_r(self::toArray(), true);
  }
  public function toDB(){
    global $cfg;
    $pic_path = sprintf('%s%s.png', $cfg['pic_dir'], $this->guid);
    $thumb_path = sprintf('%s%s_t.png', $cfg['pic_dir'], $this->guid);
    $result = mysqli_query_new(Parser::$mysqli_link, "
      SELECT `id`, `thumb_path`, `pic_path` FROM `news` WHERE `guid` = '%s'
    ", $this->guid);
    $isEdit = false;
    if($row = mysqli_fetch_array($result)){
      $isEdit = true;
      $this->pic_path = $row['pic_path'];
      $this->thumb_path = $row['thumb_path'];
    }

    if($this->og_image && (!file_exists($pic_path) || !$pic_path || !$thumb_path)){
      $img = @imagecreatefromstring(@file_get_contents2($this->og_image));
      if(@imagesx($img) && @imagesy($img)){ //有寬高，表示有抓到圖
        $this->pic_path = $pic_path;
        $this->thumb_path = $thumb_path;
        imagepng($img, $this->pic_path);
        imagepng(imageresize($img, 200, 200), $this->thumb_path);
      } else {
        $this->pic_path = $this->thumb_path = $this->og_image = "";
      }
    }

    $sqlArray = self::query(
      'title', 'body', 'url', 'guid', 'og_image', 'pic_path', 'thumb_path', 'og_description', 'og_title',
      'referral', 'rss_referral', 'publish_time', 'create_time', 'update_time', 'raw', 'is_support', 'is_headline'
    );
    foreach($sqlArray as $key=>$value){
      $sqlArray[$key] = addslashes($value);
    }

    if($isEdit){
      $update_id = $row['id'];
      $result = mysqli_query_new(Parser::$mysqli_link, "
          UPDATE `news` SET
            `title`='%s', `body`='%s', `publish_time`='%s', `raw`='%s', `og_image`='%s', `pic_path`='%s', `thumb_path`='%s', 
            `referral`='%s', `update_time`='%s', `is_support`=%d, `is_headline`=%d WHERE `id`=%d
        ", 
        $sqlArray['title'], $sqlArray['body'], $sqlArray['publish_time'], $sqlArray['raw'], $sqlArray['og_image'], $sqlArray['pic_path'], $sqlArray['thumb_path'], 
        $sqlArray['referral'], $sqlArray['update_time'], $sqlArray['is_support'], $sqlArray['is_headline'], $update_id
      );
      return $update_id;
    } else {
      $result = mysqli_query_new(Parser::$mysqli_link, "
          INSERT INTO `news` (
            `title`, `body`, `url`, `guid`, `og_image`, `pic_path`, `thumb_path`, `og_description`, `og_title`,
            `referral`, `rss_referral`, `publish_time`, `create_time`, `update_time`, `raw`, `is_support`, `is_headline`
          ) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, %d)
        ", 
        $sqlArray['title'], $sqlArray['body'], $sqlArray['url'], $sqlArray['guid'], $sqlArray['og_image'], $sqlArray['pic_path'], $sqlArray['thumb_path'],
        $sqlArray['og_description'], $sqlArray['og_title'], $sqlArray['referral'], $sqlArray['rss_referral'], $sqlArray['publish_time'], $sqlArray['create_time'], 
        $sqlArray['update_time'], $sqlArray['raw'], $sqlArray['is_support'], $sqlArray['is_headline']
      );
      return mysqli_insert_id(parser::$mysqli_link);
    }
  }
}
Parser::$mysqli_link = mysqli_link_utf8();
include('parser.yahoo.php');
include('parser.peopo.php');
include('parser.ptt.php');
include('parser.disp.php');
include('parser.apple.php');
class DefaultParser extends Parser {
  public function parse(){
    $html = $this->raw;
    $html = preg_replace('/<head [^>]*>/', '<head>', $html, 1);
    phpQuery::newDocumentHTML($html);
    $this->title = pq("title")->text();
    $this->og_title = pq('meta[property="og:title"]')->attr("content");
    if($this->og_title) $this->title = $this->og_title;
    $this->og_description = pq('meta[property="og:description"]')->attr("content");
    $this->og_image = pq('meta[property="og:image"]')->attr("content");
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
} 
class DBParser extends Parser {
  public function parse(){
  }
  function __construct($url) {
    parent::__construct($url, "FROM_DB");
    self::parse();
  }
} 
Parser::$support_url = array(
  '/\/\/www\.appledaily\.com\.tw\/[a-zA-Z0-9]+\/article\/[a-zA-Z0-9]+\/[0-9]+\/[0-9]+\//'=>AppleParser,
  '/\/\/disp\.cc\/b\/[a-zA-Z0-9\-]{7}/'=>DispParser, //http://disp.cc/b/62-6Qw2
  '/\/\/(disp\.cc\/b\/).*#\!([a-zA-Z0-9\-]{7})/'=>DispParser, //http://disp.cc/b/62-6Qw2
  '/\/\/www\.ptt\.cc\/bbs\/.*\/M\.[^\/]+\.html/'=>PttParser, //http://www.ptt.cc/bbs/asciiart/M.1383145289.A.4F8.html
  '/\/\/tw\.news\.yahoo\.com\/[^\/]+\.html/'=>YahooParser,
  '/\/\/www\.peopo\.org\/news\//'=>PeopoParser
);
if($_SERVER['PHP_SELF'] == "/hackday2013/parser.php"){
  $peopo_item = new DefaultParser(
    "https://www.youtube.com/watch?v=mTSuiGubCHE",
    "http://tw.news.yahoo.com/rss/few"
  );
  exit_r($peopo_item->toString());
}
/* Test Case PeopoParser
if($_SERVER['PHP_SELF'] == "/hackday2013/parser.php"){
  $peopo_item = new PeopoParser(
    "https://www.peopo.org/news/221631",
    "http://tw.news.yahoo.com/rss/few"
  );
  exit_r($peopo_item->toString());
}
*/
/* Test Case Yahoo
if($_SERVER['PHP_SELF'] == "/hackday2013/parser.php"){
  $yahoo_item = new YahooParser(
    "http://tw.news.yahoo.com/%E8%A3%BD%E5%94%AE%E9%BB%91%E5%BF%83%E5%86%B7%E6%B0%A3-2%E5%B9%B4%E5%89%8A6%E5%8D%83%E8%90%AC-072153306.html",
    "http://tw.news.yahoo.com/rss/technology"
  );
  //exit_r($yahoo_item->toString());
  $content = $yahoo_item->query("body");
  echo $content['body'];
  $yahoo_item->toDB();
}
*/
?>
