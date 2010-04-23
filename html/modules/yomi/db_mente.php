<?php
include("admin_header.php");
include('init.php');
xoops_cp_header();
$mymenu_fake_uri = XOOPS_URL."/modules/yomi/admin/admin.php?mode=kanri";
$_cdir = getcwd();
chdir("./admin/");
if( file_exists( './mymenu.php' ) ) include( './mymenu.php' ) ;
chdir($_cdir);

if (! empty($_POST['do_counter_mente'])) {

	$query="SELECT id FROM $EST[sqltb]rank GROUP BY id";
	$result = $xoopsDB->query($query);
	while($Rank = mysql_fetch_assoc($result)){
		$query="SELECT * FROM $EST[sqltb]log WHERE id='$Rank[id]' LIMIT 1";
		$result2 = $xoopsDB->query($query);
		$Slog = mysql_fetch_row($result2);
		if(! $Slog[0]){
			$query="DELETE FROM $EST[sqltb]rank WHERE id='$Rank[id]'";
			$xoopsDB->query($query);
			//echo $query .'<br />';
		}
	}

	$query="SELECT id FROM $EST[sqltb]rev GROUP BY id";
	$result = $xoopsDB->query($query);
	while($Rank = mysql_fetch_assoc($result)){
		$query="SELECT * FROM $EST[sqltb]log WHERE id='$Rank[id]' LIMIT 1";
		$result2 = $xoopsDB->query($query);
		$Slog = mysql_fetch_row($result2);
		if(! $Slog[0]){
			$query="DELETE FROM $EST[sqltb]rev WHERE id='$Rank[id]'";
			$xoopsDB->query($query);
			//echo $query .'<br />';
		}
	}

	$msg = '';
	$query="SELECT id,COUNT(*) AS pt FROM $EST[sqltb]rank GROUP BY id";
	$result = $xoopsDB->query($query);
	while($Rank = mysql_fetch_assoc($result)){
		$query = 'UPDATE ' . $EST['sqltb'] . 'log SET `count` = ' . $Rank['pt'] . ' WHERE `id` = ' . $Rank['id'];
		if (! $xoopsDB->query($query)) {
			$msg = 'モジュール管理よりモジュールアップデートを行ってください。';
			break;
		}
		//echo $query . '<br />';
	}

	if (! $msg) {
		$query="SELECT id,COUNT(*) AS pt FROM $EST[sqltb]rev GROUP BY id";
		$result = $xoopsDB->query($query);
		while($Rank = mysql_fetch_assoc($result)){
			$query = 'UPDATE ' . $EST['sqltb'] . 'log SET `count_rev` = ' . $Rank['pt'] . ' WHERE `id` = ' . $Rank['id'];
			$xoopsDB->query($query);
			//echo $query . '<br />';
		}
	}

	if (! $msg) $msg = 'アクセスカウンターの再集計が完了しました。';

	echo '<p>' . $msg . '</p>';
} else if (! empty($_POST['do_comments_mente'])) {
	$module_handler =& xoops_gethandler('module');
	$xoopsModule =& $module_handler->getByDirname('yomi');
	$mid = $xoopsModule->mid();
	$config_handler =& xoops_gethandler('config');
	$moduleConfig =& $config_handler->getConfigsByCat(0, $xoopsModule->mid());

	$msg = '';

	if (! isset($moduleConfig['comment_dirname'])) {
		$msg = 'モジュール管理よりモジュールアップデートを行ってください。';
	} else {
		if (!$moduleConfig['comment_dirname'] || !$moduleConfig['comment_forum_id']) {
			//XOOPS2コメント
			$sql = "SELECT `com_itemid` as 'id', count(`com_id`) as 'count' FROM `{$xoopsDB->prefix('xoopscomments')}` WHERE `com_modid`='{$mid}' GROUP BY `com_itemid`";
		} else {
			//d3forumコメント統合
			$sql = "SELECT t.topic_external_link_id as id, COUNT(p.post_id) as 'count' FROM `{$xoopsDB->prefix($moduleConfig['comment_dirname'].'_posts')}` p LEFT JOIN `{$xoopsDB->prefix($moduleConfig['comment_dirname'].'_topics')}` t ON t.topic_id=p.topic_id WHERE t.forum_id='{$moduleConfig['comment_forum_id']}' GROUP BY t.topic_external_link_id";
		}
		if ($result = $xoopsDB->query($sql)) {
			include_once XOOPS_ROOT_PATH . '/modules/yomi/include/comment_functions.php';
			while($arr = $xoopsDB->fetchArray($result)) {
				yomi_com_update($arr['id'], $arr['count']);
			}
		} else {
			$msg = "SQL文が不正です。<br />".htmlspecialchars($sql);
		}
	}
	if (! $msg) $msg = 'コメント数の再集計が完了しました。';

	echo '<p>' . $msg . '</p><hr />';
} else {

	echo <<<EOD
<h2>データーベースのメンテナンス</h2>
<br />
<div>
 <form action="./db_mente.php" method="POST">
  <input type="hidden" name="do_counter_mente" value="1" />
  <input type="submit" name="do_btn" value="アクセスカウンターの再集計をする" />
 </form>
</div>
<br />
<div>
 <form action="./db_mente.php" method="POST">
  <input type="hidden" name="do_comments_mente" value="1" />
  <input type="submit" name="do_btn" value="コメント数の再集計をする" />
 </form>
</div>
<br />
<hr />
EOD;

}

cp_cr();
