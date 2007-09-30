<?php
include("header.php");
include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
include_once(XOOPS_ROOT_PATH."/class/xoopscomments.php");

// パラメータを変数にセット
$vars = array_merge($_POST,$_GET);
$prms = array('item_id','comment_id','mode','order','ok');
foreach ($prms as $prm)
{
	if (isset($vars[$prm]))
	{
		if ($prm == 'item_id' || $prm == 'comment_id')
		{
			$vars[$prm] = intval($vars[$prm]);
		}
		$$prm = $vars[$prm];
	}
	else
	{
		$$prm = FALSE;
	}
}

$q = "select title from ".$xoopsDB->prefix("yomi_log")." where id=$item_id";
$result=$xoopsDB->query($q);
list($ltitle)=$xoopsDB->fetchRow($result);
$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
//$subject = $myts->oopsHtmlSpecialChars($ltitle);
$subject = $myts->makeTboxData4Show($ltitle);
$pid = 0;
OpenTable();
echo "<h4>・ ".$subject." のコメント投稿</h4>";
include(XOOPS_ROOT_PATH."/include/commentform.inc.php");
CloseTable();
include("footer.php");
?>