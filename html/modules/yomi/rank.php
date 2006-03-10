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

if (isset($_GET['mode'])) {
	#(1)リンクジャンプ処理(link)
	if($_GET['mode'] == "link"){
		$_GET['id']=preg_replace("/\D/", "", $_GET[id]);
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
				}
				//mysql_close($link);
			}
		}
		if($_GET['url']){location($_GET['url']);}
	}

	#(1.1)アクセスジャンプ処理(&r_link)
	elseif($_GET['mode'] == "r_link"){
		if($EST[rev_fl]){
			$_GET['id']=preg_replace("/\D/", "", $_GET['id']);
			if($_GET['id']){
				$query="SELECT id FROM $EST[sqltb]log WHERE id='$_GET[id]'";
				$result=$xoopsDB->query($query) or die("Query failed rank54 $query");
				$tmp = mysql_fetch_row($result);
				if ($tmp) { //IDが存在する場合のみ処理する
					$time=time();
					$_GET['id']=str_replace("\n", "", $_GET['id']);
					$query="SELECT id FROM $EST[sqltb]rev WHERE id='$_GET[id]' AND ip='$_SERVER[REMOTE_ADDR]' AND time > ".($time-$EST[rank_time]*3600);
					$result=$xoopsDB->query($query) or die("Query failed rank54 $query");
					$tmp = mysql_fetch_row($result);
					if(!$tmp) {
						$query="INSERT INTO $EST[sqltb]rev (id,time,ip) VALUES ('$_GET[id]', '$time' ,'$_SERVER[REMOTE_ADDR]')";
						$result=$xoopsDB->queryF($query) or die("Query failed rank58 $query");
					}
					//mysql_close($link);
				}
			}
		}
		//$EST[location]=0; #refreshジャンプにする
		location($EST[rev_url]);
	}

	#(2)キーワードランキング表示画面(&PR_keyrank)
	elseif($_GET['mode'] == "keyrank"){
		require "$EST[temp_path]keyrank.html";
		exit;
	}

	#(3)アクセス(IN)ランキング表示画面(&PR_rev)
	elseif($_GET['mode'] == "rev" || $_GET['mode'] == "rev_bf" || $_GET['mode'] == "rev_rui"){
		if(!$EST['rev_fl']){mes("アクセスランキングは実施しない設定になっています","エラー","java");}
		require "$EST[temp_path]rev_rank.html";
	}
}

#(4)人気ランキング表示画面
if(!$EST['rank_fl']){mes("人気ランキングは実施しない設定になっています","エラー","java");}
if(!isset($_GET['mode'])){$_GET['mode']="rank";}
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