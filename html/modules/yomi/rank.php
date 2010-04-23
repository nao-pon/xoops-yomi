<?php
include("header.php");

include('init.php');

##目次##
#(1)リンクジャンプ処理(link)
#(1.1)アクセスジャンプ処理(r_link)
#(2)キーワードランキング表示画面(PR_keyrank)
#(3)アクセス(IN)ランキング表示画面(PR_rev)
#(4)人気(OUT)ランキング表示画面(PR_rank)

if(!isset($_GET['page'])){$_GET['page']=1;}

$refer = (isset($_SERVER['REQUEST_URI']))? htmlspecialchars(preg_replace('#^(https?://[^/]+).*$#','$1',XOOPS_URL) . $_SERVER['REQUEST_URI']) : '';

$myts =& MyTextsanitizer::getInstance();

if (isset($_GET['mode'])) {
	#(1)リンクジャンプ処理(link)
	if($_GET['mode'] == "link"){
		$_GET['id']=preg_replace("/\D/", "", $_GET['id']);
		if($_GET['id']){
			#refererチェック
			if(!$_SERVER['HTTP_REFERER']){$fl=1;} #refererが無いときにカウントしない場合にはこの行を削除
			$ref_list=explode(",",$EST[rank_ref]);
			if(!$EST['rank_ref']){$fl=1;}
			else{
				foreach($ref_list as $tmp){
					if(strstr($_SERVER['HTTP_REFERER'],$tmp)){$fl=1;}
				}
			}
			if($fl){
				$time=time();
				$query="SELECT id FROM $EST[sqltb]rank WHERE id='$_GET[id]' AND ip='$_SERVER[REMOTE_ADDR]' AND time > ".($time-$EST[rank_time]*3600);
				$result=$xoopsDB->query($query) or die("Query failed rank32 $query");
				$tmp = mysql_fetch_row($result);
				if(!$tmp) {
					$query="INSERT INTO $EST[sqltb]rank (id,time,ip) VALUES ('$_GET[id]', '$time' ,'$_SERVER[REMOTE_ADDR]');";
					$result=$xoopsDB->queryF($query) or die("Query failed rank36 $query");
					$query = 'UPDATE ' . $EST['sqltb'] . 'log SET `count` = `count` + 1 WHERE `id` = ' . $_GET['id'];
					$result=$xoopsDB->queryF($query) or die("Query failed rank38 $query");
				}
				//mysql_close($link);
			}
		}
		if($_GET['url']){location($_GET['url']);}
	}

	#(1.1)アクセスジャンプ処理(&r_link)
	elseif($_GET['mode'] == "r_link"){
		if($EST['rev_fl']){
			$_GET['id']=preg_replace("/\D/", "", $_GET['id']);
			if($_GET['id']){
				$query="SELECT id, url FROM {$EST['sqltb']}log WHERE id='{$_GET['id']}'";
				$result=$xoopsDB->query($query) or die("Query failed rank54 $query");
				if ($result) { //IDが存在する場合のみ処理する
					list($id, $url) = mysql_fetch_row($result);
					// $_SERVER['HTTP_REFERER']チェック
					if ($ref = @$_SERVER['HTTP_REFERER']) {
						$ref = preg_replace('#^(https?://[^/]+).*$#', '$1', $ref);
						if (strpos($url, $ref) === 0) {
							$time=time();
							$_GET['id']=str_replace("\n", "", $_GET['id']);
							$query="SELECT id FROM {$EST['sqltb']}rev WHERE id='{$_GET['id']}' AND ip='{$_SERVER['REMOTE_ADDR']}' AND time > ".($time-$EST['rank_time']*3600);
							$result=$xoopsDB->query($query) or die("Query failed rank54 $query");
							$tmp = mysql_fetch_row($result);
							if(!$tmp) {
								$query="INSERT INTO {$EST['sqltb']}rev (id,time,ip) VALUES ('{$_GET['id']}', '$time' ,'{$_SERVER['REMOTE_ADDR']}')";
								$result=$xoopsDB->queryF($query) or die("Query failed rank58 $query");
								$query = 'UPDATE ' . $EST['sqltb'] . 'log SET `count` = `count_rev` + 1 WHERE `id` = ' . $_GET['id'];
								$result=$xoopsDB->queryF($query) or die("Query failed jump29 $query");
							}
						}
					}
				}
			}
		}
		//$EST[location]=0; #refreshジャンプにする
		location($EST['rev_url']);
	}

	#(2)キーワードランキング表示画面(&PR_keyrank)
	elseif($_GET['mode'] == "keyrank"){
		$bad_key=array();
		$open_key=array();
		@ include $EST['log_path']."keyrank_ys.php";
		require "$EST[temp_path]keyrank.html";
		exit;
	}

	#(3)アクセス(IN)ランキング表示画面(&PR_rev)
	elseif($_GET['mode'] == "rev" || $_GET['mode'] == "rev_bf" || $_GET['mode'] == "rev_rui"){
		if(!$EST['rev_fl']){mes("アクセスランキングは実施しない設定になっています","エラー","java");}
		$CK_data=get_cookie();

		if (isset($CK_data[6])) $EST['kt_child_show']=$CK_data[6];

		if (empty($_GET['kt'])) $_GET['kt'] = "";

		if($_GET['mode'] == "rev"){$Stitle="アクセスランキング";}
		elseif($_GET['mode'] == "rev_bf"){$Stitle="前回のアクセスランキング";}
		else{$Stitle="アクセスランキング(累計)";}
		if($_GET['kt']){$Stitle.=" - " . $ganes[$_GET['kt']];}
		// ヘッダ広告スペース抑止
		$_no_ad_space = (preg_match("/\.$/",$ganes[$_GET['kt']]));

		if (isset($_GET['child_show'])) {
			$EST['kt_child_show'] = $_GET['child_show'];
			$g_prm_child = "&child_show=".$_GET['child_show'];
			$CK_data[6]=$_GET['child_show'];
			set_cookie($CK_data);
		} else {
			$g_prm_child = "";
		}
		if ($_GET['kt'])
		{
			$child_count = Child_count($_GET['kt']);
			if ($EST['kt_child_show']){
				$kt_sql = "&".$_GET['kt'];
				if ($child_coun > 0) {
					$Stitle .= " 以下を集計";
				} else {
					$Stitle .= " のみ集計";
				}
			} else {
				$kt_sql = "&".$_GET['kt']."&";

			}
		}
		else
		{
			$Stitle .= " 全体";
		}

		//xoops2 タイトル設定
		global $xoopsModule,$xoopsTpl;
		if (is_object($xoopsTpl))
		{
			$xoops_pagetitle = $xoopsModule->name();
			$xoops_pagetitle = "$Stitle-$xoops_pagetitle";
			$xoopsTpl->assign("xoops_pagetitle",$xoops_pagetitle);
		}

		$Eref=urlencode($_SERVER['HTTP_REFERER']);
		$Slog=array();
		$log_lines=array(); $Clog=0; $bf_pt=0; $pre_pt=""; $pre_rank=$pre_rank_z=1; $pre_pt_fl=1;
		$end_no=$_GET['page']*$EST['hyouji'];
		$str_no=$end_no-$EST['hyouji']+1;
		$time=time();
		if($_GET['mode'] == "rev"){
			$start=$time-$EST['rev_kikan']*86400;
			$end=$time;
			$last_mod=date("Y/m/d H:i", $start)."　-　".date("Y/m/d H:i", $end);
		}
		elseif($_GET['mode'] == "rev_bf"){
			$start=$time-$EST['rev_kikan']*172800;
			$end=$time-$EST['rank_kikan']*86400+1;
			$last_mod=date("Y/m/d H:i", $start)."　-　".date("Y/m/d H:i", $end);
		}
		else{
			$start=0;
			$end=$time;
			$last_mod="　-　".date("Y/m/d H:i", $end);
		}

		if ($start) {
			if ($_GET['kt']){
				$query="SELECT r.id, COUNT(r.id) AS pt
				FROM $EST[sqltb]rev r, $EST[sqltb]log l
				WHERE l.id = r.id and r.time BETWEEN $start AND $end and l.category LIKE '%$kt_sql%'
				GROUP BY r.id";
			} else {
				$query="SELECT id,COUNT(*) AS pt FROM $EST[sqltb]rev WHERE time BETWEEN $start AND $end GROUP BY id";
			}
		} else {
			if ($_GET['kt']){
				$query="SELECT * FROM $EST[sqltb]log WHERE category LIKE '%$kt_sql%'";
			} else {
				$query="SELECT * FROM $EST[sqltb]log";
			}
		}

		if (!$Clog) {
			$result = $xoopsDB->query($query." LIMIT ".$EST['rank_best']);
			$Clog = mysql_num_rows($result);
		}

		$query .= $start? " ORDER BY pt DESC" : " ORDER BY count_rev DESC";

		$end_no=$EST['hyouji'];
		$str_no=$_GET['page']*$EST['hyouji']-$EST['hyouji'];
		$query .= " LIMIT $str_no , $end_no";

		$result = $xoopsDB->query($query);
		while($Rank = mysql_fetch_array($result)){
			$kt_fl=0;
			if (isset($Rank['count_rev'])) {
				$Slog = $Rank;
				$Slog[16] = $Rank['count_rev'];
			} else {
				$query="SELECT * FROM $EST[sqltb]log WHERE id='$Rank[id]' LIMIT 1";
				$result2 = $xoopsDB->query($query) or die("Query failed rev_rank120 $query");
				$Slog = mysql_fetch_row($result2);
				$Slog[16] = $Rank['pt'];
			}
			if($Slog[0]){
				$Slog[6] = str_replace('<br>', "\n", $Slog[6]);
				if ($EST['syoukai_br'] == 2) {
					$Slog[6] = $myts->displayTarea(unhtmlspecialchars($Slog[6]));
				} else if ($EST['syoukai_br'] == 1) {
					$Slog[6] = nl2br($Slog[6]);
				}
				array_push($log_lines,$Slog);
			}

		}

		#ナビゲーションバーを表示
		$navi = "";
		$kt=explode("_",$_GET['kt']); array_pop($kt);
		$temp_kt = "";
		foreach ($kt as $tmp){
			$temp_kt .= $tmp;
			$navi .= "<a href=\"$Ekt$temp_kt\">$ganes[$temp_kt]</a> &gt; ";
			$temp_kt .="_";
		}

		$tmp=array($_GET['page'],$Clog,$EST['hyouji'],"{$g_prm_child}&mode=$_GET[mode]&kt=$_GET[kt]",$EST['rank']);
		$PRmokuji=mokuji($tmp);
		require "$EST[temp_path]rev_rank.html";
	}
}

