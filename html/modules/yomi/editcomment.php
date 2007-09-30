<?php
include("header.php");
require 'pl/temp.php';
include_once(XOOPS_ROOT_PATH."/class/xoopscomments.php");
include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

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

global $xoopsUser,$xoopsDB;

$pollcomment = new XoopsComments($xoopsDB->prefix("yomi_comments"),$comment_id);
$nohtml = $pollcomment->getVar("nohtml");
$nosmiley = $pollcomment->getVar("nosmiley");
$icon = $pollcomment->getVar("icon");
$item_id=$pollcomment->getVar("item_id");
$subject=$pollcomment->getVar("subject", "E");
$message=$pollcomment->getVar("comment", "E");
OpenTable();
include(XOOPS_ROOT_PATH."/include/commentform.inc.php");
CloseTable();
include("footer.php");
?>