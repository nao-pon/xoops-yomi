<?php
if(ini_get("magic_quotes_gpc")) {
	$_GET = array_map("stripslashes", $_GET);
}
$_GET = array_map("htmlspecialchars", $_GET);
//コメント欄のパラメータ変更は single_link.php へリダイレクト
if (!empty($_GET['item_id'])){
	$redirect = "single_link.php?item_id=".$_GET['item_id']."&mode=".$_GET['mode']."&order=".$_GET['order'];
	header("Location: $redirect");  
}
// For XOOPS
if (empty($_GET['engine']) || $_GET['engine'] == "pre")
	include("header.php");
else
	include("../../mainfile.php");

include('init.php');

//開始時間
$Ebf_times = yomi_getmicrotime();

// 外部から検索窓の利用を制限する
$EST['allow_search_form'] = trim($EST['allow_search_form']);
if ($EST['allow_search_form']){
	$allow_urls = array();
	$allow_urls = explode(" ",$EST['allow_search_form']);
	$allow_ok = false;
	foreach($allow_urls as $allow_url){
		$allow_url = quotemeta($allow_url);
		$allow_url = str_replace("/","\/",$allow_url);
		if (preg_match("/^$allow_url/",$GLOBALS['HTTP_REFERER'])){
			$allow_ok = true;
			brark;
		}
	}
	if (!$allow_ok) mes("利用許可されていないURLからアクセスされているか、または参照元が特定できません。", "アクセス不可","index.php");
}

##目次##
#(1)検索結果表示画面(search)
#(2)詳細検索画面(search_ex)
#(3)外部リンク画面(meta)
#(4)検索処理用データをハッシュに入れる(open_for_search)
#(5)外部検索エンジンへのリンク一覧を表示(PR_mata_page)
#(6)キーワードを一時ランキングファイル(keyrank_temp.cgi)に記録(set_word)

##テンプレートファイル
#検索結果画面=>temp/search.html
#詳細検索画面=>temp/search_ex.html
#外部リンク画面=>temp/search_meta.html

#【カテゴリ検索】
##[オプション]
#カテゴリ指定([kt]&[option(b_all=以下)])
#日付指定=>( today-x | year/mon/day | [str_day]-[end_day] )
#新規ウィンドウ=>window=new
if(!$_GET['page']){$_GET['page']=1;}

