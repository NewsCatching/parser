<?
class DispParser extends Parser {
  public function parse(){
    $html = $this->raw;
    phpQuery::newDocumentHTML(str_replace('data-src=', 'src=', $html));

    $this->title = $this->og_title = pq('meta[property="og:title"]')->attr("content");
    $this->og_description = pq('meta[property="og:description"]')->attr("content");
    $body_img = pq('div:not(.quote_in) > a.img div.img img:eq(0)');
    if($body_img->length > 0){
      $this->og_image = $body_img->attr("src");
    } else {
      $this->og_image = pq('meta[property="og:image"]')->attr("content");
    }
    $this->referral = "Disp.cc";
    $date_dom = pq('span.TH_index:contains("時間"):first');
    if($date_dom->length == 1){
      $date_text = $date_dom->next()->text();
      if(strpos($date_text, '月')!==false){
        $date_text = str_replace('月', '-', $date_text);
        $date_text = str_replace('年', '-', $date_text);
        $date_text = str_replace('日', '', $date_text);
        $date_text = explode(' ', $date_text);
        $tmp = $date_text[4];
        $date_text[4] = $date_text[3];
        $date_text[3] = $tmp;
        $date_text = implode(' ', $date_text);

      }
      $this->publish_time = date("Y-m-d H:i:s", strtotime($date_text));
    } else {
      $this->publish_time = date("Y-m-d H:i:s");
    }
    $youtube_players = pq('div.video');
    foreach($youtube_players as $player){
      $src = pq($player)->attr('data-src');
      $vimg_src = pq($player)->find('img')->attr('src');
      if($matches){
        $video_id = $matches[1];
        pq($player)->replaceWith(sprintf("<a href=\"%s\" target=\"_blank\" lass=\"news-catch-link\"><img src=\"%s\"></a>", $src, $vimg_src));
      } else if($src){
        pq($player)->replaceWith(sprintf("<a href=\"%s\" target=\"_blank\" class=\"news-catch-link\">前往觀看</a>", $src));
      }
    }
    pq('#text_comment > *:not(#push_text_div)')->remove();
    $comment_html = pq('#text_comment')->html();
    $comment_html = str_replace('<div ', '<span ', $comment_html);
    $comment_html = str_replace('</div>', '</span>', $comment_html);
    pq('#text')->append($comment_html);
    $this->body = pq('#text')->html();
    $this->is_support = 1;
  }
  function __construct($url, $rss_referral) {
    parent::__construct($url, $rss_referral);
    self::parse();
  }
}
?>
