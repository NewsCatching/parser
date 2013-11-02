<?
include('parser.php');

if($_SERVER['PHP_SELF'] == "/hackday2013/parser_test.php"){
  $db_item = new DispParser(
    "http://disp.cc/b/62-6Qw2",
    ""
  );
  //$db_item->toDB();
  exit_r($db_item->toString());
}
?>
