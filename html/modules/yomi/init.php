<?php
// init.php
// by nao-pon http://hypweb.net/

//error_reporting(E_ERROR);

global $ganes, $gane_top, $EST;

require_once dirname(__FILE__).'/pl/cfg.php';
require_once dirname(__FILE__).'/pl/temp.php';
include_once XOOPS_ROOT_PATH . '/modules/yomi/include/func.inc.php';

#バージョン情報
include dirname(__FILE__).'/version.php';
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

// For PHP 4
if (! function_exists('array_combine')) {
function array_combine($keys, $values)
{
	if (!is_array($keys)) {
		user_error('array_combine() expects parameter 1 to be array, ' .
			gettype($keys) . ' given', E_USER_WARNING);
		return;
	}

	if (!is_array($values)) {
		user_error('array_combine() expects parameter 2 to be array, ' .
			gettype($values) . ' given', E_USER_WARNING);
		return;
	}

	$key_count = count($keys);
	$value_count = count($values);
	if ($key_count !== $value_count) {
		user_error('array_combine() Both parameters should have equal number of elements', E_USER_WARNING);
		return false;
	}

	if ($key_count === 0 || $value_count === 0) {
		user_error('array_combine() Both parameters should have number of elements at least 0', E_USER_WARNING);
		return false;
	}

	$keys	 = array_values($keys);
	$values	 = array_values($values);

	$combined = array();
	for ($i = 0; $i < $key_count; $i++) {
		$combined[$keys[$i]] = $values[$i];
	}

	return $combined;
}
}
