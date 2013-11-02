<?
class PttParser extends Parser {
  public function parse(){
    $html = $this->raw;
    phpQuery::newDocumentHTML($html);

    $this->title = $this->og_title = pq('meta[property="og:title"]')->attr("content");
    $this->og_description = pq('meta[property="og:description"]')->attr("content");
    $body_img = pq('#main-content img:eq(0)');
    if($body_img->length > 0){
      $this->og_image = $body_img->attr("src");
    } else {
      $this->og_image = pq('meta[property="og:image"]')->attr("content");
    }
    $this->referral = "批踢踢實業坊";
    $date_dom = pq('#main-content .article-meta-tag:contains("時間"):first');
    if($date_dom->length == 1){
      $this->publish_time = date("Y-m-d H:i:s", strtotime($date_dom->next()->text()));
    } else {
      $this->publish_time = date("Y-m-d H:i:s");
    }
    $youtube_players = pq('iframe.youtube-player');
    foreach($youtube_players as $player){
      $src = pq($player)->attr('src');
      preg_match('/\/embed\/([^\/]+)$/', $src, $matches);
      if($matches){
        $video_id = $matches[1];
        pq($player)->replaceWith(sprintf("<a href=\"http://youtu.be/%s\" target=\"_blank\" data-video-id=\"%s\" class=\"news-catch-link\"><img src=\"http://i.ytimg.com/vi/%s/1.jpg\"></a>", $video_id, $video_id, $video_id));
      } else if($src){
        pq($player)->replaceWith(sprintf("<a href=\"%s\" target=\"_blank\" class=\"news-catch-link\">前往觀看</a>", $src));
      }
    }
    $this->body = pq('#main-content')->html();
    $this->is_support = 1;
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
}
?>
