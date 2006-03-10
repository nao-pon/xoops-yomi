<?php
// Check config files.

if (!is_readable('pl/cfg.php'))
{
	exit ("Please file copy 'yomi/pl/cfg.php.dev' as 'yomi/pl/cfg.php'");
}
if (!is_readable('pl/other_cfg.php'))
{
	exit ("Please file copy 'yomi/pl/other_cfg.php.dev' as 'yomi/pl/other_cfg.php'");
}
if (!is_readable('pl/search.dat'))
{
	exit ("Please file copy 'yomi/pl/search.dat.dev' as 'yomi/pl/search.dat'");
}

if(ini_get("magic_quotes_gpc"))
{
	$_GET = yomi_stripslashes($_GET);
	$_POST = yomi_stripslashes($_POST);
}
$_POST = array_map("yomi_cr_replace",$_POST);


// For XOOPS
include("admin_header.php");
include_once("./include/hyp_tickets.php");
if( strtolower($_SERVER['REQUEST_METHOD']) == "post" )
{
	$xoopsHypTicket->check_ip = 1;
	$xoopsHypTicket->timeout  = 600;
	if ( ! $xoopsHypTicket->check() )
	{
		redirect_header(XOOPS_URL.'/',1,$xoopsHypTicket->getErrors());
	}
}
define('YOMI_TICKET_TAG',$xoopsHypTicket->getTicketHtml( __LINE__ ));

//if (!xoops_refcheck()) redirect_header(XOOPS_URL."/",1,"Access Denied.");

if(($_POST['mode'] == "dl_check_dl") || ($_POST['mode'] == "log_conv_act")){
	
} else{
	xoops_cp_header();
	OpenTable();
	$mymenu_fake_uri = XOOPS_URL."/modules/yomi/admin/admin.php?mode=kanri";
	$_cdir = getcwd();
	chdir("./admin/");
	if( file_exists( './mymenu.php' ) ) include( './mymenu.php' ) ;
	chdir($_cdir);
	echo "<table class=\"yomi-body\"><tr><td>";
}

global $xoopsUser;

$is_admin = 0;
$x_uid = 0;
if ( $xoopsUser ) {
	$xoopsModule = XoopsModule::getByDirname("yomi");
	if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) { 
		$is_admin = 1;
	}
	$x_uid = $xoopsUser->uid();

}

include('init.php');

//modeが指定されていない場合
if (empty($_POST['mode'])) $_POST['mode'] = $_GET['mode'];
if (empty($_POST['mode']))
	$_GET['mode'] = "kanri";

## 目次 ##
#(1)ログイン画面(&login)
#(2)管理人室(&kanri)
#(3)HTMLログファイル更新処理(&mente_html)
#(3.1)HTMLログファイル更新処理実行(&mente_html_act)
#(4)CGIログファイル更新処理(&mente_cgi)
#(4.1)CGIログファイル更新処理実行(&mente_cgi_act)
#(5)登録待ち表示画面(&temp_to_regist)
#(5.1)登録待ちの処理決定実行(&temp_to_regist_act)
#(6)キーワードランキングの設定(&key_cfg)
#(6.1)キーワードランキングの設定実行(&key_cfg_act)
#(6.2)キーワードランキングの集計対象外のキーワードを一括登録実行(&key_cfg_del_word_act)
#(7)各種ログ変換(&log_conv)
#(7.1)各種ログ変換実行(&log_conv_act)
#(7.2)Ver3からカテゴリ設定インポート実行(&log_conv_kt_act)
#(7.3)カテゴリ・ソート変換実行(&log_conv_kt_sort)
#(8)ログデータの交換・移動・削除(&log_kt_change)
#(8.1)ログデータの交換・移動・削除実行(&log_kt_change_act)
#(9)ログ(登録データ)の修復(&log_repair)
#(9.1)ログ(登録データ)の修復実行(&log_repair_act)
#(10)ログ診断 (&log_mente)
#(10.1)ログ診断実行 (&log_mente_act)
#(11)環境設定 (&config)
#(12)カテゴリ設定 (&config_kt)
#(13)人気ランキングの設定(&rank_cfg)
#(13.1)人気ランキングの初期化実行(&rank_cfg_act)
#(14)デッドリンクチェック画面(&dl_check)
#(14.1)デッドリンクチェック用ファイルをダウンロード(&dl_check_dl)
#(14.2)デッドリンクチェック実行画面(&dl_check_act)
#(15)異常ロック解除ファイル操作(&ill_lock_del)
#(16)簡易デザイン設定(&design)
#(17)テンプレートファイルの修正(&template_cfg)
#(18)バージョン情報(&ver_info)
#(19)登録者のメッセージを見る(&look_mes)

#(cfg1)環境設定(%EST)を更新(&cfg_make)
#(cfg2)環境設定(&search_form/&menu_bar)を更新(&cfg_make_PR_menu)
#(cfg3)環境設定(登録処理関係)を更新 (&cfg_make_reg)
#(cfg4)カテゴリ説明文を更新 (&cfg_make_kt_ex)
#(cfg5)カテゴリ設定を更新 (&cfg_make_kt)
#(cron1)cronコマンドによる定期処理(&cron)
#(cron1.1)通常カテゴリと特殊カテゴリを更新(&cron_make_kt)

## 個別処理 ##
#(t1)フォーム入力データを書き込みデータに反映(仮登録→正規登録用)
#   (&form_to_temp)

