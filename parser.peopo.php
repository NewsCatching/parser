<?
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
    $this->referral = "公民新聞";
    $this->publish_time = date("Y-m-d H:i:s", strtotime(str_replace('.','-',pq('div.submitted:eq(0) span')->text())));
    $this->is_support = 1;
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
} 
?>
