<?php

include("../../mainfile.php");

include('init.php');
$url = "";

#(1)リンクジャンプ処理(link)
$_GET['id']=preg_replace("/\D/", "", $_GET[id]);
if($_GET['id']){
	#referer????
	if(!$_SERVER['HTTP_REFERER']){$fl=1;}#refererが無いときにカウントしない場合にはこの行を削除
	$ref_list=explode(",",$EST['rank_ref']);
	if(!$EST['rank_ref']){$fl=1;}
	else{
		foreach($ref_list as $tmp){
			if(strstr($_SERVER['HTTP_REFERER'],$tmp)){$fl=1;}
		}
	}
	if($fl){
		$time=time();
		$query="SELECT id FROM $EST[sqltb]rank WHERE id='$_GET[id]' AND ip='$_SERVER[REMOTE_ADDR]' AND time > ".($time-$EST['rank_time']*3600);
		$result=$xoopsDB->query($query) or die("Query failed rank32 $query");
		$tmp = mysql_fetch_row($result);
		if(!$tmp) {
			$query="INSERT INTO $EST[sqltb]rank (id,time,ip) VALUES ('$_GET[id]', '$time' ,'$_SERVER[REMOTE_ADDR]');";
			$result=$xoopsDB->queryF($query) or die("Query failed jump27 $query");
		}
	}
	$query="SELECT url FROM $EST[sqltb]log WHERE id='$_GET[id]' LIMIT 1";
	$result=$xoopsDB->query($query) or die("Query failed jump31 $query");
	$tmp = mysql_fetch_row($result);
	$url = str_replace("&amp;","&",$tmp[0]);}
if($url)
{
	location($url);
}
else if (!empty($_SERVER['HTTP_REFERER']))
{
	redirect_header($_SERVER['HTTP_REFERER'],1,"選択したリンクのURLは登録されていません。");
}
else
{
	redirect_header($EST['home'],1,"選択したリンクのURLは登録されていません。");
}
?>