if($_GET['mode'] == "kanri") $_POST['mode'] = "kanri";
if($_POST['mode'] == "kanri"){
	#(2)管理人室(&kanri)
	#パスワードチェック
	if($EST['pass'] != "setup" && $is_admin == 0){
		pass_check();
	}
	#クッキーの設定
	$CK_data = get_cookie();
	if(isset($_POST['set'])){
		if($_POST['set'] == "設定"){$CK_data[4]=1;}
		else{$CK_data[4]=0;}
		set_cookie($CK_data);
	}
	if($CK_data[4]){$PRset="設定";}
	else{$PRset="解除";}
	require "$EST[temp_path]admin/admin.html";
	exit;
}
#elseif($_POST['mode'] == "mente_html"){mente_html;}
#elseif($_POST['mode'] == "mente_html_act"){mente_html_act;}
#elseif($_POST['mode'] == "mente_cgi"){mente_cgi;}
#elseif($_POST['mode'] == "mente_cgi_act"){mente_cgi_act;}
elseif($_POST['mode'] == "temp_to_regist"){
	#(5)登録待ち表示画面(&temp_to_regist)
	if ($is_admin != 1) pass_check();
	$Ctemp=0;
	EST_reg(); #登録関連の設定をロード
	$fp = fopen("$EST[log_path]$EST[temp_logfile]", "r");
	while($tmp = fgets($fp, 4096)){
		$Ctemp++;
	}
	fclose($fp);
	require "$EST[temp_path]admin/temp_to_regist.html";
	exit;
}
elseif($_POST['mode'] == "temp_to_regist_act"){
	#(5.1)登録待ちの処理決定実行(&temp_to_regist_act)
	if ($is_admin != 1) pass_check();
	EST_reg(); #登録用の設定をロード
	$temp_lines=array(); #仮登録ログへの書き込み用ログリスト
	#@Slog, #登録データの一時保存用
	$Clog_id=1; #新規登録用の登録ID
	$temp_id; #仮登録時のID
	#メール送信用ライブラリを読み込み
	if($EST[mail_new]){require "pl/mail_ys.php";}
	$fp = fopen("$EST[log_path]$EST[temp_logfile]", "r");
	$_POST=array_map("stripslashes", $_POST);
	$_POST=array_map("yomi_unhtmlspecialchars", $_POST);
	$_POST=array_map("htmlspecialchars", $_POST);
	while($tmp=fgets($fp, 4096)){
		$Tlog=explode("<>",$tmp);
		if($_POST["R$Tlog[0]"] == "reg"){ #登録
			$temp_id=$Tlog[0];
			#登録ID
			$Slog[0] = $Tlog[0];
			#(t1)フォーム入力データを書き込みデータに反映(仮登録→正規登録用)
			#   (&form_to_temp)
			#タイトル(1)
			$Slog[1]=$_POST["Ftitle$temp_id"];
			#URL(2)
			$Slog[2]=$_POST["Furl$temp_id"];
			#マークデータ(3)
			$Slog[3]="";
			for($i=1; $i <= 2; $i++){ #←マーク数を増やす場合には修正
				if(!$_POST["Fmark${i}_$temp_id"]){$_POST["Fmark${i}_$temp_id"]=0;}
				$Slog[3] .= $_POST["Fmark${i}_$temp_id"] . "_";
			}
			$Slog[3]=substr($Slog[3],0,-1);
			#更新日(4)
			$Slog[4]=get_time(0,1);
			#パスワード(5)
			#(未変更)
			$Slog[5]=$Tlog[5];
			#紹介文(6)
			$Slog[6]=$_POST["Fsyoukai$temp_id"];
			$Slog[6]=str_replace("\r", "", $Slog[6]);
			$Slog[6]=str_replace("\n", "<br>", $Slog[6]);
			#管理人コメント(7)
			$Slog[7]=$_POST["Fkanricom$temp_id"];
			$Slog[7]=str_replace("\r", "", $Slog[7]);
			$Slog[7]=str_replace("\n", "<br>", $Slog[7]);
			#お名前(8)
			$Slog[8]=$_POST["Fname$temp_id"];
			#E-Mail(9)
			$Slog[9]=$_POST["Femail$temp_id"];
			#カテゴリ(10)
			$Slog[10]="&";
			for($i=1; $i <= $EST_reg[kt_max]; $i++){
				$Slog[10] .= $_POST["F${temp_id}kt$i"] . "&";
			}
			#time形式(11)
			$Slog[11]= time();
			$Slog[13]= 0;
			#バナーURL(12)
			$Slog[12]=$_POST["Fbana_url$temp_id"];
			#キーワード(15)
			$Slog[15]=$_POST["Fkey$temp_id"];
			#登録日時(16) by nao-pon
			$Slog[16] = time();
			$Slog[17]=$_POST["Fuid$temp_id"];
			
			$Slog = array_map("addslashes", $Slog);
			$query = "INSERT INTO $EST[sqltb]log VALUES('$Slog[0]','$Slog[1]','$Slog[2]','$Slog[3]','$Slog[4]','$Slog[5]','$Slog[6]','$Slog[7]','$Slog[8]','$Slog[9]','$Slog[10]','$Slog[11]','$Slog[12]','$Slog[13]','$Slog[14]','$Slog[15]','$Slog[16]','$Slog[17]',0,0,0)";
			$result = $xoopsDB->queryF($query) or die("Query failed admin146 $query");
			if($EST[mail_new]){
				#仮登録→新規登録時のメールを送信
				$Slog[6]=str_replace("<br>", "\n", $Slog[6]);
				$Slog[7]=str_replace("<br>", "\n", $Slog[7]);
				if($EST[mail_to_admin]){ #管理人へメール送信
					sendmail($EST[admin_email],$Slog[9],"$EST[search_name] 仮登録→登録完了","new","admin",$Slog,$_POST["Fsougo$Tlog[0]"],$_POST["Fadd_kt$Tlog[0]"],$_POST["Fto_admin{$Tlog[0]}"],$_POST["Fto_reg{$Tlog[0]}"]);
				}
				if($EST[mail_to_register]){ #登録者へメール送信
					sendmail($Slog[9],$EST[admin_email],"$EST[search_name] 新規登録完了通知","new","",$Slog,$_POST["Fsougo$Tlog[0]"],$_POST["Fadd_kt$Tlog[0]"],$_POST["Fto_admin{$Tlog[0]}"],$_POST["Fto_reg{$Tlog[0]}"]);
				}
			}
		}
		elseif(!$_POST["R$Tlog[0]"]){ #保留
			array_push($temp_lines,$tmp);
		}
	}
	fclose($fp);
	#仮登録データを更新
	$fp = fopen("$EST[log_path]$EST[temp_logfile]", "w");
	foreach($temp_lines as $tmp) {
		fputs($fp, $tmp);
	}
	fclose($fp);
	mes("仮登録データの処理が完了しました","仮登録データ処理完了","kanri");
	exit;
}
elseif($_POST['mode'] == "key_cfg"){
	#(6)キーワードランキングの設定(&key_cfg)
	if ($is_admin != 1) pass_check();
	#if(-s "$EST{log_path}keyrank_temp_ys.cgi"){&keyrank_trace;} #一時ファイル→集計ファイル
	require "$EST[log_path]keyrank_ys.php";
	require "$EST[temp_path]admin/key_cfg.html";
	exit;
}
elseif($_POST['mode'] == "key_cfg_act"){
	#(6.1)キーワードランキングの設定実行(&key_cfg_act)
	if ($is_admin != 1) pass_check();
	require "$EST[log_path]keyrank_ys.php";
	$fp=fopen("$EST[log_path]keyrank_ys.php", "w");
	if($_POST[open]) {
		foreach($_POST[open] as $key){
			if($_POST["del_oo_$key"] != "on"){$open_key[$key]=$key;} #open
		}
	}
	if($_POST[bad]) {
		foreach($_POST[bad] as $key){
			$bad_key[$key]=1; #bad
		}
	}
	if($_POST[delete]) {
		foreach($_POST[delete] as $key){
			$query="DELETE FROM $EST[sqltb]key WHERE word='$key'";
			$result=$xoopsDB->queryF($query);
		}
	}
	while(list($key,)=each($bad_key)){
		if($_POST["del_bb_$key"] == "on"){unset($bad_key[$key]);}
	}
	while(list($key,)=each($open_key)){
		if($_POST["del_oo_$key"] == "on"){unset($open_key[$key]);}
	}
	fputs($fp, "<?php\n\$bad_key=array(\n");
	foreach($bad_key as $key=>$val){
		if(!$check[$key]){fputs($fp, "'$key'=>'1',\n"); $check[$key]=1;}
	}
	fputs($fp, ");\n\$open_key=array(\n");
	foreach($open_key as $key=>$val){
		if(!$check[$key]){
			if(!$_POST["hm_$key"] && $val != "1"){fputs($fp, "'$key'=>'1',\n");}
			elseif(!$_POST["hm_$key"]){fputs($fp, "'$key'=>'$val',\n");}
			else{fputs($fp, "'key'=>'".$_POST["hm_$key"]."',\n");}
		$check[$key]=1;
		}
	}
	fputs($fp, ");\n?>");
	mes("キーワード表示設定の変更が完了しました","キーワード表示設定の変更完了","kanri");
	exit;
}
elseif($_POST['mode'] == "key_cfg_del_word_act"){
	#(6.2)キーワードランキングの集計対象外のキーワードを一括登録実行(&key_cfg_del_word_act)
	if ($is_admin != 1) pass_check();
	require "$EST[log_path]keyrank_ys.php";
	$fp=fopen("$EST[log_path]keyrank_ys.php", "w");
	fputs($fp, "<?php\n\$keyrank=array(\n");
	while(list($key,$value)=each($keyrank)){
		fputs($fp, "'$key'=>'$value',\n");
	}
	fputs($fp, ");\n\$bad_key=array(\n");
	while(list($key,$value)=each($bad_key)){
		fputs($fp, "'$key'=>'$value',\n");
	}
	$del_key_list=explode(",",$_POST[del_key_list]);
	foreach($del_key_list as $tmp){
		$tmp=str_replace("\n", "", $tmp);
		fputs($fp, "'$tmp'=>'1',\n");
	}
	fputs($fp, ");\n\$open_key=array(\n");
	while(list($key,$value)=each($open_key)){
		fputs($fp, "'$key'=>'$value',\n");
	}
	fputs($fp, ");\n?>");
	mes("集計対象外のキーワードの一括登録が完了しました","登録完了","kanri");
	exit;
}
elseif($_POST['mode'] == "log_conv"){
	#(7)各種ログ変換(&log_conv)
	if ($is_admin != 1) pass_check();
	require "$EST[temp_path]admin/log_conv.html";
	exit;
}
elseif($_POST['mode'] == "log_conv_act"){
	#(7.1)各種ログ変換実行(&log_conv_act)
	if ($is_admin != 1) pass_check();
	if($_POST[check] != "on"){mes_frame("確認チェックがされていません。<br>チェックしてから実行してください。","チェックエラー");}
	if(!is_file($_POST[bf_file])){mes_frame("エラー：$bf_file が見つかりません","ファイルが見つかりません","java");}
	else{
		if($_POST[log_mode] == "v4todb"){
			// 実行時間を制限しない
			set_time_limit(0);
			// 出力をバッファリングしない
			ob_end_clean();
			echo str_pad('',256);//for IE
			echo "<html><body style=\"font-size:12px;\">";
			echo "<b>処理状況</b>(■=100件)<hr>";
			flush();
			$fp=fopen($_POST[bf_file], "r");
			$counter = 0;
			while($line=fgets($fp, 4096)) {
				$line=mb_convert_encoding($line, "EUC-JP", "SJIS");
				$line = addslashes($line);
				$Slog=explode("<>", $line);
				$Slog[10]="&".$Slog[10]."&";
				$Slog[13]=substr($Slog[11], -1);
				$Slog[11]=substr($Slog[11], 0, -2);
				$query = "INSERT INTO $EST[sqltb]log VALUES ('$Slog[0]', '$Slog[1]', '$Slog[2]', '$Slog[3]', '$Slog[4]', '$Slog[5]', '$Slog[6]', '$Slog[7]', '$Slog[8]', '$Slog[9]', '$Slog[10]', '$Slog[11]', '$Slog[12]', '$Slog[13]', '$Slog[14]', '$Slog[15]','$Slog[11]',0,0,0,0)";
				$result=$xoopsDB->queryF($query);
				$counter++;
				if (($counter/100) == (floor($counter/100))){
					echo "■";
					flush();
				}
				if (($counter/1000) == (floor($counter/1000))) echo "(".$counter."件完了！)<br />";
			}
			fclose($fp);
			$PR_msg="Ver4形式→データベースへの変換が完了しました。";
			echo $PR_msg;
			echo "</body></html>";
		}
	}
	exit;
}
#elseif($_POST[mode] == "log_conv_kt_act"){log_conv_kt_act;}
elseif($_POST['mode'] == "log_conv_kt_sort"){
	#(7.3)カテゴリ・ソート変換実行(&log_conv_kt_sort)
	if ($is_admin != 1) pass_check();
	if($_POST[check] != "on"){mes("確認チェックにチェックしてから変換ボタンを押してください","チェックミス","java");}
	#$_POST[all]の処理
	if($_POST[all] == "on"){
		while(list($key,$value)=each($ganes)){
			if(!strstr($key,"_")){$_POST[$key]="on";}
		}
	}
	#$_POST[kt_str]を解析
	if($_POST[kt_str]){
		if(preg_match("/[^\w\-\,]/", $_POST[kt_str])){mes("カテゴリ指定文に全角文字が含まれています","エラー","java");}
		$kt_str=explode(",", $_POST[kt_str]);
		$del_list=array();
		foreach($kt_str as $tmp){
			if(preg_match("/^(\d+)(n*)\-(\d+)(n*)$/", $tmp, $match)){
				$kt1=$match[1];$kt2=$match[3];$n_fl=0;
				if($match[4] == "n"){$n_fl=1;}
				$keta1=strlen($kt1);
				$keta2=strlen($kt2);
				if($keta1 != $keta2){mes("カテゴリ指定文が間違っています：<b>$tmp</b>","エラー","java");}
				$i=1;
				while($kt1 != $kt2){
					$kt1_j=sprintf("%d",$kt1);
						if(!$n_fl){$_POST[$kt1]="on";}
						else{array_push($del_list,$kt1);}
					$kt1_j++;
					$kt1=sprintf("%0${keta1}d",$kt1_j);
					$i++;
					if($i>5000){mes("<b>-</b> で 5000以上の連続するカテゴリを指定することはできません","エラー","java");}
				}
				if(!$n_fl){$_POST[$kt2]="on";}
				else{array_push($del_list,$kt2);}
			}
			elseif(preg_match("/(\d+)(n*)/", $tmp, $match)){
				if($match[2] == "n"){array_push($del_list,$match[1]);}
				else{$_POST[$match[1]]="on";}
			}
			else{mes("カテゴリ指定文が間違っています","エラー","java");}
		}
		foreach($del_list as $tmp){
			$_POST[$tmp]="";
		}
	}
	#カテゴリリストから変換対象のカテゴリ用の%kt_af,%kt_name_afを作成
	$ch_cnt=array();$ch_line=array();
	reset($ganes);
	while (list($key,)=each($ganes)){
		$oya_kt=preg_replace("/_\d+$/", "", $key);
		if(!isset($ch_cnt[$oya_kt])) {$ch_cnt[$oya_kt]=0;}
		if(strstr($key,"_")){
			$ch_cnt[$oya_kt]++;	#[01] => 6 [01_01] => 2 ...
			$ch_line[$oya_kt].=$key . ",";#[01] => 01_01,01_03,01_04,01_05,01_06,01_07,
		}
	}
	require "pl/other_cfg.php";
	while (list($key,)=each($ch_cnt)){
		list($top_kt,)=explode("_", $key);
		unset($furi);
		if($ch_cnt[$key]>1 && $_POST[$top_kt] == "on"){
			$kt=explode(",",$ch_line[$key]);
			foreach($kt as $tmp) {
				if($tmp) {$furi[$tmp]=$EST_furi[$tmp];}
			}
			asort($furi, SORT_STRING);
			$i=0;
			foreach ($furi as $cate=>$val) {
				$kt_af[$cate]=$kt[$i];
				$kt_name_af[$kt[$i]]=$ganes[$cate];
				$i++;
			}
		}
	}
	#親ktが変化するカテゴリを再定義
	foreach ($ch_line as $key=>$val){
		if($kt_af[$key]){
			$kt=explode(",",$val);
			foreach ($kt as $tmp){
				if($tmp) {
					if($kt_af[$tmp]){$new_kt=$kt_af[$tmp];}
					else {$new_kt=$tmp;}
					$new_kt=preg_replace("/^(.+)_(\d+)$/", "$kt_af[$key]_$2", $new_kt);
					$kt_af[$tmp]=$new_kt;
					$kt_name_af[$new_kt]=$ganes[$tmp];
				}
			}
		}
	}
	#%ganesを変換し、書き換え
	#副属性をロード
	EST_reg(); gane_st(); gane_guide();
	$gane_top_bk=$gane_top;
	$gane_st_bk=$ganes_st; $gane_ref_bk=$gane_ref;
	$gane_UR_bk=$gane_UR; $KTEX_bk=$KTEX;
	foreach($ganes as $key=>$value) {
		if($kt_af[$key]){
			unset ($ganes[$key]);
			#副属性 %gane_top/%gane_st/%gane_ref/%gane_UR/%KTEX/ 
			unset ($gane_top[$key]);
			unset ($gane_st[$key]);
			unset ($gane_ref[$key]);
			unset ($gane_UR[$key]);
			unset ($KTEX[$key]);
		}
	}
	foreach($kt_af as $key=>$value) {
		$ganes[$value]=$kt_name_af[$value];
		#副属性
		$gane_top[$value]=$gane_top_bk[$key];
		$gane_st[$value]=$gane_st_bk[$key];
		$gane_ref[$value]=$gane_ref_bk[$key];
		$gane_UR[$value]=$gane_UR_bk[$key];
		$KTEX[$value]=$KTEX_bk[$key];
	}
	ksort($ganes, SORT_STRING);
	ksort($gane_top, SORT_STRING);
	ksort($gane_st, SORT_STRING);
	ksort($gane_ref, SORT_STRING);
	ksort($gane_UR, SORT_STRING);
	ksort($KTEX, SORT_STRING);
	#@gane_other/
	$i=0;
	foreach($gane_other as $tmp){
		if($af_kt[$tmp]){$gane_other[$i]=$af_kt[$tmp];}
		$i++;
	}
	cfg_set(1,1,1,1);
	#その他の環境設定ファイル(other_cfg.php)を変換し、書き換え
	$EST_furi_bk=$EST_furi;
	foreach($EST_furi as $key=>$value) {
		if($kt_af[$key]){ unset($EST_furi[$key]);}
	}
	foreach($kt_af as $key=>$value) {
		$EST_furi[$kt_af[$key]]=$EST_furi_bk[$key];
	}
	unset($EST_furi_bk);
	ksort($EST_furi, SORT_STRING);
	$fp=fopen("pl/other_cfg.php", "w");
	fputs($fp, "<?php\n\$EST_furi=array(\n");
	foreach ($EST_furi as $key=>$val){
		fputs($fp, "'$key'=>'$val',\n");
	}
	fputs($fp, ");\n?>\n");
	fclose($fp);
	#本体ログを更新
	$query="SELECT id,category FROM $EST[sqltb]log";
	$result=$xoopsDB->query($query) or die("Query failed admin440 $query");
	while($Slog = mysql_fetch_row($result)){
		$kt_fl=0; $kt_line="&";
		$kt=explode("&",$Slog[1]);
		foreach ($kt as $tmp){
			if($kt_af[$tmp]){ $kt_fl=1; $kt_line .= $kt_af[$tmp] . "&";}
			elseif($tmp){ $kt_line .= $tmp . "&";}
		}
		if($kt_fl){
			$query="UPDATE $EST[sqltb]log SET category='$kt_line' WHERE id='$Slog[0]'";
			$result2=$xoopsDB->queryF($query) or die("Query failed admin449 $query");
		}
	}
	mes("カテゴリ・ソート変換が完了しました","カテゴリ・ソート変換完了","kanri");
}
elseif($_POST['mode'] == "log_kt_change"){
	#(8)ログデータの交換・移動・削除(&log_kt_change)
	if ($is_admin != 1) pass_check();
	require "$EST[temp_path]admin/log_kt_change.html";
	exit;
}
elseif($_POST['mode'] == "log_kt_change_act"){
	#(8.1)ログデータの交換・移動・削除実行(&log_kt_change_act)
	if ($is_admin != 1) pass_check();
	if($_POST[check] != "on"){mes("確認チェックがされていません。<br>戻ってチェックしてから実行してください。","チェックエラー","java");}
	#記入漏れのチェック
	if($_POST[log_mode] == "change"){
		if(!$_POST[change_kt1] || !$_POST[change_kt2]){mes("交換対象のカテゴリを指定してください","カテゴリ選択ミス","java");}
	}
	elseif($_POST[log_mode] == "move"){
		if(!$_POST[bf_move_kt] || !$_POST[af_move_kt]){mes("移動対象のカテゴリを指定してください","カテゴリ選択ミス","java");}
	}
	elseif($_POST[log_mode] == "del"){
		if(!$_POST[del_kt]){mes("削除対象のカテゴリを選択してください","カテゴリ選択ミス","java");}
	}
	else{mes("log_modeが選択されていません","log_mode選択エラー","java");}
	if($_POST[log_mode] == "change"){ #change
		#ログデータの交換
		$PR_mes="ログデータの交換が完了しました<br>『" . full_kt($_POST[change_kt1]) . "}』と『" . full_kt($_POST[change_kt2]) . "』を交換しました";
		$change_kt1=$_POST[change_kt1];
		$change_kt2=$_POST[change_kt2];
		$kousin_kt=array("&$change_kt1&"=>"&$change_kt2&", "&$change_kt2&"=>"&$change_kt1&");
		$query="SELECT id,category FROM $EST[sqltb]log WHERE (category LIKE '%&$change_kt1&%') OR (category LIKE '%&$change_kt2&%')";
		$result=$xoopsDB->query($query) or die("Query failed admin216 $query");
		while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
			$line[category]=strtr($line[category], $kousin_kt);
			$query="UPDATE $EST[sqltb]log SET category='$line[category]' WHERE id='$line[id]'";
			$result2=$xoopsDB->queryF($query) or die("Query failed admin220 $query");
		}
	}
	elseif($_POST[log_mode] == "move"){ #move
		#ログデータの移動
		$PR_mes="ログデータの移動が完了しました<br>『" . full_kt($_POST[bf_move_kt]) . "』を『" . full_kt($_POST[af_move_kt]) . "』に移動しました";
		$bf_move_kt=$_POST[bf_move_kt];
		$af_move_kt=$_POST[af_move_kt];
		$query="SELECT id,category FROM $EST[sqltb]log WHERE category LIKE '%&$bf_move_kt&%'";
		$result=$xoopsDB->query($query) or die("Query failed admin232 $query");
		while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
			$line[category]=str_replace("&$bf_move_kt&", "&$af_move_kt&", $line[category]);
			$query="UPDATE $EST[sqltb]log SET category='$line[category]' WHERE id='$line[id]'";
			$result2=$xoopsDB->queryF($query) or die("Query failed admin236 $query");
		}
	}
	else{ #del
		#ログデータの削除
		$PR_mes="ログデータの削除が完了しました<br>『" . full_kt($_POST[del_kt]) . "』を削除しました";
		$del_kt=$_POST[del_kt];
		$query="SELECT id,category FROM $EST[sqltb]log WHERE category LIKE '%&$del_kt&%'";
		$result=$xoopsDB->query($query) or die("Query failed admin232 $query");
		while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
			$line[category]=str_replace("&$del_kt&", "&", $line[category]);
			if(preg_match("/\d+/", $line[category])) {
				$query="UPDATE $EST[sqltb]log SET category='$line[category]' WHERE id='$line[id]'";
			}
			else {
				$query="DELETE FROM $EST[sqltb]log WHERE id='$line[id]' LIMIT 1";
			}
			$result2=$xoopsDB->queryF($query) or die("Query failed admin256 $query");
		}
	}
	mes($PR_mes,"ログデータの交換・移動・削除完了","kanri");
}
elseif($_POST['mode'] == "log_repair"){
	#(9)ログ(登録データ)の修復(&log_repair)
	require "$EST[temp_path]admin/log_repair.html";
	exit;
}