#(1)検索結果表示画面(search)
if($_GET['mode'] == "search"){ #検索結果表示画面
	//ソート順のクッキー処理
	$CK_data = get_cookie();
	if (!isset($_GET['sort']) && isset($CK_data[7])) $_GET['sort']=$CK_data[7];
	if (isset($_GET['sort'])) {
		$CK_data[7]=$_GET['sort'];
		set_cookie($CK_data);
	}

	$words_a = array();
	$words_o = array();
	$words_n = array();
	$kt_search_list=array();
	#検索オプションをクッキーに記録
	#オプション
	#[0]=>検索条件(a|o)/[1]=>検索式の使用有無(0|1)/[2]=>検索エンジン名(ID)/
	#[3]=>検索エンジン名(表示名)/[4]=>www.(0|1)/[5]=>カテゴリ指定(ID)
	#[6]=>カテゴリ指定(表示名)/[7]=>指定カテゴリ(0|1)/[8]=>日付指定(data)
	#[9]=>日付指定(表示名)/[10]=>日付指定コマンド(data)/[11]=>カテゴリ名検索(0|1)
	if($_GET['set_option'] == "on"){
		$CK_data = get_cookie();
		#local(@cookie_lines);
		if($_GET[method] == "and"){$cookie_lines[0]="a";} #[0]
		else{$cookie_lines[0]="o";}
		if($_GET[use_str] == "on"){$cookie_lines[1]="1";} #[1]
		else{$cookie_lines[1]="0";}
		$cookie_lines[2]=$_GET[engine]; #[2]
		if($_GET[engine] == "pre"){$cookie_lines[3]="$EST[search_name]で";} #[3]
		else{
		$fp = fopen("pl/cfg.php", "r");
			$i=0;
			while($tmp = fgets($fp, 4096)){
				if($tmp == "function search_form(){\n"){$i=1;}
				if($i){
					if(preg_match("/<option value=\"$_GET[engine]\">(.+)で/", $tmp, $match)){$cookie_lines[3]=$match[1]; break;}
				}
			}
		fclose($fp);
		}
		$cookie_lines[3]=str_replace(",","，", $cookie_lines[3]);
		if($_GET[www] == "on"){$cookie_lines[4]="0";} #[4]
		else{$cookie_lines[4]="1";}
		$cookie_lines[5]=$_GET[search_kt]; #[5]
		if($_GET[search_kt]){$cookie_lines[6]=&full_kt($_GET[search_kt]);} #[6]
		else{$cookie_lines[6]="指定しない";}
		if($_GET[search_kt_ex] != "-b_all"){$cookie_lines[7]=0;} #[7]
		else{$cookie_lines[7]=1;}
		$cookie_lines[8]=$_GET[search_day]; #[8]
		if($_GET[search_day] == "today"){$cookie_lines[9]="本日";} #[9]
		elseif(preg_match("/^(\d+)-/", $_GET[search_day], $match)){$cookie_lines[9]= $match[1] . "日以内";}
		else{$cookie_lines[9]="指定しない";}
		$cookie_lines[10]=$_GET[search_day_ex]; #[10]
		$cookie_lines[10]=str_replace(",","，", $cookie_lines[10]);
		if($_GET[kt_search] == "on"){$cookie_lines[11]=0;} #[11]
		else{$cookie_lines[11]=1;}
		$CK_data[5]=implode(",",$cookie_lines);
		$CK_data[5]=str_replace(";", "", $CK_data[5]);
		set_cookie($CK_data);
	}
	#入力値の整形
	if(!$_GET['engine']){$_GET['engine']="pre";}
	if(preg_match("/\D/", $_GET['page'])){mes("ページ指定値が不正です","ページ指定エラー","java");}
	if(!$_GET['sort']){$_GET['sort']=$EST['defo_hyouji'];}
	if($_GET['search_kt_ex']){$_GET['search_kt']=$_GET['search_kt'] . $_GET['search_kt_ex'];}
	if($_GET['search_day_ex']){$_GET['search_day']=$_GET['search_day_ex'];}
	if($_GET['kn']>0 && $_GET['kn']<=20){ #キーワードの結合
		if(preg_match("/\D/", $_GET['kn'])){mes('$_GET[kn]が不正です',"エラー","java");}
		for($i=1; $i< $_GET['kn']; $i++){
			if($_GET["word$i"]){$_GET['word'] .= " ".$_GET["word$i"];}
		}
	}
	//キーワード文字エンコード変換
	//$_GET['word'] = mb_convert_encoding($_GET['word'], "EUC-JP", "auto");

	if(!$_GET['hyouji']){$_GET['hyouji']=$EST['hyouji'];}
	
	if($EST['keyrank'] && $_GET['page']==1){ #キーワードランキング用のデータを取得
		set_word();
	}
	#検索構文の解析
	$w_line=str_replace("　", " ", $_GET['word']);
	$_GET['word']=$w_line;
	if($_GET['use_str'] == "on"){ #検索式を使う
		$words=explode(" ",$w_line);
		$w_fl="and";
		foreach ($words as $word){
			if($word == "and"){$w_fl="and";}
			elseif($word == "or"){$w_fl="or";}
			elseif($word == "not"){$w_fl="not";}
			elseif($w_fl == "and"){array_push($words_a,$word); $w_fl="and";}
			elseif($w_fl == "or"){array_push($words_o,$word); $w_fl="and";}
			elseif($w_fl == "not"){array_push($words_n,$word); $w_fl="and";}
			else{array_push($words_a,$word);}
		}
	}
	else{ #検索式を使わない
		if($_GET['method'] != "or"){ $words_a=explode(" ",$w_line);}
		else{$words_o=explode(" ",$w_line);}
	}
	#外部検索へ分岐
	if($_GET['engine'] != "pre"){
		$_GET[target]=$_GET[window];
		require "pl/meta_ys.php";
		meta("select");
	}
	if(!$_GET['word'] && !$_GET[search_day]){ #キーワード・日付指定の両方が未指定のとき
		mes("<b>キーワード</b>か<b>日付指定</b>のいずれかは必ず指定してください","記入ミス","java");
	}
	if(!$_GET['word']){$_GET['kt_search']="off";}
 	##検索処理
	#カテゴリ検索
	if($_GET['kt_search'] != "off"){
		reset($ganes);
		while (list($kt, $kt_name) = each($ganes)){
			$kt_fl=1;
			foreach ($words_a as $word){ #and検索
				if(!stristr($kt_name,$word)){$kt_fl=0; break;}
			}
			foreach ($words_o as $word){ #or検索
				$kt_fl=0;
				if(stristr($kt_name,$word)){$kt_fl=1; break;}
			}
			foreach ($words_n as $word){ #not検索
				if(stristr($kt_name,$word)){$kt_fl=0; break;}
			}
			if($kt_fl){array_push($kt_search_list,$kt);}
		}
	}
	#結果表示
	if($_GET['search_day']){
		if(preg_match("/^today-?(\d*)$/", $_GET['search_day'], $match)){ #today-x
			if($match[1]>10000){$match[1]=0;}
			if($match[1]>0){$search_day="★".$match[1]."日前に更新されたサイト<br>";}
			else{$search_day="★本日に更新されたサイト<br>";}
		}
		elseif(preg_match("/^(\d+)\-(\d+)$/", $_GET['search_day'], $match)){ #[str_day]-[end_day]
			if($match[2]>0){$search_day="★".$match[1]."日前〜".$match[2]."日前に更新されたサイト<br>";}
			else{$search_day="★".$match[1]."日以内に更新されたサイト<br>";}
		}
		elseif(preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $_GET['search_day'], $match)){ #year/mon/day
			$search_day="★".$_GET['search_day']." に更新されたサイト<br>";
		}
	
	}
	##ファイルの読み込み＆該当データ総数を得る
	$Clog=open_for_search($_GET['search_kt'],$_GET['search_day'],$_GET['sort']);
	
	$log_lines = array_splice($write,($_GET['page']-1)*$EST['hyouji'],$EST['hyouji']);
	unset($write);
	##↑で@writeを破棄
	
	// ヘッダスペース抑止カテゴリの判定
	EST_reg();
	$_no_ad_space = false;
	foreach($log_lines as $Slog)
	{
		$kt = explode("&",$Slog[10]);
		foreach ($kt as $tmp)
		{
			if($ganes[$tmp])
			{
				$_no_ad_space = (preg_match("/\.$/",$ganes[$tmp]));	
			}
			if ($_no_ad_space) break;
		}
		if ($_no_ad_space) break;
	}
	
	if($_GET['item_id']){
		//$Stitle="検索結果";
		$Stitle = $write[0][1];
	}elseif($_GET['use_str'] == "on"){
		$Stitle="検索結果(検索式：$_GET[word])";
	}
	else{
		if(!$_GET['method']){$_GET['method']="and";}
		$Stitle="検索結果(検索ワード：$_GET[word] / 検索条件：$_GET[method])";
	}
	//xoops2 タイトル設定
	global $xoopsModule,$xoopsTpl;
	if (is_object($xoopsTpl))
	{
		$xoops_pagetitle = $xoopsModule->name();
		$xoops_pagetitle = "$Stitle-$xoops_pagetitle";
		$xoopsTpl->assign("xoops_pagetitle",$xoops_pagetitle);
	}

	require $EST['temp_path']."search.html";
	
	include("footer.php");
	
	if (isset($link) && $link) {
		@mysql_close($link);
	}
	
	exit;
}

