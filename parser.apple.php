<?
class AppleParser extends Parser {
  public function parse(){
    $html = $this->raw;
    phpQuery::newDocumentHTML($html);
    //var playlist_array = [{url: 'http://video.appledaily.com.tw/video/20131102/a5fefc05b9891d699110.mp4'}];
    preg_match('/var playlist\_array = \[\{url\: \'([^\']+)\'\}\]/', $html, $video_matches);
    pq('article.mpatc header')->prevAll()->remove();
    $this->title = pq("h1#h1")->text();
    $this->og_title = pq('meta[name="Title"]')->attr("content");
    $this->og_description = pq('meta[name="description"]')->attr("content");
    $this->og_image = pq('meta[property="og:image"]')->attr("content");
    $this->referral = "蘋果日報";
    $date_text = pq('article time:first')->text();
    if(!strtotime($date_text)){
      $date_text = str_replace('月', '-', $date_text);
      $date_text = str_replace('年', '-', $date_text);
      $date_text = str_replace('日', ' ', $date_text);
    }
    $this->publish_time = date("Y-m-d H:i:s", strtotime($date_text));
    pq('article.mpatc .urcc, article.mpatc .fbii')->remove();
    if($video_matches){
        pq('article.mpatc')->append(sprintf("<h3>新聞相關影音</h3><a target=\"_blank\" href=\"%s\" class=\"news-catch-link\">前往觀看</a>", $video_matches[1]));
    }
    $this->body = pq('article.mpatc')->html();
    $this->is_support = 1;
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
} 
?>
