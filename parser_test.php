<?
include('parser.php');
if($_SERVER['PHP_SELF'] == "/hackday2013/parser_test.php"){
  $db_item = new AppleParser(
    "http://www.appledaily.com.tw/realtimenews/article/beauty/20131103/285844/%E7%B6%B2%E8%B3%BC%E5%A5%B3%E6%A8%A1%E5%A4%AA%E6%AD%A3%E8%A2%AB%E7%A5%9E%E3%80%80%E7%96%91%E6%98%AF%E5%89%8D%E6%96%B0%E5%BA%97%E9%AB%98%E4%B8%AD%E6%A0%A1%E8%8A%B1",
    ""
  );
  //$db_item->toDB();
  exit_r($db_item->toString());
}
?>
