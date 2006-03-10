<?php
include_once(XOOPS_ROOT_PATH."/modules/yomi/blocks/yomi_block_func.php");
function b_yomi_s2($options) {

	global $xoopsDB;

	$block = array();

	$block['title'] = "IN+OUTランキング";
	$block['content'] = "";

	$mode = "";	#人気ランキング
	//$mode = "rank_bf";	#前回の人気ランキング
	//$mode = "all";	#人気ランキング(累計)
	$kensu = $options[0]*$options[1];#表示する件数
	$kikan = $options[6];#集計期間（日)
	// $options[7] : カウント対象上位？件(In)
	// $options[8] : カウント対象上位？件(Out)
	$nolists = (!empty($options[9]))? explode(",",$options[9]) : "";
	$q_nolists = array();
	$q_nolist = "";
	if (is_array($nolists))
	{
		foreach($nolists as $nolist)
		{
			$q_nolists[] = "(l.category NOT LIKE \"%&{$nolist}%\")";
		}
		if (count($q_nolists))
			$q_nolist = join(" AND ",$q_nolists);
	}
	
	$log_lines=array(); $Clog=0;

	$time=time();
	if($mode == "all"){
		$start=0;
		$end=$time;
	}
	elseif($mode == "rank_bf"){
		$start=$time-$kikan*172800;
		$end=$time-$kikan*86400+1;
	}
	else {
		$start=$time-$kikan*86400;
		$end=$time;
	}
	$out=$in=$ranking = array();
	
	if (!empty($options[7]))
	{
		$limit = " LIMIT {$options[7]}";
		if ($q_nolist)
			$query="SELECT r.id,COUNT(r.id) AS pt, l.category FROM ".$xoopsDB->prefix("yomi_rank")." r LEFT JOIN ".$xoopsDB->prefix("yomi_log")." l ON r.id = l.id WHERE (time BETWEEN $start AND $end) AND {$q_nolist} GROUP BY id ORDER BY pt DESC{$limit}";
		else
			$query="SELECT id,COUNT(*) AS pt FROM ".$xoopsDB->prefix("yomi_rank")." WHERE time BETWEEN $start AND $end GROUP BY id ORDER BY pt DESC{$limit}";
		$result = $xoopsDB->query($query) or die("Query failed rank109 $query");
		while($Rank = mysql_fetch_assoc($result))
		{
			$id = strval($Rank['id']);
			$ranking[$id] += $Rank['pt'];
			$out[$id] += $Rank['pt'];
		}
	}
	
	if (!empty($options[8]))
	{
		$limit = " LIMIT {$options[8]}";
		if ($q_nolist)
			$query="SELECT r.id,COUNT(r.id) AS pt, l.category FROM ".$xoopsDB->prefix("yomi_rev")." r LEFT JOIN ".$xoopsDB->prefix("yomi_log")." l ON r.id = l.id WHERE (time BETWEEN $start AND $end) AND {$q_nolist} GROUP BY id ORDER BY pt DESC{$limit}";
		else
			$query="SELECT id,COUNT(*) AS pt FROM ".$xoopsDB->prefix("yomi_rev")." WHERE time BETWEEN $start AND $end GROUP BY id ORDER BY pt DESC{$limit}";
		$result = $xoopsDB->query($query) or die("Query failed rank109 $query");
		while($Rank = mysql_fetch_assoc($result))
		{
			$id = strval($Rank['id']);
			$ranking[$id] += $Rank['pt'];
			$in[$id] += $Rank['pt'];
		}
	}
	
	arsort($ranking);
	$count = 0;
	foreach ($ranking as $id => $pt)
	{
		$count ++;
		if ($count > $kensu) break;
		$kt_fl=0;
		$query="SELECT * FROM ".$xoopsDB->prefix("yomi_log")." WHERE id='".$id."' LIMIT 1";
		$result2 = $xoopsDB->query($query) or die("Query failed rank120 $query");
		$Slog = mysql_fetch_assoc($result2);
		if($Slog['id']){
			$Slog['pt'] = $pt;
			$Slog['out'] = (isset($out[$id]))? $out[$id] : 0;
			$Slog['in'] = (isset($in[$id]))? $in[$id] : 0;
			array_push($log_lines,$Slog);
		$Clog++;
		}
	}
	if($Clog){
		$block['content'] = b_yomi_show_cols($log_lines,$options[0],$options[2],$options[3],$options[4],$options[5]);
		$block['content'] .= "<div style=\"text-align:center\"><small>(過去".$kikan."日間のTOP".$kensu.")</small></div>";
	} else {
		$block['content'] .= "ランキングデータを集計中";
	}

	return $block;
}	
function b_yomi_s2_edit($options) {
	$form = "<table>";
	$form .= "<tr><td>表示列数</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[0]."' /></td></tr>";
	$form .= "<tr><td>表示行数</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[1]."' /></td></tr>";
	$form .= "<tr><td>サイト名最大文字数(半角文字数)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[2]."' /></td></tr>";
	$form .= "<tr><td>サイト説明最大文字数(半角文字数)<br />※ 0 を指定で表示しません。</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[3]."' /></td></tr>";
	$form .= "<tr><td>バナー幅指定(px)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[4]."' /></td></tr>";
	$form .= "<tr><td>バナー高指定(px)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[5]."' /></td></tr>";
	$form .= "<tr><td colspan=2>※バナー幅,バナー高共に 0 を指定の場合バナーを表示しません。<br / >どちらか一方を0以上で指定した場合は、その値のみセットされます。</td></tr>";
	$form .= "<tr><td>集計期間(日)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[6]."' /></td></tr>";
	$form .= "<tr><td>集計対象最大件数(In)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[7]."' /></td></tr>";
	$form .= "<tr><td>集計対象最大件数(Out)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[8]."' /></td></tr>";
	$form .= "<tr><td>除外するカテゴリNo. (半角カンマ[,]で区切る)</td>";
	$form .= "<td><input type='text' name='options[]' value='".$options[9]."' /></td></tr>";
	$form .= "</table>";
	
	return $form;
}
?>