elseif($_POST['mode'] == "log_repair_act"){
	#(9.1)ログ(登録データ)の修復実行(&log_repair_act)
	if($_POST[act] == "dump") {
		if($_POST[dump] != "on"){mes("修復確認のため、確認チェックを入れてからもう一度実行してください","確認チェックをしてください","java");}
		$query="SELECT * FROM $EST[sqltb]log ORDER BY id";
		$result=$xoopsDB->query($query) or die("Query failed admin378 $query");
		$fp=fopen($_POST[file], "w");
		while($line=mysql_fetch_row($result)) {
			fputs($fp, implode("\t", $line)."\n");
		}
		fclose($fp);
		mes("データのバックアップが完了しました","バックアップ完了","kanri");
	}
	elseif($_POST[act] == "restore") {
		if($_POST[restore] != "on"){mes("修復確認のため、確認チェックを入れてからもう一度実行してください","確認チェックをしてください","java");}
		$query="DROP TABLE $EST[sqltb]log";
		$result=$xoopsDB->queryF($query) or die("Query failed admin392 $query");
		$query="CREATE TABLE $EST[sqltb]log (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `title` varchar(255) default NULL,
		  `url` varchar(255) default NULL,
		  `mark` char(3) default NULL,
		  `last_time` varchar(21) default NULL,
		  `passwd` varchar(255) default NULL,
		  `message` text,
		  `comment` text,
		  `name` varchar(255) default NULL,
		  `mail` varchar(255) default NULL,
		  `category` varchar(255) default NULL,
		  `stamp` int(10) unsigned default NULL,
		  `banner` varchar(255) default NULL,
		  `renew` tinyint(3) unsigned default NULL,
		  `ip` varchar(15) default NULL,
		  `keywd` varchar(255) default NULL,
		  `build_time` int(10) unsigned default NULL,
		  `uid` int(5) unsigned NOT NULL default '0',
		  `rating` double(6,4) NOT NULL default '0.0000',
		  `votes` int(11) unsigned NOT NULL default '0',
		  `comments` int(11) unsigned NOT NULL default '0',
		  PRIMARY KEY  (`id`),
		  KEY `uid` (`uid`)
		)";
		$result=$xoopsDB->queryF($query) or die("Query failed admin411 $query");
		$fp=fopen($_POST[file], "r");
		while($line=fgets($fp, 4096)) {
			$Slog=explode("\t", $line);
			$query = "INSERT INTO $EST[sqltb]log VALUES ('$Slog[0]', '$Slog[1]', '$Slog[2]', '$Slog[3]', '$Slog[4]', '$Slog[5]', '$Slog[6]', '$Slog[7]', '$Slog[8]', '$Slog[9]', '$Slog[10]', '$Slog[11]', '$Slog[12]', '$Slog[13]', '$Slog[14]', '$Slog[15]','$Slog[16]','$Slog[17]','$Slog[18]','$Slog[19]','$Slog[20]')";
			$result=$xoopsDB->queryF($query);
		}
		fclose($fp);
		mes("データの復元が完了しました","復元完了","kanri");
	}
	exit;
}
elseif($_POST['mode'] == "log_mente"){log_mente;}
elseif($_POST['mode'] == "log_mente_act"){log_mente_act;}
elseif($_POST['mode'] == "config"){
	#(11)環境設定 (&config)
	require "$EST[temp_path]admin/config.html";
	exit;
}
elseif($_POST['mode'] == "config_kt"){
	#(12)カテゴリ設定 (&config_kt)
	require "$EST[temp_path]admin/config_kt.html";
	exit;
}
elseif($_POST['mode'] == "rank_cfg"){
	#(13)人気ランキングの設定(&rank_cfg)
	if ($is_admin != 1) pass_check();
	require "$EST[temp_path]admin/rank_cfg.html";
	exit;
}
#elseif($_POST['mode'] == "rank_cfg_act"){rank_cfg_act;}
elseif($_POST['mode'] == "dl_check"){
	#(14)デッドリンクチェック画面(&dl_check)
	if ($is_admin != 1) pass_check();
	#no_link_temp.cgi から no_link.cgi にデータを移行する
	$fp=fopen("$EST[log_path]no_link.cgi", "r");
	while($tmp=fgets($fp, 4096)){
		$data=explode("<>",$tmp); #id<>count(,)<>ip<>url<>com<>\n
		$ip=explode("&",$data[2]);
		$url[$data[0]]=$data[3];
		foreach ($ip as $tmp){
			$fl["$data[0]_$tmp"]=1;
		}
		$PR_ip[$data[0]]=$data[2];
		$count2=explode(",",$data[1]);
		$i=1;
		foreach($count2 as $tmp){
			$count["${i}_$data[0]"]=$tmp;
			$count[$data[0]]+=$tmp;
			$i++;
		}
		$com[$data[0]]=$data[4];
	}
	fclose($fp);
	$fp=fopen("$EST[log_path]no_link_temp.cgi", "r");
	while($tmp=fgets($fp, 4096)){
		$data=explode("<>",$tmp);
		if(!$fl["$data[0]_$data[1]"]){
			$data[2] = preg_replace("/,$/","",$data[2]);
			$count2=explode(",",$data[2]);
			foreach($count2 as $tmp){
				$count["${tmp}_$data[0]"]++; #データの報告数
				$count[$data[0]]++;
			}
			$fl["$data[0]_$data[1]"]=1; #２重チェック
			$PR_ip[$data[0]].="$data[1]&"; #ip
			$com[$data[0]].="$data[4]<2>$data[5]<2>$data[3]<1>";
			$query="SELECT id,url FROM $EST[sqltb]log WHERE id=$data[0] LIMIT 1;";
			$result=$xoopsDB->query($query) or die("Query failed admin356 $query");
			$Slog = mysql_fetch_assoc($result);
			for($i=1; $i <= 5; $i++){
				if($count{"${i}_$Slog[id]"}){$url[$Slog[id]]=$Slog[url]; break;}
			}
		}
	}
	fclose($fp);

	if(isset($url)) {
		ksort($url);
		$fp=fopen("$EST[log_path]no_link.cgi", "w");
		foreach ($url as $id=>$val){
			fputs($fp,"$id<>");
			for($i=1; $i <= 5; $i++){
				if(!$count["${i}_$id"]){$count["${i}_$id"]=0;}
				fputs($fp, $count["${i}_$id"] . ",");
			}
			fputs($fp, "<>$PR_ip[$id]<>$val<>$com[$id]<>\n");
		}
		fclose($fp);
	}
	$fp=fopen("$EST[log_path]no_link_temp.cgi", "w");
	fclose($fp);
	require "$EST[temp_path]admin/dl_check.html";
	exit;
}
elseif($_POST['mode'] == "dl_check_dl"){
	#(14.1)デッドリンクチェック用ファイルをダウンロード(&dl_check_dl)
	if ($is_admin != 1) pass_check();
	//header("Content-type: application/octet-stream);
	header("Content-Disposition: attachment; filename=\"check_urls.dat\"");
	header("Content-Type: application/x-download; name=\"check_urls.dat\"");
	$query="SELECT id,url FROM $EST[sqltb]log";
	$result=$xoopsDB->query($query) or die("Query failed admin682 $query");
	while($Slog = mysql_fetch_assoc($result)){
		echo "$Slog[id]\t$Slog[url]\t\t\t0\t\t\t\t\t\t\t\t0\t\t\t\t\n";
	}
	//mysql_close($link);
	exit;
}
elseif($_POST['mode'] == "dl_check_act"){
	#(14.2)デッドリンクチェック実行画面(&dl_check_act)
	$lines=array();
	if ($is_admin != 1) pass_check();
	if(!is_file($_POST[checkfile])){mes("指定されたファイルは存在しません","エラー","java");}
	$fp=fopen("./$_POST[checkfile]", "r");
	while($tmp=fgets($fp, 4096)){
		$data=explode("\t",$tmp);
		if(strstr($data[13],"Not Found") || strstr($data[13],"Forbidden")){
			$url[$data[0]]=$data[1];
			array_push($lines,$tmp);
		}
	}
	fclose($fp);
	$fp=fopen("./$_POST[checkfile]", "w");
	foreach($lines as $tmp) {
		fputs($fp, $tmp);
	}
	fclose($fp);
	require "$EST[temp_path]admin/dl_check_act.html";
	exit;
}
elseif($_POST['mode'] == "ill_lock_del"){ill_lock_del;}
#elseif($_POST['mode'] == "design"){design;}
#elseif($_POST['mode'] == "template_cfg"){template_cfg;}
elseif($_POST['mode'] == "ver_info"){
	#(18)バージョン情報(&ver_info)
	if ($is_admin != 1) pass_check();
	require "$EST[temp_path]admin/ver_info.html";
	exit;
}
elseif($_POST['mode'] == "look_mes"){
	#(19)登録者のメッセージを見る(&look_mes)
	if ($is_admin != 1) pass_check();
	require "$EST[temp_path]admin/look_mes.html";
	exit;
}
elseif($_POST['mode'] == "cfg_make"){
	#(cfg1)環境設定(%EST)を更新(&cfg_make)
	#パスワードチェック
	if($EST[pass] != "setup"){
		if ($is_admin != 1) pass_check();
	}
	$bf_pass=$_POST[pass];
	##パスワードを暗号化する
	if($_POST[new_pass]){$bf_pass=$_POST[pass]=$_POST[new_pass];}
	$_POST[pass]=crypt($_POST[pass]);
	foreach ($EST as $key=>$val){ #環境設定(%EST)を更新
		if(isset($_POST[$key])){$EST[$key]=$_POST[$key];}
	}
	##パスワードを暗号化前に戻す
	$_POST[pass]=$bf_pass;
	cfg_set();
	exit;
}

