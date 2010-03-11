<?php
/*
 * Created on 2008/06/23 by nao-pon http://hypweb.net/
 * License: GPL v2 or (at your option) any later version
 * $Id: func.inc.php,v 1.3 2010/03/11 00:34:32 nao-pon Exp $
 */

if( ! class_exists( 'HypCommonFunc' ) ) {
	if (defined('XOOPS_TRUST_PATH') && file_exists(XOOPS_TRUST_PATH . '/class/hyp_common/hyp_common_func.php')) {
		include_once XOOPS_TRUST_PATH . '/class/hyp_common/hyp_common_func.php';
	} else {
		include_once XOOPS_ROOT_PATH . '/modules/yomi/include/hyp_common/hyp_common_func.php';
	}
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
	
	if ($d->rc !== 200 || !$d->data) {
		$d->data = join('', file(XOOPS_ROOT_PATH."/modules/yomi/img/noimage.gif"));
	}
	
	if($fp = fopen($filename, "wb")) {
		fwrite($fp, $d->data);
		fclose($fp);
	}
	
	$imagesize = getimagesize($filename);
	
	if (!isset($imagesize[2]) || $imagesize[2] < 1 || $imagesize[2] > 3) {
		$d->data = join('', file(XOOPS_ROOT_PATH."/modules/yomi/img/noimage.gif"));
		if($fp = fopen($filename, "wb")) {
			fwrite($fp, $d->data);
			fclose($fp);
		}
		clearstatcache();
		$imagesize = getimagesize($filename);
	}
	
	return array($imgurl, getimagesize($filename));
}

function yomi_get_banner_uri($banner, $id) {
	if (!$banner) return FALSE;
	list($imguri, $imgsize) = yomi_get_banner_cache ($banner, $id);
	return $imguri;
}

function yomi_get_favicon($url, $alt = '') {
	if (!defined('XOOPS_TRUST_PATH') || !is_file(XOOPS_TRUST_PATH . '/class/hyp_common/favicon/favicon.php')) {
		return $alt;
	}
	return '<img src="' . XOOPS_URL . '/class/hyp_common/favicon.php?url=' . rawurlencode($url) . '" width="16" height="16" alt="'.$alt.'" />';
}
?>