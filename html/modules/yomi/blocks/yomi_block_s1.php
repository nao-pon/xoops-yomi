<?php
////////////////////// special blocks ///////////////////////
include_once(XOOPS_ROOT_PATH."/modules/yomi/blocks/yomi_block_func.php");
function b_yomi_new_s($options)
{
	global $xoopsDB;

	$block = array();

	$block['title'] = "新着サイト(Yomiサーチ)";
	$block['content'] = "";

	//$kensu = $options[0];#表示する件数
	$kensu = $options[0] * $options[1];#表示する件数
	$query="SELECT * FROM ".$xoopsDB->prefix("yomi_log")." WHERE id > 0 AND renew = 0 ORDER BY stamp DESC LIMIT ".$kensu;
	$result = $xoopsDB->query($query) or die("Query failed rank109 $query");

	$block['content'] = b_yomi_show_cols($result,$options[0],$options[2],$options[3],$options[4],$options[5]);

	return $block;

}
function b_yomi_renew_s($options)
{
	global $xoopsDB;

	$block = array();

	$block['title'] = "更新サイト(Yomiサーチ)";
	$block['content'] = "";

	$kensu = $options[0] * $options[1];#表示する件数
	$query="SELECT * FROM ".$xoopsDB->prefix("yomi_log")." WHERE id > 0 AND renew = 1 ORDER BY stamp DESC LIMIT ".$kensu;
	$result = $xoopsDB->query($query) or die("Query failed rank109 $query");

	$block['content'] = b_yomi_show_cols($result,$options[0],$options[2],$options[3],$options[4],$options[5]);

	return $block;

}

function b_yomi_s_edit($options) {
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
	$form .= "</table>";
	
	return $form;
}
?>