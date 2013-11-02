<?
include('parser.php');
function DOMinnerHTML(DOMNode $element) 
{ 
    $innerHTML = ""; 
    $children  = $element->childNodes;

    foreach ($children as $child) 
    { 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML; 
} 
$rss_list = array(
  "https://www.peopo.org/peopo_agg/feed?post_u=286",
  "http://tw.news.yahoo.com/rss/entertainment",
  "http://tw.news.yahoo.com/rss/politics",
  "http://tw.news.yahoo.com/rss/world",
  "http://tw.news.yahoo.com/rss/local/",
  "http://tw.news.yahoo.com/rss/health",
  "http://tw.news.yahoo.com/sentiment/informative/rss",
  "http://tw.news.yahoo.com/rss/sports",
  "http://tw.news.yahoo.com/rss/society",
  "http://tw.news.yahoo.com/rss/finance",
  "http://tw.news.yahoo.com/rss/lifestyle",
  "http://tw.news.yahoo.com/rss/art-edu",
  "http://tw.news.yahoo.com/rss/technology"
);

if($_SERVER['PHP_SELF'] == "/hackday2013/rss_parser.php"){
  echo "<h1>News Crawler</h1>";
  foreach($rss_list as $rss_referral){
    $result = mysqli_query_new(Parser::$mysqli_link, "SELECT MAX(`publish_time`) as `max_date` FROM `news` WHERE `rss_referral` = '%s'",  $rss_referral);
    $result_array = mysqli_fetch_array($result);
    $db_date = intval(strtotime($result_array['max_date']));
    printf("<ol><li><a href='%s' target='_blank'>%s</a> - after %s</li>", $rss_referral, $rss_referral, date("Y-m-d H:i:s", $db_date));
    $xml = file_get_contents($rss_referral);
    $doc = @DOMDocument::loadXML($xml);
    if($doc){
      $items = $doc->getElementsByTagName('item');
      foreach ($items as $item) {
        $pub_date = strtotime($item->getElementsByTagName('pubDate')->item(0)->nodeValue);
        if($pub_date <= $db_date){
          continue;
        }

        $title = $item->getElementsByTagName('title')->item(0)->nodeValue;
        switch($rss_referral){
        case "https://www.peopo.org/peopo_agg/feed?post_u=286":
          $url = $item->getElementsByTagName('link')->item(0)->nodeValue;
          $parse_item = new PeopoParser($url, $rss_referral);
          break;
        default:
          $url = "http://tw.news.yahoo.com/".$item->getElementsByTagName('guid')->item(0)->nodeValue;
          $parse_item = new YahooParser($url, $rss_referral);
          break;
        }
        if($parse_item->title){
          $parse_item->toDB();
          printf("<li><a href='%s' target='_blank'>%s</a> 完成</li>", $url, $title);
        } else {
          printf("<li>匯入失敗 <a href='%s' target='_blank'>%s</a></li>", $url, $title);
        }
      }
    }
    printf("</ol>");
  }
}
/*
<title>野柳自然中心 新環教場域啟用
</title> 
<description>&lt;p&gt;&lt;a href="http://tw.news.yahoo.com/%E9%87%8E%E6%9F%B3%E8%87%AA%E7%84%B6%E4%B8%AD%E5%BF%83-%E6%96%B0%E7%92%B0%E6%95%99%E5%A0%B4%E5%9F%9F%E5%95%9F%E7%94%A8-040353121.html"&gt;&lt;img src="http://l1.yimg.com/bt/api/res/1.2/0j0xCXzwuhMtGdH3cz9Ysw--/YXBwaWQ9eW5ld3M7Zmk9ZmlsbDtoPTg2O3E9NzU7dz0xMzA-/http://media.zenfs.com/en_us/News/travelrich/Info_NewsPic15085b1.jpg" width="130" height="86" alt="野柳自然中心 新環教場域啟用." align="left" title="野柳自然中心 新環教場域啟用." border="0" /&gt;&lt;/a&gt;野柳方榮獲行政院農委會林務局及台大地理系合辦的十大地景民眾票選及專家學者評分第一名！野柳在自然環教中心於101年11月 ...&lt;/p&gt;&lt;br clear="all"/&gt;
</description>
<link> 
<pubDate>Tue, 29 Oct 2013 12:03:53 +0800
</pubDate>
<source url="http://www.travelrich.com.tw/">旅遊經
</source>
<guid isPermaLink="false">%E9%87%8E%E6%9F%B3%E8%87%AA%E7%84%B6%E4%B8%AD%E5%BF%83-%E6%96%B0%E7%92%B0%E6%95%99%E5%A0%B4%E5%9F%9F%E5%95%9F%E7%94%A8-040353121
</guid>
<media:content url="http://l1.yimg.com/bt/api/res/1.2/0j0xCXzwuhMtGdH3cz9Ysw--/YXBwaWQ9eW5ld3M7Zmk9ZmlsbDtoPTg2O3E9NzU7dz0xMzA-/http://media.zenfs.com/en_us/News/travelrich/Info_NewsPic15085b1.jpg" type="image/jpeg" width="130" height="86">
</media:content>
<media:text type="html">&lt;p&gt;&lt;a href="http://tw.news.yahoo.com/%E9%87%8E%E6%9F%B3%E8%87%AA%E7%84%B6%E4%B8%AD%E5%BF%83-%E6%96%B0%E7%92%B0%E6%95%99%E5%A0%B4%E5%9F%9F%E5%95%9F%E7%94%A8-040353121.html"&gt;&lt;img src="http://l1.yimg.com/bt/api/res/1.2/0j0xCXzwuhMtGdH3cz9Ysw--/YXBwaWQ9eW5ld3M7Zmk9ZmlsbDtoPTg2O3E9NzU7dz0xMzA-/http://media.zenfs.com/en_us/News/travelrich/Info_NewsPic15085b1.jpg" width="130" height="86" alt="野柳自然中心 新環教場域啟用." align="left" title="野柳自然中心 新環教場域啟用." border="0" /&gt;&lt;/a&gt;野柳方榮獲行政院農委會林務局及台大地理系合辦的十大地景民眾票選及專家學者評分第一名！野柳在自然環教中心於101年11月 ...&lt;/p&gt;&lt;br clear="all"/&gt;
</media:text>
<media:credit role="publishing company">
</media:credit>
*/
printf("現在時間：%s", date("Y-m-d H:i:s"));
?>
<script src="../jquery.js"></script>
<script>
  $('li:only-child').map(function(){
      $(this.parentNode).appendTo('body');
  });
	setTimeout(function(){
		location.reload();
	}, 1800000);
</script>
