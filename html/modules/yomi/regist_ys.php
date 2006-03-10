<?php
// For XOOPS
include("header.php");
//

if( strtolower($_SERVER['REQUEST_METHOD']) == "post" )
{
	if ( ! $xoopsHypTicket->check() )
	{
		ob_end_clean();
		redirect_header(XOOPS_URL.'/',1,$xoopsHypTicket->getErrors());
	}
}

include('init.php');

EST_reg();
################################################################
# Yomi-Search用データ登録用プログラム
################################################################
#(1)プレビュー画面(preview)
#(2)リンク切れ報告フォーム(no_link)
#(3)新規登録実行(act_regist)
#(4)修正・削除のためのパスワード認証(enter)
#(5)ヘルプの表示(help)
#(6)登録内容変更(act_mente)
#(7)パスワードの再発行・変更(act_repass)
#(8)削除実行(act_del)
#(9)登録画面(新規登録・管理人代理登録・登録内容変更)(regist)

#(f1)登録結果画面出力(PR_kt)
#(f2)メッセージ画面出力(mes)
#(f3)入力内容のチェック(check)
#(f4)カテゴリを表示1(PR_preview_kt1)
#(f5)カテゴリを表示1(PR_preview_kt2)
#(f6)入力内容の整形(join_fld)
#(f7)新規登録用のIDを取得&２重URL登録チェック(get_id_url_ch)
#(f8)登録結果画面出力(PRend)
#(f9)マークForm管理者画面出力(PR_mark)

$Eref=$_SERVER['HTTP_REFERER'];
$_SERVER['HTTP_REFERER']="";
#管理画面のGETqueryをPOSTに変換。
if($_GET['changer'] == 'admin' && $_GET['in_mode'] == 'mente' && $_GET['Smode_name'] == 'mente') {
	$_POST['changer']=$_GET['changer'];
	$_POST['in_mode']=$_GET['in_mode'];
	$_POST['Smode_name']=$_GET['Smode_name'];
	$id = $_POST['id'] = $_GET['id'];
	$_POST['pass']=$_GET['pass'];
}
#-----------------#
# 各フィールドのデータを「$P〜」に入力
#$Pdata[0]=データID,$Pdata[1]=タイトル,$Pdata[2]=ホームページのURL,$Pdata[3]=マークデータ
#$Pdata[4]=更新日,$Pdata[5]=パスワード,$Pdata[6]=紹介文,$Pdata[7]=管理人コメント
#$Pdata[8]=お名前,$Pdata[9]=メールアドレス,$Pdata[10]=カテゴリ(リスト),$Pdata[11]=time
#$Pdata[12]=バナーURL,$Pdata[13]=更新フラグ,$Pdata[14]=IP,$Pdata[15]=キーワード
# $Pmode=>送信先のモード
# $_POST['in_mode']=>概入力値設定モード(なし,new_dairi,mente,form)
# $Smode_name=>各モードの判定用の内部変数(なし,new_dairi,mente)
# $_POST['changer'}=>変更者(なし,admin)

// POST入力をサニタイズ
for($kt_no=1; $kt_no <= $EST_reg['kt_max']; $kt_no++){
	$_POST["Fkt$kt_no"] = preg_replace("/[^0-9_]+/","",$_POST["Fkt$kt_no"]);
}

##概入力値設定($_POST['in_mode'})
if(!isset($_POST['in_mode'])){ #新規登録()
	$Pdata=array("","","http://","","","","","","","",$_POST['kt'],"","http://","","","");
}
elseif($_POST['in_mode'] == "new_dairi"){ #管理人代理登録(new_dairi)
}
elseif($_POST['in_mode'] == "mente"){ #内容変更(mente)
	$query = "SELECT * FROM $EST[sqltb]log WHERE id='$_POST[id]' LIMIT 1";
	$result = $xoopsDB->query($query) or die("Query failed");
	$Pdata = mysql_fetch_row($result);
	if($Pdata){
		if ($is_admin != 1){
			$cr_pass=crypt($_POST['pass'],$Pdata[5]);
			if(($_POST['changer'] != "admin" && $Pdata[5] != $cr_pass) || (!$_POST['pass'])){mes("パスワードが違います","パスワード認証エラー","java");}
		}
	}
	else {mes("該当するデータはありません","エラー","java");}
}
elseif($_POST['in_mode'] == "form"){ #外部入力(form)
	$_POST['Fkt']="";
	for($kt_no=1; $kt_no <= $EST_reg['kt_max']; $kt_no++){
		$_POST['Fkt'] .= "&".$_POST["Fkt$kt_no"];
	}
	$Pdata=array("",$_POST['Ftitle'],$_POST['Furl'],"","",$_POST['Fpass'],$_POST['Fsyoukai'],"",$_POST['Fname'],$_POST['Femail'],$_POST['Fkt'],"",$_POST['Fbana_url'],"","",$_POST['Fkey'],"",$_POST['Fuid']);
}
else{$Pdata=array();}

#-------------------------------

#(1)プレビュー画面(preview)
if(isset($_POST['preview']) && $_POST['preview'] == "on"){
	#※登録者の新規登録時にのみ使用
	check();
	##その他の設定
	#相互リンクの有無
	$MES_sougo[1]=" checked"; $MES_sougo[0]="";
	#紹介文の改行を変換(<br>→\n)
	$_POST['Fsyoukai']=str_replace("<br>", "\n", $_POST['Fsyoukai']);
	require "$EST[temp_path]regist_new_preview.html";
	exit;
}

#(2)リンク切れ報告フォーム(&no_link)
elseif($_REQUEST['mode'] == "no_link"){
	if($_GET["pre"] == "on"){
		$Eref=urlencode($Eref);
		$YOMI_TICKET_TAG = YOMI_TICKET_TAG;
		$mes=<<<EOM
管理者に「<b>$_GET[title]</b>」についての通知を行います<br>
「通知する」ボタンを押すと管理者へ通知できます
<br><br>
<form action="regist_ys.php" method=post target="">
  $YOMI_TICKET_TAG
  <input type=hidden name=mode value="no_link">
  <input type=hidden name=id value="$_GET[id]">
  <input type=hidden name=pre value="">
  <input type=hidden name=ref value="$Eref">
  <input type=hidden name=title value="$_GET[title]">
	<ul>
	[通知種別]<br>
		<input type=checkbox name=type_no_link value="1">リンク切れ<br>
		<input type=checkbox name=type_move value="2">ホームページ移転<br>
		<input type=checkbox name=type_bana_no_link value="3">バナーリンク切れ<br>
		<input type=checkbox name=type_ill value="4">規約違反[<a href="$EST[cgi_path_url]regist_ys.php?mode=new">規約はこちら</a>]<br>
		<input type=checkbox name=type_other value="5">その他(コメント欄にもご記入ください)<br>
	<br>
	[コメント](必要があればご記入ください)<br>
		<textarea name=com cols=40 rows=4></textarea><br>
	<br>
	[お名前](任意)<br>
	<input type=text name=c_name><br>
	[E-Mail](任意)<br>
	<input type=text name=c_email><br>
	</ul>
</ul>
<center>
  <input type=submit value="通知する">
</center>
</form>
<hr width="90%">
<center>
<form><input type=button value=" 前の画面に戻る " onClick="history.back()"></form>
</center>
EOM;
		mes($mes,"管理者への通知画面");
	}
	$_POST['id']=preg_replace("/\D/", "", $_POST['id']);
	if($_POST['id'] && (strstr($_SERVER['HTTP_USER_AGENT'],"Mozilla") || strstr($_SERVER['HTTP_USER_AGENT'],"Lynx") || strstr($_SERVER[HTTP_USER_AGENT],"Opera"))){
		$ip_fl=1;
		if($EST['no_link_ip']){
			$ip=explode(",",$EST['no_link_ip']);
			foreach($ip as $tmp){
				if(strstr($_SERVER['REMOTE_ADDR'],$tmp)){$ip_fl=0;break;}
			}
		}
		if($ip_fl){
			$fl=0;
			#報告種別(リンク切れ=0/サイト移転=1/バナーリンク切れ=2/規約違反=3/その他=4)
			if($_POST['type_no_link']){$Dhoukoku.="1,";$fl=1;}
			if($_POST['type_move']){$Dhoukoku.="2,";$fl=1;}
			if($_POST['type_bana_no_link']){$Dhoukoku.="3,";$fl=1;}
			if($_POST['type_ill']){$Dhoukoku.="4,";$fl=1;}
			if($_POST['type_other']){$Dhoukoku.="5,";$fl=1;}
			if(!$fl){mes("「通知種別」に最低一つはチェックしてください","チェックミス","java");}
			#コメント
			$Dcom=str_replace("\n", "<br>", $_POST['com']);
			#名前
			$Dname=str_replace("\n", "", $_POST['c_name']);
			#E-Mail
			$Demail=str_replace("\n", "", $_POST['c_email']);
			if(strlen("$Dcom$Dname$Demail")>500){mes("コメント、お名前、E-Mailの文字数は<br>合計で250文字(全角換算)までで、お願いします。","文字数オーバー","java");}
			$fp=fopen("$EST[log_path]no_link_temp.cgi", "a");
			flock($fp, LOCK_EX);
			fputs($fp, "$_POST[id]<>$_SERVER[REMOTE_ADDR]<>$Dhoukoku<>$Dcom<>$Dname<>$Demail<>\n");
			fclose($fp);
		}
	}
	$_POST[ref]=urldecode($_POST[ref]);
	mes("ご報告ありがとうございました<br>管理人に「<b>".htmlspecialchars($_POST['title'])."</b>」についての通知を行いました","ご報告ありがとうございます",$_POST['ref']);
	exit;
}

