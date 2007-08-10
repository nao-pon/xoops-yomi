<?php
// $Id: data.inc.php,v 1.1 2007/08/10 06:47:58 nao-pon Exp $

// 2005-10-10 K.OHWADA
// category

//================================================================
// What's New Module
// get aritciles from module
// yomi-search 0.84 <http://hypweb.net/>
// 2005-08-15 K.OHWADA
//================================================================

function yomi_new($limit=0, $offset=0)
{
	global $xoopsDB;

	$URL_MOD = XOOPS_URL."/modules/yomi";

// dont display notice in yomi cfg.php
	global $xoopsConfig;
	if ( !isset($xoopsConfig['dbhost']) )
	{
		$xoopsConfig['dbhost']  = XOOPS_DB_HOST;
		$xoopsConfig['dbuname'] = XOOPS_DB_USER;
		$xoopsConfig['dbname']  = XOOPS_DB_NAME;
		$xoopsConfig['dbpass']  = XOOPS_DB_PASS;
		$xoopsConfig['prefix']  = XOOPS_DB_PREFIX;
	}

	include XOOPS_ROOT_PATH.'/modules/yomi/init.php';

	$i = 0;
	$ret = array();

	$sql = "SELECT * FROM ".$xoopsDB->prefix("yomi_log")." WHERE id > 0 ORDER BY stamp DESC";
	$result = $xoopsDB->query($sql, $limit, $offset);

	while( $row = $xoopsDB->fetchArray($result) )
	{
		$id = $row['id'];
		$cat_key  = split('&',$row['category']);
		$cat_id   = $cat_key[1];

		$cat_name = '';
		if ( isset($ganes) )
		{
			$cat_name = $ganes[$cat_id];
		}

		$ret[$i]['link']     = $URL_MOD."/single_link.php?item_id=".$id;
		$ret[$i]['cat_link'] = $URL_MOD."/index.php?mode=kt&kt=".$cat_id;
		$ret[$i]['title']    = $row['title'];
		$ret[$i]['cat_name'] = $cat_name;

		$ret[$i]['id']    = $id;
		$ret[$i]['time']  = $row['stamp'];
		$ret[$i]['description'] = htmlspecialchars( $row['message'] );

		$i++;
	}

	return $ret;
}

?>
