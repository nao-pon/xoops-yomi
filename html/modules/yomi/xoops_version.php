<?php

$modversion['name'] = _MI_YOMI_NAME;
$modversion['dirname'] = "yomi";
include(XOOPS_ROOT_PATH."/modules/".$modversion['dirname']."/version.php");
$modversion['version'] = $_md_yomi_info['x_ver'];
$modversion['description'] = "Yomi-Search[XOOPS]";
$modversion['credits'] = "<a href='http://hypweb.net/'><b>\"Yomi-Search[XOOPS]\"</b></a><br />Based on <a href='http://sql.s28.xrea.com:8080/'>Yomi-Search[PHP]</a><br /><br /><b>\"Yomi-Search[PHP]\"</b><br />Baced on <a href='http://yomi.pekori.to/'>Yomi-Search Ver4.19</a>";
$modversion['author'] = "nao-pon <a href='http://hypweb.net/'>http://hypweb.net/</a>";
$modversion['help'] = "";
$modversion['license'] = "Consent is required";
$modversion['official'] = 1;

//管理画面アイコン好きなほうを選択
//オリジナルバージョン(ダサダサ)
//$modversion['image'] = "img/yomi_logo.gif";
//DAN さん作(XOOPS2風でかっこいい)[http://www.gameha.com/]
$modversion['image'] = "img/ys_slogo.png";

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/yomi.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "yomi_key";
$modversion['tables'][1] = "yomi_log";
$modversion['tables'][2] = "yomi_rank";
$modversion['tables'][3] = "yomi_rev";
$modversion['tables'][4] = "yomi_votedata";
$modversion['tables'][5] = "yomi_comments";

//Admin things
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin.php?mode=kanri";
$modversion['adminmenu'] = "admin/menu.php";

// Menu
$modversion['hasMain'] = 1;
$modversion['sub'][0]['name'] = _MI_YOMI_REG;
$modversion['sub'][0]['url'] = "regist_ys.php?mode=new";
$modversion['sub'][1]['name'] = _MI_YOMI_EDIT;
$modversion['sub'][1]['url'] = "regist_ys.php?mode=enter";
$modversion['sub'][2]['name'] = _MI_YOMI_NEW;
$modversion['sub'][2]['url'] = "?mode=new";
$modversion['sub'][3]['name'] = _MI_YOMI_RENEW;
$modversion['sub'][3]['url'] = "?mode=renew";
$modversion['sub'][4]['name'] = _MI_YOMI_RANK;
$modversion['sub'][4]['url'] = "rank.php";
$modversion['sub'][5]['name'] = _MI_YOMI_MAP;
$modversion['sub'][5]['url'] = "sitemap.php";
$modversion['sub'][6]['name'] = _MI_YOMI_HELP;
$modversion['sub'][6]['url'] = "regist_ys.php?mode=help";

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "xoops_search.inc.php";
$modversion['search']['func'] = "yomi_search";

// Blocks
$modversion['blocks'][1]['file'] = "yomi_block.php";
$modversion['blocks'][1]['name'] = _MI_YOMI_BNAME1;
$modversion['blocks'][1]['description'] = "Yomi-Search Category's";
$modversion['blocks'][1]['show_func'] = "b_yomi_show";
$modversion['blocks'][1]['edit_func'] = "b_yomi_edit";
$modversion['blocks'][1]['options'] = "4";
$modversion['blocks'][1]['can_clone'] = true ;

$modversion['blocks'][2]['file'] = "yomi_block.php";
$modversion['blocks'][2]['name'] = _MI_YOMI_BNAME2;
$modversion['blocks'][2]['description'] = "Yomi-Search Tops";
$modversion['blocks'][2]['show_func'] = "b_yomi_top";
$modversion['blocks'][2]['edit_func'] = "b_yomi_top_edit";
$modversion['blocks'][2]['options'] = "10|14";
$modversion['blocks'][2]['can_clone'] = true ;

$modversion['blocks'][3]['file'] = "yomi_block.php";
$modversion['blocks'][3]['name'] = _MI_YOMI_BNAME3;
$modversion['blocks'][3]['description'] = "Yomi-Search NewLinks";
$modversion['blocks'][3]['show_func'] = "b_yomi_new";
$modversion['blocks'][3]['edit_func'] = "b_yomi_new_edit";
$modversion['blocks'][3]['options'] = "10";
$modversion['blocks'][3]['can_clone'] = true ;