#外部リンク画面
elseif($_GET['mode'] == "meta"){
	if($EST[keyrank] && $_GET[page]==1){ #キーワードランキング用のデータを取得
		set_word();
	}
	require "pl/meta_ys.php";
	require "$EST[temp_path]search_meta.html";
}

#詳細検索画面
else{
	require "$EST[temp_path]search_ex.html";
}
exit;

#(4)検索処理用データをハッシュに入れる(&open_for_search)
#対象全配列を@writeに入れる
#$arg1=>カテゴリ指定([kt]-[option(b_all=以下)])
#$arg2=>日付指定=>( today-x | year/mon/day | [str_day]-[end_day]_[option(re)] )
#$arg3=>ソート方法(id/time/ac/mark)
function open_for_search($target_kt, $target_day, $sort){
	global $xoopsDB,$EST,$ganes, $words_a, $words_o, $words_n, $write;
	$i = 0;
	#カテゴリ指定部分
	if($target_kt){
		list($target_kt1,$target_kt2)=explode("-",$target_kt);
		$target_kt1 = preg_replace("/[^0-9_]+/","",$target_kt1);
		list($oya_kt,)=explode("_",$target_kt1);
		if(!$ganes[$oya_kt]){mes("カテゴリ指定が不正です","カテゴリ指定エラー","java");}
		if($target_kt2){$category = "category LIKE '%&$target_kt1%'";}
		else{$category = "category LIKE '%&$target_kt1&%'";}
	}
	#ワード検索部分
	if(count($words_a)>=0){ #and検索
		foreach ($words_a as $word){
			$word = addslashes($word);
			if (preg_match("/#id:([0-9]*)/",$word,$reg)){
				$where .= " AND id=".$reg[1];
			} else {
				$where .= " AND (title LIKE '%$word%' OR message LIKE '%$word%' OR keywd LIKE '%$word%' OR url LIKE '%$word%')";
			}
		}
	}
	if(count($words_o)>=0){ #or検索
		foreach ($words_o as $word){
			$word = addslashes($word);
			$where .= " OR (title LIKE '%$word%' OR message LIKE '%$word%' OR keywd LIKE '%$word%' OR url LIKE '%$word%')";
		}
	}
	if(count($words_n)>=0){ #not検索
		foreach ($words_n as $word){
			$word = addslashes($word);
			$where .= " AND (title NOT LIKE '%$word%' AND message NOT LIKE '%$word%' AND keywd NOT LIKE '%$word%' AND url NOT LIKE '%$word%')";
		}
	}
	if($where) {$where = substr($where, 4);}
	#日付検索部分
	if($target_day){
		if(preg_match("/^today-?(\d*)$/", $target_day, $match)){ #today-x
			if($match[1]>10000){$match[1]=0;}
			//$bf_day=time()-86400*$match[1];
			//$ltime = "stamp > '$bf_day'";
			$str_times=time()-86400*$match[1];
			$end_times=time()-86400*($match[1]-1);
			$ltime = "stamp BETWEEN '$str_times' AND '$end_times'";
		}
		elseif(preg_match("/^(\d+)\-(\d+)$/", $target_day, $match)){ #[str_day]-[end_day]
			$str_times=time()-86400*$match[1];
			$end_times=time()-86400*$match[2];
			$ltime = "stamp BETWEEN '$str_times' AND '$end_times'";
		}
		elseif(preg_match("/^(\d+)\/(\d+)\/(\d+)$/", $target_day, $match)){ #year/mon/day
			$month=array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
			$mon=$month[$match[2]];
			$str_times = strtotime ("$match[3] $mon $match[1]");
			$end_times = $str_times + 86400;
			$ltime = "stamp BETWEEN '$str_times' AND '$end_times'";
		}
		else{mes("日付指定のコマンドが正しくありません","エラー","java");}
	}
	#データ格納部分
	if($sort == "id_new"){$order = "ORDER BY id DESC";}
	elseif($sort == "id_old"){$order = "ORDER BY id";}
	elseif($sort == "time_new"){$order = " ORDER BY stamp DESC";}
	elseif($sort == "time_old"){$order = " ORDER BY stamp";}
	elseif($sort == "ac_new"){$order = " ORDER BY title";}
	elseif($sort == "vote"){$order = " ORDER BY votes DESC";}
	elseif($sort == "rating"){$order = " ORDER BY rating DESC";}
	elseif($sort == "comment"){$order = " ORDER BY comments DESC";}
	elseif($sort == "ac_old"){$order = " ORDER BY title DESC";}
	else{$order = " ORDER BY mark DESC, id DESC";} #mark
	if($category) {$query = " ($category)";}
	if($query and $where) {$query .= " AND ";}
	if($where) {$query .= "($where)";}
	if($query and $ltime) {$query .= " AND ";}
	if($ltime) {$query .= "($ltime)";}
	$query = "SELECT * FROM $EST[sqltb]log WHERE".$query.$order;
	##検索処理実行
	//$link=mysql_connect($EST[host], $EST[sqlid], $EST[sqlpass]) or die("Could not connect");
	//mysql_select_db($EST[sqldb]) or die("Could not select database");
	$result = $xoopsDB->query($query) or die("Query failed1");
		while($line = mysql_fetch_row($result)){
			$write[] = $line;
			$i++;
		}
	if(!count($write)){$i=0;}
	return $i;
}