#(4)人気ランキング表示画面
if(!$EST['rank_fl']){mes("人気ランキングは実施しない設定になっています","エラー","java");}
if(!isset($_GET['mode'])){$_GET['mode']="rank";}
$CK_data=get_cookie();

if (isset($CK_data[6])) $EST['kt_child_show']=$CK_data[6];

if (empty($_GET['page'])) $_GET['page'] = 1;
if (empty($_GET['kt'])) $_GET['kt'] = "";

if($_GET['mode'] == "rank"){$Stitle="人気ランキング";}
elseif($_GET['mode'] == "rank_bf"){$Stitle="前回の人気ランキング";}
else{$Stitle="人気ランキング(累計)";}
if($_GET['kt']){$Stitle.=" - " . $ganes[$_GET['kt']];}

// ヘッダ広告スペース抑止
$_no_ad_space = (preg_match("/\.$/",$ganes[$_GET['kt']]));

if (isset($_GET['child_show'])) {
	$EST['kt_child_show'] = $_GET['child_show'];
	$g_prm_child = "&child_show=".$_GET['child_show'];
	$CK_data[6]=$_GET['child_show'];
	set_cookie($CK_data);
} else {
	$g_prm_child = "";
}
if ($_GET['kt'])
{
	$child_count = Child_count($_GET['kt']);
	if ($EST['kt_child_show']){
		$kt_sql = "&".$_GET['kt'];
		if ($child_count > 0) {
			$Stitle .= " 以下を集計";
		} else {
			$Stitle .= " のみ集計";
		}
	} else {
		$kt_sql = "&".$_GET['kt']."&";

	}
}
else
{
	$Stitle .= " 全体";
}

