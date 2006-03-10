<?php
// init.php
// by nao-pon http://hypweb.net/

error_reporting(E_ERROR);

require_once 'pl/cfg.php';
require_once 'pl/temp.php';

#バージョン情報
include ("version.php");
$ver = $_md_yomi_info['ver'];
$x_ver = $_md_yomi_info['x_ver'];

$EST['shorturl'] = (defined("YOMI_SHORTURL"))? YOMI_SHORTURL : "";

//ナビゲーションの「ホーム」へのリンク
if (empty($EST['shorturl']))
	define('YOMI_HOME_URL','<a href="'.$EST['home'].'">'._MD_YOMI_TOP.'</a>');
else
	define('YOMI_HOME_URL','<a href="'.XOOPS_URL.'/'.$EST['shorturl'].'/">'._MD_YOMI_TOP.'</a>');

#データ形式のフィールド数
$Efld=16;  #0~15まで

#CGI/HTMLリンク先表示の設定
if (empty($EST['shorturl']))
	$Ekt = $EST['cgi_path_url'].$EST['script']."?mode=kt&kt=";
else
	$Ekt = XOOPS_URL."/".$EST['shorturl']."/";
$Eend="";

?>