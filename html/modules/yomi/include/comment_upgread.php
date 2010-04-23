<?php
/*
 * Created on 2010/04/22 by nao-pon http://xoops.hypweb.net/
 * $Id: comment_upgread.php,v 1.1 2010/04/23 02:14:37 nao-pon Exp $
 */

if (! defined('_NOW_YOMI_ONUPDATE')) exit();

$sql = 'SELECT `com_id` FROM '.$db->prefix('xoopscomments').' ORDER BY `com_id` DESC LIMIT 1';
if ($result = $db->query($sql)) {
	$result = $db->fetchArray($result);
	$offset = $result['com_id'];
} else {
	$offset = '0';
}

$table = $db->prefix('yomi_comments');
$sql = 'SELECT * FROM `'.$table.'` ORDER BY comment_id ASC';

if ($result = $db->query($sql)) {
	$com_modid = $module->getVar('mid');
	$xoopscomments = $db->prefix('xoopscomments');
	while($arr = $db->fetchArray($result)) {
		//var_dump($arr);
		$com_id = $arr['comment_id'] + $offset;
		$com_pid = $arr['pid'];
		if (! $com_pid) {
			$com_rootid = $com_id;
			$rootids[$com_id] = $com_rootid;
		} else {
			$com_pid += $offset;
			$com_rootid = $rootids[$com_pid];
			$rootids[$com_id] = $com_rootid;
		}
		$com_itemid = $arr['item_id'];
		$com_icon = $arr['icon'];
		$com_created = $arr['date'];
		$com_modified = $com_created;
		$com_uid = $arr['user_id'];
		$com_ip = $arr['ip'];
		$com_title = addslashes($arr['subject']);
		$com_text = addslashes($arr['comment']);
		$com_sig = 0;
		$com_status = XOOPS_COMMENT_ACTIVE;
		$com_exparams = '';
		$dohtml = ($arr['nohtml'])? 0 : 1;
		$dosmiley = ($arr['nosmiley'])? 0 : 1;
		$doxcode = ($arr['noxcode'])? 0 : 1;
		$doimage = 1;
		$dobr = 1;
		$sql = sprintf("INSERT INTO %s (com_id, com_pid, com_modid, com_icon, com_title, com_text, com_created, com_modified, com_uid, com_ip, com_sig, com_itemid, com_rootid, com_status, dohtml, dosmiley, doxcode, doimage, dobr) VALUES (%u, %u, %u, '%s', '%s', '%s', %u, %u, %u, '%s', %u, %u, %u, %u, %u, %u, %u, %u, %u)", $xoopscomments,
		                               $com_id,$com_pid,$com_modid,$com_icon,$com_title,$com_text,$com_created,$com_modified,$com_uid,$com_ip,$com_sig,$com_itemid,$com_rootid,$com_status,$dohtml,$dosmiley,$doxcode,$doimage,$dobr);

		if ($db->query($sql)) {
			// echo htmlspecialchars($sql) . '<br />';
		}
	}
	$sql = 'DROP TABLE `'.$table.'';
	$db->query($sql);
}