//xoops2 タイトル設定
global $xoopsModule,$xoopsTpl;
if (is_object($xoopsTpl))
{
	$xoops_pagetitle = $xoopsModule->name();
	$xoops_pagetitle = "$Stitle-$xoops_pagetitle";
	$xoopsTpl->assign("xoops_pagetitle",$xoops_pagetitle);
}

$Eref=urlencode($_SERVER['HTTP_REFERER']);
//$i=1;$rank_z=1;$rank=1;

$log_lines=array();
$pre_pt = 0;
//$pre_rank = 1;
//$Clog = $_GET['cl'];
$Clog = 0;
$pre_rank=$pre_rank_z = ($_GET['page']-1)*$EST['hyouji']+1;


$end_no=$EST['hyouji'];
$str_no=$_GET['page']*$EST['hyouji']-$EST['hyouji'];

$time=time();
if($_GET['mode'] == "rank"){
	$start=$time-$EST['rank_kikan']*86400;
	$end=$time;
	$last_mod=date("Y/m/d H:i", $start)."　-　".date("Y/m/d H:i", $end);
}
elseif($_GET['mode'] == "rank_bf"){
	$start=$time-$EST['rank_kikan']*172800;
	$end=$time-$EST['rank_kikan']*86400+1;
	$last_mod=date("Y/m/d H:i", $start)."　-　".date("Y/m/d H:i", $end);
}
else {
	$start=0;
	$end=$time;
	$last_mod="　-　".date("Y/m/d H:i", $end);
}

