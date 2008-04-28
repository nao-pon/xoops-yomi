<?php
/*
 * Created on 2008/04/28 by nao-pon http://hypweb.net/
 * $Id: dbcheck.inc.php,v 1.1 2008/04/28 14:00:18 nao-pon Exp $
 */

function check_db_admin() {
	$db =& Database::getInstance();
	
	// Add Keys
	$check = array();
	$check['yomi_rank'][] = 'id';
	$check['yomi_rank'][] = 'time';
	$check['yomi_rev'][] = 'id';
	$check['yomi_rev'][] = 'time';
	foreach ($check as $table => $keys) {
		$table = $db->prefix($table);
		foreach($keys as $key) {
			$sql = 'SHOW COLUMNS FROM `' . $table . '` LIKE \'' . $key . '\'';
			if ($result = $db->queryF($sql)) {
				$res = $db->fetchArray($result);
				if (empty($res['Key'])) {
					$sql = 'ALTER TABLE `' . $table . '` ADD INDEX (`' . $key . '`)';
					$db->queryF($sql);
				}
			}
		}
	}
	
	// Set build_time if "0".
	$sql = 'SELECT `id`, `stamp` FROM `' . $db->prefix('yomi_log') . '` WHERE `build_time` <=0';
	if ($result = $db->queryF($sql)) {
		while(list($id, $stamp) = $db->fetchRow($result)) {
			$sql = 'UPDATE `' . $db->prefix('yomi_log') . '` SET `build_time` = ' . $stamp;
			$db->queryF($sql);
		} 
	}
}
?>