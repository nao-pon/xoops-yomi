<?php
################################################################
# Yomi-Search Ver4 [サーチエンジン] (Since:1999/09/22)
#   (C) 1999-2001 by yomi
#   Eメール: yomi@pekori.to
#   ホームページ: http://yomi.pekori.to/
################################################################

## ---[利用規約]------------------------------------------------------------+
## 1. このスクリプトはフリーソフトです。このスクリプトを使用した
##    いかなる損害に対して作者は一切の責任を負いません。
## 2. このスクリプトを使用した時点で利用規約(http://yomi.pekori.to/kiyaku.html)
##    に同意したものとみなさせていただきます。
##    ご使用になる前に必ずお読みください。
## 3. 同梱の「アイコン (new.gif/recom.gif/sougogif.gif) 」の著作権は
##   「牛飼いとアイコンの部屋  (http://www.ushikai.com/)」に帰属しています。
## -------------------------------------------------------------------------+

if (is_readable("shorturl.php")) include("shorturl.php");

if (defined("YOMI_SHORTURL") && YOMI_SHORTURL)
{
	if(!empty($_SERVER["PATH_INFO"]) && preg_match("/^\/(.+)$/",$_SERVER["PATH_INFO"],$match))
	{
		$match[1] = preg_replace("/\/$/","",$match[1]);
		if ($match[1] == "new") $_GET['mode'] = "new";
		else if ($match[1] == "renew") $_GET['mode'] = "renew";
		else if ($match[1] == "m1") $_GET['mode'] = "m1";
		else if ($match[1] == "m2") $_GET['mode'] = "m2";
		else
		{
			$parms = explode("/",$match[1]);
			$_GET['kt'] = join("_",$parms);
			$_GET['mode'] = "kt";
		}
	}
}

//For XOOPS
include("header.php");

// Hide Notice Error. orz... TODO
error_reporting('E_ERROR | E_WARNING | E_PARSE');

if (isset($_SERVER['_REQUEST_URI'])) $_SERVER['REQUEST_URI'] = $_SERVER['_REQUEST_URI'];

include('init.php');

