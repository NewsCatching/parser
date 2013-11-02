<?
/*
為 news item 補上圖的程式
*/
include('parser.php');
$result = mysqli_query_new(Parser::$mysqli_link,"SELECT `id`,`title`, `url` FROM `news` WHERE `pic_path` = '' AND `og_image` <> '' LIMIT 10");
if(!mysqli_num_rows($result)) {
  exit("全部圖片都抓完囉");
}
while($rows = mysqli_fetch_array($result)){
  $db_item = new DBParser(
    $rows['url']
  );
  $db_item->toDB();
  printf("<a target=\"_blank\" href=\"%s\">%d - %s</a> Done<br>", $rows['url'], $rows['id'], $rows['title']);
}
?>
<script>
setTimeout(function(){
  location.reload();
}, 3000);
</script>
