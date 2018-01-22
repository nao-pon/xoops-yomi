<?php
include_once(XOOPS_ROOT_PATH."/modules/yomi/blocks/yomi_block_func.php");
function b_yomi_s3($options) {

	global $xoopsDB;

	$block = array();

	$block['title'] = "ランダム表示(Yomi)";
	$block['content'] = "";

	$kensu = $options[0]*$options[1];#表示する件数

	$log_lines=array(); $Clog=0;

	if($options[6] == 'm1')
		$where = " WHERE mark LIKE '1%'";
	elseif($options[6] == 'm2')
		$where = " WHERE mark LIKE '%1'";
	else
		$where = "";

	$query = "SELECT COUNT(*) FROM ".$xoopsDB->prefix("yomi_log").$where.";";
	$result = $xoopsDB->query($query) or die("Query failed $query");
	list($count) = $xoopsDB->fetchRow($result);
	srand(b_yomi_s3_make_seed());
	$limit = rand(0,$count-$kensu);

	$query = "SELECT * FROM ".$xoopsDB->prefix("yomi_log").$where." ORDER BY rand() LIMIT $limit,$kensu;";
	$result = $xoopsDB->query($query) or die("Query failed $query");

	if($result){
		$block['content'] .= b_yomi_show_cols($result,$options[0],$options[2],$options[3],$options[4],$options[5]);
	} else {
		$block['content'] .= "該当データはありません。";
	}

	return $block;
}
function b_yomi_s3_edit($options) {
	$form = "<table>";
	$form .= "<tr><td>表示列数</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[0]."' /></td></tr>";
	$form .= "<tr><td>表示行数</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[1]."' /></td></tr>";
	$form .= "<tr><td>サイト名最大文字数(半角文字数)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[2]."' /></td></tr>";
	$form .= "<tr><td>サイト説明最大文字数(半角文字数)<br />※ 0 を指定で表示しません。</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[3]."' /></td></tr>";
	$form .= "<tr><td>バナー幅指定(px)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[4]."' /></td></tr>";
	$form .= "<tr><td>バナー高指定(px)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[5]."' /></td></tr>";
	$form .= "<tr><td colspan=2>※バナー幅,バナー高共に 0 を指定の場合バナーを表示しません。<br / >どちらか一方を0以上で指定した場合は、その値のみセットされます。</td></tr>";
	$form .= "<tr><td>対象マーク 'm1' or 'm2' (無指定ですべて対象)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[6]."' /></td></tr>";
	$form .= "</table>";

	return $form;
}

// マイクロでシードを設定する
function b_yomi_s3_make_seed() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}

?>