if (!isset($_GET['mode'])) $_GET['mode']="";
#各モードへ分岐
#-----------------#
if($_GET['mode']){
	if(!$EST['home']){$EST['home']=$EST['script'];}
	if($_GET['mode'] == "kt"){
		#各カテゴリの時の表示タイトル
		$Stitle=$ganes[$_GET['kt']];
		list($Spre_kt_file)=explode("_",$_GET['kt']);
	}
	elseif($_GET['mode'] == "new"){$Stitle="新着サイト"; $Spre_kt_file="new_ys";} #新着サイトの表示タイトル,ファイル名
	elseif($_GET['mode'] == "renew"){$Stitle="更新サイト"; $Spre_kt_file="renew_ys";} #更新サイトの表示タイトル,ファイル名
	elseif($_GET['mode'] == "m1"){$Stitle="超おすすめサイト"; $Spre_kt_file="m1_ys";} #m1サイトの表示タイトル,ファイル名
	elseif($_GET['mode'] == "m2"){$Stitle="おすすめサイト"; $Spre_kt_file="m2_ys";} #m2サイトの表示タイトル,ファイル名
	elseif($_GET['mode'] == "random"){random();} #ランダムジャンプ
	elseif($_GET['mode'] == "link"){yomi_link();} #リンクジャンプ
	else{mes("指定したモードは存在しません(mode=".$_GET['mode'].")","モード選択エラー","java");}
	##ページ設定

	$_GET['page']=(isset($_GET['page']))? preg_replace("/\D/", "", $_GET['page']) : 1 ;
	if($_GET['page']<1 || $_GET['page']>1000){$_GET['page']=1;}

	$CK_data=get_cookie();
	#ファイルの読み込み＆下層カテゴリ表示
	##ファイルの読み込み
	$time=time();
	$start=$time-$EST['rank_kikan']*86400;
	$end=$time;
	$_no_ad_space = false;
	if($_GET['mode'] == "kt"){ #各カテゴリの場合

		$Stitle=$ganes[$_GET['kt']];
		// ヘッダ広告スペース抑止
		$_no_ad_space = (preg_match("/\.$/",$Stitle));

		//xoops2 タイトル設定
		global $xoopsModule,$xoopsTpl;
		$_kt = $kt;
		$kt=explode("_",$_GET['kt']);
		array_pop($kt);
		$temp_kt = "";
		$navi = "";
		foreach ($kt as $tmp){
			$temp_kt .= $tmp;
			$navi = $ganes[$temp_kt]."-".$navi;
			$temp_kt .="_";
		}
		if (is_object($xoopsTpl))
		{
			$xoops_pagetitle = $xoopsModule->name();
			$xoops_pagetitle = $Stitle."-".$navi.$xoops_pagetitle;
			$xoopsTpl->assign("xoops_pagetitle",$xoops_pagetitle);
		}

		// マーク優先設定
		$order_mark = NULL;
		if (!isset($_GET['sort']) && $CK_data[8] !== "") {
			$order_mark = ($CK_data[8])? 'mark DESC, ' : '';
		}
		if (isset($_GET['sort'])) {
			$CK_data[8] = (empty($_GET['mark']))? '0' : '1';
			$order_mark = (empty($_GET['mark']))? '' : 'mark DESC, ';
		}
		if (is_null($order_mark)) $order_mark = ''; #マーク優先デフォルト
		$_GET['mark'] = $order_mark? '1' : '0';

		// ソート設定
		if (!isset($_GET['sort']) && $CK_data[7] !== "") $EST['defo_hyouji']=$_GET['sort']=$CK_data[7];
		if (isset($_GET['sort'])) $CK_data[7]=$_GET['sort'];

		// 下層カテゴリデータ表示設定
		if ($CK_data[6]==='1' || $CK_data[6]==='0') $EST['kt_child_show']=$CK_data[6];
		if (isset($_GET['child_show'])) {
			$EST['kt_child_show'] = $_GET['child_show'];
			$g_prm_child .= "&amp;child_show=".$_GET['child_show'];
			$CK_data[6]=($_GET['child_show'])? '1' : '0';
		} else {
			$g_prm_child = '';
		}

		set_cookie($CK_data);

		if ($EST['kt_child_show']){
			$kt_sql = "&".str_replace('_', '\_', $_GET['kt']);
			if (Child_count($_GET['kt']) > 0) $Stitle .= " (下層カテゴリデータ表示中)";
		} else {
			$kt_sql = "&".str_replace('_', '\_', $_GET['kt'])."&";
		}

		if (empty($_GET['sort'])) $_GET['sort'] = 'id_new'; #デフォルトの読み込み方法
		switch($_GET['sort']) {
			case "id_new": $order="id DESC"; break;
			case "id_old": $order="id"; break;
			case "time_new": $order="stamp DESC"; break;
			case "time_old": $order="stamp"; break;
			case  "ac_new": $order="title"; break;
			case  "ac_old": $order="title DESC"; break;
			case  "rating": $order = "rating DESC"; break;
			case  "vote": $order = "votes DESC"; break;
			case  "comment": $order = "comments DESC"; break;
			default:
				$order="id DESC";
				$_GET['sort'] = 'id_new';
		}
		$order = $order_mark . $order;

		// 選択中のデータを設定
		$sort_selected = array_combine(array('id_new', 'id_old', 'time_new', 'time_old', 'as_new', 'ac_old', 'rating', 'vote', 'comment'), array_pad(array(), 9, ''));
		$sort_selected[$_GET['sort']] = ' selected="selected"';
		$sort_selected['mark'] = ($order_mark)? ' checked="checked"' : '';

		$Ssearch_kt=$_GET['kt']; #検索対象のカテゴリ番号
		$log_lines=array(); #表示データリスト
		$Clog=array(); #各カテゴリの登録数
		$st_no=$EST['hyouji']*($_GET['page'] -1);

		$query = "SELECT `category`, `id` FROM ".$EST['sqltb']."log WHERE category LIKE '%&".str_replace('_', '\_', $Ssearch_kt)."%';";
		$result = $xoopsDB->query($query) or die("Query failed");
		$_counter = array();
		$_kt_len = strlen($Ssearch_kt) + 1;
		while ($line = mysql_fetch_row($result)) {
			$tmp = explode("&", trim($line[0], '&'));
			foreach($tmp as $tmp2) {
				if (strpos($tmp2, $Ssearch_kt) !== 0) continue;
				$_cats = array();
				if ($tmp2 !== $Ssearch_kt) {
					if ($tmp3 = substr($tmp2, $_kt_len)) {
						foreach(explode('_', $tmp3) as $_cat) {
							$_cats[] = $_cat;
							$_key = $Ssearch_kt . '_' . join('_', $_cats);
							$_counter[$_key][strval($line[1])] = true;
							if ($EST['kt_child_show']) $_counter[$Ssearch_kt][strval($line[1])] = true;
						}
					}
				} else {
					$_counter[$Ssearch_kt][strval($line[1])] = true;
				}
			}
		}

		foreach(array_keys($_counter) as $_key) {
			$Clog[$_key] = count($_counter[$_key]);
		}

		$query = "SELECT * FROM ".$EST['sqltb']."log WHERE category LIKE '%$kt_sql%' ORDER BY $order LIMIT $st_no, ".$EST['hyouji'].";";
		$result = $xoopsDB->query($query) or die("Query failed");
		while ($Slog = mysql_fetch_row($result)) {
			if($CK_data[3] || $is_admin == 1) {
				$query2="SELECT count, count_rev FROM $EST[sqltb]log WHERE id='$Slog[0]'";
				$result2 = $xoopsDB->query($query2) or die("Query failed kt52 $query");
				list($acc, $rev)=mysql_fetch_row($result2);

				$query2="SELECT COUNT(*) FROM $EST[sqltb]rank WHERE time BETWEEN $start AND $end AND id='$Slog[0]'";
				$result2 = $xoopsDB->query($query2) or die("Query failed kt40 $query");
				$count=mysql_fetch_row($result2);
				$Slog['count'] = "${EST['rank_kikan']}日(${count[0]})";

				$Slog['count'] .= "_"."総(${acc})";

				$query2="SELECT COUNT(*) FROM $EST[sqltb]rev WHERE time BETWEEN $start AND $end AND id='$Slog[0]'";
				$result2 = $xoopsDB->query($query2) or die("Query failed kt48 $query");
				$count=mysql_fetch_row($result2);
				$Slog['count'] .= ":逆リンク "."${EST['rank_kikan']}日(${count[0]})";

				$Slog['count'] .= "_"."総(${rev})";
			}

			$Slog['jump_url'] = $EST['rank_fl']? $EST['cgi_path_url']."jump.php?id=$Slog[0]" : $Slog[2];
			$Slog['favicon'] = yomi_get_favicon($Slog[2], '■');

			array_push($log_lines,$Slog);
		}
	}
	else{ #その他の特殊カテゴリ
		$Stitle=$EST["name_".$_GET['mode']];

		//xoops2 タイトル設定
		global $xoopsModule,$xoopsTpl;
		if (is_object($xoopsTpl))
		{
			$xoops_pagetitle = $xoopsModule->name();
			$xoops_pagetitle = "$Stitle-$xoops_pagetitle";
			$xoopsTpl->assign("xoops_pagetitle",$xoops_pagetitle);
		}

		$log_lines=array(); #表示データリスト
		$Ssearch_kt=$Spre_kt_file;
		$st_no=$EST['hyouji']*($_GET['page'] -1);
		if($_GET['mode'] == 'new') {
			$ntime=time()-$EST['new_time']*24*3600;
			//$query = " stamp > $ntime AND renew = 0 ORDER BY mark DESC, id DESC";
			$query = " build_time > $ntime ORDER BY build_time DESC";
		} elseif($_GET['mode'] == 'renew') {
			$ntime=time()-$EST['new_time']*24*3600;
			$query = " stamp > $ntime AND renew = 1 ORDER BY stamp DESC";
		} elseif($_GET['mode'] == 'm1') {
			$query = " mark LIKE '1%'";
		} elseif($_GET['mode'] == 'm2') {
			$query = " mark LIKE '%1'";
		} else {echo "STOP in temp.php in 245"; exit;}
		$query1="SELECT * FROM ".$EST['sqltb']."log WHERE".$query." LIMIT $st_no, ".$EST['hyouji'];
		$result = $xoopsDB->query($query1) or die("Query failed kt110 $query");
		while($Slog = mysql_fetch_row($result)){
			if($CK_data[3] || $is_admin == 1) {
				$query2="SELECT count, count_rev FROM $EST[sqltb]log WHERE id='$Slog[0]'";
				$result2 = $xoopsDB->query($query2) or die("Query failed kt52 $query");
				list($acc, $rev)=mysql_fetch_row($result2);

				$query2="SELECT COUNT(*) FROM $EST[sqltb]rank WHERE time BETWEEN $start AND $end AND id='$Slog[0]'";
				$result2 = $xoopsDB->query($query2) or die("Query failed kt40 $query");
				$count=mysql_fetch_row($result2);
				$Slog['count'] = "${EST['rank_kikan']}日(${count[0]})";

				$Slog['count'] .= "_"."総(${acc})";

				$query2="SELECT COUNT(*) FROM $EST[sqltb]rev WHERE time BETWEEN $start AND $end AND id='$Slog[0]'";
				$result2 = $xoopsDB->query($query2) or die("Query failed kt48 $query");
				$count=mysql_fetch_row($result2);
				$Slog['count'] .= ":逆リンク "."${EST['rank_kikan']}日(${count[0]})";

				$Slog['count'] .= "_"."総(${rev})";
			}
			$Slog['jump_url'] = $EST['rank_fl']? $EST['cgi_path_url']."jump.php?id=$Slog[0]" : $Slog[2];
			$Slog['favicon'] = yomi_get_favicon($Slog[2], '■');
			array_push($log_lines,$Slog);
		}
		$query3="SELECT COUNT(*) FROM $EST[sqltb]log WHERE".$query;
		$result = $xoopsDB->query($query3) or die("Query failed kt115 $query");
		$num = mysql_fetch_row($result);
		$Clog[$Ssearch_kt]=$num[0];
	}
	#ナビゲーションバーを表示
	$navi = "";
	$kt=explode("_",$_GET['kt']); array_pop($kt);
	$temp_kt = "";
	$_no_head_sp = false;
	foreach ($kt as $tmp){
		$temp_kt .= $tmp;
		$navi .= "<a href=\"".yomi_makelink($temp_kt)."$Eend\">$ganes[$temp_kt]</a> &gt; ";
		$temp_kt .="_";
	}
	if($_GET['mode'] == "new"){
		$query = "SELECT COUNT(*) FROM $EST[sqltb]log";
		$result = $xoopsDB->query($query) or die("Query failed yomi43 $query");
		$total_url = mysql_fetch_row($result);
		$navi .= " - 現在の総登録数:<b>$total_url[0]</b>サイト";
	}
	##ページ説明を表示
	gane_guide();
	if($_GET['mode'] == "kt"){$guide = $KTEX[$_GET['kt']];}
	else{$guide = $KTEX[$Spre_kt_file];}
	unset($KTEX);

	$refer = (isset($_SERVER['REQUEST_URI']))? htmlspecialchars(preg_replace('#^(https?://[^/]+).*$#','$1',XOOPS_URL) . $_SERVER['REQUEST_URI']) : '';

	require $EST['temp_path']."kt.html";

	include("footer.php");
	if (isset($link) && $link) {
		@mysql_close($link);
	}

}elseif($EST['home'] && $EST['top']){
	location($EST['home']);
}else{
	$query = "SELECT COUNT(*) FROM ".$EST['sqltb']."log";
	$result = $xoopsDB->query($query) or die("Query failed yomi40 $query");
	$tmp = mysql_fetch_row($result); #総登録数
	$Cpre_gane=$tmp[0];
	$CK_data=get_cookie();
	if (isset($CK_data[7])) $EST['defo_hyouji']=$CK_data[7];
	require $EST['temp_path']."top.html";
}
exit;

