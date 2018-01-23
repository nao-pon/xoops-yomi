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

$link = mysqli_connect(XOOPS_DB_HOST, XOOPS_DB_USER, XOOPS_DB_PASS, XOOPS_DB_NAME) or die('MySQL Connect Error');

$query = "SELECT COUNT(*) FROM `".$EST['sqltb']."log`";

$result = mysqli_query($link, $query) or die(mysqli_error($link));
list($count) = mysqli_fetch_row($result); #総登録数
mysqli_close($link);

header('Content-Type:text/javascript');
echo "document.getElementById('yomi_bcat_count').innerHTML = '{$count}';";