elseif($_POST['mode'] == "cfg_make_PR_menu"){
	#(cfg2)環境設定(&search_form/&menu_bar)を更新(&cfg_make_PR_menu)
	// Magic_Quote の対応
	if(get_magic_quotes_gpc()) {
		$_POST=array_map("stripslashes", $_POST);
	}
	$file_data=array();
	if ($is_admin != 1) pass_check();
	$fl=0;$p_fl=1;
	$_POST =str_replace("&lt;", "<", $_POST);
	$_POST =str_replace("&gt;", ">", $_POST);
	$_POST =str_replace("’", "'", $_POST);
	//$_POST =str_replace("\r\n", "\n", $_POST);
	//$_POST =str_replace("\r", "\n", $_POST);

	$est_find = false;
	$allow_search_form = trim(preg_replace("/(\n| )+/"," ",$_POST['allow_search_form']));

	$file=file("pl/cfg.php");
	
	if (!trim($_POST['search_form'])) $_POST['search_form'] = get_search_form();
	
	foreach($file as $tmp){
		$tmp = trim($tmp);
		if ($tmp == "\$EST=array(") $est_find = true;
		if (preg_match("/^'allow_search_form'=>/",$tmp)) {
			$tmp = "'allow_search_form'=>'".$allow_search_form."',";
			$est_find = false;
		}
		if (($est_find) && ($tmp == ");")) {
			$tmp = "\n#検索窓を外部から使用許可するURL\n'allow_search_form'=>'".$allow_search_form."',\n".$tmp;
			$est_find = false;
		}
		
		if($fl==1){array_push($file_data,$_POST[search_form]); $fl=0;}
		elseif($fl==2){array_push($file_data,$_POST[menu_bar]); $fl=0;}
		elseif($fl==3){array_push($file_data,$_POST[head_sp]); $fl=0;}
		elseif($fl==4){array_push($file_data,$_POST[foot_sp]); $fl=0;}
		
		if($tmp == "} #end of &search_form"){array_push($file_data,"<?php");$p_fl=1;}
		elseif($tmp == "} #end of &menu_bar"){array_push($file_data,"<?php");$p_fl=1;}
		elseif($tmp == "} #end of &head_sp"){array_push($file_data,"<?php");$p_fl=1;}
		elseif($tmp == "} #end of &foot_sp"){array_push($file_data,"<?php");$p_fl=1;}
		
		if($p_fl){
			array_push($file_data,$tmp);
		}
		if($tmp == "function search_form(){"){ #search
			array_push($file_data,"global \$EST;");
			array_push($file_data,"?>");
			$fl=1; $p_fl=0;
		}
		elseif($tmp == "function menu_bar(){"){ #menu
			array_push($file_data,"global \$EST;");
			array_push($file_data,"?>");
			$fl=2; $p_fl=0;
		}
		elseif($tmp == "function head_sp(){"){ #head
			array_push($file_data,"?>");
			$fl=3; $p_fl=0;
		}		
		elseif($tmp == "function foot_sp(){"){ #foot
			array_push($file_data,"?>");
			$fl=4; $p_fl=0;
		}		
	}
	$fp=fopen("pl/cfg.php", "w");
	foreach($file_data as $tmp) {
		$tmp=rtrim($tmp);
		fputs($fp, $tmp."\n");
	}
	fclose($fp);
	mes("メニューバー/外部検索エンジン/ヘッダ・フッタスペースの設定が完了しました。","更新完了","kanri");
	exit;
}
elseif($_POST['mode'] == "cfg_make_reg"){
	#(cfg3)環境設定(登録処理関係)を更新 (&cfg_make_reg)
	if ($is_admin != 1) pass_check();
	EST_reg();
	foreach ($EST_reg as $key=>$val){ #環境設定(%EST_reg)を更新
		if(isset($_POST[$key])){$EST_reg[$key]=$_POST[$key];}
	}
	cfg_set(1);
	exit;
}
elseif($_POST['mode'] == "cfg_make_kt_ex"){
	#(cfg4)カテゴリ説明文を更新 (&cfg_make_kt_ex)
	if ($is_admin != 1) pass_check();
	gane_guide();
	foreach ($KTEX as $key=>$val){ #環境設定(%KTEX)を更新
		if($_POST["d_$key"]){unset($KTEX[$key]);}
		elseif(isset($_POST["ex_$key"])){$KTEX[$key]=$_POST["ex_$key"];}
	}
	#新規追加分を定義
	if($_POST[ktex]){
		$_POST[ktex]=str_replace(array("&gt;", "&lt;"), array(">", "<"), $_POST[ktex]);
		$new=explode("\n",$_POST[ktex]);
		foreach($new as $tmp){
			$ktex=explode("<>",$tmp);
			$KTEX[$ktex[0]]=$ktex[1];
		}
	}
	ksort ($KTEX,SORT_STRING);
	cfg_set(0,0,1);
	exit;
}
elseif($_POST['mode'] == "cfg_make_kt"){
	#(cfg5)カテゴリ設定を更新 (&cfg_make_kt)
	if ($is_admin != 1) pass_check();
	gane_st();
	require "pl/other_cfg.php";
	if($_POST['mente_mode'] == "mente"){
		$gane_other=array();
		foreach ($ganes as $key=>$val){ #カテゴリ設定(%ganes)を更新
			if($_POST["d_$key"])
			{ #削除
				unset ($ganes[$key]);
				unset ($EST_furi[$key]);
				if (isset($gane_top[$key])) unset ($gane_top[$key]);
				if (isset($gane_UR[$key])) unset ($gane_UR[$key]);
				if (isset($gane_ref[$key])) unset ($gane_ref[$key]);
				
				$_tmp = array();
				foreach($gane_ref as $_key=>$_val)
				{
					$_val = str_replace($key,"",$_val);
					$_val = str_replace("&&","&",$_val);
					$_val = preg_replace("/^&|&$/","",$_val);
					$_tmp[$_key] = $_val;
				}
				$gane_ref = $_tmp;
				
				$_key = array_search($key,$gane_other);
				if ($_key !== false) array_splice($gane_other,$_key);
			}
			elseif(isset ($_POST["kt_$key"]))
			{ #カテゴリがあれば
				$ganes[$key]=$_POST["kt_$key"];
				if($_POST["t_$key"]){$gane_top[$key]=1;}//else{$gane_top[$key]=0;}
				elseif($gane_top[$key]!==1){unset ($gane_top[$key]);}
				if($_POST["o_$key"]){array_push($gane_other,$key);}
				if($_POST["no_$key"]){$gane_UR[$key]=1;}//else{$gane_UR[$key]=0;}
				elseif($gane_UR[$key]!==1){unset ($gane_UR[$key]);}
				if($_POST["ref_$key"]){$gane_ref[$key]=$_POST["ref_$key"];}
				elseif($gane_ref[$key]===""){unset ($gane_ref[$key]);}
			}
			ksort ($ganes,SORT_STRING);
			ksort ($gane_top,SORT_STRING);
			ksort ($gane_UR,SORT_STRING);
			sort ($gane_other);
			#ふりがなの設定
			$EST_furi[$key]=$_POST["furi_$key"]; #$EST_furi[$key]=~s/'/’/g;
		}
	}
	if($_POST['mente_mode'] == "new"){
		#新規追加分を定義
		if($_POST['kt_new']){
			$_POST['kt_new']=trim(str_replace(array("&gt;", "&lt;"), array(">", "<"), $_POST['kt_new']));
			$new=explode("\n",$_POST['kt_new']);
			foreach($new as $tmp){
				$kt=explode("<>",$tmp);
				if (!empty($kt[0]) && !empty($kt[1])) $ganes[$kt[0]]=$kt[1];
			}
		}
	}
	
	#ふりがなを設定
	$fp = fopen("pl/other_cfg.php", "wb");
	fputs($fp, "<?php\n\$EST_furi=array(\n");
	foreach ($ganes as $key=>$val){
		if($EST_furi[$key]){
			fputs($fp, "'$key'=>'$EST_furi[$key]',\n");
		}
		else{
			fputs($fp, "'$key'=>'$val',\n");
		}
	}
	fputs($fp, ");\n?>");
	fclose($fp);
	
	ksort ($ganes,SORT_STRING);
	cfg_set(0,1,0);
	exit;
}
#elseif($_POST['mode'] == "cron"){cron;}
else{
	#(1)ログイン画面(&login)
	require "$EST[temp_path]admin/login.html";
	exit;
}
/*
sub log_mente{
	#(10)ログ診断 (&log_mente)
	#	・フィールド数を正常にする
	#	・未定義カテゴリを削除
	#	・所属カテゴリが0のデータを任意のカテゴリに移動又は削除
	if ($is_admin != 1) pass_check();
	require "$EST[temp_path]admin/log_mente.html";
}

sub log_mente_act{
	#(10.1)ログ診断実行 (&log_mente_act)
	if ($is_admin != 1) pass_check();
	#入力コマンドの整合チェック＆整形
	##データフィールド数の整形
	$FORM{fld_custom}=~s/\D//g;
	if($FORM{set_fld} == "custom" && (!$FORM{fld_custom} || $FORM{fld_custom}<15)){
		&mes("データフィールド数を指定してください<br>$Efld未満の数は指定できません","記入ミス","java");
	}
	local($Cfld,$plus_fld);
	if($FORM{set_fld} == "custom"){$Cfld=$FORM{fld_custom};}
	else{$Cfld=$Efld;} #デフォルト値(temp.cgiで設定)
	local($fld=$FORM{fld_custom}-$Efld);
	if($fld>0){
		foreach(1 .. $fld+1){
			$plus_fld .="<>";
		}
	}
	##未定義データの移動先の設定
	if($FORM{set_no} == "move" && !$FORM{set_no_move_kt}){
		&mes("移動先のカテゴリを指定してください","記入ミス","java");
	}
	local($move_kt,$del_mode);
	if($FORM{set_no} == "move"){$move_kt=$FORM{set_no_move_kt}; $del_mode="off";}
	else{$move_kt=""; $del_mode="on";}
	local(@log_lines,@Slog,$line,@kt,$kt,$del_fl);
	open(IN,"$EST{log_path}$EST{logfile}");
	while($line=<IN>){
		$line=~s/\n//g; $line .=$plus_fld;
		@Slog=split(/<>/,$line,$Cfld+1);
		if($Slog[12] == "http://"){$Slog[12]="";}
		@pt=split(/_/,$Slog[13]);
		foreach(0 .. 3){
			if(!$pt[$_]){$pt[$_]=0;}
		}
		$Slog[13]=join("_",@pt);
		pop(@Slog);
		@kt=split(/&/,$Slog[10]);
		$kt=""; $del_fl=0; #削除フラグ
		foreach(@kt){
			if($ganes{$_}){$kt .=$_ . "&";}
		}
		$Slog[10]=$kt;
		if(!$kt && $del_mode == "on"){$del_fl=1;}
		elseif(!$kt){$Slog[10]=$move_kt;}
		$Slog[0]=~s/\D//g;
		if(!$del_fl && $Slog[0]){
			$line=join("<>",@Slog,"\n");
			push(@log_lines,$line);
		}
	}
	open(OUT,">$EST{log_path}$EST{logfile}");
	print OUT @log_lines;
	close(OUT);
	@log_lines=();
	&mes("ログ診断が完了しました","ログ診断完了","kanri");
}

#(13.2)人気ランキングファイルを更新(&rank_cfg_make_rank)
#→temp.cgiへ
*/
function cfg_set($EST_reg_fl=0, $gane_st_fl=0, $gane_guide_fl=0, $exit_fl=0){
	global $EST, $EST_reg, $ganes, $gane_top, $gane_ref, $gane_UR, $gane_other, $KTEX;
	#(cfg1.1)環境設定ファイルを更新(&cfg_set)
	#$EST_reg_fl=$_[0], #sub EST_regを更新(1/0)
	#$gane_st_fl=$_[1], #sub gane_stを更新(1/0)
	#$gane_guide_fl=$_[2], #sub gane_guideを更新(1/0)
	#$exit_fl=$_[3], #終了する=0/しない=1)
	#修正フラグで読み込むかどうかを判定
	if(!$EST_reg_fl){EST_reg();}
	if(!$gane_st_fl){gane_st();}
	if(!$gane_guide_fl){gane_guide();}
	#&search_form/&menu_bar/&head_sp/&foot_spを読み込み
	$fl_sf=0;$fl_mb=0;$fl_hs=0;$fl_fs=0;
	$fp=fopen("pl/cfg.php", "r");
	while($tmp=fgets($fp, 4096)){
		//改行コード統一 by nao-pon
		$tmp = preg_replace("/\x0D\x0A|\x0D|\x0A/","\n",$tmp);
		if($tmp == "function search_form(){\n"){$fl_sf=1;}
		elseif($tmp == "} #end of &search_form\n"){$fl_sf=0;}
		elseif($fl_sf){$PR_search_form.=$tmp;}
		elseif($tmp == "function menu_bar(){\n"){$fl_mb=1;}
		elseif($tmp == "} #end of &menu_bar\n"){$fl_mb=0;}
		elseif($fl_mb){$PR_menu_bar.=$tmp;}
		elseif($tmp == "function head_sp(){\n"){$fl_hs=1;}
		elseif($tmp == "} #end of &head_sp\n"){$fl_hs=0;}
		elseif($fl_hs){$PR_head_sp.=$tmp;}
		elseif($tmp == "function foot_sp(){\n"){$fl_fs=1;}
		elseif($tmp == "} #end of &foot_sp\n"){$fl_fs=0;}
		elseif($fl_fs){$PR_foot_sp.=$tmp;}
	}
	fclose($fp);
	##%ESTを更新
	require "$EST[temp_path]admin/cfg_lib.php";
	if(!$exit_fl){
		mes("環境設定の変更/カテゴリの設定が完了しました","環境設定/カテゴリ設定完了","kanri");
	}
}

