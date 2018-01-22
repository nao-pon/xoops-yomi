<?php
// For XOOPS
include("header.php");

include('init.php');

if(ini_get("magic_quotes_gpc")) {
	$_GET = array_map("stripslashes", $_GET);
}
$_GET = array_map("htmlspecialchars", $_GET);

if(!$_GET['page']){$_GET['page']=1;}

#(1)検索結果表示画面(search)
#入力値の整形
if(!$_GET['item_id']){$_GET['item_id']=$_POST['item_id'];}
if(preg_match("/\D/", $_GET['item_id']) || (!$_GET['item_id'])){mes("指定値が不正です","ページ指定エラー","java");}
$item_id = intval($_GET['item_id']);
$single_link = true;

//$mode = $_GET['mode'];
//$order = intval($_GET['order']);
//$comment_id = intval($_GET['comment_id']);


#結果表示

##ファイルの読み込み＆該当データ総数を得る
$Clog=open_for_search($item_id);

$log_lines = $write;
unset($write);
##↑で@writeを破棄

$myts =& MyTextsanitizer::getInstance();

// ヘッダスペース抑止カテゴリの判定
EST_reg();
$_no_ad_space = false;
foreach($log_lines as $key => $Slog)
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
	$Slog[6] = str_replace('<br>', "\n", $Slog[6]);
	if ($EST['syoukai_br'] == 2) {
		$log_lines[$key][6] = $myts->displayTarea(unhtmlspecialchars($Slog[6]));
	} else if ($EST['syoukai_br'] == 1) {
		$log_lines[$key][6] = nl2br($Slog[6]);
	}
}

$Stitle = $Slog[1];

if (is_object($xoopsTpl)) {
	// For comments with d3forum
	$module_config =& $config_handler->getConfigsByCat(0, $xoopsModule->mid());
	$content = array();
	$content['item_id'] = $item_id;
	$content['subject'] = htmlspecialchars($Stitle);
	$xoopsTpl->assign(
		array(
			'mod_config' => $module_config,
			'mydirname'  => 'yomi',
			'content'    => $content
		)
	);
}

//xoops2 タイトル設定
//global $xoopsModule,$xoopsTpl;
if (is_object($xoopsTpl))
{
	$xoops_pagetitle = $xoopsModule->name();
	$xoops_pagetitle = "$Stitle - $xoops_pagetitle";
	$xoopsTpl->assign("xoops_pagetitle",$xoops_pagetitle);
}

require $EST['temp_path']."search.html";

include("footer.php");

exit;


function open_for_search($item_id){
	global $xoopsDB, $EST, $write;
	$i = 0;
	$query = "SELECT * FROM {$EST['sqltb']}log WHERE id=".$item_id;
	##検索処理実行
	$result = $xoopsDB->query($query) or die("Query failed1");
	if ($write[0] = $xoopsDB->fetchRow($result)) {
		$i = 1;
	}
	return $i;
}

#(5)外部検索エンジンへのリンク一覧を表示(&PR_mata_page)
function PR_meta_page($location_list){
	$T_flag=1;
	echo "<table style=\"width:90%;padding:8px;\" align=\"center\" width=\"90%\" cellpadding=8>";
	foreach ($location_list as $list){
		list($Dengine,$Durl)=explode("<>",$list);
		if($T_flag==5){echo "</tr>"; $T_flag=1;}
		if($T_flag==1){echo "<tr>";}
		?>
<td class="yomi-s" style="text-align:center;"><a href="<?php echo $Durl?>" target="<?php echo $_POST['target']?>"><font size="+1"><?php echo $Dengine?></font></a></td>
<?php
		$T_flag++;
	}
	if($T_flag!=2){echo "</tr>";}
	echo "</table>";
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