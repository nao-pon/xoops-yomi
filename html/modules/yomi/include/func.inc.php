<?php
/*
 * Created on 2008/06/23 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: func.inc.php,v 1.1 2008/06/25 23:42:09 nao-pon Exp $
 */

if( ! class_exists( 'HypCommonFunc' ) ) {
	include_once(XOOPS_ROOT_PATH."/modules/yomi/include/hyp_common/hyp_common_func.php");
}

// URLから画像キャッシュURIを返す
function yomi_get_banner_cache ($url, $id)
{
	$false = array('', FALSE);
	// URLチェック
	if (!preg_match("#^https?://.+\.(jpe?g|png|gif)$#i",$url)) return $false;
	
	// 画像ディレクトリ パーミッション 666
	$dir = XOOPS_ROOT_PATH."/modules/yomi/blocks/logos/";
	$imgurl = XOOPS_URL."/modules/yomi/blocks/logos/";
	// キャッシュ時間(h)
	$cache_h = 48;
	$ext = preg_replace("/^.*(\.[^.]+)/","$1",$url);
	$filename = $dir.$id.$ext;
	$imgurl = $imgurl.$id.$ext;
	if (file_exists($filename) && filemtime($filename) + $cache_h*3600 > time())
		return array($imgurl, getimagesize($filename));
	
	// 指定ファイルをキャッシュする
	
	$d = new Hyp_HTTP_Request();
	$d->url = $url;
	$d->connect_timeout = 3;
	$d->read_timeout = 2;
	$d->get();
	
	if ($d->rc !== 200 || !$d->data) return $false;
	
	if($fp = fopen($filename, "wb")) {
		fwrite($fp, $d->data);
		fclose($fp);
	}
	
	$imagesize = getimagesize($filename);
	
	if (!isset($imagesize[2]) || $imagesize[2] < 1 || $imagesize[2] > 3) {
		unlink($filename);
		return $false;
	}
	
	return array($imgurl, getimagesize($filename));
}

function yomi_get_banner_uri($banner, $id) {
	list($imguri, $imgsize) = yomi_get_banner_cache ($banner, $id);
	return $imguri;
}
?>