<?php
// Yomi-Serch[XOOPS版] 総登録件数出力スクリプト
//              by nao-pon (http://hypweb.net/)
//
// 使用方法
// HTMLからJavaScriptとして呼び出します。
// 
// 使用例
// 総登録:<script language="JavaScript" src="http://XOOPSのルート/modules/yomi/count.js.php"></script>サイト

define('_LEGACY_PREVENT_LOAD_CORE_', TRUE); // for XOOPS Cube Legacy
$xoopsOption['nocommon'] = 1;
require '../../mainfile.php';
require 'pl/cfg.php';

$db = mysql_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS) or die(mysql_error());
mysql_select_db(XOOPS_DB_NAME,$db);

$query = "SELECT COUNT(*) FROM `".$EST['sqltb']."log`";
//echo $query;
$result = mysql_query($query) or die(mysql_error());
list($count) = mysql_fetch_row($result); #総登録数

echo "document.open();
document.write(\"{$count}\");
document.close();";

mysql_close($db);
?>