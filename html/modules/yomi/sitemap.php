<?php
if (is_readable("shorturl.php")) include("shorturl.php");
// For XOOPS
include("header.php");

include('init.php');
?>
<!-- あ -->
<link rel="stylesheet" href="<?php echo $EST['html_path_url']?>style.css" type="text/css">
<script language="javascript">
<!--
function ch_type(sel){
// onchangeでウィンドウを開く方法を選択
var form=document.form1;
var open_type=sel.options[sel.selectedIndex].value;
if(open_type==1){form.target="_blank";}
else{form.target="";}
}
//-->
</script>

<a name=top></a>
<!-- メニューバー -->
<div align=right><font id=small>
<?php
menu_bar();
?>
</font></div>
<hr>
<!-- ナビゲーションバー -->
<?php echo YOMI_HOME_URL?> &gt; 
<table width=100%><tr><td id="title-bar">
<b>サイトマップ</b>
</td></tr></table>
<?php
#ヘッダスペース
head_sp();
?>
<!-- ページ中段の検索フォーム -->
<hr>
<table width="100%" cellpadding=8 cellspacing=0 border=0>
<tr id="mid-bar">
<td colspan=2>
	<form action="<?php echo $EST['search']?>" method=get  target="" name="form1">
	<input type=hidden name=mode value=search>
	<input type=hidden name=page value=1>
	<input type=hidden name=sort value="<?php echo $_POST['sort']?>">
	<input type=text name=word value="<?php echo $_POST['word']?>" size="20"><input type=submit value=" 検 索 "> <input type=reset value="リセット">
	&nbsp;
	<select name=method>
		<option value="and" selected>すべての語を含む
		<option value="or">いずれかの語を含む
	</select>
	<select name=engine>
<?php
search_form();
?>
	</select>
	<select name=open_type onchange=ch_type(this)>
		<option value="0"selected>次ページで
		<option value="1">別窓で
	</select>
	<font id=small>
	 [<a href="<?php echo $EST['search']?>">More</a>]
	 [<a href="<?php echo $EST['search']?>?window=_blank">New Window</a>]
	</font>
	<input type=hidden name=hyouji value="30">
</td>
</tr>
<tr><td></form>
</td><td align=right>	<font id=small>
	[<a href="<?php echo $EST['cgi_path_url']?>regist_ys.php?mode=help">ヘルプ</a>] 
	[<a href="<?php echo $EST['cgi_path_url']?>regist_ys.php?mode=enter">修正・削除</a>] 
</font></td></tr>
</table>
<ul>
<table cellpadding=3>
<?php
gane_guide(); #説明文をロード
foreach($ganes as $key=>$val){
	echo "<tr valign=bottom nowrap><td nowrap>";
	if(!strstr($key,"_")){ #トップカテゴリの場合
		echo "<br><br>"; 
		echo "<font color=\"#FFFFFF\">$key</font><font size=\"+1\">■</font><a href=\"".yomi_makelink($key)."\"><font size=\"+1\"><b>$val</b></font></a>\n"; #背景色と合わせる
	}
	else{
		echo "<font color=\"#FFFFFF\">$key</font><a href=\"".yomi_makelink($key)."\">$val</a>\n"; #背景色と合わせる
	}
	echo "</td><td nowrap>";
	if (isset($KTEX[$key])) {
		echo "$KTEX[$key]</td></tr>";
	} else {
		echo "</td></tr>";
	}
}
?>
</table>
</ul>
<hr>
<?php
#フッタスペース
foot_sp();
include("footer.php");
?>
<?php
exit;
?>