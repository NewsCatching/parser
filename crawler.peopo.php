<?
function crawl_peopo(){
  $rss_referral = 'https://www.peopo.org/list/post/all/all/all?page=3';
  $peopo_list_html = file_get_contents2($rss_referral);
  phpQuery::newDocumentHTML(str_replace('<head profile="http://www.w3.org/1999/xhtml/vocab">', '<head>', $peopo_list_html));
  $item_list = pq('.post-list-grid-row');
  $item_list_array = array();
  foreach($item_list as $item){
    $pq_item_a = pq($item)->find('h3 > a');
    $item_list_array[] = array(
      'title'=>$pq_item_a->text(),
      'url'=>"https://www.peopo.org".($pq_item_a->attr('href'))
    );
  }
  printf("<ol><li><a href='%s' target='_blank'>%s</a></li>\n", $rss_referral, $rss_referral);
  foreach($item_list_array as $item){
    $title = $item['title'];
    $url = $item['url'];
    $result = mysqli_query_new(Parser::$mysqli_link, "SELECT `id` FROM `news` WHERE `guid` = '%s'", md5($url));
    if(!mysqli_num_rows($result)){
      $parse_item = new PeopoParser($url, "");
      if($parse_item->title){
        $parse_item->toDB();
        printf("<li><a href='%s' target='_blank'>%s</a> 完成</li>\n", $url, $title);
      } else {
        printf("<li>匯入失敗 <a href='%s' target='_blank'>%s</a></li>\n", $url, $title);
      }
    }
  }
  printf("</ol>\n");
}
crawl_peopo();
?>
