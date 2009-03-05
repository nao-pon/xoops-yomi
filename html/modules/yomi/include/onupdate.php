<?php
/**
 * On module update function
 * @version $Rev: 255 $ $Date: 2009/03/05 04:24:31 $
 * @link $URL: https://ajax-discuss.svn.sourceforge.net/svnroot/ajax-discuss/openid/trunk/openid/include/onupdate.php $
 */

function xoops_module_update_yomi ( $module ) {
	$db =& Database::getInstance();
	
	$table = $db->prefix('yomi_rev');
    if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array('id' => true, 'time' => true);
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        foreach (array_keys($keys) as $key) {
        	$db->query('ALTER TABLE `' . $table . '` ADD INDEX(`'.$key.'`)');
        }
    }

	$table = $db->prefix('yomi_rank');
    if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array('id' => true, 'time' => true);
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        foreach (array_keys($keys) as $key) {
        	$db->query('ALTER TABLE `' . $table . '` ADD INDEX(`'.$key.'`)');
        }
    }

	return TRUE;
}