#新規登録実行
#(3)新規登録実行(act_regist)
elseif($_POST['mode'] == "act_regist"){
	#$new=>追加データ書き込み用/$TASK=>更新するカテゴリリスト
	#$hyouji_log=>結果表示用のログデータ
	#パスワード認証(管理者認証)
	if($_POST['changer'] == "admin" && $is_admin != 1){
		$cr_pass=crypt($_POST['pass'],$EST['pass']);
		if($cr_pass != $EST['pass'] || (!$_POST['pass'])){
			if(!$_SERVER['REMOTE_HOST']){$_SERVER['REMOTE_HOST']=gethostbyaddr($_SERVER['REMOTE_ADDR']);}
			mes("パスワードの認証に失敗しました<br>認証したコンピュータのIPアドレス：<b>$_SERVER[REMOTE_ADDR]</b><br>認証したコンピュータのホスト名：<b>$_SERVER[REMOTE_HOST]</b>","パスワード認証失敗","java");
		}
	}
	check(); #入力内容のチェック
	#ID取得&２重URL登録チェック
	if($EST_reg['nijyu_url']){$new_id=get_id_url_ch(1);}
	else{
		$query = "SELECT id FROM $EST[sqltb]log ORDER BY id DESC LIMIT 1";
		$result = $xoopsDB->query($query) or die("Query failed");
		$num=mysql_fetch_row($result);
		$new_id=++$num[0];
		$fp=fopen("$EST[log_path]$EST[temp_logfile]", "r");
		while($tmp=fgets($fp, 4096)) {
			$Tlog=explode("<>",$tmp);
			if ($new_id <= $Tlog[0]) $new_id = ++$Tlog[0];
		}
		fclose($fp);
	}
	$Slog=join_fld($new_id); #入力内容の整形
	$hyouji_log=$Slog;
	if($EST['user_check'] && $_POST['changer'] != "admin" && $_POST['mode'] == "act_regist"){ #<仮登録時>
		#仮登録ログデータに追加書き込み
		$Slog[6]=str_replace("\n", "<br>", $Slog[6]);
		$Slog[7]=str_replace("\n", "<br>", $Slog[7]);
		$new=implode("<>",$Slog);
		$new .= "<>\n";
		$fp = fopen("$EST[log_path]$EST[temp_logfile]", "a");
		flock($fp, LOCK_EX);
		fputs ($fp, $new);
		fclose($fp);
		##メールを送信
		#件名に付けるマークを設定
		if($_POST['Fsougo']){$PR_mail_sougo="(link)";}
		else{$PR_mail_sougo="";}
		if($_POST['Fto_admin']){$PR_mail_com="(com)";}
		else{$PR_mail_com="";}
		if($_POST['Fadd_kt']){$PR_mail_kt="(kt)";}
		else{$PR_mail_kt="";}
		$PR_mail_add_line=$PR_mail_sougo . $PR_mail_com . $PR_mail_kt;
		$Slog[6]=str_replace("<br>", "\n", $Slog[6]);
		$Slog[7]=str_replace("<br>", "\n", $Slog[7]);
		if($EST['mail_temp']){require "pl/mail_ys.php";}
		if($EST['mail_to_admin'] && $EST['mail_temp']){ #管理人へメール送信
			sendmail($EST['admin_email'],$Slog[9],"$EST[search_name] 仮登録がありました".$PR_mail_add_line,"temp","admin",$Slog,$_POST['Fsougo'],$_POST['Fadd_kt'],$_POST['Fto_admin']);
		}
		if($EST['mail_to_register'] && $EST['mail_temp']){ #登録者へメール送信
			sendmail($Slog[9],$EST['admin_email'],"$EST[search_name] 仮登録完了通知","temp","",$Slog,$_POST['Fsougo'],$_POST['Fadd_kt'],$_POST['Fto_admin']);
		}
		//$Slog[6]=str_replace("\n", "<br>", $Slog[6]);
		//$Slog[7]=str_replace("\n", "<br>", $Slog[7]);
		##登録結果出力
		require "$EST[temp_path]regist_new_end_temp.html";
	} #</仮登録時>
	else{ #<新規登録時>
		$Slog = array_map("addslashes", $Slog);
		$query = "INSERT INTO $EST[sqltb]log VALUES ('$Slog[0]','$Slog[1]','$Slog[2]','$Slog[3]','$Slog[4]','$Slog[5]','$Slog[6]','$Slog[7]','$Slog[8]','$Slog[9]','$Slog[10]','$Slog[11]','$Slog[12]','$Slog[13]','$Slog[14]','$Slog[15]','$Slog[16]','$Slog[17]',0,0,0)";
		$result = $xoopsDB->query($query) or die("Query failed regist633");
		##登録者のメッセージを保存する設定の場合
		if(($_POST['Fadd_kt'] || $_POST['Fto_admin']) && $EST_reg['look_mes'] && preg_match("/(\d+)(\w*)/", $EST_reg['look_mes'], $match)){
			$i=0;
			$look_mes_list=array();
			$max=$match[1];
			$fp = fopen("$EST[log_path]look_mes.cgi", "r");
			while($tmp = fgets($fp, 4096)){
				if($i<$max){array_push($look_mes_list,$tmp);}
				else{break;}
				$i++;
			}
			fclose($fp);
			#一括送信する場合
			if($match[2] == "m" && $i>=$max){
				$mail_mes=<<<EOM
				## $EST[search_name] 登録者からのメッセージ通知 ##

EOM;
				foreach($look_mes_list as $tmp){
					$tlook_mes=explode("<>",$tmp);
					$mail_mes.=<<<EOM
+-------------------------+
登録日：$tlook_mes[1] / お名前：$tlook_mes[5] / Email： $tlook_mes[4]
タイトル：$tlook_mes[7]
URL：
$tlook_mes[6]
修正用URL：
$EST[cgi_path_url]regist_ys.php?mode=enter&id=$tlook_mes[0]
EOM;
					if($tlook_mes[2]){$mail_mes.="新設希望カテゴリ：$tlook_mes[2]\n";}
					if($tlook_mes[3]){
						$tlook_mes[3]=str_replace("<br>", "\n", $tlook_mes[3]);
						$mail_mes.=$tlook_mes[3] . "\n";
					}
				}
				$mail_mes.="+-------------------------+\n";
				require "pl/mail_ys.php";
				sendmail($EST['admin_email'],$EST['admin_email'],"$EST[search_name] 登録者からのメッセージ通知(${max}件)","any","","","","","","",$mail_mes);
				$i=0;
				$look_mes_list=array();
			}
			if($i == $max){array_pop ($look_mes_list);}
			#新規追加データ($look_mes)を作成
			$look_mes[0]=$Slog[0];
			$look_mes[1]=$Slog[4];
			$look_mes[2]=$_POST['Fadd_kt'];
			$look_mes[3]=$_POST['Fto_admin']; $look_mes[4]=str_replace("\n", "<br>", $look_mes[4]);
			$look_mes[4]=$Slog[9];
			$look_mes[5]=$Slog[8];
			$look_mes[6]=$Slog[2];
			$look_mes[7]=$Slog[1];
			$look_mes=join("<>",$look_mes); $look_mes=str_replace("\n", "", $look_mes);
			$look_mes.="<>\n";
			array_unshift($look_mes_list,$look_mes);
			$fp = fopen("$EST[log_path]look_mes.cgi", "w");
			foreach($look_mes_list as $tmp) {
				fputs($fp, $tmp);
			}
			fclose($fp);
		}
		##メールを送信
		if($_POST[FCmail] != "no" or $_POST['changer'] != "admin"){ #送信する設定なら
			#件名に付けるマークを設定
			#local($PR_mail_sougo,$PR_mail_com,$PR_mail_kt);
			if($_POST['Fsougo']){$PR_mail_sougo="(link)";}
			else{$PR_mail_sougo="";}
			if($_POST['Fto_admin']){$PR_mail_com="(com)";}
			else{$PR_mail_com="";}
			if($_POST['Fadd_kt']){$PR_mail_kt="(kt)";}
			else{$PR_mail_kt="";}
			$PR_mail_add_line=$PR_mail_sougo . $PR_mail_com . $PR_mail_kt;
			$Slog[6]=str_replace("<br>", "\n", $Slog[6]);
			$Slog[7]=str_replace("<br>", "\n", $Slog[7]);
			if($EST['mail_new']){require "pl/mail_ys.php";}
			if($EST['mail_to_admin'] && $EST['mail_new']){ #管理人へメール送信
				sendmail($EST['admin_email'],$Slog[9],"$EST[search_name] 新規登録がありました${PR_mail_add_line}","new","admin",$Slog,$_POST['Fsougo'],$_POST['Fadd_kt'],$_POST['Fto_admin']);
			}
			if($EST['mail_to_register'] && $EST['mail_new']){ #登録者へメール送信
				sendmail($Slog[9],$EST['admin_email'],"$EST[search_name] 新規登録完了通知","new","",$Slog,$_POST['Fsougo'],$_POST['Fadd_kt'],$_POST['Fto_admin']);
			}
		}
		##登録結果出力
		$Slog=$hyouji_log;
		require "$EST[temp_path]regist_new_end.html";
	} #</新規登録時>
}
#(4)修正・削除のためのパスワード認証(enter)
elseif($_GET['mode'] == "enter"){
	if(empty($_POST['id'])) {$_POST['id'] = (empty($_GET['id']))? 0 : $_GET['id'];}

	#クッキーの読み込み
	$CK_data=get_cookie();
	
	$id_from_cookie = FALSE;
	if (!x_uid && !empty($CK_data[1]))
	{
		// ゲストで cookie に id を持っている場合
		$query = "SELECT count(*) FROM ".$EST['sqltb']."log WHERE id='".$CK_data[1]."'";
		$result = $xoopsDB->query($query) or die("Query failed registys.php in ".__LINE__);
		$_count = 0;
		if ($result)
		{
			list($_count) = mysql_fetch_row($result);
		}
		if ($_count)
		{
			$_POST['id'] = $CK_data[1];
			$id_from_cookie = TRUE;
		}
	}
	
	if ($_POST['id'])
	{
		// idが指定されている
		$query = "SELECT * FROM $EST[sqltb]log WHERE id='".$_POST['id']."' LIMIT 1";
		$result = $xoopsDB->query($query) or die("Query failed registys.php in ".__LINE__);
		$Pdata = mysql_fetch_row($result);
		$Pdata = array_map("stripslashes", $Pdata);
		$_count = count($Pdata);
		
		if ($_count && (($CK_data[4] && $CK_data[3]) || $is_admin == 1 || (($x_uid) && $Pdata[17] == $x_uid)))
		{
			// 直接認証
			$_POST['pass']=$CK_data[3];
			if ($is_admin) $_POST['changer']="admin";
			$_POST['in_mode']="mente";
			$result = "";
		}
		else
		{
			if ($_count)
			{
				#概入力値の設定
				$PR_data=<<<EOM
[登録データ]<br>
<table width=200><tr><td>
■タイトル：<br>$Pdata[1]<br>
■URL：<br><a href="$Pdata[2]">$Pdata[2]</a>
<div align=right>[<a href="$Pdata[2]" target="_blank">確認</a>]</div>
</td></tr>
</table>
EOM;
			}
			else
			{
				if (!$id_from_cookie)
				{
					mes("指定されたIDのデータは存在しません","エラー","java");
				}
				else
				{
					$_POST['id'] = "";
					$PR_data = "";
				}
			}
			require "$EST[temp_path]enter.html";
			exit;
		}
	}
	else
	{
		// idが指定されていない
		if ($x_uid)
		{
			// ログインユーザー
			$query = "SELECT * FROM $EST[sqltb]log WHERE uid='".$x_uid."' ORDER BY `stamp` DESC LIMIT 100";
			$result = $xoopsDB->query($query) or die("Query failed registys.php in ".__LINE__);
			$msg = "";
			while ($Pdata = mysql_fetch_row($result))
			{
				$msg .= '<li><a href="?mode=enter&amp;id='.$Pdata[0].'">'.htmlspecialchars($Pdata[1]).'</a></li>';
			}
			$title = "登録済みデータの更新";
			if ($msg)
			{
				$msg = "編集したいタイトルをクリックしてください。"."<ul>".$msg."</ul>";
			}
			else
			{
				$msg = "登録したデータはありません。";
			}
			mes($msg, $title, "java");
		}
		else
		{
			$_POST['id'] = "";
			$PR_data = "";
			require "$EST[temp_path]enter.html";
			exit;
		}
	}
}
#(5)ヘルプの表示(help)
elseif($_GET['mode'] == "help"){
	require "$EST[temp_path]help.html";
	exit;
}
#(6)登録内容変更(act_mente)
elseif($_POST['mode'] == "act_mente"){
	#$new=>追加データ書き込み用/$TASK=>更新するカテゴリリスト#
	#その他の設定
	$Smode_name="mente";
	#パスワード認証(管理者認証)
	if($_POST['changer'] == "admin" && $is_admin != 1){
		$cr_pass=crypt($_POST['pass'],$EST['pass']);
		if($cr_pass != $EST['pass'] || (!$_POST['pass'])){
			if(!$_SERVER['REMOTE_HOST']){$_SERVER['REMOTE_HOST']=gethostbyaddr($_SERVER['REMOTE_ADDR']);}
			mes("($x_uid:$Pdata[17])パスワードの認証に失敗しました<br>認証したコンピュータのIPアドレス：<b>$_SERVER[REMOTE_ADDR]</b><br>認証したコンピュータのホスト名：<b>$_SERVER[REMOTE_HOST]</b>","パスワード認証失敗","java");
		}
	}
	elseif(!$is_admin && $EST_reg['no_mente']){mes("現在、登録者による修正・削除は停止されています","エラー","java");}
	check(); #入力内容のチェック
	#$Spre_log取得&２重URL登録チェック
	if($EST_reg['nijyu_url']){get_id_url_ch(2);}
	$query = "SELECT * FROM $EST[sqltb]log WHERE id='$_POST[id]' LIMIT 1";
	$result = $xoopsDB->query($query) or die("Query failed");
	$Spre_log = mysql_fetch_row($result);
	#登録者のパスワード認証
	if($_POST[changer] != "admin"){
		#$cr_pass=crypt($_POST[pass],$Spre_log[5]);
		if($Spre_log[5] != $_POST['Fpass']){mes("パスワードが間違っています".$_POST['Fpass']." $Spre_log[5]","パスワード認証エラー","java");}
	}
	$Slog=join_fld($_POST[id]);
	#本体ログデータに書き込み
	$Tlog = array_map("addslashes", $Slog);
	$query = "UPDATE $EST[sqltb]log SET title='$Tlog[1]',url='$Tlog[2]',mark='$Tlog[3]',last_time='$Tlog[4]',message='$Tlog[6]',comment='$Tlog[7]',name='$Tlog[8]',mail='$Tlog[9]',category='$Tlog[10]',stamp='$Tlog[11]',banner='$Tlog[12]',renew='$Tlog[13]',keywd='$Tlog[15]',build_time='$Tlog[16]',uid='$Tlog[17]' WHERE id='$_POST[id]'";
	$result = $xoopsDB->query($query) or die("Query failed1");
	##メールを送信
	if($_POST[FCmail] != "no" or $_POST['changer'] != "admin"){ #送信する設定なら
		$Slog[6]=str_replace("<br>", "\n", $Slog[6]);
		$Slog[7]=str_replace("<br>", "\n", $Slog[7]);
		if($EST['mail_new']){require "pl/mail_ys.php";}
		if($EST['mail_to_admin'] && $EST['mail_ch']){ #管理人へメール送信
			sendmail($EST['admin_email'],$Slog[9],"$EST[search_name] 登録内容変更完了通知","mente","admin",$Slog);
		}
		if($EST['mail_to_register'] && $EST['mail_ch']){ #登録者へメール送信
			sendmail($Slog[9],$EST['admin_email'],"$EST[search_name] 登録内容変更完了通知","mente","",$Slog);
		}
		//$Slog[6]=str_replace("\n", "<br>", $Slog[6]);
		//$Slog[7]=str_replace("\n", "<br>", $Slog[7]);
	}
	##更新するカテゴリリストを作成
	#%TASKを使用
	#マークの表示設定
	$i=1; $PR_mark="";
	$mark=explode("_",$Slog[3]);
	foreach ($mark as $tmp){
		if($tmp){$PR_mark .= $EST["name_m$i"] . " ";}
		$i++;
	}
	#カテゴリの変更表示設定
	if($EST[user_change_kt]){$PR_kt="※登録者によるカテゴリ変更は現在禁止されています";}
	else{$PR_kt="";}
	##登録結果出力
	require "$EST[temp_path]regist_mente_end.html";
}
#(7)パスワードの再発行・変更(act_repass)
elseif($_POST['mode'] == "act_repass"){
	if($_POST['repass_mode'] == "repass"){ #パスワード再発行時
		if($_POST['repass_check'] != "on"){mes("パスワード再発行の確認チェックがありません。もう一度戻ってからチェックを入れて再度実行してください","確認チェックをしてください","java");}
		if(!$EST['re_pass_fl']){mes("パスワードの再発行はできない設定になっています","エラー","java");}
		#新しいパスワードを作成
		#local($tane,$data_temp,@pass_rm);
		$new_pass = uniqid("");
		$cr_new_pass=crypt($new_pass, "ys");
		if($EST['mail_pass']){$PR_mes="パスワードの再発行が完了しました<br>新しいパスワードはメールアドレスに送信されます";}
		else{$PR_mes="パスワードの再発行が完了しました<br>新しいパスワードは「 <b>$new_pass</b> 」です";}
	}
	else{ #パスワード変更時
		$_POST['new_pass']=preg_replace("/\W/", "", $_POST[new_pass]);
		$new_pass=$_POST['new_pass'];
		$cr_new_pass=crypt($new_pass, "ys");
		if($EST['mail_pass']){$PR_mes="パスワードの変更が完了しました<br>新しいパスワードはメールアドレスに送信されます";}
		else{$PR_mes="パスワードの変更が完了しました<br>新しいパスワードは「 <b>$new_pass</b> 」です";}
	}
	$query = "SELECT * FROM $EST[sqltb]log WHERE id='$_POST[id]' LIMIT 1";
	$result = $xoopsDB->query($query) or die("Query failed1");
	$Slog = mysql_fetch_row($result);
	if($Slog) {
		if($_POST['repass_mode'] != "repass"){
			if($_POST['changer'] != "admin"){$cr_pass=crypt($_POST['pass'],$Slog[5]);}
			else{$cr_pass=crypt($_POST['pass'],$EST['pass']);}
			if($_POST['changer'] != "admin"){
				if($cr_pass != $Slog[5]){mes("パスワードが間違っています","エラー","java");}
			}
			else{
				if($cr_pass != $EST['pass']){mes("管理パスワードが間違っています","エラー","java");}
			}
		}
		elseif($_POST['email'] != $Slog[9]){
			mes("IDとメールアドレスが一致しませんでした","エラー","java");
		}
		$mail_to=$Slog[9];
		$Slog[5]=$cr_new_pass;
		$query = "UPDATE $EST[sqltb]log SET passwd='$Slog[5]' WHERE id='$_POST[id]' LIMIT 1";
		$result = $xoopsDB->queryF($query) or die("Query failed1");
		if($EST['mail_pass']){
			require "pl/mail_ys.php";
			sendmail($mail_to,$EST['admin_email'],"$EST[search_name] パスワード変更通知","pass","",$Slog);
		}
		mes($PR_mes,"パスワード変更完了","$EST[home]");
	}
	else {mes("該当するIDはありません","エラー","java");}
	exit;
}
#(8)削除実行(act_del)
elseif($_POST['mode'] == "act_del"){
	/*
	// 管理者のみ削除できるようにする
	global $xoopsUser;
	if ( $xoopsUser )
	{
		$xoopsModule = XoopsModule::getByDirname("yomi");
		if (!$xoopsUser->isAdmin($xoopsModule->mid()))
			mes("現在、登録者による削除は停止されています","エラー","java");
	}
	else
		mes("現在、登録者による削除は停止されています","エラー","java");
	*/
	$Cdel=0;
	if($_POST[del_mode] == "single"){ #del_mode:single
		if($_POST['del_check'] != "on"){mes("削除確認のためにチェックを入れてから削除ボタンを押してください","確認チェックをしてください","java");}
		if($_POST['changer'] != "admin" && $EST_reg['no_mente']){mes("現在、登録者による修正・削除は停止されています","エラー","java");}
		if($_POST['changer'] == "admin"){pass_check();}
		$fl=0;
		$query = "SELECT passwd FROM $EST[sqltb]log WHERE id='$_POST[id]' LIMIT 1";
		$result = $xoopsDB->query($query) or die("Query failed1");
		$Slog = mysql_fetch_row($result);
		if($Slog) {
			if(!$x_uid || ($_POST['changer'] != "admin" && $x_uid != $_POST['Fuid'])) { #削除する人が登録者の場合
				$cr_pass=crypt($_POST['pass'],$Slog[0]);
				if($cr_pass != $Slog[0] || (!$_POST['pass'])){mes("パスワードの認証に失敗しました","エラー","java");}
			}
			$query = "DELETE FROM $EST[sqltb]log WHERE id='$_POST[id]' LIMIT 1";
			$result = $xoopsDB->queryF($query) or die("Query failed regist916");
			// rankログ内該当ID削除
			$query = "DELETE FROM $EST[sqltb]rank WHERE id='$_POST[id]'";
			$result = $xoopsDB->queryF($query) or die("Query failed regist916");
			// revログ内該当ID削除
			$query = "DELETE FROM $EST[sqltb]rev WHERE id='$_POST[id]'";
			$result = $xoopsDB->queryF($query) or die("Query failed regist916");
		}
		else {mes("該当するデータは見つかりません","エラー","java");}
	}
	else{ #del_mode:multi
		if($_POST['changer'] != "admin"){mes("変更者指定が不正です","エラー","java");}
		pass_check();
		#リンク切れリストからの削除の場合
		if($_POST['no_link'] == "on"){
			$lines = array();
			$fp=fopen("$EST[log_path]no_link.cgi", "r");
				while($tmp = fgets($fp, 4096)){
					$data=explode("<>",$tmp); #id<>count<>ip<>url<>\n
					if(!$_POST["id_$data[0]"]){array_push($lines,$tmp);}
					if($_POST["id_$data[0]"] == "on") {$_POST['del'][] = $data[0];}
				}
			fclose($fp);
			$fp=fopen("$EST[log_path]no_link.cgi", "w");
				foreach($lines as $tmp) {
					fputs($fp, $tmp);
				}
			fclose($fp);
		}
		#デッドリンクチェック済みリストからの削除の場合
		if($_POST['dl_check'] == "on"){
			if(!is_file($_POST['checkfile'])){mes("ファイル指定が異常です","エラー","java");}
			$lines = array();
			$fp=fopen("./$_POST[checkfile]", "r");
				while($tmp = fgets($fp, 4096)){
					$data=explode("\t",$tmp); #id=0<><><>url=13<>\n
					if(!$_POST["id_$data[0]"]){array_push($lines,$tmp);}
					if($_POST["id_$data[0]"] == "on") {$_POST['del'][] = $data[0];}
				}
			fclose($fp);
			$fp=fopen("./$_POST[checkfile]", "r");
				foreach($lines as $tmp) {
					fputs($fp, $tmp);
				}
			fclose($fp);
		}
		if($_POST[del]) {
			foreach($_POST['del'] as $del){
				$query = "DELETE FROM $EST[sqltb]log WHERE id='$del' LIMIT 1";
				$result = $xoopsDB->queryF($query) or die("Query failed regist558 $query");
			}
		}
	}
	if($_POST['changer'] == "admin" && ($_POST['no_link'] == "on" || $_POST['dl_check'] == "on")){mes("削除処理が完了しました","削除完了","kanri");}
	else{mes("削除処理が完了しました","削除完了",$EST[home]);}
	exit;
}
#(9)登録画面(新規登録・管理人代理登録・登録内容変更)(regist)
#クッキーを記録
if($_POST['in_mode'] == "mente"){ #登録内容変更時
	$CK_data=get_cookie();
	if($_POST['changer'] != "admin" && $_POST['pass']){$CK_data[0]=$_POST['pass'];} #登録者パスワード
	$CK_data[1]=$_POST[id]; #ID
	if($_POST['changer'] == "admin"){$CK_data[2]="admin";} #変更者
	if($_POST['changer'] == "admin" && $_POST['pass']){$CK_data[3]=$_POST['pass'];} #管理者パスワード
	if($_POST['cookie'] == "off"){set_fo_cookie();}
	else{set_cookie($CK_data);}
}
#パスワード認証(管理者認証)
if ($is_admin != 1 && $x_uid == $Pdate[17]){
	if($_POST['changer'] == "admin"){
		$cr_pass=crypt($_POST['pass'],$EST['pass']);
		if($cr_pass != $EST['pass'] || (!$_POST['pass'])){
			if(!$_SERVER['REMOTE_HOST']){$_SERVER['REMOTE_HOST']=gethostbyaddr($_SERVER['REMOTE_ADDR']);}
			mes("パスワードの認証に失敗しました<br>認証したコンピュータのIPアドレス：<b>".$_SERVER['REMOTE_ADDR']."</b><br>認証したコンピュータのホスト名：<b>".$_SERVER['REMOTE_HOST']."</b>","パスワード認証失敗","java");
		}
	}
}
#管理人のみが登録できるモード
if(($EST_reg['no_regist']==1) && $_POST['in_mode'] != "mente" && $_POST['changer'] != "admin"){
	mes("現在、訪問者による新規登録は停止されています","エラー","java");
}
##$Smode_nameの設定
#管理人代理登録
if($_POST['changer'] == "admin" && $_POST['in_mode'] != "mente"){$Smode_name="new_dairi";}
#登録内容変更
elseif($_POST['in_mode'] == "mente"){$Smode_name="mente";}
#登録者の新規登録
else{$Smode_name="";}
##$Pmodeの設定
#登録内容変更
if($_POST['Smode_name'] == "mente"){
	$Pmode="act_mente";
}
#新規登録
else{
	if ($is_admin == 1 && !$_POST['id']) {
		$Smode_name = "new_dairi";
		$_POST['changer'] = "admin";
	}
	$Pmode="act_regist";
}
##その他の設定
#相互リンクの有無
$MES_sougo[1]=" checked"; $MES_sougo{0}="";
#テンプレートの読み込み
if($Smode_name == "new_dairi"){
	if(!$Pdata[10]) $Pdata[10] = "&".$_GET['kt'];
	require "$EST[temp_path]regist_new_admin.html";
}
elseif($_POST['changer'] != "admin" && $Smode_name == "mente"){
	if($EST['syoukai_br']){
		$Pdata[6]=str_replace("<br>", "\n", $Pdata[6]);
		$Pdata[7]=str_replace("<br>", "\n", $Pdata[7]);
	}
	if($EST_reg[no_mente]){mes("現在、登録者による修正・削除は停止されています","エラー","java");}
	require "$EST[temp_path]regist_mente.html";
}
elseif($_POST['changer'] == "admin" && $Smode_name == "mente"){
	if($EST[syoukai_br]){
		$Pdata[6]=str_replace("<br>", "\n", $Pdata[6]);
		$Pdata[7]=str_replace("<br>", "\n", $Pdata[7]);
	}
	require "$EST[temp_path]regist_mente_admin.html";
}
else{
	if ($xoopsUser) {
		$name_xoops = $xoopsUser->name();
		if(!$name_xoops) $name_xoops = $xoopsUser->uname();
		if(!$Pdata[8]) $Pdata[8] = $name_xoops;
		if(!$Pdata[9]) $Pdata[9] = $xoopsUser->email();
		if(!$Pdata[17]) $Pdata[17] = $xoopsUser->uid();
	} elseif($EST_reg['no_regist']) {
		mes("現在、ゲストによる登録は停止されています","エラー","java");
	}
	if(!$Pdata[10]) {$Pdata[10] = "&".$_GET['kt'];}
	require "$EST[temp_path]regist_new.html";
	exit;
}
#----------------------------------------------------------------------------
#(f1)登録するカテゴリを表示(PR_kt)
function PR_kt($category=""){
	global $EST, $EST_reg, $ganes, $gane_UR;
	$kt_no=1;
	$PRselect=" selected";
	$Pkt=explode("&",$category);
	if($EST_reg['kt_min'] != $EST_reg['kt_max']){
		echo "<ul>※<b>$EST_reg[kt_min]</b>〜<b>$EST_reg[kt_max]</b>個まで選択できます<br>";
	}
	else{
		echo "<ul>※<b>$EST_reg[kt_max]</b>個選択してください<br>";
	}
	?>
		※各カテゴリの詳細は「<a href="<?=$EST['cgi_path_url']?>sitemap.php" target="_blank">カテゴリ一覧</a>」を参考にしてください<br>
<?php
	gane_st(); #ジャンルステータスをロード
	for ($kt_no=1; $kt_no <= $EST_reg['kt_max']; $kt_no++){
		$PRselect=" selected";
		?>
		<!--<select name=Fkt<?=$kt_no?> size=7>-->
		<select name=Fkt<?=$kt_no?> size=1>
<?php
		if($Pkt[$kt_no]){echo '<option value="' . $Pkt[$kt_no] . "\"$PRselect>" . full_kt($Pkt[$kt_no]) . "\n"; $PRselect="";}
		?>
			<option value=""<?=$PRselect?>>--指定しない--
<?php
		foreach ($ganes as $line=>$val){
			if($_POST['changer'] == "admin" || !$gane_UR[$line]){
				echo "<option value=\"$line\">" . full_kt($line) . "\n";
			}
		}
		?>
		</select><br><br>
<?php
	}
	echo "</ul><br>";
}

