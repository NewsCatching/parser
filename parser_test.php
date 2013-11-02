<?
include('parser.php');
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
if($_SERVER['PHP_SELF'] == "/hackday2013/parser_test.php"){
  $peopo_item = new DefaultParser(
    "https://www.youtube.com/watch?v=mTSuiGubCHE",
    "http://tw.news.yahoo.com/rss/few"
  );
  exit_r($peopo_item->toString());
}
?>
