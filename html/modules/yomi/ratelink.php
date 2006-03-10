<?php
// ------------------------------------------------------------------------- //
//                XOOPS - PHP Content Management System                      //
//                       <http://www.xoops.org/>                             //
// ------------------------------------------------------------------------- //
// Based on:								     //
// myPHPNUKE Web Portal System - http://myphpnuke.com/	  		     //
// PHP-NUKE Web Portal System - http://phpnuke.org/	  		     //
// Thatware - http://thatware.org/					     //
// ------------------------------------------------------------------------- //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------- //
include("../../mainfile.php");

$xoopsOption['show_rblock'] =0;
include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

global $xoopsUser,$xoopsDB,$_POST;

include_once(XOOPS_ROOT_PATH."/class/module.errorhandler.php");
//for XOOPS 2
if (file_exists(XOOPS_ROOT_PATH."/include/old_functions.php"))
	include_once(XOOPS_ROOT_PATH."/include/old_functions.php");
//--------------//

$myts =& MyTextSanitizer::getInstance(); // MyTextSanitizer object
if($_POST['submit']) {
	$eh = new ErrorHandler; //ErrorHandler object
	if(!$xoopsUser){
		$ratinguser = 0;
	}else{
		$ratinguser = $xoopsUser->uid();
	}
    	//Make sure only 1 anonymous from an IP in a single day.
    	$anonwaitdays = 1;
    	$ip = getenv("REMOTE_ADDR");
	$lid = intval($_POST['lid']);
	$rating = intval($_POST['rating']);
    	// Check if Rating is Null
    	if ($rating=="--") {
		redirect_header("ratelink.php?lid=".$lid."",4,_MD_NORATING);
		exit();
    	}
    	// Check if Link POSTER is voting (UNLESS Anonymous users allowed to post)
    	if ($ratinguser != 0) {
        	$result=$xoopsDB->query("select uid from ".$xoopsDB->prefix("yomi_log")." where id=$lid");
        	while(list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
        		if ($ratinguserDB == $ratinguser) {
				redirect_header("single_link.php?item_id=".$lid,4,_MD_CANTVOTEOWN);
				exit();
                	}
        	}

    	// Check if REG user is trying to vote twice.
    		$result=$xoopsDB->query("select ratinguser from ".$xoopsDB->prefix("yomi_votedata")." where lid=$lid");
        	while(list($ratinguserDB) = $xoopsDB->fetchRow($result)) {
        		if ($ratinguserDB == $ratinguser) {
				redirect_header("single_link.php?item_id=".$lid,4,_MD_VOTEONCE2);
				exit();
                	}
        	}
    	}
     	// Check if ANONYMOUS user is trying to vote more than once per day.
    	if ($ratinguser == 0){
    		$yesterday = (time()-(86400 * $anonwaitdays));
        	$result=$xoopsDB->query("select count(*) FROM ".$xoopsDB->prefix("yomi_votedata")." WHERE lid=$lid AND ratinguser=0 AND ratinghostname = '$ip' AND ratingtimestamp > $yesterday");
    		list($anonvotecount) = $xoopsDB->fetchRow($result);
    		if ($anonvotecount > 0) {
			redirect_header("single_link.php?item_id=".$lid,4,_MD_VOTEONCE2);
			exit();
        	}
    	}
	if($rating > 10){
		$rating = 10;
	}
        //All is well.  Add to Line Item Rate to DB.
	$newid = $xoopsDB->genId($xoopsDB->prefix("yomi_votedata")."_ratingid_seq");
	$datetime = time();
    	$xoopsDB->query("INSERT INTO ".$xoopsDB->prefix("yomi_votedata")." (ratingid, lid, ratinguser, rating, ratinghostname, ratingtimestamp) VALUES ($newid, $lid, $ratinguser, $rating, '$ip', $datetime)") or $eh->show("0013");
        //All is well.  Calculate Score & Add to Summary (for quick retrieval & sorting) to DB.
        updaterating($lid);
	$ratemessage = _MD_VOTEAPPRE."<br>".sprintf(_MD_THANKURATE,$xoopsConfig[sitename]);
	redirect_header("single_link.php?item_id=".$lid,2,$ratemessage);
	exit();
} else {
    	include(XOOPS_ROOT_PATH."/header.php");
	$lid = $_GET['lid'];
    	OpenTable();
    	//mainheader();
    	$result=$xoopsDB->query("select title from ".$xoopsDB->prefix("yomi_log")." where id=$lid");
	list($title) = $xoopsDB->fetchRow($result);
	$title = $myts->makeTboxData4Show($title);
//	$title = $myts->oopsHtmlSpecialChars($title);

    	echo "
    	<hr size=1 noshade>
	<table border=0 cellpadding=1 cellspacing=0 width=\"80%\"><tr><td>
    	<h4><center>$title</center></h4>
    	<ul>
     	<li>"._MD_VOTEONCE."
     	<li>"._MD_RATINGSCALE."
     	<li>"._MD_BEOBJECTIVE."
     	<li>"._MD_DONOTVOTE."";
    	echo "
     	</ul>
     	</td></tr>
     	<tr><td align=\"center\">
     	<form method=\"POST\" action=\"ratelink.php\">
     	<input type=\"hidden\" name=\"lid\" value=\"".$lid."\">
     	<select name=\"rating\">
     	<option>--</option>";
     	for($i=10;$i>0;$i--){
		echo "<option value=\"".$i."\">".$i."</option>\n";
	}
     	echo "</select>&nbsp;&nbsp;<input type=\"submit\" name=\"submit\" value=\""._MD_RATEIT."\">\n";
	echo "&nbsp;<input type=button value="._MD_CANCEL." onclick=\"javascript:history.go(-1)\">\n";
    	echo "</form></td></tr></table>\n";
    	CloseTable();    	
	
}

include(XOOPS_ROOT_PATH."/footer.php");

function updaterating($sel_id){
	global $xoopsDB;
	$query = "select rating FROM ".$xoopsDB->prefix("yomi_votedata")." WHERE lid = ".$sel_id."";
	//echo $query;
	$voteresult = $xoopsDB->query($query);
    	$votesDB = $xoopsDB->getRowsNum($voteresult);
	$totalrating = 0;
    	while(list($rating)=$xoopsDB->fetchRow($voteresult)){
		$totalrating += $rating;
	}
	$finalrating = $totalrating/$votesDB;
	$finalrating = number_format($finalrating, 4);
	$query =  "UPDATE ".$xoopsDB->prefix("yomi_log")." SET rating=$finalrating, votes=$votesDB WHERE id = $sel_id";
	//echo $query;
    	$xoopsDB->query($query) or die();
}

?>