#(f2)メッセージ画面出力(mes)
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
function mes($MES, $TITLE="", $arg3=""){
	global $EST, $EST_reg, $link;
	global $xoopsOption,$xoopsConfig,$xoopsLogger,$xoopsTpl;
	global $x_ver,$ver;
	if(!$TITLE) {$TITLE="メッセージ画面";}
	if($arg3 == "java" || ($arg3 == "back_reg" && $_POST['mode'] == "act_mente")){
		$BACK_URL="<form><input type=button value=\"&nbsp;&nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;&nbsp;\" onClick=\"history.back()\"></form>";
	}
	elseif($arg3 == "env"){
		$BACK_URL="【<a href=\"$SERVER[HTTP_REFERER]\">戻る</a>】";
	}
	elseif($arg3 == "kanri"){
		$BACK_URL="<form action=\"$EST[admin]\" method=post>".YOMI_TICKET_TAG."<input type=hidden name=mode value=kanri><input type=hidden name=pass value=\"$_POST[pass]\">	<?php echo YOMI_TICKET_TAG; ?><input type=submit value=\"管理室へ\"></form>";
	}
	elseif(!$arg3){$BACK_URL="";}
	elseif($arg3 == "back_reg"){
		$_POST['Fsyoukai']=str_replace("<br>", "\n", $_POST['Fsyoukai']);
		if($_POST['changer'] == "admin"){$_POST[in_mode]="new_dairi";}
		else{$_POST['in_mode']="form";}
		$BACK_URL=<<<EOM
<form action="regist_ys.php" method=post>
	<input type=hidden name="in_mode" value="form">
	<input type=hidden name="pass" value="$_POST[pass]">
	<input type=hidden name="changer" value="$_POST[changer]">
	<input type=hidden name="Fname" value="$_POST[Fname]">
	<input type=hidden name="Femail" value="$_POST[Femail]">
	<input type=hidden name="Fpass" value="$_POST[Fpass]">
	<input type=hidden name="Fpass2" value="$_POST[Fpass2]">
	<input type=hidden name="Furl" value="$_POST[Furl]">
	<input type=hidden name="Fbana_url" value="$_POST[Fbana_url]">
	<input type=hidden name="Ftitle" value="$_POST[Ftitle]">
	<input type=hidden name="Fsyoukai" value="$_POST[Fsyoukai]">
	<input type=hidden name="Fkanricom" value="$_POST[Fkanricom]">
EOM;
		for($i = 1; $i <= $EST_reg[kt_max]; $i++){
			$BACK_URL .='<input type=hidden name="Fkt'.$i.'" value="'.$_POST["Fkt$i"]."\">\n";
		}
		$BACK_URL .= YOMI_TICKET_TAG;
		$BACK_URL .=<<<EOM
	<input type=hidden name="Fkey" value="$_POST[Fkey]">
	<input type=hidden name="Fadd_kt" value="$_POST[Fadd_kt]">
	<input type=hidden name="Fto_admin" value="$_POST[Fto_admin]">
	<input type=hidden name=Fsougo value="$_POST[Fsougo]">
	<input type=submit value="登録画面に戻る">
EOM;
	}
	else{$BACK_URL="【<a href=\"$arg3\">戻る</a>】";}
	require "$EST[temp_path]mes.html";
	exit;
}