#(1)メッセージ画面出力(mes)
#書式:mes($arg1,$arg2,$arg3);
#機能:メッセージ画面を出力する
#引数:$arg1=>表示するメッセージ
#     $arg2=>ページのタイトル(省略時は「メッセージ画面」)
#     $arg3=>・JavaScriptによる「戻る」ボタン表示=java
#            ・HTTP_REFERERを使う場合=env
#            ・管理室へのボタン=kanri
#            ・通常のURL又はパスを指定する場合にはそのURL又はパスを記入
#            ・省略時は非表示
#戻り値:なし
function mes($MES,$TITLE,$arg3){
	global $EST;
	global $xoopsOption,$xoopsConfig,$xoopsLogger,$xoopsTpl;
	global $x_ver,$ver;
	if(!$TITLE){$TITLE="メッセージ画面";}
	if($arg3 == "java"){
		$BACK_URL='<form><input type=button value="&nbsp;&nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;&nbsp;" onClick="history.back()"></form>';
	}
	elseif($arg3 == "env"){
		$BACK_URL="【<a href=\"".$_SERVER['HTTP_REFERER']."\">戻る</a>】";
	}
	elseif(!$arg3){$BACK_URL="";}
	else{$BACK_URL="【<a href=\"$arg3\">戻る</a>】";}
	require $EST['temp_path']."mes.html";
	exit;
}

