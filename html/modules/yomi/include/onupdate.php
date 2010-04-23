<?php
/**
 * On module update function
 * @version $Rev: 255 $ $Date: 2010/04/23 02:14:37 $
 * @link $URL: https://ajax-discuss.svn.sourceforge.net/svnroot/ajax-discuss/openid/trunk/openid/include/onupdate.php $
 */

function xoops_module_update_yomi ( $module ) {
	define('_NOW_YOMI_ONUPDATE', true);

	$db =& Database::getInstance();

	$table = $db->prefix("yomi_log");
	if (! $db->query('SELECT `uid` FROM ' . $table)) {
		$sql = "ALTER TABLE `".$db->prefix("yomi_log")."`
 CHANGE `title` `title` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `name` `name` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `mail` `mail` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `category` `category` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `banner` `banner` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `url` `url` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `passwd` `passwd` VARCHAR( 255 ) DEFAULT NULL ,
 CHANGE `keywd` `keywd` VARCHAR( 255 ) DEFAULT NULL,
 CHANGE `message` `message` TEXT DEFAULT NULL,
 CHANGE `comment` `comment` TEXT DEFAULT NULL";
		$db->query($sql);
		$db->query("ALTER TABLE `".$table."` ADD `uid` INT( 5 ) UNSIGNED DEFAULT '0' NOT NULL");
	}


	//テーブルへ項目追加 Ver 0.84b6以降
	$table = $db->prefix("yomi_log");
	if (! $db->query('SELECT `rating` FROM ' . $table)) {
		$sql = "ALTER TABLE `".$table."`
 ADD rating double(6,4) NOT NULL default '0.0000',
 ADD votes int(11) unsigned NOT NULL default '0',
 ADD comments int(11) unsigned NOT NULL default '0'";
 		$db->query($sql);
	}
	if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array('uid' => true);
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        foreach (array_keys($keys) as $key) {
        	$db->query('ALTER TABLE `' . $table . '` ADD INDEX(`'.$key.'`)');
        }
    }

	//テーブル追加 Ver 0.84b6以降
	$table = $db->prefix("yomi_votedata");
	if (! $db->query('SELECT * FROM ' . $table)) {
		$sql = "CREATE TABLE `".$table."`(
  ratingid int(11) unsigned NOT NULL auto_increment,
  lid int(11) unsigned NOT NULL default '0',
  ratinguser int(11) unsigned NOT NULL default '0',
  rating tinyint(3) unsigned NOT NULL default '0',
  ratinghostname varchar(60) NOT NULL default '',
  ratingtimestamp int(10) NOT NULL default '0',
  PRIMARY KEY  (ratingid),
  KEY ratinguser (ratinguser),
  KEY ratinghostname (ratinghostname)
) TYPE=MyISAM;";
 		$db->query($sql);
	}


	// Ver 0.91 ランキング集計方法変更
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

	$table = $db->prefix('yomi_log');
	if(! $db->query('SELECT `count` FROM ' . $table)) {
		$db->query(
'ALTER TABLE `' . $table . "`
 ADD `count` int(11) unsigned NOT NULL default '0',
 ADD `count_rev` int(11) unsigned NOT NULL default '0'"
		);

	}

	// Ver 0.92 XOOPS2コメントシステム対応アップグレード
	$table = $db->prefix('yomi_comments');
	$sql = 'SELECT * FROM `'.$table.'` LIMIT 1';
	if ($db->query($sql)) {
		include dirname(__FILE__) . '/comment_upgread.php';
	}

	return TRUE;
}