#(6)メッセージ画面出力(&mes)
#書式:&mes($arg1,$arg2,$arg3);
#機能:メッセージ画面を出力する
#引数:$arg1=>表示するメッセージ
#     $arg2=>ページのタイトル(省略時は「メッセージ画面」)
#     $arg3=>・JavaScriptによる「戻る」ボタン表示=java
#            ・$ENV{'HTTP_REFERER'}を使う場合=env
#            ・管理室へのボタン=kanri
#            ・通常のURL又はパスを指定する場合にはそのURL又はパスを記入
#            ・省略時は非表示
#     $arg4=>ロック解除=unlock
#戻り値:なし
function mes($MES, $TITLE="", $arg3=""){
	global $EST;
	global $xoopsOption,$xoopsConfig,$xoopsLogger,$xoopsTpl,$xoopsHypTicket;
	global $x_ver,$ver;
	if(!$TITLE){$TITLE="メッセージ画面";}
	if($arg3 == "java"){
		$BACK_URL="<form><input type=button value=\"&nbsp;&nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;&nbsp;\" onClick=\"history.back()\"></form>";
	}
	elseif($arg3 == "env"){
		$BACK_URL="【<a href=\"{$_SERVER['HTTP_REFERER']}\">戻る</a>】";
	}
	elseif($arg3 == "kanri"){
		$BACK_URL="<form action=\"{$EST['admin']}\" method=\"post\"><input type=\"hidden\" name=\"mode\" value=\"kanri\">".$xoopsHypTicket->getTicketHtml( __LINE__ )."<input type=\"submit\" value=\"管理室へ\"></form>";
	}
	elseif(!$arg3){$BACK_URL="";}
	else{$BACK_URL="【<a href=\"$arg3\">戻る</a>】";}
	$cp_function = true;
	require "$EST[temp_path]mes.html";
	exit;
}

