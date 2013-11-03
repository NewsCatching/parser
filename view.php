<?
  include('parser.php');
  $news_id = intval($_GET['id']);
  if(!$news_id){
    $news_id = 2354;
  }
  try {
    $news_item = new DBParser($news_id, 'FROM_DB_ID');
    $news_item = $news_item->query(
      "title", "body", "url", "og_image", "pic_path", "thumb_path", "referral", "publish_time", "is_support", "is_headline"
    );
  } catch (Exception $e) {
    $news_item = array(
      "title"=>"囧a 找不到新聞",
      "body"=>"你好像找了一篇不存在的新聞 0rz... 所以我們無法顯示，您可以下載我們的 NEWS CATCHING Android APP",
      "url"=>"http://news.is.gy/404",
      "og_image"=>$thumb_path,
      "pic_path"=>$thumb_path,
      "thumb_path"=>$thumb_path,
      "referral"=>"喔喔！監介了...",
      "publish_time"=>date('Y-m-d H:i:s'),
      "is_support"=>1,
      "is_headline"=>0
    );
  }
	$_title = $news_item['title']."- NEWS CATCHING 網路報民 - YAHOO HACK DAY";
	$_thumb = $news_item['og_image'];
	$_description = $news_item['og_description']; //TODO: 等營養協助
	$_url = "http://news.is.gy/grassboy/view.php?id=".$news_id;
?>
<!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="<?=$_description?>" />
	<meta property="og:title" content="<?=$_title?>" />
	<meta property="og:url" content="<?=$_url?>" />
	<meta property="og:image" content="<?=$_thumb?>" />
	<meta property="og:site_name" content="<?=$_title?>" />
	<link rel="shortcut icon" href="/favicon.ico" />
	<link href="stylesheets/screen.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<link href="stylesheets/printer.css" media="screen, projection" rel="stylesheet" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js" type="text/javascript"></script>
	<title><?=$_title?></title>
</head>
<body>
  <div id="main-container">
    <nav class="nav-links">
      <ul>
        <li><a class="home_logo" href="http://news.is.gy" title="回首頁">回首頁</a></li>
        <li><a class="download_link" href="javascript:alert('按下去前往 Google Play 下載頁')">下載 NEWS CATCHING<span class="barcode"></span></a></li>
      </ul>
    </nav>
    <article class="main-content">
      <header>
        <hgroup>
          <h2 class="main-referral"><?=sprintf("新聞來源：%s",$news_item['referral'])?></h2>
          <h1 class="main-title"><?=$news_item['title']?></h1>
        </hgroup>
      </header>
      <div class="news-body">
        <?=$news_item['body']?>
      </div>
    </article>
  </div>
	<script src="javascripts/controller.js" type="text/javascript"></script>
</body>
</html>