#(5)外部検索エンジンへのリンク一覧を表示(&PR_mata_page)
function PR_meta_page($location_list){
	$T_flag=1;
	$target = (!empty($_POST['target']))? $_POST['target'] : (!empty($_GET['target']))? $_GET['target'] : "";
	$target = htmlspecialchars($target);
	echo "<table style=\"width:90%;padding:8px;\" align=\"center\" width=\"90%\" cellpadding=8>";
	foreach ($location_list as $list){
		list($Dengine,$Durl)=explode("<>",$list);
		if($T_flag==5){echo "</tr>"; $T_flag=1;}
		if($T_flag==1){echo "<tr>";}
		?>
<td class="yomi-s" style="text-align:center;"><a href="<?=$Durl?>" target="<?=$target?>"><?=$Dengine?></a></td>
<?php
		$T_flag++;
	}
	if($T_flag!=2){echo "</tr>";}
	echo "</table>";
}

#(6)キーワードをデータベースに記録(&set_word)
function set_word(){
	global $EST,$xoopsDB;
	$time=time();
	$keyword=str_replace("'", "’", $_GET['word']);
	if(strlen($keyword)<50){
		$keyword=str_replace("　", " ", $keyword);
		//$keyword=mb_strtolower($keyword);
		//$keyword=strtolower($keyword);
		//XREAでなぜかstrtolower()で文字化けするので仕方なく
		$u_word = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		$l_word = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		$keyword = str_replace($u_word,$l_word,$keyword);

		$keywords=explode(" ",$keyword);
		$keywords = array_map("addslashes", $keywords);

		//$link=mysql_connect($EST[host], $EST[sqlid], $EST[sqlpass]) or die("Could not connect");
		//mysql_select_db($EST[sqldb]) or die("Could not select database");

		if(count($keywords)>0){
			foreach ($keywords as $i){
				if($i && $i != "and" && $i != "or" && $i != "not" && (!preg_match("/#id:[0-9]*/",$i))){
					//if(preg_match("/\w/", $i)){$i=strtolower($i);}
					$i=str_replace("\n", "", $i);
					$query = "SELECT word FROM $EST[sqltb]key WHERE word='$i' AND ip='$_SERVER[REMOTE_ADDR]' AND time > ".($time-24*3600);
					$result=$xoopsDB->query($query) or die("Query failed search289 $query");
					$tmp = mysql_fetch_row($result);
					if(!$tmp) {
						$query="INSERT INTO $EST[sqltb]key (word, time, ip) VALUES ('$i', '$time', '$_SERVER[REMOTE_ADDR]')";
						$result=$xoopsDB->queryF($query) or die("Query failed search291 $query");
					}
				}
			}
		}
	}
}

#(t1)メッセージ画面出力(mes)
#書式:&mes($arg1,$arg2,$arg3);
#機能:メッセージ画面を出力する
#引数:$arg1=>表示するメッセージ
#     $arg2=>ページのタイトル(省略時は「メッセージ画面」)
#     $arg3=>・JavaScriptによる「戻る」ボタン表示=java
#            ・HTTP_REFERERを使う場合=env
#            ・管理室へのボタン=kanri
#            ・通常のURL又はパスを指定する場合にはそのURL又はパスを記入
#            ・省略時は非表示
#戻り値:なし
function mes($MES, $TITLE, $arg3=""){
	global $EST;
	global $xoopsOption,$xoopsConfig,$xoopsLogger,$xoopsTpl;
	global $x_ver,$ver;
	if(!$TITLE){$TITLE="メッセージ画面";}
	if($arg3 == "java"){
		$BACK_URL="<form><input type=button value=\"&nbsp;&nbsp;&nbsp;&nbsp;戻る&nbsp;&nbsp;&nbsp;&nbsp;\" onClick=\"history.back()\"></form>";
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