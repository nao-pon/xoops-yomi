<?php
include("header.php");
require 'pl/temp.php';
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

$pollcomment = new XoopsComments($xoopsDB->prefix("yomi_comments"),$comment_id);
$r_date = formatTimestamp($pollcomment->getVar("date"));
$r_name = XoopsUser::getUnameFromId($pollcomment->getVar("user_id"));
$r_content = _PL_POSTERC."".$r_name."&nbsp;"._PL_DATEC."".$r_date."<br /><br />";
$r_content .= $pollcomment->getVar("comment");
$r_subject=$pollcomment->getVar("subject");
$subject=$pollcomment->getVar("subject", "E");
themecenterposts($r_subject,$r_content);
$pid=$comment_id;
unset($comment_id);
$item_id=$pollcomment->getVar("item_id");
OpenTable();
include(XOOPS_ROOT_PATH."/include/commentform.inc.php");
CloseTable();
include("footer.php");
?>
