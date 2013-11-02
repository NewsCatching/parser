<?
/************************************************************************/
/*
(後端工具)
MYSQL的相關函數
*/
/************************************************************************/
include("config/config.php");
date_default_timezone_set("Asia/Taipei");

if(!$required_mysql_system){		//如果未include此檔案，則required_msg_box必為false
	global $required_mysql_system;	
	$required_mysql_system=true;
//*****************************************************************************************************************************//
	function mysqli_link_utf8() //連結至資料庫
//*****************************************************************************************************************************//	
	{
		global $cfg;
		$link=mysqli_connect($cfg['db_path'], $cfg['db_user'], $cfg['db_pass'], $cfg['db_default']);
		if($link){
			mysqli_query($link,"SET CHARACTER SET utf8");
			mysqli_query($link,"SET collation_connection = 'utf8_unicode_ci'");
			return $link;
		} else {
			?>
			<br>
			<br>
			錯誤：MySQL資料庫無法連結！<br><br>
			
			若一段時間後仍然發生此問題…<br>
			請向 grassboy 反應<br>
			<br>
			<?			
			exit();
		}
	}

//*****************************************************************************************************************************//	
	function mysqli_query_new(&$link, $query)//新式的mysqli_query，有錯誤便停止執行程式並顯示錯誤訊息！
//*****************************************************************************************************************************//	
	{		
		global $cfg;
		$argc = func_num_args();
		$argv = func_get_args();
		unset($argv[0], $argv[1]);
		if(is_array($argv[2])) $argv = $argv[2];

/*/	if($_SERVER['REMOTE_ADDR']=="140.115.205.65") {
		sscanf(microtime(),"%s %s",&$a,&$b);
		$start=sprintf("%2.8f<br>",$b.substr($a, 1)); }//*/

		$query_raw = vsprintf($query, $argv);
		if($link==""){
			echo $query_raw."<br />";
			exit();
		}

		$result=mysqli_query($link, $query_raw);
		
/*/	if($_SERVER['REMOTE_ADDR']=="140.115.205.65") {
		sscanf(microtime(),"%s %s",&$a,&$b);
		$end=sprintf("%2.8f",$b.substr($a, 1));
		printf("<script>alert(\"%s\\n\"+%2.8f);</script>",str_replace("\r\n","\\n",$query),$end-$start); }//*/

		
		if(mysqli_errno($link)){
			echo '<meta http-equiv=Content-Type content="text/html; charset=utf-8">';
			$date=date("YmdHis.");
			$msg="MySQL執行過程錯誤，程式強行中止：<br>\r\n".
				 "執行的PHP程式為：".$_SERVER['PHP_SELF']."<br>\r\n".
				 "執行的Query為：".$query_raw."<br>\r\n".
			     "錯誤訊息為：".mysqli_error($link)."<br><br>\r\n";
			$fout=fopen("error_logs/".$date.$_SERVER['REMOTE_ADDR'].".txt","w");
			fwrite($fout,$msg);
			fclose($fout);
			if(true || $_SERVER['REMOTE_ADDR']==$_SERVER["SERVER_ADDR"]) echo $msg;
			printf("MySQL執行過程錯誤，程式強行中止：<br>\r\n錯誤識別碼:<b>%s%s</b>", str_replace(".", "", $_SERVER['REMOTE_ADDR']), $date);
				?>
				若一段時間後仍然發生此問題…<br>
				請E-Mail至jeson.wu@msa.hinet.net反應…謝謝！<br>		
				<br>
				 <?
			exit();
		} else {
			return $result;
		}
	}
//*****************************************************************************************************************************//	
	function mysqli_fetch_htmlarray(&$result, $type) //重寫mysqli_fetch_htmlarray，讓他有將array轉html的功能，except為允許html格式的欄位
//*****************************************************************************************************************************//	
	{
		$except = func_get_args();
		unset($except[0], $except[1]);
		$array=mysqli_fetch_array($result, $type);
		if(is_array($array)){
			foreach($array as $key => $value){
				if(!in_array($key, $except))
					$array[$key]=htmlspecialchars($value);
			}
		}
		return $array;
	}
}
?>