#(2)ランダムジャンプ(random)
function random(){
	global $EST,$xoopsDB;
	$i=1;
	$query = "SELECT * FROM ".$EST['sqltb']."log";
	$result = $xoopsDB->query($query) or die("Query failed yomi85 $query");
	$total_url=mysql_num_rows($result);
	list($usec, $sec) = explode(' ', microtime());
	srand((float)$sec + ((float)$usec * 100000));
	$id = rand(1, $total_url);
	$query = "SELECT url FROM ".$EST['sqltb']."log";
	$result = $xoopsDB->query($query) or die("Query failed yomi91");
	while($tmp = mysql_fetch_assoc($result)){
		if($i == $id){
			break;
		}
		$i++;
	}
	//mysql_close($link);
	location($tmp['url']);
	exit;
}

#(3)リンクジャンプ処理(link)
function yomi_link(){
	$_GET['id']=preg_replace("/\D/", "", $_GET['id']);
	if($_GET['id']){
		#refererチェック
		if(!$_SERVER['HTTP_REFERER']){$fl=1;} #refererが無いときにカウントしない場合にはこの行を削除
		$ref_list=explode(",",$EST['rank_ref']);
		if(!$EST['rank_ref']){$fl=1;}
		else{
			foreach($ref_list as $tmp){
				if(strstr($_SERVER['HTTP_REFERER'],$tmp)){$fl=1;}
			}
		}
		if($fl){
			$_GET['id']=str_replace("\n", "", $_GET['id']);
			$fp=fopen($EST['log_path']."rank_temp_ys.cgi", "a");
			flock($fp, LOCK_EX);
			fputs($fp, $_GET['id']."<>" . time() . "<>".$_SERVER['REMOTE_ADDR']."\n");
			fclose($fp);
		}
	}
	if($_GET['url']){location($_GET['url']);}
}
##-- end of yomi.php --##