#(f3)入力内容のチェック(check)
function check(){
	global $xoopsUser;
	##禁止ワードのチェック
	global $EST_reg, $ganes, $gane_UR, $is_admin;
	$_POST = array_map("stripslashes", $_POST);
	$_Fkanricom = $_POST['Fkanricom'];
	$_POST = array_map("htmlspecialchars", $_POST);
	$_POST['Fkanricom'] = $_Fkanricom;
	if($EST_reg['kt_no_word']){
		#ワードチェック対象の項目
		$check_str = $_POST['Fname']." ".$_POST['Femail']." ".$_POST['Furl']." ".$_POST['Fbana_url']." ".$_POST['Ftitle']." ".$_POST['Fsyoukai']." ".$_POST['Fkey'];
		$no_words=explode(" ",$EST_reg['kt_no_word']);
		foreach ($no_words as $word){
			if(stristr($check_str,$word)){mes("登録データの中にが禁止されている言葉が入っています。<br>登録しようとしているデータのジャンルをこのサーチエンジンが禁止している可能性があります。","ワードチェックエラー","back_reg");}
		}
		if(!$_SERVER['REMOTE_HOST']){$_SERVER['REMOTE_HOST']=gethostbyaddr($_SERVER['REMOTE_ADDR']);}
		$addr_host=$_SERVER['REMOTE_ADDR'] . " " . $_SERVER['REMOTE_HOST'];
		foreach ($no_words as $word){
			if(stristr($addr_host,$word)){mes("このIP又はホスト名からの登録は禁止されている可能性があります。<br>$_SERVER[REMOTE_ADDR]/$_SERVER[REMOTE_HOST]<br>","IP/HOSTチェックエラー","back_reg");}
		}
	}
	##名前
	if($EST_reg['Fname'] && !$_POST['Fname']){mes("<b>お名前</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
	$num=strlen($_POST['Fname'])-$EST_reg['Mname']*2;if($num>0){mes("<b>お名前</b>は全角<b>$EST_reg[Mname]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
	$_POST['Fname']=str_replace("\n", "", $_POST['Fname']);
	##メールアドレス
	if($EST_reg['Femail'] && !$_POST['Femail']){mes("<b>メールアドレス</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
	elseif(strlen($_POST['Femail'])-$EST_reg['Memail']>0){$num=strlen($_POST['Femail'])-$EST_reg['Memail'];mes("<b>メールアドレス</b>は半角<b>$EST_reg[Memail]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
	elseif($EST_reg['Femail'] && !preg_match("/(.+)\@(.+)\.(.+)/", $_POST['Femail'])){mes("<b>メールアドレス</b>の入力が正しくありません","記入ミス","back_reg");}
	$_POST['Femail']=str_replace("\n", "", $_POST['Femail']);
	##パスワード
	//if ($is_admin != 1){
	if (!$xoopsUser){
		if($_POST['mode'] != "act_mente"){
			if(!$_POST['Fpass']){mes("<b>パスワード</b>は<font color=red>!記入必須項目</font>です","記入ミス","back_reg");}
			elseif($num=strlen($_POST['Fpass'])>8){$num=strlen($_POST['Fpass'])-8;mes("<b>パスワード</b>は半角<b>8</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
			elseif($_POST['Fpass'] != $_POST['Fpass2']){mes("２回の<b>パスワード</b>入力が一致しませんでした","入力ミス","back_reg");}
			$_POST['Fpass']=str_replace("\n", "", $_POST['Fpass']);
		}
	}
	##ホームページアドレス(２重登録チェックは別のところに記述)
	if($_POST['Furl'] == "http://"){$_POST['Furl']="";}
	if($EST_reg['Furl'] && !$_POST['Furl']){mes("<b>ホームページアドレス</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
	elseif(strlen($_POST['Furl'])-$EST_reg['Murl']>0){$num=strlen($_POST['Furl'])-$EST_reg['Murl'];mes("<b>ホームページアドレス</b>は半角<b>$EST_reg[Murl]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
	elseif($_POST['Furl'] && !preg_match("/^https?:\/\/.+\..+/", $_POST['Furl'])){mes("<b>ホームページアドレス</b>の入力が正しくありません","記入ミス","back_reg");}
	$_POST[Furl]=str_replace("\n", "", $_POST['Furl']);
	##タイトルバナーのURL
	if($EST_reg['bana_url']){
		if($_POST['Fbana_url'] == "http://"){$_POST['Fbana_url']="";}
		if($EST_reg['Fbana_url'] && !$_POST['Fbana_url']){mes("<b>タイトルバナーのURL</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
		elseif(strlen($_POST['Fbana_url'])-$EST_reg['Mbana_url']>0){$num=strlen($_POST['Fbana_url'])-$EST_reg['Mbana_url'];mes("<b>タイトルバナーのURL</b>は半角<b>$EST_reg[Mbana_url]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
		elseif($_POST['Fbana_url'] && !preg_match("/^https?:\/\/.+\..+\.(gif|jpg|jpeg|png)$/", $_POST['Fbana_url'])){mes("<b>タイトルバナーのURL</b>の入力が正しくありません","記入ミス","back_reg");}
	}
	else{$_POST['Fbana_url']="";}
	$_POST['Fbana_url']=str_replace("\n", "", $_POST['Fbana_url']);
	##ホームページのタイトル
	if($EST_reg['Ftitle'] && !$_POST['Ftitle']){mes("<b>ホームページのタイトル</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
	if(strlen($_POST['Ftitle'])-($EST_reg['Mtitle']*2)>0){$num=strlen($_POST['Ftitle'])-($EST_reg['Mtitle']*2);mes("<b>ホームページのタイトル</b>は全角<b>$EST_reg[Mtitle]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
	$_POST['Ftitle']=str_replace("\n", "", $_POST['Ftitle']);
	##ホームページの紹介文
	if($EST_reg['Fsyoukai'] && !$_POST['Fsyoukai']){mes("<b>ホームページの紹介文</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
	if(strlen($_POST['Fsyoukai'])-($EST_reg['Msyoukai']*2)>0){$num=strlen($_POST['Fsyoukai'])-($EST_reg['Msyoukai']*2);mes("<b>ホームページの紹介文</b>は全角<b>$EST_reg[Msyoukai]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
	if(!$EST['syoukai_br']){$_POST['Fsyoukai']=str_replace("\n", "", $_POST['Fsyoukai']);}
	//else{$_POST[Fsyoukai]=str_replace("\n", "<br>", $_POST[Fsyoukai]);}
	##管理人コメント
	//$_POST[Fkanricom]=str_replace("\n", "<br>", $_POST[Fkanricom]);
	##カテゴリ
	{#local(%kt_fl,$i,$j=0,$PR_kt);
		$kt_fl=array();
		gane_st(); #ジャンルステータスをロード
		for($i=1; $i <= $EST_reg['kt_max']; $i++){
			$_POST["Fkt$i"]=str_replace("\n", "", $_POST["Fkt$i"]);
			if($kt_fl[$_POST["Fkt$i"]]){$_POST["Fkt$i"]="";}
			elseif($ganes[$_POST["Fkt$i"]]){$kt_fl[$_POST["Fkt$i"]]=1;}
			else{$_POST["Fkt$i"]="";}
			##禁止カテゴリに登録しようとした場合
			if($_POST[changer] != "admin" && $gane_UR[$_POST["Fkt$i"]]){
				mes("登録者の登録ができないカテゴリに変更しようとしています","カテゴリ選択ミス","back_reg");
			}
		}
		$j = count($kt_fl);
		if($EST_reg['kt_min'] == $EST_reg['kt_max']){$PR_kt="<b>$EST_reg[kt_max]</b>個";}
		else{$PR_kt="<b>$EST_reg[kt_min]</b>〜<b>$EST_reg[kt_max]</b>個";}
		if($EST_reg['kt_min']>$j || $j>$EST_reg['kt_max']){mes("<b>カテゴリ</b>は${PR_kt}選択してください","選択数ミス","back_reg");}
	}
	##キーワード
	if($EST_reg['Fkey'] && !$_POST['Fkey']){mes("<b>キーワード</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
	if(strlen($_POST['Fkey'])-($EST_reg['Mkey']*2)>0){$num=strlen($_POST['Fkey'])-($EST_reg['Mkey']*2);mes("<b>キーワード</b>は全角<b>$EST_reg[Mkey]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
	$_POST[Fkey]=str_replace("\n", "<br>", $_POST[Fkey]);
	##追加して欲しいカテゴリ
	if($_POST['mode'] != "act_mente" && $_POST['changer'] != "admin"){
		if($EST_reg['Fadd_kt'] && !$_POST['Fadd_kt']){mes("<b>追加して欲しいカテゴリ</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
		if(strlen($_POST['Fadd_kt'])-($EST_reg['Madd_kt']*2)>0){$num=strlen($_POST['Fadd_kt'])-($EST_reg['Madd_kt']*2);mes("<b>追加して欲しいカテゴリ</b>は全角<b>$EST_reg[Madd_kt]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
		$_POST['Fadd_kt']=str_replace("\n", "<br>", $_POST['Fadd_kt']);
	}
	##相互リンクの有無
	$MES_sougo[1]="する"; $MES_sougo[0]="しない";
	if($_POST['Fsougo'] != "1"){$_POST['Fsougo']=0;}
	##管理人へのメッセージ
	if($_POST['mode'] != "act_mente" && $_POST['changer'] != "admin"){
		if($EST_reg['Fto_admin'] && !$_POST['Fto_admin']){mes("<b>管理人へのメッセージ</b>は<font color=red>記入必須項目</font>です","記入ミス","back_reg");}
		if(strlen($_POST['Fto_admin'])-($EST_reg['Mto_admin']*2)>0){$num=strlen($_POST['Fto_admin'])-($EST_reg['Mto_admin']*2);mes("<b>管理人へのメッセージ</b>は全角<b>$EST_reg[Mto_admin]</b>文字以内でご記入ください","文字数オーバー(半角換算で${num}文字分)","back_reg");}
		if(!$EST['syoukai_br']){$_POST['Fto_admin']=str_replace("\n", "", $_POST['Fto_admin']);}
		else{$_POST['Fto_admin']=str_replace("\n", "<br>", $_POST['Fto_admin']);}
	}
}

#(f4)カテゴリを表示1(PR_preview_kt1)
function PR_preview_kt1(){
	global $EST_reg;
	for($kt_no=1; $kt_no<=$EST_reg['kt_max']; $kt_no++){
		$value = $_POST["Fkt$kt_no"];
		echo "	<input type=hidden name=Fkt" . $kt_no . " value=\"" . $value . "\">\n";
	}
}

#(f5)カテゴリを表示2(PR_preview_kt2)
function PR_preview_kt2(){
	global $EST_reg;
	for($kt_no=1; $kt_no<=$EST_reg['kt_max']; $kt_no++){
		$value = $_POST["Fkt$kt_no"];
		echo full_kt($value);
		?>
<input type=hidden name=Fkt<?=$kt_no?> value="<?=$value?>">
<br>
<?php
	}
}

#(f6)入力内容の整形(join_fld)
function join_fld($arg=""){
	#登録更新用のデータ配列
	#$arg=登録用のデータID
	#[モード]
	# $Smode_name=>各モードの判定用の内部変数(なし,new_dairi,mente)
	# $_POST['changer']=>変更者(なし,admin)
	#※登録内容変更の場合の変更前データは「$Spre_log」に格納されている
	global $Smode_name, $Spre_log, $EST, $EST_reg; 
	##登録No(データID)(0)
	$Slog[0]=$arg;
	##タイトル(1)
	$Slog[1]=$_POST['Ftitle'];
	##URL(2)
	$Slog[2]=$_POST['Furl'];
	##マークデータ(3)
	if($_POST['changer'] == "admin"){ #変更者が管理人
		$_POST['Fmark']="";
		for($i=1; $i <=2; $i++){ #←マーク数を増やすときは修正
			if($_POST["Fmark$i"]){$_POST['Fmark'] .= "1_";}
			else{$_POST['Fmark'] .= "0_";}
		}
		$_POST['Fmark']=substr($_POST['Fmark'],0,-1);
		$Slog[3]=$_POST['Fmark'];
	}
	elseif(!$Smode_name){$Slog[3]="0_0";} #登録者の新規登録
	else{$Slog[3]=$Spre_log[3];} #登録者の変更
	##更新日(4)
	#日時の取得
	if ($_POST['Fhold_timestamp']=="on"){
		$Slog[4]=$Spre_log[4];
	} else {
		$Slog[4]=get_time(0,1);
	}
	##パスワード(5)
	if($Smode_name == "mente"){$Slog[5]=$Spre_log[5];} #内容変更時
	else{ #新規登録時
		$Slog[5]=crypt($_POST['Fpass'], "ys");
	}
	##紹介文(6)
	//改行コード統一 by nao-pon
	$_POST['Fsyoukai'] = preg_replace("/\x0D\x0A|\x0D|\x0A/","\n",$_POST['Fsyoukai']);
	$Slog[6]=$_POST['Fsyoukai'];
	##管理人コメント(7)
	if($_POST['changer'] == "admin"){ #変更者が管理人
	//改行コード統一 by nao-pon
		$_POST['Fkanricom'] = preg_replace("/\x0D\x0A|\x0D|\x0A/","\n",$_POST['Fkanricom']);
		$Slog[7]=$_POST['Fkanricom'];
	}
	elseif(!$Smode_name){$Slog[7]="";} #登録者の新規登録
	else{$Slog[7]=$Spre_log[7];} #登録者の変更
	##お名前(8)
	$Slog[8]=$_POST['Fname'];
	##E-mail(9)
	$Slog[9]=$_POST['Femail'];
	##カテゴリ(10)
	if($EST[user_change_kt] && $_POST['mode'] == "act_mente" && $_POST['changer'] != "admin"){ #登録者の変更でカテゴリ変更禁止の場合
		$i=0;
		$kt=explode("&",$Spre_log[10]);
		$Slog[10]=$Spre_log[10];
		foreach ($kt as $tmp){
			$_POST["Fkt$i"]=$tmp;
			$i++;
		}
	}
	else{ #その他の場合
		$Slog[10]="&";
		for($i = 1; $i <= $EST_reg['kt_max']; $i++){
			$Slog[10] .= $_POST["Fkt$i"] . "&";
		}
	}
	##time形式(11)新規or更新(13)
	if ($_POST['Fhold_timestamp']=="on" || $Spre_log[11] + (86400 * $EST['new_time']) > time()){
		$Slog[11]=$Spre_log[11];
		$Slog[13]=$Spre_log[13];
	} else {
		$Slog[11] = time();
		if($Smode_name == "mente"){ #内容変更時
			$Slog[13]= "1";
		} else {
			$Slog[13]= "0"; #新規登録時
		}
	}
	##バナーURL(12)
	$Slog[12]=$_POST['Fbana_url'];
	##最終アクセスIP(14)
	if($Smode_name == "mente"){$Slog[14]=$Spre_log[14];} #内容変更時
	else{$Slog[14]="";} #新規登録時
	##キーワード(15)
	$Slog[15]=$_POST['Fkey'];
	##仮登録モードの場合の設定
	if($EST['user_check'] && $_POST['changer'] != "admin" && $_POST['mode'] == "act_regist"){
		$Slog[14]=implode("<1>",array($_POST['Fsougo'],$_POST['Fadd_kt'],$_POST['Fto_admin']));
	}
	##登録日時(16) by nao-pon
	if($Smode_name == "mente"){$Slog[16] = $Spre_log[16];} #内容変更時
	else{$Slog[16] = $times;} #新規登録時
	##XOOPSユーザID
	$Slog[17]=$_POST['Fuid'];
	
	ksort($Slog);
	return($Slog);
}

#(f7)新規登録用のIDを取得&２重URL登録チェック(get_id_url_ch)
#チェックに掛かった場合にはロックも解除
#$arg=>(新規登録=1/内容変更=2)
function get_id_url_ch($fl){
	global $EST,$xoopsDB;
	$i=0;
	if (!empty($_POST['Furl']))
	{
		//登録済みデータのチェック
		$Tlog=array();
		$query = "SELECT * FROM {$EST['sqltb']}log WHERE url='{$_POST['Furl']}' LIMIT 1";
		$result = $xoopsDB->query($query) or die("Query failed");
		$Tlog = mysql_fetch_row($result);
		if($Tlog)
		{
			$Tlog = array_map("addslashes", $Tlog);
			if($_POST['Furl'] == $Tlog[2]){$i++; $pre_title=$Tlog[1];}
			if($_POST['id'] == $Tlog[0]){$Spre_log=$Tlog;}
			if($fl<=$i){mes("そのURLはすでに登録されています<br><br>$Tlog[1] :<br>$Tlog[2]","２重登録エラー","java");}
			if($fl == "2" && $i == "1" && $Spre_log[2] != $_POST['Furl']){mes("そのURLはすでに登録されています<br><br>$pre_title :<br>$_POST[Furl]","２重登録エラー","java");}
		}
	}
	
	$query = "SELECT id FROM {$EST['sqltb']}log ORDER BY id DESC LIMIT 1";
	$result = $xoopsDB->query($query) or die("Query failed");
	$num=mysql_fetch_row($result);
	$id=++$num[0];
	
	//仮登録データのチェック
	$fp = fopen("{$EST['log_path']}{$EST['temp_logfile']}", "r");
	while($line=fgets($fp, 4096))
	{
		$Tlog=explode("<>",$line);
		if ($id <= $Tlog[0]) $id = ++$Tlog[0];
		if($EST['user_check'] && $_POST['mode'] == "act_regist")
		{
			#仮登録モードでユーザの新規登録時
			if($_POST['Furl'] == $Tlog[2]){$i++;}
			if(!empty($_POST['Furl']) && $fl<=$i)
			{
				fclose($fp);
				mes("そのURLは現在登録申請中です<br><br>$Tlog[1] :<br>$Tlog[2]","２重登録エラー","java");
			}
		}
	}
	fclose($fp);
	
	return $id;
}

#(f8)登録結果画面出力(PRend)
function PRend(){
	global $EST;
	require "$EST[temp_path]regist_new_end.html";
}

#(f9)マークForm管理者画面出力(PR_mark)
function PR_mark(){
	global $Pdata, $EST;
	if($_POST['changer'] == "admin"){
		$mark=explode("_",$Pdata[3]);
		?>
	<li>【マーク】
		<ul>
<?php
		for($i=1; $i<=2; $i++){ #←マーク数を増やすときは修正
			echo "<input type=checkbox name=Fmark$i value=1";
			if($mark[$i-1]){echo " checked";}
			echo ">" . $EST["name_m$i"] . "　 ";
		}
		?>
		</ul><br>
<?php
	}
}

?>