$modversion['blocks'][4]['file'] = "yomi_block_s1.php";
$modversion['blocks'][4]['name'] = "新着サイト";
$modversion['blocks'][4]['description'] = "Yomi-Search NewLinks";
$modversion['blocks'][4]['show_func'] = "b_yomi_new_s";
$modversion['blocks'][4]['edit_func'] = "b_yomi_s_edit";
$modversion['blocks'][4]['options'] = "4|1|20|60|88|31";
$modversion['blocks'][4]['can_clone'] = true ;

$modversion['blocks'][5]['file'] = "yomi_block_s1.php";
$modversion['blocks'][5]['name'] = "更新サイト";
$modversion['blocks'][5]['description'] = "Yomi-Search NewLinks";
$modversion['blocks'][5]['show_func'] = "b_yomi_renew_s";
$modversion['blocks'][5]['edit_func'] = "b_yomi_s_edit";
$modversion['blocks'][5]['options'] = "1|5|20|60|88|31";
$modversion['blocks'][5]['can_clone'] = true ;

$modversion['blocks'][6]['file'] = "yomi_block_s2.php";
$modversion['blocks'][6]['name'] = "In + Out ランキング #1";
$modversion['blocks'][6]['description'] = "Yomi-Search In+Out #1";
$modversion['blocks'][6]['show_func'] = "b_yomi_s2";
$modversion['blocks'][6]['edit_func'] = "b_yomi_s2_edit";
$modversion['blocks'][6]['options'] = "1|5|20|60|88|31|14|50|50|";
$modversion['blocks'][6]['can_clone'] = true ;

$modversion['blocks'][7]['file'] = "yomi_block_s2.php";
$modversion['blocks'][7]['name'] = "In + Out ランキング #2";
$modversion['blocks'][7]['description'] = "Yomi-Search In+Out #2";
$modversion['blocks'][7]['show_func'] = "b_yomi_s2";
$modversion['blocks'][7]['edit_func'] = "b_yomi_s2_edit";
$modversion['blocks'][7]['options'] = "1|5|20|60|88|31|14|50|50|";

$modversion['blocks'][8]['file'] = "yomi_block_s3.php";
$modversion['blocks'][8]['name'] = "ランダム表示(Yomi) #1";
$modversion['blocks'][8]['description'] = "Yomi-Search Randam #1";
$modversion['blocks'][8]['show_func'] = "b_yomi_s3";
$modversion['blocks'][8]['edit_func'] = "b_yomi_s3_edit";
$modversion['blocks'][8]['options'] = "2|3|20|60|88|31|m1";
$modversion['blocks'][8]['can_clone'] = true ;

$modversion['blocks'][9]['file'] = "yomi_block_s3.php";
$modversion['blocks'][9]['name'] = "ランダム表示(Yomi) #2";
$modversion['blocks'][9]['description'] = "Yomi-Search Randam #1";
$modversion['blocks'][9]['show_func'] = "b_yomi_s3";
$modversion['blocks'][9]['edit_func'] = "b_yomi_s3_edit";
$modversion['blocks'][9]['options'] = "2|3|20|60|88|31|m2";

$modversion['blocks'][10]['file'] = "yomi_block_s3.php";
$modversion['blocks'][10]['name'] = "ランダム表示(Yomi) #3";
$modversion['blocks'][10]['description'] = "Yomi-Search Randam #1";
$modversion['blocks'][10]['show_func'] = "b_yomi_s3";
$modversion['blocks'][10]['edit_func'] = "b_yomi_s3_edit";
$modversion['blocks'][10]['options'] = "2|3|20|60|88|31|";

$xoopsConfig['anonpost'] = 1;

// On Update
if( ! empty( $_POST['fct'] ) && ! empty( $_POST['op'] ) && $_POST['fct'] == 'modulesadmin' && $_POST['op'] == 'update_ok' && $_POST['dirname'] == $modversion['dirname'] ) {
	include dirname( __FILE__ ) . "/include/onupdate.inc.php" ;
}
?>
