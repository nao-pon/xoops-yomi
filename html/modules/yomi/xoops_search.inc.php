<?php
function yomi_search($queryarray, $andor, $limit, $offset, $userid){
	global $xoopsDB;
	$sql = "SELECT id,title,url,message,comment,stamp,keywd,uid FROM ".$xoopsDB->prefix("yomi_log")." WHERE";
	
	if ( $userid != 0 ) {
		$sql .= " uid=".$userid;
	} else {
		$sql .= " id>0";
	}
	
	// because count() returns 1 even if a supplied variable
	// is not an array, we must check if $querryarray is really an array
	if ( is_array($queryarray) && $count = count($queryarray) ) {
		$sql .= " AND ((title LIKE '%$queryarray[0]%' OR url LIKE '%$queryarray[0]%' OR message LIKE '%$queryarray[0]%' OR comment LIKE '%$queryarray[0]%' OR keywd LIKE '%$queryarray[0]%')";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor ";
			$sql .= "(title LIKE '%$queryarray[$i]%' OR url LIKE '%$queryarray[$i]%' OR message LIKE '%$queryarray[$i]%' OR comment LIKE '%$queryarray[$i]%' OR keywd LIKE '%$queryarray[$i]%')";
		}
		$sql .= ") ";
	}
	$sql .= " ORDER BY stamp DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);
	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
		$ret[$i]['image'] = "img/home.gif";
		$ret[$i]['link'] = "single_link.php?item_id=".$myrow['id'];
		$ret[$i]['title'] = $myrow['title'];
		$ret[$i]['time'] = $myrow['stamp'];
		$ret[$i]['uid'] = $myrow['uid'];
		if (function_exists("xoops_make_context"))
			$ret[$i]['context'] = xoops_make_context($myrow['message'],$queryarray);
		$i++;
	}
	return $ret;
}
?>