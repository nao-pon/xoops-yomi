<?php
// init.php
// by nao-pon http://hypweb.net/

//error_reporting(E_ERROR);

global $ganes, $gane_top, $EST;

define('_MI_YOMI_ROOT_PATH', dirname(__FILE__));
require_once _MI_YOMI_ROOT_PATH . '/pl/cfg.php';
require_once _MI_YOMI_ROOT_PATH . '/pl/temp.php';
include_once _MI_YOMI_ROOT_PATH . '/include/func.inc.php';

#バージョン情報
include _MI_YOMI_ROOT_PATH . '/version.php';
$ver = $_md_yomi_info['ver'];
$x_ver = $_md_yomi_info['x_ver'];

$EST['shorturl'] = (defined("YOMI_SHORTURL"))? YOMI_SHORTURL : "";

//ナビゲーションの「ホーム」へのリンク
if (! defined('YOMI_HOME_URL')) {
	if (empty($EST['shorturl']))
		define('YOMI_HOME_URL','<a href="'.$EST['home'].'">'._MD_YOMI_TOP.'</a>');
	else
		define('YOMI_HOME_URL','<a href="'.XOOPS_URL.'/'.$EST['shorturl'].'/">'._MD_YOMI_TOP.'</a>');
}

#データ形式のフィールド数
$Efld=16;  #0〜15まで

$Eend="";

// Setup $EST
if (! defined('_MD_YOMI_INIT_LOADED')) {
	define('_MD_YOMI_INIT_LOADED', TRUE);
	$EST['script'] = $EST['cgi_path_url'] . basename($EST['script']);
	$EST['search'] = $EST['cgi_path_url'] . basename($EST['search']);
	$EST['rank'] = $EST['cgi_path_url'] . basename($EST['rank']);
	$EST['admin'] = $EST['cgi_path_url'] . basename($EST['admin']);
	if (strpos($EST['html_path_url'], $EST['cgi_path_url']) !== 0)
		$EST['html_path_url'] = $EST['cgi_path_url'] . $EST['html_path_url'];
	if (strpos($EST['img_path_url'], $EST['cgi_path_url']) !== 0)
		$EST['img_path_url'] = $EST['cgi_path_url'] . $EST['img_path_url'];
}

#CGI/HTMLリンク先表示の設定
if (empty($EST['shorturl']))
	$Ekt = $EST['script'] . "?mode=kt&kt=";
else
	$Ekt = XOOPS_URL."/".$EST['shorturl']."/";
