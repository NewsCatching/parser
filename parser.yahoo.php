<?
class YahooParser extends Parser {
  public function parse(){
    $html = $this->raw;
    phpQuery::newDocumentHTML($html);

    $this->title = pq("h1.headline")->text();
    $this->body = pq('#mediaarticlebody .bd')->html();
    $this->og_title = pq('meta[property="og:title"]')->attr("content");
    $this->og_description = pq('meta[property="og:description"]')->attr("content");
    $this->og_image = pq('meta[property="og:image"]')->attr("content");
    $this->referral = "Yahoo!新聞";
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
?>