function mes_frame($mes,$title=""){
	echo "<html><body style=\"font-size:12px;\">";
	echo "<b>$title</b><hr>";
	echo "$mes";
	echo "</body></html>";
	exit;
}

function yomi_unhtmlspecialchars($str)
{
	return str_replace(array('&amp;','&lt;','&gt;','&quot;'),array('&','<','>','"'),$str);
}

function get_search_form()
{
	global $EST;
	$data = @file("./pl/search.dat");
	$selects = '<option value="pre" selected>'.$EST['search_name'].'で'."\n";
	if (is_array($data))
	{
		foreach($data as $line)
		{
			if (strpos($line,"//") === 0) continue;
			$lines = explode("\t",str_replace(array("\n","\r"),"",$line));
			if (strpos($line,"-") === 0)
				$selects .= '<option value="">-----------------'."\n";
			else
				$selects .= '<option value="'.$lines[0].'">'.$lines[1].'で'."\n";
		}
	}
	return $selects;
}

function yomi_cr_replace($v)
{
	return str_replace(array("\r\n","\r"),"\n",$v);
}

function yomi_stripslashes($v)
{
	if (is_array($v))
	{
		$v= array_map("yomi_stripslashes",$v);
	}
	else
	$v = stripslashes($v);
	
	return $v;
}
##-- end of admin.php --##
?>