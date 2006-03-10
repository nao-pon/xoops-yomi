<?php
function install_header(){
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
	<title>Yomiサーチ　データベースアップグレード</title>
	<meta http-equiv='Content-Type' content='text/html; charset=EUC-JP' />
	</head>
	<body>
	<div style='text-align:center'><img src='img/yomi_logo.gif' />
	<h3>Yomiサーチ　データベースアップグレード</h3><br />
<?php
}

function install_footer(){
	global $PHP_SELF;
?>
	<br /><br />

	<a href="../../">XOOPSトップへ戻る。</a>
	</div>
	</body>
	</html>
<?php
}
if ( !isset($action) || $action == "" ) {
	$action = "message";
}

if ( $action == "message" ) {
	install_header();
	echo "
	<table width='70%' border='0'><tr><td colspan='2'>Yomiサーチ [ XOOPS ] データベースを最新のバージョン用にアップグレードします。</td></tr>
	<tr><td>○</td><td><span style='color:#ff0000;font-weight:bold;'>事前にデータベースのバックアップをすることを強くお勧めします。</span></td></tr>
	</table>
	";
	echo "<p>以下の「アップグレード開始」をクリックすると、アップグレードされます。</p>";
	echo "<form action='".$PHP_SELF."' method='post'><input type='submit' value='アップグレード開始' /><input type='hidden' value='upgrade' name='action' /></form>";
	install_footer();
	exit();
}

if ( $action == "upgrade" ) {
	include("../../mainfile.php");
	install_header();
	echo "<h4>データベースアップグレード開始</h4>\n";
	$error = array();
	
	echo "<p>...Updating</p>\n";

	//各項目サイズの変更
	$sql = "ALTER TABLE `".$xoopsDB->prefix("yomi_log")."`
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

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$error[] = "Failed ALTER TABLE ".$xoopsDB->prefix("yomi_log")."";
	}

	//テーブルへ項目追加
	$result = $xoopsDB->queryF("ALTER TABLE `".$xoopsDB->prefix("yomi_log")."` ADD `uid` INT( 5 ) UNSIGNED DEFAULT '0' NOT NULL");
	if (!$result) {
		$error[] = "".$xoopsDB->prefix("yomi_log")."はアップグレード済みです。[1]<br />";
	} else {

		$result = $xoopsDB->queryF("ALTER TABLE `".$xoopsDB->prefix("yomi_log")."` ADD INDEX ( `uid` )");
		if (!$result) {
			$error[] = "Failed ALTER TABLE ".$xoopsDB->prefix("yomi_log")." uid";
		}
	}

	//テーブルへ項目追加 Ver 0.84b6以降
	$sql = "ALTER TABLE `".$xoopsDB->prefix("yomi_log")."`
  ADD rating double(6,4) NOT NULL default '0.0000',
  ADD votes int(11) unsigned NOT NULL default '0',
  ADD comments int(11) unsigned NOT NULL default '0'";

	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$error[] = "".$xoopsDB->prefix("yomi_log")."はアップグレード済みです。[2]<br />";
	} else {

		$result = $xoopsDB->queryF("ALTER TABLE `".$xoopsDB->prefix("yomi_log")."` ADD INDEX ( `uid` )");
		if (!$result) {
			$error[] = "Failed ALTER TABLE ".$xoopsDB->prefix("yomi_log")." uid";
		}
	}

	//テーブル追加 Ver 0.84b6以降
	$sql = "CREATE TABLE `".$xoopsDB->prefix("yomi_votedata")."`(
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
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$error[] = "".$xoopsDB->prefix("yomi_votedata")."は作成済みです。<br />";
	} else {

		$result = $xoopsDB->queryF("ALTER TABLE `".$xoopsDB->prefix("yomi_log")."` ADD INDEX ( `uid` )");
		if (!$result) {
			$error[] = "Failed ALTER TABLE ".$xoopsDB->prefix("yomi_log")." uid";
		}
	}

	//テーブル追加 Ver 0.84b6以降
	$sql = "CREATE TABLE `".$xoopsDB->prefix("yomi_comments")."`(
  comment_id int(8) unsigned NOT NULL auto_increment,
  pid int(8) unsigned NOT NULL default '0',
  item_id int(8) unsigned NOT NULL default '0',
  date int(10) unsigned NOT NULL default '0',
  user_id int(5) unsigned NOT NULL default '0',
  ip varchar(15) NOT NULL default '',
  subject varchar(255) NOT NULL default '',
  comment text NOT NULL,
  nohtml tinyint(1) unsigned NOT NULL default '0',
  nosmiley tinyint(1) unsigned NOT NULL default '0',
  noxcode tinyint(1) unsigned NOT NULL default '0',
  icon varchar(25) NOT NULL default '',
  PRIMARY KEY  (comment_id),
  KEY pid (pid),
  KEY item_id (item_id),
  KEY user_id (user_id),
  KEY subject (subject(40))
) TYPE=MyISAM;";
	$result = $xoopsDB->queryF($sql);
	if (!$result) {
		$error[] = "".$xoopsDB->prefix("yomi_comments")."は作成済みです。<br />";
	} else {

		$result = $xoopsDB->queryF("ALTER TABLE `".$xoopsDB->prefix("yomi_log")."` ADD INDEX ( `uid` )");
		if (!$result) {
			$error[] = "Failed ALTER TABLE ".$xoopsDB->prefix("yomi_log")." uid";
		}
	}
	
if ( count($error) ) {

	foreach( $error as $err ) {
		echo $err."<br>";
	}
	echo "追加アップグレードは完了しました。<br />";
	echo "以後、アップグレードスクリプトは必要ないので削除してください。<br /><br />";
} else {
	echo "アップグレードは完了しました。<br />";
	echo "以後、アップグレードスクリプトは必要ないので削除してください。<br /><br />";

}
	install_footer();
}
?>