if ($start) {
	if ($_GET['kt']){
		$query="SELECT r.id, COUNT(r.id) AS pt, l.category, l.id
		FROM $EST[sqltb]rank r, $EST[sqltb]log l
		WHERE l.id = r.id and r.time BETWEEN $start AND $end and l.category LIKE '%$kt_sql%'
		GROUP BY r.id";
	} else {
		$query="SELECT id,COUNT(*) AS pt FROM $EST[sqltb]rank WHERE time BETWEEN $start AND $end GROUP BY id";
	}
} else {
	if ($_GET['kt']){
		$query="SELECT * FROM $EST[sqltb]log WHERE category LIKE '%$kt_sql%'";
	} else {
		$query="SELECT * FROM $EST[sqltb]log";
	}
}

if (!$Clog) {
	$result = $xoopsDB->query($query." LIMIT ".$EST['rank_best']) or die("Query failed rank109 $query");
	$Clog = mysql_num_rows($result);
}

$query .= $start? " ORDER BY pt DESC" : " ORDER BY count DESC";

$query .= " LIMIT $str_no , $end_no";

$result = $xoopsDB->query($query) or die("Query failed rank109 $query");
while($Rank = mysql_fetch_array($result)){
	if (isset($Rank['count_rev'])) {
		$Slog = $Rank;
		$Slog['pt'] = $Rank['count'];
	} else {
		$query="SELECT * FROM $EST[sqltb]log WHERE id='$Rank[id]' LIMIT 1";
		$result2 = $xoopsDB->query($query) or die("Query failed rank120 $query");
		$Slog = mysql_fetch_row($result2);
		$Slog['pt'] = $Rank['pt'];
	}
	if($Slog[0]){
		$Slog[6] = str_replace('<br>', "\n", $Slog[6]);
		if ($EST['syoukai_br'] == 2) {
			$Slog[6] = $myts->displayTarea(unhtmlspecialchars($Slog[6]));
		} else if ($EST['syoukai_br'] == 1) {
			$Slog[6] = nl2br($Slog[6]);
		}
		array_push($log_lines,$Slog);
	}
}

#ナビゲーションバーを表示
$navi = "";
$kt=explode("_",$_GET['kt']); array_pop($kt);
$temp_kt = "";
foreach ($kt as $tmp){
	$temp_kt .= $tmp;
	$navi .= "<a href=\"$Ekt$temp_kt\">$ganes[$temp_kt]</a> &gt; ";
	$temp_kt .="_";
}
require "$EST[temp_path]rank.html";
exit;

#(t1)メッセージ画面出力(mes)
#書式:mes($arg1,$arg2,$arg3);
#機能:メッセージ画面を出力する
#引数:$arg1=>表示するメッセージ
#     $arg2=>ページのタイトル(省略時は「メッセージ画面」)
#     $arg3=>・JavaScriptによる「戻る」ボタン表示=java
#            ・$ENV{'HTTP_REFERER'}を使う場合=env
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
		$BACK_URL="【<a href=\"$_SERVER[HTTP_REFERER]\">戻る</a>】";
	}
	elseif(!$arg3){$BACK_URL="";}
	else{$BACK_URL="【<a href=\"$arg3\">戻る</a>】";}
	require "$EST[temp_path]mes.html";
	exit;
}

?>