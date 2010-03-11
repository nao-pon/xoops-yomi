<?php
include("admin_header.php");
include('init.php');
xoops_cp_header();
$mymenu_fake_uri = XOOPS_URL."/modules/yomi/admin/admin.php?mode=kanri";
$_cdir = getcwd();
chdir("./admin/");
if( file_exists( './mymenu.php' ) ) include( './mymenu.php' ) ;
chdir($_cdir);

if (! empty($_POST['do_db_mente'])) {

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
} else {

	echo <<<EOD
<div>
 <form action="./db_mente.php" method="POST">
  <input type="hidden" name="do_db_mente" value="1" />
  <input type="submit" name="do_btn" value="アクセスカウンターの再集計をする" />
 </form>
</div>
EOD;

}

cp_cr();
