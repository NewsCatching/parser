<?
include('parser.php');
if($_SERVER['PHP_SELF'] == "/hackday2013/parser_test.php"){
  $db_item = new DBParser(
    "http://tw.news.yahoo.com/nba--%E7%90%83%E9%9E%8B%E7%BE%A9%E8%B3%A3%E6%8B%8D%E5%BE%9777%E8%90%AC-%E6%9E%97%E6%9B%B8%E8%B1%AA%E9%8C%84%E5%BD%B1%E8%AC%9D%E8%B2%B7%E4%B8%BB-070009566"
  );
  $db_item->toDB();
  exit_r($db_item->toString());
}
?>
