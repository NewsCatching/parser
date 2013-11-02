<?
include('../include/debug.php');
include('mysql.php');
include('phpquery.php');
class Parser {
  public static $mysqli_link;
  public $title;
  public $body;
  public $url;
  public $guid;
  public $og_image;
  public $og_title;
  public $og_description;
  public $referral;
  public $rss_referral;
  public $counter;
  public $ref_counter;
  public $publish_time;
  public $create_time;
  public $update_time;
  public $raw;
  public $is_support;
  function __construct($url, $rss_referral) {
    $this->url = $url;
    $this->rss_referral = $rss_referral;
    $this->guid = md5($url);
    $this->raw = file_get_contents($url);
    $this->is_support = 0;
    $this->counter = 0;
    $this->ref_counter = 0;
    $this->create_time = date("Y-m-d H:i:s");
    $this->update_time = date("Y-m-d H:i:s");
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
      "og_title"=>$this->og_title,
      "og_description"=>$this->og_description,
      "referral"=>$this->referral,
      "rss_referral"=>$this->rss_referral,
      "counter"=>$this->counter,
      "ref_counter"=>$this->ref_counter,
      "publish_time"=>$this->publish_time,
      "create_time"=>$this->create_time,
      "update_time"=>$this->update_time,
      "raw"=>$this->raw,
      "is_support"=>$this->is_support
    );
    return $result;
  }
  public function toString(){
    return print_r(self::toArray(), true);
  }
  public function toDB(){
    $sqlArray = self::query(
      'title', 'body', 'url', 'guid', 'og_image', 'og_description', 'og_title',
      'referral', 'rss_referral', 'publish_time', 'create_time', 'raw', 'is_support'
    );
    foreach($sqlArray as $key=>$value){
      $sqlArray[$key] = addslashes($value);
    }
    $result = mysqli_query_new(Parser::$mysqli_link, "
        INSERT INTO `news` (
          `title`, `body`, `url`, `guid`, `og_image`, `og_description`, `og_title`,
          `referral`, `rss_referral`, `counter`, `ref_count`, `publish_time`, `update_time`, `raw`, `is_support`
        ) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 0, 0, '%s', '%s', '%s', %d)
      ", 
      $sqlArray['title'], $sqlArray['body'], $sqlArray['url'], $sqlArray['guid'], $sqlArray['og_image'], $sqlArray['og_description'], $sqlArray['og_title'],
      $sqlArray['referral'], $sqlArray['rss_referral'], $sqlArray['publish_time'], $sqlArray['create_time'], $sqlArray['raw'], $sqlArray['is_support']
    );
  }
}
Parser::$mysqli_link = mysqli_link_utf8();
class YahooParser extends Parser {
  public function parse(){
    $html = $this->raw;
    phpQuery::newDocumentHTML($html);

    $this->title = pq("h1.headline")->text();
    $this->body = pq('#mediaarticlebody .bd')->html();
    $this->og_title = pq('meta[property="og:title"]')->attr("content");
    $this->og_description = pq('meta[property="og:description"]')->attr("content");
    $this->og_image = pq('meta[property="og:image"]')->attr("content");
    $this->referral = pq('cite.vcard .provider.org')->text();
    $this->publish_time = date("Y-m-d H:i:s", strtotime(pq('cite.vcard abbr')->attr("title")));
    if(strpos($this->body, "新聞相關影音") !== false ){
      $media_src = pq('span.yui-editorial-embed .video-wrap iframe')->attr("src");
      pq('span.yui-editorial-embed')->replaceWith('<a target="_blank" href="http://tw.news.yahoo.com/'.$media_src.'" class="news-catch-link">前往觀看</a>');
      $this->body = pq('#mediaarticlebody .bd')->html();
    }
    $this->is_support = 1;
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
} 

class PeopoParser extends Parser {
  public function parse(){
    $html = $this->raw;
    $html = str_replace('<head profile="http://www.w3.org/1999/xhtml/vocab">', '<head>', $html);
    phpQuery::newDocumentHTML($html);
    $this->title = pq("h1.page-title")->text();
    if(pq('.field.field-name-field-video-id')->length > 0){
      $embed_code = pq('#embed-code')->attr('value');
      preg_match('/ src=[\"\']([^\"\']+)[\"\']/i', $embed_code, $matches);
      if($matches){
        pq('.field.field-name-body')->append(sprintf("<h3>新聞相關影音</h3><a target=\"_blank\" href=\"%s\" class=\"news-catch-link\">前往觀看</a>", $matches[1]));
      }
    }
    $this->body = pq('.field.field-name-body')->html();
    $this->og_title = pq('meta[property="og:title"]')->attr("content");
    $this->og_description = pq('meta[property="og:description"]')->attr("content");
    $body_img = pq('.field.field-name-body img:eq(0)');
    if($body_img->length > 0){
      $this->og_image = $body_img->attr("src");
    } else {
      $this->og_image = pq('meta[property="og:image"]')->attr("content");
    }
    $this->referral = "PeoPo 公民新聞";
    $this->publish_time = date("Y-m-d H:i:s", strtotime(str_replace('.','-',pq('div.submitted:eq(0) span')->text())));
    $this->is_support = 1;
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
} 

if($_SERVER['PHP_SELF'] == "/hackday2013/parser.php"){
  $peopo_item = new PeopoParser(
    "https://www.peopo.org/news/221631",
    "http://tw.news.yahoo.com/rss/few"
  );
  exit_r($peopo_item->toString());
}

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
