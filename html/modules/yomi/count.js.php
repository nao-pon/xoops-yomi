<?php
// Yomi-Serch[XOOPS版] 総登録件数出力スクリプト
//              by nao-pon (http://hypweb.net/)
//
// 使用方法
// HTMLからJavaScriptとして呼び出します。
// 
// 使用例
// 総登録:<script language="JavaScript" src="http://XOOPSのルート/modules/yomi/count.js.php"></script>サイト

require 'pl/cfg.php';

$db = mysql_connect($EST['host'],$EST['sqlid'],$EST['sqlpass']);
mysql_select_db($EST['sqldb'],$db);
$query = "SELECT COUNT(*) FROM ".$EST['sqltb']."log";
//echo $query;
$result = mysql_query($query) or die("Query failed $query");
list($count) = mysql_fetch_row($result); #総登録数

echo "document.open();
document.write(\"{$count}\");
document.close();";

mysql_close($db);
?>